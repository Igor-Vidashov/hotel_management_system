<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

checkAdminAuth();

$page_title = "Админ панель - Главная";
include '../includes/header-admin.php';

$stats = getDashboardStats();
?>

<div class="admin-container">
    <h1>Панель управления</h1>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?= $stats['total_bookings']; ?></div>
            <div class="stat-label">Всего бронирований</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value"><?= $stats['active_bookings']; ?></div>
            <div class="stat-label">Активные бронирования</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value"><?= $stats['available_rooms']; ?></div>
            <div class="stat-label">Доступные номера</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value"><?= $stats['total_revenue']; ?> ₽</div>
            <div class="stat-label">Общий доход</div>
        </div>
    </div>
    
    <div class="recent-bookings">
        <h2>Последние бронирования</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Гость</th>
                    <th>Номер</th>
                    <th>Даты</th>
                    <th>Сумма</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats['recent_bookings'] as $booking): ?>
                <tr>
                    <td><?= $booking['booking_id']; ?></td>
                    <td><?= $booking['guest_name']; ?></td>
                    <td><?= $booking['room_number']; ?></td>
                    <td><?= date('d.m.Y', strtotime($booking['check_in_date'])); ?> - <?= date('d.m.Y', strtotime($booking['check_out_date'])); ?></td>
                    <td><?= number_format($booking['total_amount'], 0, ',', ' '); ?> ₽</td>
                    <td><span class="status-badge <?= $booking['status']; ?>"><?= getBookingStatusText($booking['status']); ?></span></td>
                    <td>
                        <a href="bookings.php?action=view&id=<?= $booking['booking_id']; ?>" class="btn-action view" title="Просмотр"><i class="fas fa-eye"></i></a>
                        <a href="bookings.php?action=edit&id=<?= $booking['booking_id']; ?>" class="btn-action edit" title="Редактировать"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer-admin.php'; ?>