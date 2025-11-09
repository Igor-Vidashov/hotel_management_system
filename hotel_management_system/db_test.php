<?php
$host = 'localhost';
$user = 'root';
$password = ''; // Для XAMPP пароль обычно пустой
$database = 'hotel_management_system';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
} else {
    echo "Подключение к БД успешно!<br>";
    
    // Проверяем данные
    $result = $conn->query("SELECT * FROM hotels WHERE hotel_id = 1");
    if ($result && $row = $result->fetch_assoc()) {
        echo "Отель: " . $row['name'] . "<br>";
        echo "Адрес: " . $row['address'];
    }
    
    $conn->close();
}
?>