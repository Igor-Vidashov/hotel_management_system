<?php
// includes/functions.php
require_once 'db.php';

function getHotelDetails() {
    global $conn;
    $sql = "SELECT * FROM hotels WHERE hotel_id = 1 LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        // Возвращаем заглушку если нет данных
        return [
            'name' => 'The Ritz-Carlton Moscow',
            'description' => 'Роскошный пятизвездочный отель',
            'star_rating' => 5,
            'address' => 'Тверская ул., 3',
            'city' => 'Москва',
            'country' => 'Россия',
            'check_in_time' => '14:00:00',
            'check_out_time' => '12:00:00'
        ];
    }
}

function getHotelAmenities() {
    global $conn;
    $sql = "SELECT a.* FROM hotel_amenities a
            JOIN hotel_amenity_relations ar ON a.amenity_id = ar.amenity_id
            WHERE ar.hotel_id = 1";
    $result = $conn->query($sql);
    $amenities = [];
    while ($row = $result->fetch_assoc()) {
        $amenities[] = $row;
    }
    return $amenities;
}

function getHotelPhotos() {
    global $conn;
    $sql = "SELECT * FROM hotel_photos WHERE hotel_id = 1 ORDER BY display_order";
    $result = $conn->query($sql);
    $photos = [];
    while ($row = $result->fetch_assoc()) {
        $photos[] = $row;
    }
    return $photos;
}

function getRoomTypes() {
    global $conn;
    $sql = "SELECT * FROM room_types WHERE hotel_id = 1";
    $result = $conn->query($sql);
    $roomTypes = [];
    while ($row = $result->fetch_assoc()) {
        $roomTypes[] = $row;
    }
    return $roomTypes;
}

function getRoomTypeById($type_id) {
    global $conn;
    
    $sql = "SELECT * FROM room_types WHERE type_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $type_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

function getAvailableRooms($type_id, $check_in, $check_out) {
    global $conn;
    
    $sql = "SELECT r.room_id, r.room_number, rt.type_name, rt.base_price 
            FROM rooms r
            JOIN room_types rt ON r.type_id = rt.type_id
            WHERE r.type_id = ? AND r.status = 'available'
            AND r.room_id NOT IN (
                SELECT b.room_id FROM bookings b
                WHERE b.check_in_date < ? 
                AND b.check_out_date > ?
                AND b.status IN ('confirmed', 'checked-in')
            )";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $type_id, $check_out, $check_in);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $rooms = [];
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
    return $rooms;
}

function createBooking($guest_data, $booking_data) {
    global $conn;
    
    // Начинаем транзакцию
    $conn->begin_transaction();
    
    try {
        // Создаем или находим гостя
        $guest_id = null;
        
        // Проверяем, существует ли гость с таким email
        $stmt = $conn->prepare("SELECT guest_id FROM guests WHERE email = ?");
        $stmt->bind_param("s", $guest_data['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $guest_id = $row['guest_id'];
            
            // Обновляем данные гостя
            $stmt = $conn->prepare("UPDATE guests SET first_name = ?, last_name = ?, phone = ?, passport_number = ? WHERE guest_id = ?");
            $stmt->bind_param("ssssi", $guest_data['first_name'], $guest_data['last_name'], $guest_data['phone'], $guest_data['passport'], $guest_id);
            $stmt->execute();
        } else {
            // Создаем нового гостя
            $stmt = $conn->prepare("INSERT INTO guests (first_name, last_name, email, phone, passport_number) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $guest_data['first_name'], $guest_data['last_name'], $guest_data['email'], $guest_data['phone'], $guest_data['passport']);
            $stmt->execute();
            $guest_id = $conn->insert_id;
        }
        
        // Создаем бронирование
        $stmt = $conn->prepare("INSERT INTO bookings (guest_id, room_id, check_in_date, check_out_date, adults, children, total_amount, hotel_id) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->bind_param("iissiid", $guest_id, $booking_data['room_id'], $booking_data['check_in'], $booking_data['check_out'], $booking_data['adults'], $booking_data['children'], $booking_data['total_amount']);
        $stmt->execute();
        $booking_id = $conn->insert_id;
        
        // Обновляем статус номера
        $stmt = $conn->prepare("UPDATE rooms SET status = 'reserved' WHERE room_id = ?");
        $stmt->bind_param("i", $booking_data['room_id']);
        $stmt->execute();
        
        // Создаем платеж
        $stmt = $conn->prepare("INSERT INTO payments (booking_id, amount, payment_method, status) VALUES (?, ?, 'online', 'completed')");
        $stmt->bind_param("id", $booking_id, $booking_data['total_amount']);
        $stmt->execute();
        
        // Подтверждаем транзакцию
        $conn->commit();
        
        return [
            'success' => true,
            'booking_id' => $booking_id,
            'guest_id' => $guest_id
        ];
        
    } catch (Exception $e) {
        // Откатываем транзакцию при ошибке
        $conn->rollback();
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function checkRoomAvailability($room_id, $check_in, $check_out) {
    global $conn;
    
    $sql = "SELECT COUNT(*) as count FROM bookings 
            WHERE room_id = ? AND status IN ('confirmed', 'checked-in')
            AND (
                (check_in_date <= ? AND check_out_date >= ?) OR
                (check_in_date <= ? AND check_out_date >= ?) OR
                (check_in_date >= ? AND check_out_date <= ?)
            )";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss", $room_id, $check_out, $check_in, $check_in, $check_out, $check_in, $check_out);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'] == 0;
}

function getSeasonalPrice($type_id, $date) {
    global $conn;
    
    $sql = "SELECT price_multiplier FROM seasonal_pricing 
            WHERE room_type_id = ? AND ? BETWEEN start_date AND end_date
            ORDER BY price_multiplier DESC LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $type_id, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['price_multiplier'];
    }
    
    return 1.0; // Множитель по умолчанию
}

function calculateTotalPrice($room_id, $check_in, $check_out, $adults, $children) {
    global $conn;
    
    // Получаем информацию о номере
    $sql = "SELECT rt.base_price, rt.capacity FROM rooms r
            JOIN room_types rt ON r.type_id = rt.type_id
            WHERE r.room_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $room = $result->fetch_assoc();
    
    if (!$room) {
        return false;
    }
    
    // Проверяем вместимость
    $total_guests = $adults + $children;
    if ($total_guests > $room['capacity']) {
        return false;
    }
    
    // Рассчитываем количество ночей
    $check_in_date = new DateTime($check_in);
    $check_out_date = new DateTime($check_out);
    $nights = $check_out_date->diff($check_in_date)->days;
    
    // Получаем сезонный множитель
    $seasonal_multiplier = getSeasonalPrice($room['type_id'], $check_in);
    
    // Рассчитываем общую стоимость
    $base_price = $room['base_price'];
    $total_price = $base_price * $seasonal_multiplier * $nights;
    
    // Возвращаем результат
    return [
        'base_price' => $base_price,
        'seasonal_multiplier' => $seasonal_multiplier,
        'nights' => $nights,
        'total_price' => $total_price
    ];
}

// Проверка авторизации администратора
function checkAdminAuth() {
    session_start();
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: login.php");
        exit;
    }
}

// Аутентификация администратора
function authenticateAdmin($username, $password) {
    // В реальной системе использую хеширование паролей и базу данных
    $admin_username = 'admin';
    $admin_password = 'password123'; // В реальном приложении храню хеш пароля
    
    return $username === $admin_username && $password === $admin_password;
}

function validateAdminLogin($username, $password) {
    // Временная заглушка - заменю на реальную проверку из базы данных
    $admin_username = 'admin';
    $admin_password = 'admin123'; // В реальном проекте использую хеширование!
    
    return $username === $admin_username && $password === $admin_password;
}

// Получение статистики для дашборда
function getDashboardStats() {
    global $conn;
    
    $stats = [];
    
    // Всего бронирований
    $sql = "SELECT COUNT(*) as total FROM bookings WHERE hotel_id = 1";
    $result = $conn->query($sql);
    $stats['total_bookings'] = $result->fetch_assoc()['total'];
    
    // Активные бронирования (подтвержденные или заселенные)
    $sql = "SELECT COUNT(*) as total FROM bookings WHERE hotel_id = 1 AND status IN ('confirmed', 'checked-in')";
    $result = $conn->query($sql);
    $stats['active_bookings'] = $result->fetch_assoc()['total'];
    
    // Доступные номера
    $sql = "SELECT COUNT(*) as total FROM rooms WHERE status = 'available'";
    $result = $conn->query($sql);
    $stats['available_rooms'] = $result->fetch_assoc()['total'];
    
    // Общий доход
    $sql = "SELECT SUM(total_amount) as total FROM bookings WHERE hotel_id = 1 AND status != 'cancelled'";
    $result = $conn->query($sql);
    $stats['total_revenue'] = $result->fetch_assoc()['total'] ?? 0;
    
    // Последние 5 бронирований
    $sql = "SELECT b.booking_id, b.check_in_date, b.check_out_date, b.total_amount, b.status, 
                   CONCAT(g.first_name, ' ', g.last_name) as guest_name,
                   r.room_number, rt.type_name
            FROM bookings b
            JOIN guests g ON b.guest_id = g.guest_id
            JOIN rooms r ON b.room_id = r.room_id
            JOIN room_types rt ON r.type_id = rt.type_id
            WHERE b.hotel_id = 1
            ORDER BY b.created_at DESC
            LIMIT 5";
    $result = $conn->query($sql);
    $stats['recent_bookings'] = [];
    while ($row = $result->fetch_assoc()) {
        $stats['recent_bookings'][] = $row;
    }
    
    return $stats;
}

// Получение текста статуса бронирования
function getBookingStatusText($status) {
    $statuses = [
        'confirmed' => 'Подтверждено',
        'cancelled' => 'Отменено',
        'checked-in' => 'Заселен',
        'checked-out' => 'Выселен',
        'no-show' => 'Не явился'
    ];
    
    return $statuses[$status] ?? $status;
}

// Получение текста статуса услуги
function getServiceStatusText($status) {
    $statuses = [
        'requested' => 'Запрошено',
        'confirmed' => 'Подтверждено',
        'delivered' => 'Выполнено',
        'cancelled' => 'Отменено'
    ];
    
    return $statuses[$status] ?? $status;
}

// Получение деталей бронирования
function getBookingDetails($booking_id) {
    global $conn;
    
    $sql = "SELECT b.*, 
                   CONCAT(g.first_name, ' ', g.last_name) as guest_name,
                   g.email, g.phone, g.passport_number,
                   r.room_number, r.floor,
                   rt.type_name, rt.base_price
            FROM bookings b
            JOIN guests g ON b.guest_id = g.guest_id
            JOIN rooms r ON b.room_id = r.room_id
            JOIN room_types rt ON r.type_id = rt.type_id
            WHERE b.booking_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return false;
    }
    
    $booking = $result->fetch_assoc();
    
    // Получаем дополнительные услуги для этого бронирования
    $sql = "SELECT bs.*, s.service_name
            FROM booking_services bs
            JOIN services s ON bs.service_id = s.service_id
            WHERE bs.booking_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $booking['services'] = [];
    while ($row = $result->fetch_assoc()) {
        $booking['services'][] = $row;
    }
    
    return $booking;
}

// Обновление бронирования
function updateBooking($booking_id, $data) {
    global $conn;
    
    $sql = "UPDATE bookings SET 
            room_id = ?,
            check_in_date = ?,
            check_out_date = ?,
            adults = ?,
            children = ?,
            status = ?,
            total_amount = ?,
            updated_at = NOW()
            WHERE booking_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issiisdi", 
        $data['room_id'],
        $data['check_in_date'],
        $data['check_out_date'],
        $data['adults'],
        $data['children'],
        $data['status'],
        $data['total_amount'],
        $booking_id
    );
    
    return $stmt->execute();
}

// Получение всех бронирований
function getAllBookings($filters = []) {
    global $conn;
    
    $sql = "SELECT b.booking_id, b.check_in_date, b.check_out_date, b.total_amount, b.status, 
                   CONCAT(g.first_name, ' ', g.last_name) as guest_name,
                   r.room_number, rt.type_name
            FROM bookings b
            JOIN guests g ON b.guest_id = g.guest_id
            JOIN rooms r ON b.room_id = r.room_id
            JOIN room_types rt ON r.type_id = rt.type_id
            WHERE b.hotel_id = 1";
    
    // Добавляем фильтры, если они есть
    if (!empty($filters['status'])) {
        $sql .= " AND b.status = '" . $conn->real_escape_string($filters['status']) . "'";
    }
    
    if (!empty($filters['date_from'])) {
        $sql .= " AND b.check_in_date >= '" . $conn->real_escape_string($filters['date_from']) . "'";
    }
    
    if (!empty($filters['date_to'])) {
        $sql .= " AND b.check_out_date <= '" . $conn->real_escape_string($filters['date_to']) . "'";
    }
    
    $sql .= " ORDER BY b.created_at DESC";
    
    $result = $conn->query($sql);
    $bookings = [];
    
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    
    return $bookings;
}

// Получение комнаты по ID
function getRoomById($room_id) {
    global $conn;
    
    $sql = "SELECT r.*, rt.type_name 
            FROM rooms r
            JOIN room_types rt ON r.type_id = rt.type_id
            WHERE r.room_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0 ? $result->fetch_assoc() : false;
}

// Добавление номера
function addRoom($data) {
    global $conn;
    
    $sql = "INSERT INTO rooms (room_number, type_id, floor, status) VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siis", 
        $data['room_number'],
        $data['type_id'],
        $data['floor'],
        $data['status']
    );
    
    return $stmt->execute();
}

// Обновление номера
function updateRoom($room_id, $data) {
    global $conn;
    
    $sql = "UPDATE rooms SET 
            room_number = ?,
            type_id = ?,
            floor = ?,
            status = ?
            WHERE room_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siisi", 
        $data['room_number'],
        $data['type_id'],
        $data['floor'],
        $data['status'],
        $room_id
    );
    
    return $stmt->execute();
}

// Удаление номера
function deleteRoom($room_id) {
    global $conn;
    
    $sql = "DELETE FROM rooms WHERE room_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $room_id);
    
    return $stmt->execute();
}

// Получение всех номеров
function getAllRooms() {
    global $conn;
    
    $sql = "SELECT r.*, rt.type_name 
            FROM rooms r
            JOIN room_types rt ON r.type_id = rt.type_id
            ORDER BY r.room_number";
    
    $result = $conn->query($sql);
    $rooms = [];
    
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
    
    return $rooms;
}

// Получение номеров по типу
function getRoomsByType($type_id) {
    global $conn;
    
    $sql = "SELECT * FROM rooms WHERE type_id = ? ORDER BY room_number";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $type_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $rooms = [];
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
    
    return $rooms;
}

// Получение услуги по ID
function getServiceById($service_id) {
    global $conn;
    
    $sql = "SELECT * FROM services WHERE service_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0 ? $result->fetch_assoc() : false;
}

// Добавление услуги
function addService($data) {
    global $conn;
    
    $sql = "INSERT INTO services (service_name, description, price, is_active, hotel_id) 
            VALUES (?, ?, ?, ?, 1)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdi", 
        $data['service_name'],
        $data['description'],
        $data['price'],
        $data['is_active']
    );
    
    return $stmt->execute();
}

// Обновление услуги
function updateService($service_id, $data) {
    global $conn;
    
    $sql = "UPDATE services SET 
            service_name = ?,
            description = ?,
            price = ?,
            is_active = ?
            WHERE service_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdii", 
        $data['service_name'],
        $data['description'],
        $data['price'],
        $data['is_active'],
        $service_id
    );
    
    return $stmt->execute();
}

// Удаление услуги
function deleteService($service_id) {
    global $conn;
    
    $sql = "DELETE FROM services WHERE service_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $service_id);
    
    return $stmt->execute();
}

// Получение всех услуг
function getAllServices() {
    global $conn;
    
    $sql = "SELECT * FROM services WHERE hotel_id = 1 ORDER BY service_name";
    
    $result = $conn->query($sql);
    $services = [];
    
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
    
    return $services;
}

// Получение активных услуг
function getAllActiveServices() {
    global $conn;
    
    $sql = "SELECT * FROM services WHERE hotel_id = 1 AND is_active = 1 ORDER BY service_name";
    
    $result = $conn->query($sql);
    $services = [];
    
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
    
    return $services;
}

// Обрезка текста
function truncateText($text, $length) {
    if (strlen($text) > $length) {
        $text = substr($text, 0, $length) . '...';
    }
    return $text;
}