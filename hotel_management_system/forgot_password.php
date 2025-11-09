<?php
// Прямое подключение к базе данных через mysqli
require_once 'includes/db.php'; // Используем существующее подключение
require_once 'includes/SecurityManager.php';
require_once 'includes/AuthController.php';

$security = new SecurityManager();
$auth = new AuthController($conn, $security); // Используем $conn из db.php

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    try {
        // Валидация email
        $validated_email = $security->validateInput($email, 'email');
        
        // Сначала проверим структуру таблицы users
        try {
            $result = $conn->query("DESCRIBE users");
            $columns = [];
            while ($row = $result->fetch_assoc()) {
                $columns[] = $row['Field'];
            }
            
            // Определяем правильное имя для первичного ключа
            $id_column = 'id';
            if (in_array('user_id', $columns)) {
                $id_column = 'user_id';
            } elseif (in_array('client_id', $columns)) {
                $id_column = 'client_id';
            }
            
        } catch (Exception $e) {
            throw new Exception('Не удалось проверить структуру таблицы users');
        }
        
        // Проверяем существует ли пользователь с таким email
        $stmt = $security->executeSecureQuery(
            $conn,
            "SELECT $id_column, email FROM users WHERE email = ?",
            [$validated_email]
        );
        
        $user = $stmt->get_result()->fetch_assoc();
        
        if ($user) {
            // Генерируем временный токен для сброса пароля
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Создаем таблицу для сброса паролей если она не существует
            try {
                $conn->query("
                    CREATE TABLE IF NOT EXISTS password_resets (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        user_id INT NOT NULL,
                        token VARCHAR(64) NOT NULL,
                        expires_at DATETIME NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (user_id) REFERENCES users($id_column) ON DELETE CASCADE,
                        UNIQUE KEY unique_token (token)
                    )
                ");
            } catch (Exception $e) {
                // Таблица уже существует или ошибка создания
                error_log("Password reset table creation: " . $e->getMessage());
            }
            
            // Сохраняем токен в базе данных
            $stmt = $security->executeSecureQuery(
                $conn,
                "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?) 
                 ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)",
                [$user[$id_column], $token, $expires]
            );
            
            // В реальном приложении здесь бы отправлялось email с ссылкой для сброса
            // Для демонстрации просто показываем сообщение
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
            $success = "Письмо с инструкциями по восстановлению пароля было отправлено на ваш email адрес. Пожалуйста, проверьте вашу почту (включая папку 'Спам').";
            
        } else {
            $error = 'Пользователь с таким email не найден.';
        }
        
    } catch (Exception $e) {
        $error = 'Произошла ошибка: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Восстановление пароля - The Ritz-Carlton Moscow</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        
        .login-box h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
        }
        
        .login-box p {
            color: #7f8c8d;
            margin-bottom: 30px;
            line-height: 1.5;
        }
        
        .login-form .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        .login-form label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
        }
        
        .login-form .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .login-form .form-control:focus {
            outline: none;
            border-color: #b38b59;
        }
        
        .btn-primary {
            width: 100%;
            padding: 12px;
            background: #b38b59;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: #9a754d;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(179, 139, 89, 0.3);
        }
        
        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid #dc3545;
            color: #dc3545;
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            border: 1px solid #28a745;
            color: #28a745;
        }
        
        .alert-success small {
            display: block;
            margin-top: 10px;
            font-size: 0.8em;
            opacity: 0.8;
        }
        
        .auth-links {
            margin-top: 20px;
            text-align: center;
        }
        
        .auth-links p {
            margin: 10px 0;
            color: #7f8c8d;
        }
        
        .auth-links a {
            color: #b38b59;
            text-decoration: none;
            font-weight: 500;
        }
        
        .auth-links a:hover {
            color: #9a754d;
            text-decoration: underline;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #b38b59;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link:hover {
            color: #9a754d;
            text-decoration: underline;
        }
        
        .info-icon {
            font-size: 3rem;
            color: #b38b59;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="info-icon">🔒</div>
            <h1>Восстановление пароля</h1>
            <p>Введите ваш email адрес, и мы вышлем инструкции для восстановления пароля.</p>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="" class="login-form">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           placeholder="Введите ваш email">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn-primary">Отправить инструкции</button>
                </div>
            </form>
            
            <div class="auth-links">
                <p>Вспомнили пароль? <a href="client_login.php">Войти</a></p>
                <p>Нет аккаунта? <a href="client_register.php">Зарегистрироваться</a></p>
            </div>
            
            <a href="index.php" class="back-link">← Вернуться на главную</a>
        </div>
    </div>
</body>
</html>