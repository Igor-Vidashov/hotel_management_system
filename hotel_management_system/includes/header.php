
<?php
// header.php - Шапка сайта отеля The Ritz-Carlton Moscow
// Переменные для динамического контента
$hotel_name = "The Ritz-Carlton Moscow";
$phone_number = "+7 (495) 225-8888";
$email = "info@ritzcarltonmoscow.com";
$base_url = "/hotel_management_system"; // Базовая директория проекта

// Массив навигационных пунктов
$nav_items = [
    'index.php' => 'Главная',
    'rooms.php' => 'Номера',
    'services.php' => 'Услуги',
    'about.php' => 'Об отеле',
    'contact.php' => 'Контакты'
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?php echo $hotel_name; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            margin: 0; 
            padding: 0; 
            font-family: helvetica, arial, sans-serif; 
        }
        .container { 
            width: 1300px; 
            margin: 0 auto; 
        }
        .header-contacts { 
            margin: 10px 0; 
        }
        .contact-phone, .contact-email { 
            margin: 5px 0; 
        }
        .logo-img {
            display: block;
            margin: 0 auto;
        }
        .hotel-title {
            font-size: 24px;
            font-weight: bold;
            display: block;
            margin-bottom: 15px;
            text-decoration: none;
            color: #000;
        }
        .booking-btn {
            display: block;
            border: none;
            background: none;
            cursor: pointer;
        }
        .booking-btn img {
            display: block;
            transition: transform 0.3s ease;
        }
        .booking-btn img:hover {
            transform: scale(1.05);
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
        }
        .main-table td {
            padding: 20px;
            vertical-align: middle;
            text-align: center;
        }
        .nav-row {
            background: #f8f8f8;
        }
        .nav-row td {
            padding: 25px 10px;
            border-top: 2px solid #ddd;
        }
        .nav-link {
            text-decoration: none;
            color: #333;
            font-size: 24px;
            font-family: helvetica, arial, sans-serif;
            transition: color 0.3s ease;
            padding: 15px 25px;
        }
        .nav-link:hover {
            color: #007bff;
            background-color: #f0f0f0;
            border-radius: 5px;
        }
        .content-row td {
            padding: 40px 20px;
        }
    </style>
</head>
<body>

<table class="main-table" width="1300">
    <tbody>
        <!-- Первая строка с логотипом, информацией и кнопкой -->
        <tr class="content-row">
            <td width="300">
                <img src="<?php echo $base_url; ?>/assets/images/logo.png" height="300" width="300" class="logo-img" alt="<?php echo $hotel_name; ?>" />
            </td>
            <td width="500">
                <a href="<?php echo $base_url; ?>/index.php" class="hotel-title"><?php echo $hotel_name; ?></a>
                <div class="header-contacts">
                    <div class="contact-phone">
                        <i class="fas fa-phone"></i> 
                        <a href="tel:<?php echo str_replace(' ', '', $phone_number); ?>" style="text-decoration: none; color: #000;">
                            <?php echo $phone_number; ?>
                        </a>
                    </div>
                    <div class="contact-email">
                        <i class="fas fa-envelope"></i> 
                        <a href="mailto:<?php echo $email; ?>" style="text-decoration: none; color: #000;">
                            <?php echo $email; ?>
                        </a>
                    </div>
                </div>
            </td>
            <td width="300">
                <a href="<?php echo $base_url; ?>/booking.php" class="booking-btn">
                    <img src="<?php echo $base_url; ?>/assets/images/brone.png" title="Забронировать" height="60" width="280" alt="Забронировать номер" />
                </a>
            </td>
        </tr>
        
        <!-- Вторая строка с навигацией -->
        <tr class="nav-row">
            <td colspan="3">
                <table width="100%" style="border: none;">
                    <tr>
                        <?php foreach ($nav_items as $url => $title): ?>
                            <td width="20%">
                                <a href="<?php echo $base_url . '/' . $url; ?>" class="nav-link">
                                    <?php echo $title; ?>
                                </a>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                </table>
            </td>
        </tr>
    </tbody>
</table>

<!-- Основной контент страницы будет здесь -->
<div class="container">
