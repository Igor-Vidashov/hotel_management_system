<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

checkAdminAuth();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$service_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$page_title = "Управление услугами";
include '../includes/header-admin.php';

switch ($action) {
    case 'add':
        // Добавление новой услуги
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'service_name' => $_POST['service_name'],
                'description' => $_POST['description'],
                'price' => $_POST['price'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            
            if (addService($data)) {
                $_SESSION['success_message'] = 'Услуга успешно добавлена';
                header("Location: services.php");
                exit;
            } else {
                $error_message = 'Ошибка при добавлении услуги';
            }
        }
        ?>
        <div class="admin-container">
            <h1>Добавить услугу</h1>
            
            <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= $error_message; ?></div>
            <?php endif; ?>
            
            <form method="POST" class="service-form">
                <div class="form-group">
                    <label for="service_name">Название услуги</label>
                    <input type="text" id="service_name" name="service_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Описание</label>
                    <textarea id="description" name="description" class="form-control" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="price">Цена</label>
                    <input type="number" id="price" name="price" class="form-control" min="0" step="0.01" required>
                </div>
                
                <div class="form-group form-check">
                    <input type="checkbox" id="is_active" name="is_active" class="form-check-input" checked>
                    <label for="is_active" class="form-check-label">Активна</label>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Добавить услугу</button>
                    <a href="services.php" class="btn btn-secondary">Отмена</a>
                </div>
            </form>
        </div>
        <?php
        break;
        
    case 'edit':
        // Редактирование услуги
        $service = getServiceById($service_id);
        if (!$service) {
            header("Location: services.php");
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'service_name' => $_POST['service_name'],
                'description' => $_POST['description'],
                'price' => $_POST['price'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            
            if (updateService($service_id, $data)) {
                $_SESSION['success_message'] = 'Услуга успешно обновлена';
                header("Location: services.php");
                exit;
            } else {
                $error_message = 'Ошибка при обновлении услуги';
            }
        }
        ?>
        <div class="admin-container">
            <h1>Редактировать услугу</h1>
            
            <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= $error_message; ?></div>
            <?php endif; ?>
            
            <form method="POST" class="service-form">
                <div class="form-group">
                    <label for="service_name">Название услуги</label>
                    <input type="text" id="service_name" name="service_name" class="form-control" 
                           value="<?= $service['service_name']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Описание</label>
                    <textarea id="description" name="description" class="form-control" rows="4"><?= $service['description']; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="price">Цена</label>
                    <input type="number" id="price" name="price" class="form-control" 
                           value="<?= $service['price']; ?>" min="0" step="0.01" required>
                </div>
                
                <div class="form-group form-check">
                    <input type="checkbox" id="is_active" name="is_active" class="form-check-input" <?= $service['is_active'] ? 'checked' : ''; ?>>
                    <label for="is_active" class="form-check-label">Активна</label>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                    <a href="services.php" class="btn btn-secondary">Отмена</a>
                </div>
            </form>
        </div>
        <?php
        break;
        
    case 'delete':
        // Удаление услуги
        if (deleteService($service_id)) {
            $_SESSION['success_message'] = 'Услуга успешно удалена';
        } else {
            $_SESSION['error_message'] = 'Ошибка при удалении услуги';
        }
        header("Location: services.php");
        exit;
        break;
        
    default:
        // Список услуг
        $services = getAllServices();
        ?>
        <div class="admin-container">
            <div class="admin-header">
                <h1>Управление услугами</h1>
                <a href="services.php?action=add" class="btn btn-primary">Добавить услугу</a>
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
                        <th>Название</th>
                        <th>Описание</th>
                        <th>Цена</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service): ?>
                    <tr>
                        <td><?= $service['service_id']; ?></td>
                        <td><?= $service['service_name']; ?></td>
                        <td><?= truncateText($service['description'], 50); ?></td>
                        <td><?= number_format($service['price'], 0, ',', ' '); ?> ₽</td>
                        <td>
                            <span class="status-badge <?= $service['is_active'] ? 'active' : 'inactive'; ?>">
                                <?= $service['is_active'] ? 'Активна' : 'Неактивна'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="services.php?action=edit&id=<?= $service['service_id']; ?>" class="btn-action edit" title="Редактировать"><i class="fas fa-edit"></i></a>
                            <a href="services.php?action=delete&id=<?= $service['service_id']; ?>" class="btn-action delete" title="Удалить" onclick="return confirm('Вы уверены, что хотите удалить эту услугу?');"><i class="fas fa-trash"></i></a>
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