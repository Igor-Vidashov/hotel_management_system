<?php
/**
 * SecurityManager - Комплексная система безопасности
 * Реализует многоуровневую защиту от веб-угроз согласно OWASP Top 10
 * Обеспечивает защиту от SQL-инъекций, XSS-атак, CSRF и других угроз
 */
class SecurityManager {
    
    /**
     * Защита от SQL-инъекций через подготовленные выражения mysqli
     * Использует параметризованные запросы для полного разделения кода и данных
     *
     * @param mysqli $conn Объект подключения к базе данных mysqli
     * @param string $sql SQL-запрос с плейсхолдерами (?)
     * @param array $params Массив параметров для подстановки в запрос
     * @param string $types Строка типов параметров (i - integer, d - double, s - string)
     * @return mysqli_stmt Подготовленное выражение для дальнейшей работы
     * @throws Exception При ошибках подготовки или выполнения запроса
     */
    public function executeSecureQuery($conn, $sql, $params = [], $types = '') {
        // Подготовка SQL-запроса - ключевой этап защиты от SQL-инъекций
        $stmt = $conn->prepare($sql);
        
        // Проверка успешности подготовки запроса
        if (!$stmt) {
            // Логирование ошибки для администратора без раскрытия деталей пользователю
            error_log("Security: Failed to prepare SQL query: " . $conn->error);
            throw new Exception('Ошибка подготовки запроса к базе данных');
        }

        // Обработка параметров, если они переданы
        if (!empty($params)) {
            // Автоматическое определение типов, если не указано явно
            if (empty($types)) {
                $types = $this->detectParamTypes($params);
            }
            
            // Привязка параметров с использованием bind_param
            $bindParams = [$types];
            foreach ($params as $key => $value) {
                $bindParams[] = &$params[$key];
            }
            
            call_user_func_array([$stmt, 'bind_param'], $bindParams);
        }

        // Выполнение подготовленного запроса
        if (!$stmt->execute()) {
            // Логирование ошибки выполнения
            error_log("Security: Query execution failed: " . $stmt->error);
            throw new Exception('Ошибка выполнения запроса к базе данных');
        }

        // Возврат подготовленного выражения для дальнейшей работы с результатами
        return $stmt;
    }

    /**
     * Защита от XSS (Cross-Site Scripting) через санацию вывода
     * Преобразует специальные символы в HTML-сущности, предотвращая выполнение скриптов
     *
     * @param mixed $data Данные для санации (строка или массив)
     * @return mixed Санированные данные, безопасные для вывода в HTML
     */
    public function sanitizeOutput($data) {
        // Рекурсивная обработка массивов
        if (is_array($data)) {
            return array_map([$this, 'sanitizeOutput'], $data);
        }

        // Основная санация: преобразование специальных символов в HTML-сущности
        return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Комплексная валидация входных данных с проверкой типа и длины
     * Соответствует принципу "не доверяй пользовательскому вводу"
     *
     * @param string $input Входные данные для валидации
     * @param string $type Тип данных для проверки ('string', 'email', 'int', 'date')
     * @param int $maxLength Максимальная допустимая длина строки
     * @return mixed Валидированные и санированные данные
     * @throws Exception При несоответствии данных требованиям
     */
    public function validateInput($input, $type = 'string', $maxLength = 255) {
        // Базовая предобработка: удаление пробелов по краям
        $input = trim($input);

        // Удаление экранированных символов (защита от некоторых видов инъекций)
        $input = stripslashes($input);

        // Проверка длины строки для предотвращения атак на переполнение буфера
        if (mb_strlen($input) > $maxLength) {
            throw new Exception("Превышена максимальная длина: $maxLength символов");
        }

        // Специфическая валидация в зависимости от типа данных
        switch ($type) {
            case 'email':
                // Проверка корректности формата email с помощью встроенного фильтра PHP
                if (!filter_var($input, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Некорректный email адрес');
                }
                // Санация email - удаление недопустимых символов
                return filter_var($input, FILTER_SANITIZE_EMAIL);

            case 'int':
                // Проверка, что строка представляет собой целое число
                if (!filter_var($input, FILTER_VALIDATE_INT)) {
                    throw new Exception('Ожидается целое число');
                }
                // Явное приведение к целому типу
                return (int)$input;

            case 'float':
                // Проверка числа с плавающей точкой
                if (!filter_var($input, FILTER_VALIDATE_FLOAT)) {
                    throw new Exception('Ожидается число');
                }
                return (float)$input;

            case 'date':
                // Проверка формата даты (YYYY-MM-DD) и ее корректности
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $input) || !strtotime($input)) {
                    throw new Exception('Некорректная дата. Ожидается формат: YYYY-MM-DD');
                }
                return $input;

            case 'phone':
                // Валидация номера телефона (российский формат)
                $cleaned = preg_replace('/[^\d+]/', '', $input);
                if (!preg_match('/^\+7\d{10}$/', $cleaned)) {
                    throw new Exception('Некорректный номер телефона. Формат: +7XXXXXXXXXX');
                }
                return $cleaned;

            default: // 'string' - обработка строк по умололчанию
                // Санация специальных символов для предотвращения XSS
                return filter_var($input, FILTER_SANITIZE_SPECIAL_CHARS);
        }
    }

    /**
     * Генерация CSRF-токена для защиты от межсайтовой подделки запросов
     *
     * @return string Уникальный CSRF-токен
     */
    public function generateCSRFToken() {
        // Генерация криптографически безопасного случайного токена
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_generated'] = time();
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Проверка CSRF-токена для подтверждения легитимности запроса
     *
     * @param string $token Токен из формы для проверки
     * @return bool Результат проверки (true - токен верный)
     * @throws Exception При несовпадении токенов
     */
    public function verifyCSRFToken($token) {
        // Сравнение токенов с защитой от атак по времени (timing attacks)
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            throw new Exception('Недействительный CSRF токен');
        }
        return true;
    }

    /**
     * Безопасное хеширование паролей с использованием алгоритма bcrypt
     *
     * @param string $password Пароль для хеширования
     * @return string Хеш пароля для безопасного хранения
     */
    public function hashPassword($password) {
        // Использование современного алгоритма bcrypt с достаточным фактором стоимости
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Проверка пароля против хранимого хеша
     *
     * @param string $password Введенный пароль
     * @param string $hash Хранимый хеш из базы данных
     * @return bool Результат проверки (true - пароль верный)
     */
    public function verifyPassword($password, $hash) {
        // Безопасная проверка пароля с защитой от timing-атак
        return password_verify($password, $hash);
    }

    /**
     * Автоматическое определение типов параметров для подготовленных выражений
     * Вспомогательный метод для executeSecureQuery()
     *
     * @param array $params Массив параметров
     * @return string Строка типов в формате mysqli ('i', 'd', 's')
     */
    private function detectParamTypes($params) {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i'; // integer
            } elseif (is_float($param)) {
                $types .= 'd'; // double (float)
            } else {
                $types .= 's'; // string
            }
        }
        return $types;
    }

    /**
     * Очистка старых CSRF-токенов (вызывается периодически)
     * Механизм безопасности для предотвращения повторного использования токенов
     */
    public function cleanupOldTokens() {
        // Очистка токенов старше 24 часов
        if (isset($_SESSION['csrf_token_generated']) && 
            time() - $_SESSION['csrf_token_generated'] > 86400) {
            unset($_SESSION['csrf_token']);
            unset($_SESSION['csrf_token_generated']);
        }
    }

    /**
     * Безопасная санация данных для SQL запросов (дополнительная защита)
     *
     * @param mixed $data Данные для санации
     * @return mixed Санированные данные
     */
    public function sanitizeForSQL($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeForSQL'], $data);
        }
        
        // Удаление потенциально опасных SQL-символов
        return str_replace(
            ['\\', '\'', '"', ';', '--', '/*', '*/', '`'],
            ['\\\\', '\\\'', '\\"', '\\;', '\\--', '\\/*', '\\*/', '\\`'],
            $data
        );
    }
}
?>