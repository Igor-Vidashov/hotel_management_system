<?php
$page_title = "Бронирование";
require_once 'includes/db.php'; 
require_once 'includes/functions.php';

// Устанавливаем правильную временную зону
date_default_timezone_set('Europe/Moscow');

// Обработка формы бронирования
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ПРОВЕРКА CSRF ТОКЕНА
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("Недействительный CSRF токен");
    }
    
    // СОЗДАНИЕ ГОСТЯ
    try {
        // Сначала создаем гостя
        $guest_data = [
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'], 
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'passport_number' => $_POST['passport']
        ];
        
        // Валидация данных гостя
        $validated_guest = [
            'first_name' => $security->validateInput($guest_data['first_name'], 'string'),
            'last_name' => $security->validateInput($guest_data['last_name'], 'string'),
            'email' => $security->validateInput($guest_data['email'], 'email'),
            'phone' => $security->validateInput($guest_data['phone'], 'phone'),
            'passport_number' => $security->validateInput($guest_data['passport_number'], 'string')
        ];
        
        // Проверяем, существует ли гость с таким email или паспортом
        $stmt = $security->executeSecureQuery(
            $conn,
            "SELECT guest_id FROM guests WHERE email = ? OR passport_number = ?",
            [$validated_guest['email'], $validated_guest['passport_number']]
        );
        
        $existing_guest = $stmt->get_result()->fetch_assoc();
        
        if ($existing_guest) {
            $guest_id = $existing_guest['guest_id'];
        } else {
            // Создаем нового гостя
            $stmt = $security->executeSecureQuery(
                $conn,
                "INSERT INTO guests (first_name, last_name, email, phone, passport_number) 
                 VALUES (?, ?, ?, ?, ?)",
                [
                    $validated_guest['first_name'],
                    $validated_guest['last_name'],
                    $validated_guest['email'], 
                    $validated_guest['phone'],
                    $validated_guest['passport_number']
                ]
            );
            $guest_id = $conn->insert_id;
        }
        
        // СОЗДАНИЕ БРОНИРОВАНИЯ ЧЕРЕЗ КОНТРОЛЛЕР
        $result = $bookingController->createBooking([
            'guest_id' => $guest_id,
            'room_id' => $_POST['room_id'],
            'check_in' => $_POST['check_in'],
            'check_out' => $_POST['check_out'],
            'total_amount' => $_POST['total_amount']
        ]);
        
        if ($result['success']) {
            header("Location: booking-confirmation.php?id=" . $result['booking_id']);
            exit;
        } else {
            $error_message = "Ошибка при бронировании: " . $result['message'];
        }
        
    } catch (Exception $e) {
        $error_message = "Ошибка при бронировании: " . $e->getMessage();
    }
}

// ИСПРАВЛЕННЫЙ КОД ДЛЯ ДАТ - используем текущую дату сервера
$current_date = date('Y-m-d');
$next_day = date('Y-m-d', strtotime('+1 day'));

$type_id = isset($_GET['type']) ? (int)$_GET['type'] : 0;
$check_in = isset($_GET['check_in']) ? $_GET['check_in'] : $current_date;
$check_out = isset($_GET['check_out']) ? $_GET['check_out'] : $next_day;

$roomTypes = getRoomTypes();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo safe_output($page_title); ?></title> 
    <style>
    .booking-form-container {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        padding: 30px;
        margin-top: 20px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 30px;
        margin-bottom: 20px;
    }

    .form-column {
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .form-column h3 {
        color: #2c3e50;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #3498db;
        font-size: 18px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #2c3e50;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .btn {
        padding: 12px 25px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #2980b9, #1c6ea4);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #95a5a6, #7f8c8d);
        color: white;
    }

    .btn-secondary:hover {
        background: linear-gradient(135deg, #7f8c8d, #6c7b7d);
        transform: translateY(-2px);
    }

    .btn-small {
        padding: 8px 16px;
        font-size: 12px;
    }

    .btn-select-room {
        background: linear-gradient(135deg, #27ae60, #229954);
        color: white;
    }

    .btn-select-room:hover {
        background: linear-gradient(135deg, #229954, #1e7e34);
    }

    /* Стили для доступных номеров */
    .available-rooms-list {
        display: grid;
        gap: 15px;
    }

    .room-option {
        background: white;
        padding: 20px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .room-option:hover {
        border-color: #3498db;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .room-option.selected {
        border-color: #27ae60;
        background: linear-gradient(135deg, #f8fff9, #e8f5e8);
        box-shadow: 0 4px 15px rgba(39, 174, 96, 0.2);
    }

    .room-option h4 {
        color: #2c3e50;
        margin-bottom: 8px;
        font-size: 16px;
    }

    .room-option p {
        color: #7f8c8d;
        margin-bottom: 5px;
        font-size: 14px;
    }

    .room-option .price {
        color: #27ae60;
        font-weight: 600;
        font-size: 16px;
    }

    /* Сообщения */
    .no-rooms-message,
    .error-message {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 8px;
        padding: 20px;
        color: #856404;
        text-align: center;
    }

    .error-message {
        background: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }

    /* Сводка бронирования */
    .booking-summary {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        margin-bottom: 20px;
    }

    .booking-summary h4 {
        color: #2c3e50;
        margin-bottom: 15px;
        font-size: 16px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 1px solid #dee2e6;
    }

    .summary-item span:first-child {
        font-weight: 600;
        color: #2c3e50;
    }

    .summary-item span:last-child {
        color: #7f8c8d;
        text-align: right;
    }

    .summary-item.total {
        border-bottom: none;
        padding-top: 10px;
        margin-top: 10px;
        border-top: 2px solid #3498db;
        font-size: 18px;
        font-weight: 700;
    }

    .summary-item.total span:last-child {
        color: #27ae60;
        font-size: 20px;
    }

    /* Адаптивность */
    @media (max-width: 1024px) {
        .form-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .form-row {
            grid-template-columns: 1fr;
            gap: 10px;
        }
    }

    @media (max-width: 768px) {
        .booking-form-container {
            padding: 20px;
            margin: 10px;
        }
        
        .form-column {
            padding: 15px;
        }
    }

    /* Анимации */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .room-option {
        animation: fadeIn 0.3s ease;
    }

    .booking-summary {
        animation: fadeIn 0.5s ease;
    }

    .alert-danger {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .section-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .section-header h2 {
        color: #2c3e50;
        margin-bottom: 10px;
    }

    .section-header p {
        color: #7f8c8d;
        font-size: 16px;
    }
    </style>
</head>
<body>

<section class="booking-section">
    <div class="container">
        <div class="section-header">
            <h2>Бронирование номера</h2>
            <p>Заполните форму для бронирования номера в нашем отеле</p>
        </div>
        
        <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <?php echo safe_output($error_message); ?>
        </div>
        <?php endif; ?>
        
        <div class="booking-form-container">
            <form id="booking-form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <!-- CSRF ТОКЕН -->
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                
                <div class="form-grid">
                    <div class="form-column">
                        <h3>Информация о бронировании</h3>
                        
                        <div class="form-group">
                            <label for="room_type">Тип номера</label>
                            <select id="room_type" name="room_type" class="form-control" required>
                                <option value="">Выберите тип номера</option>
                                <?php foreach ($roomTypes as $type): ?>
                                <option value="<?php echo safe_output($type['type_id']); ?>" <?php echo $type_id == $type['type_id'] ? 'selected' : ''; ?>>
                                    <?php echo safe_output($type['type_name']); ?> (от <?php echo number_format($type['base_price'], 0, ',', ' '); ?> ₽)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="check_in">Дата заезда</label>
                            <input type="date" id="check_in" name="check_in" class="form-control" 
                                   value="<?php echo safe_output($check_in); ?>" 
                                   min="<?php echo $current_date; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="check_out">Дата выезда</label>
                            <input type="date" id="check_out" name="check_out" class="form-control" 
                                   value="<?php echo safe_output($check_out); ?>" 
                                   min="<?php echo $next_day; ?>" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="adults">Взрослые</label>
                                <select id="adults" name="adults" class="form-control" required>
                                    <option value="1">1</option>
                                    <option value="2" selected>2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="children">Дети</label>
                                <select id="children" name="children" class="form-control">
                                    <option value="0" selected>0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="button" id="check-availability" class="btn btn-secondary">Проверить доступность</button>
                        </div>
                    </div>
                    
                    <div class="form-column">
                        <h3>Доступные номера</h3>
                        <div id="available-rooms-container">
                            <div class="no-rooms-message">
                                <p>Пожалуйста, выберите тип номера и даты, чтобы увидеть доступные варианты.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-column">
                        <h3>Информация о госте</h3>
                        
                        <div class="form-group">
                            <label for="first_name">Имя</label>
                            <input type="text" id="first_name" name="first_name" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="last_name">Фамилия</label>
                            <input type="text" id="last_name" name="last_name" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Телефон</label>
                            <input type="tel" id="phone" name="phone" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="passport">Паспортные данные</label>
                            <input type="text" id="passport" name="passport" class="form-control" required>
                        </div>
                        
                        <div id="booking-summary" class="booking-summary" style="display: none;">
                            <h4>Сводка бронирования</h4>
                            <div class="summary-item">
                                <span>Номер:</span>
                                <span id="summary-room-type"></span>
                            </div>
                            <div class="summary-item">
                                <span>Даты:</span>
                                <span id="summary-dates"></span>
                            </div>
                            <div class="summary-item">
                                <span>Гости:</span>
                                <span id="summary-guests"></span>
                            </div>
                            <div class="summary-item total">
                                <span>Итого:</span>
                                <span id="summary-total"></span>
                            </div>
                        </div>
                        
                        <input type="hidden" id="room_id" name="room_id">
                        <input type="hidden" id="total_amount" name="total_amount">
                        
                        <div class="form-group">
                            <button type="submit" id="submit-booking" class="btn btn-primary" disabled>Подтвердить бронирование</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Элементы формы
    const roomTypeSelect = document.getElementById('room_type');
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const adultsSelect = document.getElementById('adults');
    const childrenSelect = document.getElementById('children');
    const checkAvailabilityBtn = document.getElementById('check-availability');
    const availableRoomsContainer = document.getElementById('available-rooms-container');
    const bookingSummary = document.getElementById('booking-summary');
    const submitBookingBtn = document.getElementById('submit-booking');
    
    // Устанавливаем минимальные даты на основе текущей даты браузера
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    // Форматируем даты для input type="date"
    const todayFormatted = today.toISOString().split('T')[0];
    const tomorrowFormatted = tomorrow.toISOString().split('T')[0];
    
    // Устанавливаем минимальные значения
    checkInInput.min = todayFormatted;
    checkOutInput.min = tomorrowFormatted;
    
    // Проверка доступности номеров
    checkAvailabilityBtn.addEventListener('click', function() {
        const typeId = roomTypeSelect.value;
        const checkIn = checkInInput.value;
        const checkOut = checkOutInput.value;
        
        if (!typeId || !checkIn || !checkOut) {
            alert('Пожалуйста, заполните все поля для проверки доступности.');
            return;
        }
        
        // Проверяем, что дата выезда позже даты заезда
        if (new Date(checkOut) <= new Date(checkIn)) {
            alert('Дата выезда должна быть позже даты заезда.');
            return;
        }
        
        fetch(`/hotel_management_system/api/rooms_api.php?type=${typeId}&check_in=${checkIn}&check_out=${checkOut}`)
            .then(response => {
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        throw new TypeError(`Сервер вернул HTML вместо JSON. Проверьте путь к API.`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.rooms.length > 0) {
                    renderAvailableRooms(data.rooms);
                } else {
                    availableRoomsContainer.innerHTML = `
                        <div class="no-rooms-message">
                            <p>К сожалению, нет доступных номеров выбранного типа на указанные даты.</p>
                            <p>Попробуйте изменить даты или выбрать другой тип номера.</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                availableRoomsContainer.innerHTML = `
                    <div class="error-message">
                        <p>Произошла ошибка при проверке доступности. Пожалуйста, попробуйте позже.</p>
                        <p>Ошибка: ${error.message}</p>
                        <p>Проверьте путь: /hotel_management_system/api/rooms_api.php</p>
                    </div>
                `;
            });
    });

    // Рендер доступных номеров
    function renderAvailableRooms(rooms) {
        let html = '<div class="available-rooms-list">';
        
        rooms.forEach(room => {
            html += `
                <div class="room-option" data-room-id="${room.room_id}" data-room-number="${room.room_number}" data-base-price="${room.base_price}">
                    <h4>Номер ${room.room_number}</h4>
                    <p>${room.type_name}</p>
                    <p class="price">${parseFloat(room.base_price).toLocaleString('ru-RU')} ₽ за ночь</p>
                    <button type="button" class="btn btn-small btn-select-room">Выбрать номер</button>
                </div>
            `;
        });
        
        html += '</div>';
        availableRoomsContainer.innerHTML = html;
        
        // Обработка выбора номера
        document.querySelectorAll('.btn-select-room').forEach(btn => {
            btn.addEventListener('click', function() {
                const roomOption = this.closest('.room-option');
                const roomId = roomOption.dataset.roomId;
                const roomNumber = roomOption.dataset.roomNumber;
                const basePrice = parseFloat(roomOption.dataset.basePrice);
                
                // Убираем выделение со всех номеров
                document.querySelectorAll('.room-option').forEach(opt => {
                    opt.classList.remove('selected');
                    opt.querySelector('.btn-select-room').textContent = 'Выбрать номер';
                });
                
                // Выделяем выбранный номер
                roomOption.classList.add('selected');
                this.textContent = '✓ Выбрано';
                
                // Заполняем скрытые поля
                document.getElementById('room_id').value = roomId;
                
                // Рассчитываем общую стоимость
                calculateTotalPrice(roomId, basePrice);
                
                // Активируем кнопку подтверждения
                submitBookingBtn.disabled = false;
            });
        });
    }
    
    // Расчет общей стоимости
    function calculateTotalPrice(roomId, basePrice) {
        const checkIn = new Date(checkInInput.value);
        const checkOut = new Date(checkOutInput.value);
        const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
        
        const totalPrice = basePrice * nights;
        
        document.getElementById('total_amount').value = totalPrice;
        updateBookingSummary(roomId, basePrice, nights, totalPrice);
    }
    
    // Обновление сводки бронирования
    function updateBookingSummary(roomId, basePrice, nights, totalPrice) {
        const roomType = roomTypeSelect.options[roomTypeSelect.selectedIndex].text.split(' (')[0];
        const checkInFormatted = new Date(checkInInput.value).toLocaleDateString('ru-RU');
        const checkOutFormatted = new Date(checkOutInput.value).toLocaleDateString('ru-RU');
        const adults = adultsSelect.value;
        const children = childrenSelect.value;
        
        document.getElementById('summary-room-type').textContent = roomType;
        document.getElementById('summary-dates').textContent = `${checkInFormatted} - ${checkOutFormatted} (${nights} ночей)`;
        document.getElementById('summary-guests').textContent = `${adults} взрослых${children > 0 ? `, ${children} детей` : ''}`;
        document.getElementById('summary-total').textContent = `${totalPrice.toLocaleString('ru-RU')} ₽`;
        
        bookingSummary.style.display = 'block';
    }
    
    // Валидация дат
    checkInInput.addEventListener('change', function() {
        const checkInDate = new Date(this.value);
        const checkOutDate = new Date(checkOutInput.value);
        
        if (checkInDate >= checkOutDate) {
            const nextDay = new Date(checkInDate);
            nextDay.setDate(nextDay.getDate() + 1);
            checkOutInput.valueAsDate = nextDay;
        }
        
        // Обновляем минимальную дату для выезда
        const minCheckOut = new Date(checkInDate);
        minCheckOut.setDate(minCheckOut.getDate() + 1);
        checkOutInput.min = minCheckOut.toISOString().split('T')[0];
    });
    
    checkOutInput.addEventListener('change', function() {
        const checkOutDate = new Date(this.value);
        const checkInDate = new Date(checkInInput.value);
        
        if (checkOutDate <= checkInDate) {
            const prevDay = new Date(checkOutDate);
            prevDay.setDate(prevDay.getDate() - 1);
            checkInInput.valueAsDate = prevDay;
        }
    });
    
    // Автоматическая проверка доступности при загрузке страницы с параметрами
    if (roomTypeSelect.value && checkInInput.value && checkOutInput.value) {
        checkAvailabilityBtn.click();
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>