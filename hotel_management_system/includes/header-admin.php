<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title; ?> - Админ панель</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2>The Ritz-Carlton</h2>
                <p>Административная панель</p>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Дашборд</a></li>
                    <li><a href="bookings.php" class="<?= basename($_SERVER['PHP_SELF']) == 'bookings.php' ? 'active' : ''; ?>"><i class="fas fa-calendar-check"></i> Бронирования</a></li>
                    <li><a href="rooms.php" class="<?= basename($_SERVER['PHP_SELF']) == 'rooms.php' ? 'active' : ''; ?>"><i class="fas fa-bed"></i> Номера</a></li>
                    <li><a href="services.php" class="<?= basename($_SERVER['PHP_SELF']) == 'services.php' ? 'active' : ''; ?>"><i class="fas fa-concierge-bell"></i> Услуги</a></li>
                    <li><a href="/" target="_blank"><i class="fas fa-external-link-alt"></i> Перейти на сайт</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Выйти</a></li>
                </ul>
            </nav>
        </aside>
        
        <main class="admin-main">
            <header class="admin-header">
                <div class="header-left">
                    <button class="sidebar-toggle"><i class="fas fa-bars"></i></button>
                    <h1><?= $page_title; ?></h1>
                </div>
                <div class="header-right">
                    <div class="admin-profile">
                        <span><?= $_SESSION['admin_username'] ?? 'Администратор'; ?></span>
                        <i class="fas fa-user-circle"></i>
                    </div>
                </div>
            </header>
            
            <div class="admin-content">