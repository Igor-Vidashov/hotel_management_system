<?php
// debug.php
echo "<h1>Debug Information</h1>";

// 1. Проверка PHP
echo "<p>PHP Version: " . phpversion() . "</p>";

// 2. Проверка подключения файлов
$files = ['config.php', 'db.php', 'functions.php', 'header.php'];
foreach ($files as $file) {
    if (file_exists('includes/' . $file)) {
        echo "<p>✓ includes/$file exists</p>";
    } else {
        echo "<p>✗ includes/$file NOT FOUND</p>";
    }
}

// 3. Проверка подключения к БД
require_once 'includes/config.php';
require_once 'includes/db.php';

if ($conn->connect_error) {
    echo "<p>✗ DB Connection Error: " . $conn->connect_error . "</p>";
} else {
    echo "<p>✓ DB Connection Successful</p>";
    
    // 4. Проверка таблиц
    $result = $conn->query("SHOW TABLES");
    if ($result) {
        echo "<p>✓ Tables exist: " . $result->num_rows . " tables found</p>";
    }
}

// 5. Проверка функций
if (function_exists('getHotelDetails')) {
    echo "<p>✓ getHotelDetails() function exists</p>";
} else {
    echo "<p>✗ getHotelDetails() function NOT FOUND</p>";
}
?>