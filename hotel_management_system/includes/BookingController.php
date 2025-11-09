<?php
/**
 * BookingController - Управление операциями бронирования
 * Обеспечивает безопасное создание бронирований и проверку доступности
 * Реализует бизнес-логику бронирования с полным циклом безопасности
 * Соответствует требованиям GDPR и 152-ФЗ "О персональных данных"
 */
class BookingController {

    // Объект подключения к базе данных
    private $db;
    // Менеджер безопасности для защиты от веб-угроз
    private $security;

    /**
     * Конструктор класса - инициализация зависимостей
     * Внедрение зависимостей (Dependency Injection) для тестируемости и гибкости
     *
     * @param mysqli $dbConnection Объект подключения к MySQL
     * @param SecurityManager $securityManager Менеджер безопасности
     */
    public function __construct($dbConnection, $securityManager) {
        $this->db = $dbConnection;
        $this->security = $securityManager;
    }

    /**
     * Создание бронирования с комплексной проверкой безопасности
     * Полный цикл: валидация → проверка доступности → создание брони
     *
     * @param array $bookingData Массив данных бронирования:
     * - guest_id: ID гостя (обязательный)
     * - room_id: ID номера (обязательный)
     * - check_in: Дата заезда (формат YYYY-MM-DD)
     * - check_out: Дата выезда (формат YYYY-MM-DD)
     * - total_amount: Общая стоимость
     * @return array Результат операции:
     * - success: boolean - успех/неудача
     * - booking_id: int - ID созданного бронирования (при успехе)
     * - message: string - сообщение для пользователя
     */
    public function createBooking($bookingData) {
        try {
            // === ЭТАП 1: ВАЛИДАЦИЯ ВХОДНЫХ ДАННЫХ ===
            $validatedData = $this->validateBookingData($bookingData);

            // === ЭТАП 2: ПРОВЕРКА БИЗНЕС-ЛОГИКИ ===
            if (!$this->checkRoomAvailability(
                $validatedData['room_id'],
                $validatedData['check_in'],
                $validatedData['check_out']
            )) {
                throw new Exception('Номер недоступен на выбранные даты');
            }

            // === ЭТАП 3: СОЗДАНИЕ БРОНИРОВАНИЯ ===
            $stmt = $this->security->executeSecureQuery(
                $this->db,
                "INSERT INTO bookings (guest_id, room_id, check_in_date, check_out_date, total_amount, status, created_at, hotel_id)
                 VALUES (?, ?, ?, ?, ?, 'confirmed', NOW(), 1)",
                [
                    $validatedData['guest_id'],
                    $validatedData['room_id'], 
                    $validatedData['check_in'],
                    $validatedData['check_out'],
                    $validatedData['total_amount']
                ],
                'iissd'
            );

            // === ЭТАП 4: УСПЕШНОЕ ЗАВЕРШЕНИЕ ===
            return [
                'success' => true,
                'booking_id' => $this->db->insert_id,
                'message' => 'Бронирование успешно создано'
            ];

        } catch (Exception $e) {
            // === ОБРАБОТКА ОШИБОК ===
            error_log("Booking Error [".date('Y-m-d H:i:s')."]: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Произошла ошибка при бронировании'
            ];
        }
    }

    /**
     * Проверка доступности номера с защитой от SQL-инъекций
     * Определяет, свободен ли номер на указанный период
     * Учитывает только активные бронирования (confirmed, checked-in)
     *
     * @param int $roomId ID номера для проверки
     * @param string $checkIn Дата заезда (YYYY-MM-DD)
     * @param string $checkOut Дата выезда (YYYY-MM-DD)
     * @return bool true - номер доступен, false - номер занят
     * @throws Exception При ошибках валидации или выполнения запроса
     */
    public function checkRoomAvailability($roomId, $checkIn, $checkOut) {
        // Валидация входных параметров перед использованием в запросе
        $roomId = $this->security->validateInput($roomId, 'int');
        $checkIn = $this->security->validateInput($checkIn, 'date');
        $checkOut = $this->security->validateInput($checkOut, 'date');

        // Проверка логики дат (выезд должен быть после заезда)
        if (strtotime($checkOut) <= strtotime($checkIn)) {
            throw new Exception('Дата выезда должна быть после даты заезда');
        }

        // Безопасный SQL-запрос с подготовленными выражениями
        $stmt = $this->security->executeSecureQuery(
            $this->db,
            "SELECT COUNT(*) as conflicting_bookings
             FROM bookings 
             WHERE room_id = ? 
             AND status IN ('confirmed', 'checked-in')
             AND (
                 (check_in_date <= ? AND check_out_date > ?) OR
                 (check_in_date < ? AND check_out_date >= ?)
             )",
            [
                $roomId,
                $checkOut,
                $checkIn, 
                $checkOut,
                $checkIn
            ],
            'issss'
        );

        $result = $stmt->get_result();
        $conflictingCount = $result->fetch_row()[0];

        // Номер доступен, если нет конфликтующих бронирований (count = 0)
        return $conflictingCount == 0;
    }

    /**
     * Валидация данных бронирования
     * Проверяет и санирует все поля перед обработкой
     * Соответствует принципу "не доверяй пользовательскому вводу"
     *
     * @param array $data Сырые данные бронирования из формы
     * @return array Валидированные и санированные данные
     * @throws Exception При ошибках валидации
     */
    private function validateBookingData($data) {
        // Проверка наличия обязательных полей
        $requiredFields = ['guest_id', 'room_id', 'check_in', 'check_out', 'total_amount'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new Exception("Обязательное поле '$field' не заполнено");
            }
        }

        // Индивидуальная валидация каждого поля с указанием типа
        return [
            'guest_id' => $this->security->validateInput($data['guest_id'], 'int'),
            'room_id' => $this->security->validateInput($data['room_id'], 'int'),
            'check_in' => $this->validateFutureDate($data['check_in']),
            'check_out' => $this->validateCheckoutDate($data['check_out'], $data['check_in']),
            'total_amount' => $this->validatePositiveAmount($data['total_amount'])
        ];
    }

    /**
     * Дополнительная валидация даты заезда
     * Проверяет, что дата не в прошлом
     *
     * @param string $date Дата для проверки
     * @return string Валидированная дата
     * @throws Exception Если дата в прошлом
     */
    private function validateFutureDate($date) {
        $validatedDate = $this->security->validateInput($date, 'date');
        
        // Проверка, что дата не раньше сегодняшнего дня
        if (strtotime($validatedDate) < strtotime(date('Y-m-d'))) {
            throw new Exception('Дата заезда не может быть в прошлом');
        }
        return $validatedDate;
    }

    /**
     * Дополнительная валидация даты выезда
     * Проверяет корректность относительно даты заезда
     *
     * @param string $checkOut Дата выезда
     * @param string $checkIn Дата заезда
     * @return string Валидированная дата
     * @throws Exception Если даты некорректны
     */
    private function validateCheckoutDate($checkOut, $checkIn) {
        $validatedCheckOut = $this->security->validateInput($checkOut, 'date');
        $validatedCheckIn = $this->security->validateInput($checkIn, 'date');

        // Проверка, что выезд после заезда
        if (strtotime($validatedCheckOut) <= strtotime($validatedCheckIn)) {
            throw new Exception('Дата выезда должна быть после даты заезда');
        }

        // Проверка максимального срока проживания (30 дней)
        $maxStay = 30;
        $nights = (strtotime($validatedCheckOut) - strtotime($validatedCheckIn)) / (60 * 60 * 24);
        if ($nights > $maxStay) {
            throw new Exception("Максимальный срок проживания: $maxStay дней");
        }

        return $validatedCheckOut;
    }

    /**
     * Валидация суммы бронирования
     * Проверяет, что сумма положительная и в допустимом диапазоне
     *
     * @param mixed $amount Сумма для проверки
     * @return float Валидированная сумма
     * @throws Exception Если сумма некорректна
     */
    private function validatePositiveAmount($amount) {
        $validatedAmount = $this->security->validateInput($amount, 'float');

        if ($validatedAmount <= 0) {
            throw new Exception('Стоимость бронирования должна быть положительной');
        }

        // Проверка максимальной суммы (защита от ошибок ввода)
        if ($validatedAmount > 1000000) {
            throw new Exception('Слишком большая сумма бронирования');
        }

        return $validatedAmount;
    }

    /**
     * Получение информации о бронировании по ID
     * Безопасный метод для просмотра деталей брони
     *
     * @param int $bookingId ID бронирования
     * @return array|null Данные бронирования или null если не найдено
     */
    public function getBookingDetails($bookingId) {
        $bookingId = $this->security->validateInput($bookingId, 'int');
        
        $stmt = $this->security->executeSecureQuery(
            $this->db,
            "SELECT b.booking_id, b.check_in_date, b.check_out_date, b.total_amount, b.status,
                    r.room_number, rt.type_name, g.first_name, g.last_name, g.email
             FROM bookings b
             JOIN rooms r ON b.room_id = r.room_id
             JOIN room_types rt ON r.type_id = rt.type_id
             JOIN guests g ON b.guest_id = g.guest_id
             WHERE b.booking_id = ?",
            [$bookingId],
            'i'
        );

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Поиск доступных номеров по датам и типу
     *
     * @param string $checkIn Дата заезда
     * @param string $checkOut Дата выезда  
     * @param int|null $typeId ID типа номера (опционально)
     * @return array Список доступных номеров
     */
    public function findAvailableRooms($checkIn, $checkOut, $typeId = null) {
        $checkIn = $this->security->validateInput($checkIn, 'date');
        $checkOut = $this->security->validateInput($checkOut, 'date');
        
        $sql = "SELECT r.room_id, r.room_number, rt.type_name, rt.base_price, rt.capacity
                FROM rooms r
                JOIN room_types rt ON r.type_id = rt.type_id
                WHERE r.status = 'available'
                AND r.room_id NOT IN (
                    SELECT b.room_id FROM bookings b
                    WHERE b.status IN ('confirmed', 'checked-in')
                    AND (
                        (b.check_in_date <= ? AND b.check_out_date > ?) OR
                        (b.check_in_date < ? AND b.check_out_date >= ?)
                    )
                )";
        
        $params = [$checkOut, $checkIn, $checkOut, $checkIn];
        $types = 'ssss';
        
        if ($typeId) {
            $sql .= " AND r.type_id = ?";
            $params[] = $typeId;
            $types .= 'i';
        }
        
        $stmt = $this->security->executeSecureQuery($this->db, $sql, $params, $types);
        $result = $stmt->get_result();
        
        $rooms = [];
        while ($row = $result->fetch_assoc()) {
            $rooms[] = $row;
        }
        
        return $rooms;
    }
}
?>