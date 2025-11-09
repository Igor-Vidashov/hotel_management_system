<?php
// includes/config.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'hotel_management_system');

// Установка временной зоны
date_default_timezone_set('Europe/Moscow');

// Определяем базовый URL для сайта
$base_url = 'http://localhost/hotel_management_system';

// Инициализация сессии
session_start();

// Обработка ошибок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
