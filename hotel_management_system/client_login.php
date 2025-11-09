<?php
// Прямое подключение к базе данных через PDO
try {
    $host = 'localhost';
    $dbname = 'hotel_management_system';
    $username = 'root';
    $password = '';
    
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

require_once 'includes/SecurityManager.php';
require_once 'includes/AuthController.php';

$security = new SecurityManager();
$auth = new AuthController($db, $security);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
        $result = $auth->login($email, $password);
        
        if ($result['success']) {
            // Перенаправление в зависимости от роли
            if ($result['role'] === 'user') {
                header('Location: index.php');
                exit;
            } else {
                // Персонал перенаправляется в админку
                header('Location: admin/index.php');
                exit;
            }
        } else {
            $error = $result['message'];
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход для клиентов - The Ritz-Carlton Moscow</title>
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
            max-width: 400px;
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
            <h1>Вход для клиентов</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="" class="login-form">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           placeholder="Введите ваш email">
                </div>
                
                <div class="form-group">
                    <label for="password">Пароль:</label>
                    <input type="password" id="password" name="password" class="form-control" required
                           placeholder="Введите ваш пароль">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn-primary">Войти</button>
                </div>
            </form>
            
            <div class="auth-links">
                <p>Нет аккаунта? <a href="client_register.php">Зарегистрироваться</a></p>
                <p>Забыли пароль? <a href="forgot_password.php">Восстановить</a></p>
                <p>Вы сотрудник? <a href="login.php">Вход для персонала</a></p>
            </div>
            
            <a href="index.php" class="back-link">← Вернуться на главную</a>
        </div>
    </div>
</body>
</html>