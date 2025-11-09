<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

checkAdminAuth();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$page_title = "Управление бронированиями";
include '../includes/header-admin.php';

switch ($action) {
    case 'view':
        // Просмотр деталей бронирования
        $booking = getBookingDetails($booking_id);
        if (!$booking) {
            header("Location: bookings.php");
            exit;
        }
        ?>
        <div class="admin-container">
            <h1>Бронирование #<?= $booking['booking_id']; ?></h1>
            
            <div class="booking-details">
                <div class="detail-section">
                    <h2>Информация о бронировании</h2>
                    <div class="detail-row">
                        <span class="detail-label">Номер:</span>
                        <span class="detail-value"><?= $booking['room_number']; ?> (<?= $booking['type_name']; ?>)</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Даты:</span>
                        <span class="detail-value"><?= date('d.m.Y', strtotime($booking['check_in_date'])); ?> - <?= date('d.m.Y', strtotime($booking['check_out_date'])); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Гости:</span>
                        <span class="detail-value"><?= $booking['adults']; ?> взрослых, <?= $booking['children']; ?> детей</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Статус:</span>
                        <span class="detail-value status-badge <?= $booking['status']; ?>"><?= getBookingStatusText($booking['status']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Общая стоимость:</span>
                        <span class="detail-value"><?= number_format($booking['total_amount'], 0, ',', ' '); ?> ₽</span>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h2>Информация о госте</h2>
                    <div class="detail-row">
                        <span class="detail-label">Имя:</span>
                        <span class="detail-value"><?= $booking['first_name']; ?> <?= $booking['last_name']; ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value"><?= $booking['email']; ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Телефон:</span>
                        <span class="detail-value"><?= $booking['phone']; ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Паспорт:</span>
                        <span class="detail-value"><?= $booking['passport_number']; ?></span>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h2>Дополнительные услуги</h2>
                    <?php if (!empty($booking['services'])): ?>
                        <table class="services-table">
                            <thead>
                                <tr>
                                    <th>Услуга</th>
                                    <th>Дата</th>
                                    <th>Количество</th>
                                    <th>Цена</th>
                                    <th>Сумма</th>
                                    <th>Статус</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($booking['services'] as $service): ?>
                                <tr>
                                    <td><?= $service['service_name']; ?></td>
                                    <td><?= date('d.m.Y', strtotime($service['service_date'])); ?></td>
                                    <td><?= $service['quantity']; ?></td>
                                    <td><?= number_format($service['price_per_unit'], 0, ',', ' '); ?> ₽</td>
                                    <td><?= number_format($service['price_per_unit'] * $service['quantity'], 0, ',', ' '); ?> ₽</td>
                                    <td><?= getServiceStatusText($service['status']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Нет дополнительных услуг</p>
                    <?php endif; ?>
                </div>
                
                <div class="action-buttons">
                    <a href="bookings.php" class="btn btn-secondary">Назад к списку</a>
                    <a href="bookings.php?action=edit&id=<?= $booking['booking_id']; ?>" class="btn btn-primary">Редактировать</a>
                </div>
            </div>
        </div>
        <?php
        break;
        
    case 'edit':
        // Редактирование бронирования
        $booking = getBookingDetails($booking_id);
        if (!$booking) {
            header("Location: bookings.php");
            exit;
        }
        
        $roomTypes = getRoomTypes();
        $statuses = [
            'confirmed' => 'Подтверждено',
            'cancelled' => 'Отменено',
            'checked-in' => 'Заселен',
            'checked-out' => 'Выселен',
            'no-show' => 'Не явился'
        ];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'room_id' => $_POST['room_id'],
                'check_in_date' => $_POST['check_in_date'],
                'check_out_date' => $_POST['check_out_date'],
                'adults' => $_POST['adults'],
                'children' => $_POST['children'],
                'status' => $_POST['status'],
                'total_amount' => $_POST['total_amount']
            ];
            
            if (updateBooking($booking_id, $data)) {
                $_SESSION['success_message'] = 'Бронирование успешно обновлено';
                header("Location: bookings.php?action=view&id=$booking_id");
                exit;
            } else {
                $error_message = 'Ошибка при обновлении бронирования';
            }
        }
        ?>
        <div class="admin-container">
            <h1>Редактирование бронирования #<?= $booking['booking_id']; ?></h1>
            
            <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= $error_message; ?></div>
            <?php endif; ?>
            
            <form method="POST" class="booking-edit-form">
                <div class="form-grid">
                    <div class="form-column">
                        <div class="form-group">
                            <label for="room_id">Номер</label>
                            <select id="room_id" name="room_id" class="form-control" required>
                                <?php foreach ($roomTypes as $type): ?>
                                <optgroup label="<?= $type['type_name']; ?>">
                                    <?php 
                                    $rooms = getRoomsByType($type['type_id']);
                                    foreach ($rooms as $room): 
                                    ?>
                                    <option value="<?= $room['room_id']; ?>" <?= $room['room_id'] == $booking['room_id'] ? 'selected' : ''; ?>>
                                        №<?= $room['room_number']; ?> (<?= $type['type_name']; ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="check_in_date">Дата заезда</label>
                            <input type="date" id="check_in_date" name="check_in_date" class="form-control" 
                                   value="<?= date('Y-m-d', strtotime($booking['check_in_date'])); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="check_out_date">Дата выезда</label>
                            <input type="date" id="check_out_date" name="check_out_date" class="form-control" 
                                   value="<?= date('Y-m-d', strtotime($booking['check_out_date'])); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-column">
                        <div class="form-group">
                            <label for="adults">Взрослые</label>
                            <select id="adults" name="adults" class="form-control" required>
                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                <option value="<?= $i; ?>" <?= $i == $booking['adults'] ? 'selected' : ''; ?>><?= $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="children">Дети</label>
                            <select id="children" name="children" class="form-control">
                                <?php for ($i = 0; $i <= 2; $i++): ?>
                                <option value="<?= $i; ?>" <?= $i == $booking['children'] ? 'selected' : ''; ?>><?= $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Статус</label>
                            <select id="status" name="status" class="form-control" required>
                                <?php foreach ($statuses as $key => $value): ?>
                                <option value="<?= $key; ?>" <?= $key == $booking['status'] ? 'selected' : ''; ?>><?= $value; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="total_amount">Общая сумма</label>
                            <input type="number" id="total_amount" name="total_amount" class="form-control" 
                                   value="<?= $booking['total_amount']; ?>" required step="0.01">
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                    <a href="bookings.php?action=view&id=<?= $booking_id; ?>" class="btn btn-secondary">Отмена</a>
                </div>
            </form>
        </div>
        <?php
        break;
        
    default:
        // Список бронирований
        $bookings = getAllBookings();
        ?>
        <div class="admin-container">
            <div class="admin-header">
                <h1>Управление бронированиями</h1>
                <a href="bookings.php?action=add" class="btn btn-primary">Добавить бронирование</a>
            </div>
            
            <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success_message']; ?></div>
            <?php unset($_SESSION['success_message']); endif; ?>
            
            <div class="bookings-filter">
                <form method="GET" class="filter-form">
                    <input type="hidden" name="action" value="list">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="status_filter">Статус</label>
                            <select id="status_filter" name="status" class="form-control">
                                <option value="">Все статусы</option>
                                <option value="confirmed" <?= isset($_GET['status']) && $_GET['status'] == 'confirmed' ? 'selected' : ''; ?>>Подтверждено</option>
                                <option value="cancelled" <?= isset($_GET['status']) && $_GET['status'] == 'cancelled' ? 'selected' : ''; ?>>Отменено</option>
                                <option value="checked-in" <?= isset($_GET['status']) && $_GET['status'] == 'checked-in' ? 'selected' : ''; ?>>Заселен</option>
                                <option value="checked-out" <?= isset($_GET['status']) && $_GET['status'] == 'checked-out' ? 'selected' : ''; ?>>Выселен</option>
                                <option value="no-show" <?= isset($_GET['status']) && $_GET['status'] == 'no-show' ? 'selected' : ''; ?>>Не явился</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="date_from">С</label>
                            <input type="date" id="date_from" name="date_from" class="form-control" 
                                   value="<?= isset($_GET['date_from']) ? $_GET['date_from'] : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="date_to">По</label>
                            <input type="date" id="date_to" name="date_to" class="form-control" 
                                   value="<?= isset($_GET['date_to']) ? $_GET['date_to'] : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-secondary">Фильтровать</button>
                            <a href="bookings.php" class="btn btn-outline">Сбросить</a>
                        </div>
                    </div>
                </form>
            </div>
            
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
                    <?php foreach ($bookings as $booking): ?>
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
        <?php
}
?>

<?php include '../includes/footer-admin.php'; ?>