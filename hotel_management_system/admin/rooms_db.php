<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

checkAdminAuth();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$room_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$page_title = "Управление номерами";
include '../includes/header-admin.php';

switch ($action) {
    case 'add':
        // Добавление нового номера
        $roomTypes = getRoomTypes();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'room_number' => $_POST['room_number'],
                'type_id' => $_POST['type_id'],
                'floor' => $_POST['floor'],
                'status' => $_POST['status']
            ];
            
            if (addRoom($data)) {
                $_SESSION['success_message'] = 'Номер успешно добавлен';
                header("Location: rooms.php");
                exit;
            } else {
                $error_message = 'Ошибка при добавлении номера';
            }
        }
        ?>
        <div class="admin-container">
            <h1>Добавить номер</h1>
            
            <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= $error_message; ?></div>
            <?php endif; ?>
            
            <form method="POST" class="room-form">
                <div class="form-group">
                    <label for="room_number">Номер комнаты</label>
                    <input type="text" id="room_number" name="room_number" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="type_id">Тип номера</label>
                    <select id="type_id" name="type_id" class="form-control" required>
                        <?php foreach ($roomTypes as $type): ?>
                        <option value="<?= $type['type_id']; ?>"><?= $type['type_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="floor">Этаж</label>
                    <input type="number" id="floor" name="floor" class="form-control" min="1" required>
                </div>
                
                <div class="form-group">
                    <label for="status">Статус</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="available">Доступен</option>
                        <option value="occupied">Занят</option>
                        <option value="maintenance">На обслуживании</option>
                        <option value="reserved">Зарезервирован</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Добавить номер</button>
                    <a href="rooms.php" class="btn btn-secondary">Отмена</a>
                </div>
            </form>
        </div>
        <?php
        break;
        
    case 'edit':
        // Редактирование номера
        $room = getRoomById($room_id);
        if (!$room) {
            header("Location: rooms.php");
            exit;
        }
        
        $roomTypes = getRoomTypes();
        $statuses = [
            'available' => 'Доступен',
            'occupied' => 'Занят',
            'maintenance' => 'На обслуживании',
            'reserved' => 'Зарезервирован'
        ];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'room_number' => $_POST['room_number'],
                'type_id' => $_POST['type_id'],
                'floor' => $_POST['floor'],
                'status' => $_POST['status']
            ];
            
            if (updateRoom($room_id, $data)) {
                $_SESSION['success_message'] = 'Номер успешно обновлен';
                header("Location: rooms.php");
                exit;
            } else {
                $error_message = 'Ошибка при обновлении номера';
            }
        }
        ?>
        <div class="admin-container">
            <h1>Редактировать номер #<?= $room['room_id']; ?></h1>
            
            <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= $error_message; ?></div>
            <?php endif; ?>
            
            <form method="POST" class="room-form">
                <div class="form-group">
                    <label for="room_number">Номер комнаты</label>
                    <input type="text" id="room_number" name="room_number" class="form-control" 
                           value="<?= $room['room_number']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="type_id">Тип номера</label>
                    <select id="type_id" name="type_id" class="form-control" required>
                        <?php foreach ($roomTypes as $type): ?>
                        <option value="<?= $type['type_id']; ?>" <?= $type['type_id'] == $room['type_id'] ? 'selected' : ''; ?>>
                            <?= $type['type_name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="floor">Этаж</label>
                    <input type="number" id="floor" name="floor" class="form-control" 
                           value="<?= $room['floor']; ?>" min="1" required>
                </div>
                
                <div class="form-group">
                    <label for="status">Статус</label>
                    <select id="status" name="status" class="form-control" required>
                        <?php foreach ($statuses as $key => $value): ?>
                        <option value="<?= $key; ?>" <?= $key == $room['status'] ? 'selected' : ''; ?>><?= $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                    <a href="rooms.php" class="btn btn-secondary">Отмена</a>
                </div>
            </form>
        </div>
        <?php
        break;
        
    case 'delete':
        // Удаление номера
        if (deleteRoom($room_id)) {
            $_SESSION['success_message'] = 'Номер успешно удален';
        } else {
            $_SESSION['error_message'] = 'Ошибка при удалении номера';
        }
        header("Location: rooms.php");
        exit;
        break;
        
    default:
        // Список номеров
        $rooms = getAllRooms();
        ?>
        <div class="admin-container">
            <div class="admin-header">
                <h1>Управление номерами</h1>
                <a href="rooms.php?action=add" class="btn btn-primary">Добавить номер</a>
            </div>
            
            <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success_message']; ?></div>
            <?php unset($_SESSION['success_message']); endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error_message']; ?></div>
            <?php unset($_SESSION['error_message']); endif; ?>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Номер</th>
                        <th>Тип</th>
                        <th>Этаж</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rooms as $room): ?>
                    <tr>
                        <td><?= $room['room_id']; ?></td>
                        <td>№<?= $room['room_number']; ?></td>
                        <td><?= $room['type_name']; ?></td>
                        <td><?= $room['floor']; ?></td>
                        <td><span class="status-badge <?= $room['status']; ?>"><?= $statuses[$room['status']]; ?></span></td>
                        <td>
                            <a href="rooms.php?action=edit&id=<?= $room['room_id']; ?>" class="btn-action edit" title="Редактировать"><i class="fas fa-edit"></i></a>
                            <a href="rooms.php?action=delete&id=<?= $room['room_id']; ?>" class="btn-action delete" title="Удалить" onclick="return confirm('Вы уверены, что хотите удалить этот номер?');"><i class="fas fa-trash"></i></a>
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