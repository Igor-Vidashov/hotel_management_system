<?php
/**
 * AuthController - Безопасная аутентификация пользователей
 * Реализует многоуровневую защиту системы аутентификации
 * Защита от брут-форс атак, сессионных hijacking и других угроз
 * Соответствует требованиям OWASP Authentication Cheat Sheet
 */
class AuthController {

    // Объект подключения к базе данных для выполнения запросов
    private $db;
    // Менеджер безопасности для валидации и санации данных
    private $security;

    // Константы безопасности для настройки политик аутентификации
    const MAX_LOGIN_ATTEMPTS = 5; // Максимальное количество попыток входа перед блокировкой
    const SESSION_TIMEOUT = 1800; // Таймаут сессии в секундах (30 минут)
    const PASSWORD_MIN_LENGTH = 8; // Минимальная длина пароля

    /**
     * Конструктор класса - внедрение зависимостей
     * Принцип Dependency Injection для тестируемости и гибкости архитектуры
     *
     * @param PDO $dbConnection Объект подключения к PDO
     * @param SecurityManager $securityManager Менеджер безопасности для защиты данных
     */
    public function __construct($dbConnection, $securityManager) {
        $this->db = $dbConnection;
        $this->security = $securityManager;
        
        // Инициализация сессии если еще не начата
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Аутентификация пользователя с многоуровневой защитой от брут-форс атак
     * Полный цикл безопасности: валидация → проверка блокировки → верификация → создание сессии
     *
     * @param string $email Email пользователя для аутентификации
     * @param string $password Пароль пользователя (в открытом виде)
     * @return array Результат аутентификации:
     * - success: boolean - успех/неудача аутентификации
     * - user_id: int - ID пользователя (при успехе)
     * - role: string - роль пользователя (при успехе)
     * - message: string - сообщение об ошибке (при неудаче)
     * @throws Exception При нарушениях политики безопасности или ошибках БД
     */
    public function login($email, $password) {
        // === ЭТАП 1: ВАЛИДАЦИЯ И САНАЦИЯ ВХОДНЫХ ДАННЫХ ===
        $email = $this->security->validateInput($email, 'email');
        $password = $this->security->validateInput($password, 'string');

        try {
            // === ЭТАП 2: ПОИСК ПОЛЬЗОВАТЕЛЯ В БАЗЕ ДАННЫХ ===
            $stmt = $this->security->executeSecureQuery(
                $this->db,
                "SELECT user_id, email, password_hash, role, login_attempts, 
                        last_login_attempt, is_active, first_name, last_name
                 FROM users 
                 WHERE email = ?",
                [$email]
            );

            $user = $stmt->fetch();

            if ($user) {
                // === ЭТАП 3: ПРОВЕРКА БЛОКИРОВКИ АККАУНТА ===
                if ($user['login_attempts'] >= self::MAX_LOGIN_ATTEMPTS) {
                    $lockTime = strtotime($user['last_login_attempt']);
                    $currentTime = time();
                    
                    // Автоматическая разблокировка через 30 минут
                    if (($currentTime - $lockTime) > 1800) {
                        $this->resetLoginAttempts($user['user_id']);
                    } else {
                        throw new Exception('Аккаунт временно заблокирован. Попробуйте через 30 минут');
                    }
                }

                // Проверка активности аккаунта (административная блокировка)
                if (!$user['is_active']) {
                    throw new Exception('Аккаунт деактивирован. Обратитесь к администратору');
                }

                // === ЭТАП 4: ВЕРИФИКАЦИЯ ПАРОЛЯ ===
                if (password_verify($password, $user['password_hash'])) {
                    // === ЭТАП 5: ОБНОВЛЕНИЕ СТАТИСТИКИ И СЕССИИ ===
                    $this->resetLoginAttempts($user['user_id']);
                    $this->updateLastLogin($user['user_id']);
                    $this->createSecureSession($user);

                    return [
                        'success' => true,
                        'user_id' => $user['user_id'],
                        'role' => $user['role'],
                        'first_name' => $user['first_name'],
                        'message' => 'Аутентификация успешна'
                    ];
                } else {
                    // === ОБРАБОТКА НЕУДАЧНОЙ ПОПЫТКИ ===
                    $this->incrementLoginAttempts($user['user_id']);
                    error_log("Failed login attempt for email: " . $email . " from IP: " . $_SERVER['REMOTE_ADDR']);
                    throw new Exception('Неверный email или пароль');
                }
            } else {
                // Пользователь не найден - унифицированное сообщение для безопасности
                throw new Exception('Неверный email или пароль');
            }
        } catch (Exception $e) {
            // === ОБРАБОТКА ИСКЛЮЧЕНИЙ ===
            error_log("Auth Error [".date('Y-m-d H:i:s')."] - IP: " . $_SERVER['REMOTE_ADDR'] . " - " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Сброс счетчика неудачных попыток входа
     * Вызывается после успешной аутентификации или истечения времени блокировки
     *
     * @param int $userId ID пользователя для сброса счетчика
     */
    private function resetLoginAttempts($userId) {
        $this->security->executeSecureQuery(
            $this->db,
            "UPDATE users 
             SET login_attempts = 0, 
                 last_login_attempt = NULL 
             WHERE user_id = ?",
            [$userId]
        );
    }

    /**
     * Увеличение счетчика неудачных попыток входа
     * Механизм защиты от брут-форс атак
     *
     * @param int $userId ID пользователя для увеличения счетчика
     */
    private function incrementLoginAttempts($userId) {
        $this->security->executeSecureQuery(
            $this->db,
            "UPDATE users 
             SET login_attempts = login_attempts + 1, 
                 last_login_attempt = NOW() 
             WHERE user_id = ?",
            [$userId]
        );
    }

    /**
     * Обновление времени последнего успешного входа
     * Для аудита и анализа активности пользователей
     *
     * @param int $userId ID пользователя
     */
    private function updateLastLogin($userId) {
        $this->security->executeSecureQuery(
            $this->db,
            "UPDATE users 
             SET last_success_login = NOW() 
             WHERE user_id = ?",
            [$userId]
        );
    }

    /**
     * Создание безопасной сессии с защитой от hijacking
     * Реализует best practices для управления сессиями
     *
     * @param array $user Данные пользователя для сохранения в сессии
     */
    private function createSecureSession($user) {
        // Регенерация ID сессии для защиты от fixation attacks
        session_regenerate_id(true);

        // Сохранение основных данных пользователя в сессии
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];

        // === ДОПОЛНИТЕЛЬНЫЕ МЕРЫ БЕЗОПАСНОСТИ СЕССИИ ===
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $_SESSION['last_activity'] = time();
        $_SESSION['session_start'] = time();
        $_SESSION['session_timeout'] = self::SESSION_TIMEOUT;
    }

    /**
     * Валидация активной сессии с проверкой безопасности
     * Проверяет, не истекла ли сессия и не была ли она украдена
     *
     * @return bool true - сессия валидна, false - требуется повторная аутентификация
     */
    public function validateSession() {
        // Проверка наличия необходимых данных сессии
        if (!isset($_SESSION['user_id'], $_SESSION['last_activity'], $_SESSION['ip_address'])) {
            return false;
        }

        // Проверка таймаута сессии
        if ((time() - $_SESSION['last_activity']) > self::SESSION_TIMEOUT) {
            $this->logout();
            return false;
        }

        // Проверка fingerprint сессии для обнаружения hijacking
        if ($_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR'] || 
            $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            $this->logout();
            return false;
        }

        // Обновление времени последней активности
        $_SESSION['last_activity'] = time();
        return true;
    }

    /**
     * Проверка прав доступа пользователя
     *
     * @param string $requiredRole Требуемая роль
     * @return bool true - доступ разрешен, false - доступ запрещен
     */
    public function checkPermission($requiredRole) {
        if (!$this->validateSession()) {
            return false;
        }
        
        // Простая система ролей: admin > manager > user
        $roleHierarchy = [
            'user' => 1,
            'manager' => 2, 
            'admin' => 3
        ];
        
        $userRole = $_SESSION['role'] ?? 'user';
        $requiredLevel = $roleHierarchy[$requiredRole] ?? 0;
        $userLevel = $roleHierarchy[$userRole] ?? 0;
        
        return $userLevel >= $requiredLevel;
    }

    /**
     * Безопасное завершение сессии
     * Полная очистка данных сессии и cookies
     */
    public function logout() {
        // Очистка всех данных сессии
        $_SESSION = array();

        // Удаление cookie сессии
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Уничтожение сессии
        session_destroy();
    }

    /**
     * Регистрация нового пользователя с безопасным хешированием пароля
     *
     * @param string $email Email пользователя
     * @param string $password Пароль пользователя
     * @param string $firstName Имя пользователя
     * @param string $lastName Фамилия пользователя
     * @param string $phone Телефон пользователя
     * @param string $role Роль пользователя (user/admin/manager)
     * @return array Результат регистрации
     */
    public function register($email, $password, $firstName, $lastName, $phone, $role = 'user') {
        // Валидация входных данных
        $email = $this->security->validateInput($email, 'email');
        $password = $this->security->validateInput($password, 'string');
        $firstName = $this->security->validateInput($firstName, 'string');
        $lastName = $this->security->validateInput($lastName, 'string');
        $phone = $this->security->validateInput($phone, 'phone');
        $role = $this->security->validateInput($role, 'string');

        // Проверка минимальной длины пароля
        if (strlen($password) < self::PASSWORD_MIN_LENGTH) {
            throw new Exception('Пароль должен содержать минимум ' . self::PASSWORD_MIN_LENGTH . ' символов');
        }

        // Проверка существования пользователя с таким email
        $stmt = $this->security->executeSecureQuery(
            $this->db,
            "SELECT user_id FROM users WHERE email = ?",
            [$email]
        );

        if ($stmt->fetch()) {
            throw new Exception('Пользователь с таким email уже существует');
        }

        // Безопасное хеширование пароля с использованием bcrypt
        $passwordHash = $this->security->hashPassword($password);

        // Создание нового пользователя
        $stmt = $this->security->executeSecureQuery(
            $this->db,
            "INSERT INTO users (email, password_hash, first_name, last_name, role, is_active, created_at)
             VALUES (?, ?, ?, ?, ?, 1, NOW())",
            [$email, $passwordHash, $firstName, $lastName, $role]
        );

        $user_id = $this->db->lastInsertId();

        // Создание записи гостя
        $stmt = $this->security->executeSecureQuery(
            $this->db,
            "INSERT INTO guests (user_id, first_name, last_name, email, phone) 
             VALUES (?, ?, ?, ?, ?)",
            [$user_id, $firstName, $lastName, $email, $phone]
        );

        return [
            'success' => true,
            'user_id' => $user_id,
            'message' => 'Пользователь успешно зарегистрирован'
        ];
    }

    /**
     * Получение информации о текущем пользователе
     *
     * @return array|null Данные пользователя или null если не аутентифицирован
     */
    public function getCurrentUser() {
        if (!$this->validateSession()) {
            return null;
        }
        
        return [
            'user_id' => $_SESSION['user_id'],
            'email' => $_SESSION['email'],
            'role' => $_SESSION['role'],
            'first_name' => $_SESSION['first_name'],
            'last_name' => $_SESSION['last_name']
        ];
    }

    /**
     * Смена пароля пользователя
     *
     * @param int $userId ID пользователя
     * @param string $currentPassword Текущий пароль
     * @param string $newPassword Новый пароль
     * @return array Результат операции
     */
    public function changePassword($userId, $currentPassword, $newPassword) {
        // Валидация входных данных
        $userId = $this->security->validateInput($userId, 'int');
        $currentPassword = $this->security->validateInput($currentPassword, 'string');
        $newPassword = $this->security->validateInput($newPassword, 'string');

        // Проверка минимальной длины нового пароля
        if (strlen($newPassword) < self::PASSWORD_MIN_LENGTH) {
            throw new Exception('Новый пароль должен содержать минимум ' . self::PASSWORD_MIN_LENGTH . ' символов');
        }

        // Получение текущего хеша пароля
        $stmt = $this->security->executeSecureQuery(
            $this->db,
            "SELECT password_hash FROM users WHERE user_id = ?",
            [$userId]
        );

        $user = $stmt->fetch();
        if (!$user) {
            throw new Exception('Пользователь не найден');
        }

        // Проверка текущего пароля
        if (!password_verify($currentPassword, $user['password_hash'])) {
            throw new Exception('Текущий пароль неверен');
        }

        // Хеширование нового пароля
        $newPasswordHash = $this->security->hashPassword($newPassword);

        // Обновление пароля в базе данных
        $this->security->executeSecureQuery(
            $this->db,
            "UPDATE users SET password_hash = ? WHERE user_id = ?",
            [$newPasswordHash, $userId]
        );

        return [
            'success' => true,
            'message' => 'Пароль успешно изменен'
        ];
    }
}
?>