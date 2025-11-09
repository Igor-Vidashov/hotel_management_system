<?php
// Прямое подключение к базе данных
try {
    $host = 'localhost';
    $dbname = 'hotel_management_system';
    $username = 'root';
    $password = '';
    
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Создаем таблицы если они не существуют
    createTablesIfNotExist($db);
    
} catch(PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

require_once 'includes/SecurityManager.php';
require_once 'includes/AuthController.php';

$security = new SecurityManager();
$auth = new AuthController($db, $security);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    
    try {
        if ($password !== $confirm_password) {
            throw new Exception('Пароли не совпадают');
        }
        
        // Исправленный вызов метода register с дополнительными параметрами
        $result = $auth->register($email, $password, $first_name, $last_name, $phone, 'user');
        
        if ($result['success']) {
            $success = 'Регистрация успешна! Теперь вы можете войти.';
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

/**
 * Создает необходимые таблицы если они не существуют
 */
function createTablesIfNotExist($db) {
    $tables = [
        "CREATE TABLE IF NOT EXISTS users (
            user_id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            role ENUM('user', 'admin', 'staff') DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            is_active BOOLEAN DEFAULT TRUE
        ) ENGINE=InnoDB",
        
        "CREATE TABLE IF NOT EXISTS guests (
            guest_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
        ) ENGINE=InnoDB",
        
        "CREATE TABLE IF NOT EXISTS password_resets (
            reset_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            token VARCHAR(64) NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
            UNIQUE KEY unique_token (token)
        ) ENGINE=InnoDB"
    ];
    
    foreach ($tables as $sql) {
        try {
            $db->exec($sql);
        } catch (PDOException $e) {
            error_log("Ошибка создания таблицы: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - The Ritz-Carlton Moscow</title>
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
            background: var(--white);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }
        
        .login-box h1 {
            color: var(--dark-color);
            margin-bottom: 30px;
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
        }
        
        .login-form .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        .login-form label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-color);
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
            border-color: var(--primary-color);
        }
        
        .btn-primary {
            width: 100%;
            padding: 12px;
            background: var(--primary-color);
            color: var(--white);
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
            border: 1px solid var(--danger-color);
            color: var(--danger-color);
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            border: 1px solid var(--success-color);
            color: var(--success-color);
        }
        
        .auth-links {
            margin-top: 20px;
            text-align: center;
        }
        
        .auth-links p {
            margin: 10px 0;
            color: var(--text-light);
        }
        
        .auth-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .auth-links a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Регистрация</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST" action="" class="login-form">
                <div class="form-group">
                    <label for="first_name">Имя:</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" required 
                           value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                           placeholder="Введите ваше имя">
                </div>
                
                <div class="form-group">
                    <label for="last_name">Фамилия:</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" required 
                           value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
                           placeholder="Введите вашу фамилию">
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           placeholder="Введите ваш email">
                </div>
                
                <div class="form-group">
                    <label for="phone">Телефон:</label>
                    <input type="tel" id="phone" name="phone" class="form-control" required 
                           value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                           placeholder="+7XXXXXXXXXX">
                </div>
                
                <div class="form-group">
                    <label for="password">Пароль:</label>
                    <input type="password" id="password" name="password" class="form-control" required
                           placeholder="Введите пароль">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Подтвердите пароль:</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required
                           placeholder="Повторите пароль">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn-primary">Зарегистрироваться</button>
                </div>
            </form>
            
            <div class="auth-links">
                <p>Уже есть аккаунт? <a href="client_login.php">Войти</a></p>
            </div>
            
            <a href="index.php" class="back-link">← Вернуться на главную</a>
        </div>
    </div>
</body>
</html>