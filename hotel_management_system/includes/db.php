<?php
// Подключение менеджера безопасности и контроллеров
require_once 'SecurityManager.php';
require_once 'BookingController.php';
require_once 'AuthController.php';

// Параметры подключения к базе данных
$servername = "localhost";
$username = "root"; // замените на вашего пользователя БД
$password = ""; // замените на ваш пароль БД
$dbname = "hotel_management_system";

// Создание подключения
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Установка кодировки
$conn->set_charset("utf8mb4");

// Создание экземпляров классов
$security = new SecurityManager();
$bookingController = new BookingController($conn, $security);
$authController = new AuthController($conn, $security);

// Функция для безопасного вывода данных
function safe_output($data) {
    global $security;
    return $security->sanitizeOutput($data);
}

// Функция для проверки аутентификации
function require_auth() {
    global $authController;
    if (!$authController->validateSession()) {
        header("Location: login.php");
        exit();
    }
}

// Функция для проверки прав доступа
function require_role($requiredRole) {
    global $authController;
    if (!$authController->checkPermission($requiredRole)) {
        http_response_code(403);
        die("Доступ запрещен. Недостаточно прав.");
    }
}

// Генерация CSRF токена для форм
function generate_csrf_token() {
    global $security;
    return $security->generateCSRFToken();
}

// Проверка CSRF токена
function verify_csrf_token($token) {
    global $security;
    try {
        return $security->verifyCSRFToken($token);
    } catch (Exception $e) {
        return false;
    }
}
?>