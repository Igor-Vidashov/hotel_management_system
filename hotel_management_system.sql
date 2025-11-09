-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Ноя 09 2025 г., 19:18
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `hotel_management_system`
--

-- --------------------------------------------------------

--
-- Структура таблицы `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `adults` int(11) NOT NULL DEFAULT 1,
  `children` int(11) NOT NULL DEFAULT 0,
  `status` enum('confirmed','cancelled','checked-in','checked-out','no-show') DEFAULT 'confirmed',
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `discount_id` int(11) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `hotel_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL
) ;

--
-- Дамп данных таблицы `bookings`
--

INSERT INTO `bookings` (`booking_id`, `guest_id`, `room_id`, `check_in_date`, `check_out_date`, `adults`, `children`, `status`, `total_amount`, `created_at`, `discount_id`, `discount_amount`, `hotel_id`, `created_by`, `ip_address`, `user_agent`) VALUES
(2, 8, 1, '2025-11-09', '2025-11-10', 1, 0, 'confirmed', 15000.00, '2025-11-09 12:05:47', NULL, 0.00, 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `booking_services`
--

CREATE TABLE `booking_services` (
  `booking_service_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price_per_unit` decimal(10,2) NOT NULL,
  `discount_applied` decimal(10,2) DEFAULT 0.00,
  `service_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('requested','confirmed','delivered','cancelled') DEFAULT 'requested'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `discounts`
--

CREATE TABLE `discounts` (
  `discount_id` int(11) NOT NULL,
  `discount_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_type` enum('percentage','fixed_amount') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `min_stay` int(11) DEFAULT 1,
  `code` varchar(20) DEFAULT NULL,
  `applicable_to` enum('all','room_type','specific_room','service') NOT NULL,
  `max_uses` int(11) DEFAULT NULL,
  `current_uses` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `hotel_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL
) ;

--
-- Дамп данных таблицы `discounts`
--

INSERT INTO `discounts` (`discount_id`, `discount_name`, `description`, `discount_type`, `discount_value`, `start_date`, `end_date`, `is_active`, `min_stay`, `code`, `applicable_to`, `max_uses`, `current_uses`, `created_at`, `hotel_id`, `created_by`) VALUES
(1, 'Раннее бронирование', 'Скидка 15% при бронировании за 60 дней', 'percentage', 15.00, '2023-01-01', '2023-12-31', 1, 2, 'EARLY15', 'all', 100, 0, '2025-11-08 20:40:34', 1, NULL),
(2, 'Долгий отпуск', 'Скидка 10% при проживании от 7 ночей', 'percentage', 10.00, '2023-01-01', '2023-12-31', 1, 7, 'LONG10', 'all', NULL, 0, '2025-11-08 20:40:34', 1, NULL),
(3, 'Специальное предложение', 'Скидка 5000 руб. на номера Делюкс', 'fixed_amount', 5000.00, '2023-06-01', '2023-08-31', 1, 2, 'DELUXE5000', 'room_type', 50, 0, '2025-11-08 20:40:34', 1, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `guests`
--

CREATE TABLE `guests` (
  `guest_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `passport_number` varchar(50) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `guests`
--

INSERT INTO `guests` (`guest_id`, `user_id`, `first_name`, `last_name`, `email`, `phone`, `address`, `passport_number`, `date_of_birth`, `created_at`) VALUES
(1, NULL, 'Владимир', 'Путин', 'v.putin@example.com', '+7 123 456-7890', 'Москва, Кремль', '1234567890', '1952-10-07', '2025-11-08 20:40:34'),
(2, NULL, 'Юрий', 'Гагарин', 'y.gagarin@example.com', '+7 987 654-3210', 'Москва', '0987654321', '1934-03-09', '2025-11-08 20:40:34'),
(3, NULL, 'Лев', 'Толстой', 'l.tolstoy@example.com', '+7 555 123-4567', 'Ясная Поляна', '1122334455', '1828-09-09', '2025-11-08 20:40:34'),
(4, NULL, 'Анна', 'Нетребко', 'a.netrebko@example.com', '+7 911 222-3344', 'Санкт-Петербург', '5566778899', '1971-09-18', '2025-11-08 20:40:34'),
(5, NULL, 'Фёдор', 'Достоевский', 'f.dostoevsky@example.com', '+7 495 111-2233', 'Санкт-Петербург', '3344556677', '1821-11-11', '2025-11-08 20:40:34'),
(8, 5, 'Мария', 'Сумочкина', 'o.diop@yandex.ru', '+79063023222', NULL, NULL, NULL, '2025-11-09 11:53:22');

-- --------------------------------------------------------

--
-- Структура таблицы `hotels`
--

CREATE TABLE `hotels` (
  `hotel_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(50) NOT NULL,
  `country` varchar(50) NOT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `star_rating` int(11) DEFAULT NULL CHECK (`star_rating` between 1 and 5),
  `check_in_time` time DEFAULT '14:00:00',
  `check_out_time` time DEFAULT '12:00:00',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `hotels`
--

INSERT INTO `hotels` (`hotel_id`, `name`, `address`, `city`, `country`, `postal_code`, `phone`, `email`, `website`, `description`, `star_rating`, `check_in_time`, `check_out_time`, `created_at`, `updated_at`) VALUES
(1, 'The Ritz-Carlton Moscow', 'Тверская ул., 3', 'Москва', 'Россия', '125009', '+7 495 225-8888', 'info@ritzcarltonmoscow.com', 'https://www.ritzcarlton.com/moscow', 'Роскошный пятизвездочный отель в самом центре Москвы с видом на Красную площадь и Кремль. Отель предлагает элегантные номера, рестораны высокой кухни, спа-центр мирового класса и исключительный сервис.', 5, '14:00:00', '12:00:00', '2025-11-08 20:40:33', '2025-11-08 20:40:33');

-- --------------------------------------------------------

--
-- Структура таблицы `hotel_amenities`
--

CREATE TABLE `hotel_amenities` (
  `amenity_id` int(11) NOT NULL,
  `amenity_name` varchar(50) NOT NULL,
  `icon_class` varchar(50) DEFAULT NULL,
  `category` enum('general','room','bathroom','food','service','accessibility') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `hotel_amenities`
--

INSERT INTO `hotel_amenities` (`amenity_id`, `amenity_name`, `icon_class`, `category`) VALUES
(1, 'Бесплатный WiFi', 'fa-wifi', 'general'),
(2, 'Бассейн', 'fa-swimming-pool', 'general'),
(3, 'Спа-центр', 'fa-spa', 'general'),
(4, 'Фитнес-центр', 'fa-dumbbell', 'general'),
(5, 'Ресторан', 'fa-utensils', 'food'),
(6, 'Бар', 'fa-glass-martini-alt', 'food'),
(7, 'Круглосуточная стойка регистрации', 'fa-clock', 'service'),
(8, 'Обслуживание в номерах', 'fa-concierge-bell', 'service'),
(9, 'Прачечная', 'fa-tshirt', 'service'),
(10, 'Бизнес-центр', 'fa-briefcase', 'service'),
(11, 'Парковка', 'fa-parking', 'general'),
(12, 'Кондиционер', 'fa-snowflake', 'room'),
(13, 'Телевизор', 'fa-tv', 'room'),
(14, 'Мини-бар', 'fa-wine-bottle', 'room'),
(15, 'Сейф', 'fa-lock', 'room'),
(16, 'Фен', 'fa-wind', 'bathroom'),
(17, 'Номера для гостей с ограниченными возможностями', 'fa-wheelchair', 'accessibility'),
(18, 'Консьерж-сервис', 'fa-bell-concierge', 'service'),
(19, 'Терраса на крыше', 'fa-umbrella-beach', 'general'),
(20, 'Джакузи', 'fa-hot-tub-person', 'general');

-- --------------------------------------------------------

--
-- Структура таблицы `hotel_amenity_relations`
--

CREATE TABLE `hotel_amenity_relations` (
  `relation_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `amenity_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `hotel_amenity_relations`
--

INSERT INTO `hotel_amenity_relations` (`relation_id`, `hotel_id`, `amenity_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 1, 7),
(8, 1, 8),
(9, 1, 9),
(10, 1, 10),
(11, 1, 11),
(12, 1, 12),
(13, 1, 13),
(14, 1, 14),
(15, 1, 15),
(16, 1, 16),
(17, 1, 17),
(18, 1, 18),
(19, 1, 19),
(20, 1, 20);

-- --------------------------------------------------------

--
-- Структура таблицы `hotel_photos`
--

CREATE TABLE `hotel_photos` (
  `photo_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `photo_url` varchar(255) NOT NULL,
  `caption` varchar(100) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `hotel_photos`
--

INSERT INTO `hotel_photos` (`photo_id`, `hotel_id`, `photo_url`, `caption`, `is_primary`, `display_order`, `uploaded_at`) VALUES
(1, 1, 'https://www.archistone.ru/upload/iblock/eac/arhikamen_web-tm.jpg', 'Фасад отеля', 1, 1, '2025-11-08 20:40:34'),
(2, 1, 'https://russianasha.ru/files/hotels/ru/moscow/f_moscow-ritz_11.jpg', 'Роскошный лобби', 0, 2, '2025-11-08 20:40:34'),
(3, 1, 'https://s.101hotelscdn.ru/uploads/image/hotel_image/344/720967.jpg', 'Крытый бассейн', 0, 3, '2025-11-08 20:40:34'),
(4, 1, 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/2b/e5/af/a9/view-terrace.jpg?w=1400&h=-1&s=1', 'Ресторан O2', 0, 4, '2025-11-08 20:40:34'),
(5, 1, 'https://www.rubtur.ru/upload/iblock/b27/b272ef4c9f242c80563b06f30c8d65a2.jpg', 'Люкс с видом на Кремль', 0, 5, '2025-11-08 20:40:34'),
(6, 1, 'https://posta-magazine.ru/wp-content/uploads/2020/01/l_main_spa_la_prairie_the-ritz-carlton_moscow_posta-magazine.jpg', 'Спа-центр', 0, 6, '2025-11-08 20:40:34');

-- --------------------------------------------------------

--
-- Структура таблицы `password_resets`
--

CREATE TABLE `password_resets` (
  `reset_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `password_resets`
--

INSERT INTO `password_resets` (`reset_id`, `user_id`, `token`, `expires_at`, `created_at`) VALUES
(1, 5, '2fb320bd1cd8e351507d4064d375a0ce05aa9933598ce3246a2740e73683910d', '2025-11-09 14:58:47', '2025-11-09 12:58:47'),
(2, 5, '314db46e506bf342059ae5080ad78302927a69224621fd445837705498bcf53f', '2025-11-09 14:59:02', '2025-11-09 12:59:02'),
(3, 5, 'afd6dc48c6b87c3610222fa327ae9d7dca905ab08bcfa151fe05f5bdfe12ea52', '2025-11-09 15:03:42', '2025-11-09 13:03:42'),
(4, 5, 'dae7abc1434463e8714e342e059868808d44b0e7507010f1c7cd0a26033e58c5', '2025-11-09 15:03:58', '2025-11-09 13:03:58');

-- --------------------------------------------------------

--
-- Структура таблицы `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` enum('cash','credit_card','debit_card','bank_transfer','online') NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','completed','failed','refunded') DEFAULT 'completed',
  `notes` text DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `ip_address` varchar(45) DEFAULT NULL,
  `processed_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `review_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_approved` tinyint(1) DEFAULT 0,
  `approved_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `reviews`
--

INSERT INTO `reviews` (`review_id`, `guest_id`, `room_id`, `hotel_id`, `rating`, `comment`, `review_date`, `is_approved`, `approved_by`) VALUES
(1, 1, 5, 1, 5, 'Отличный отель с прекрасным видом на Кремль. Обслуживание на высшем уровне!', '2025-11-08 20:40:34', 1, NULL),
(2, 2, 10, 1, 4, 'Очень комфортный номер, но хотелось бы больше вариантов завтрака.', '2025-11-08 20:40:34', 1, NULL),
(3, 3, 15, 1, 5, 'Роскошный люкс, впечатляющий сервис. Особенно понравился спа-центр.', '2025-11-08 20:40:34', 1, NULL),
(4, 4, 3, 1, 5, 'Прекрасное место для отдыха в центре Москвы. Персонал очень внимательный.', '2025-11-08 20:40:34', 1, NULL),
(5, 5, 8, 1, 4, 'Хороший отель, но цены на дополнительные услуги завышены.', '2025-11-08 20:40:34', 1, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `type_id` int(11) NOT NULL,
  `floor` int(11) NOT NULL,
  `status` enum('available','occupied','maintenance','reserved') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_number`, `type_id`, `floor`, `status`) VALUES
(1, '101', 1, 1, 'available'),
(2, '102', 1, 1, 'available'),
(3, '103', 1, 1, 'available'),
(4, '104', 1, 1, 'available'),
(5, '105', 1, 1, 'available'),
(6, '201', 2, 2, 'available'),
(7, '202', 2, 2, 'available'),
(8, '203', 2, 2, 'available'),
(9, '204', 2, 2, 'available'),
(10, '301', 3, 3, 'available'),
(11, '302', 3, 3, 'available'),
(12, '303', 3, 3, 'available'),
(13, '401', 4, 4, 'available'),
(14, '402', 4, 4, 'available'),
(15, '501', 5, 5, 'available'),
(16, '502', 5, 5, 'available'),
(17, '503', 5, 5, 'available');

-- --------------------------------------------------------

--
-- Структура таблицы `room_discounts`
--

CREATE TABLE `room_discounts` (
  `room_discount_id` int(11) NOT NULL,
  `discount_id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `room_type_id` int(11) DEFAULT NULL
) ;

--
-- Дамп данных таблицы `room_discounts`
--

INSERT INTO `room_discounts` (`room_discount_id`, `discount_id`, `room_id`, `room_type_id`) VALUES
(1, 3, NULL, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `room_services`
--

CREATE TABLE `room_services` (
  `room_service_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `is_included` tinyint(1) DEFAULT 0,
  `special_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `room_types`
--

CREATE TABLE `room_types` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `capacity` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `room_types`
--

INSERT INTO `room_types` (`type_id`, `type_name`, `description`, `base_price`, `capacity`, `hotel_id`) VALUES
(1, 'Стандартный номер', 'Элегантный номер с одной кроватью king-size или двумя twin-кроватями, видом на город', 15000.00, 2, 1),
(2, 'Делюкс', 'Просторный номер с гостиной зоной и улучшенным видом', 22000.00, 2, 1),
(3, 'Люкс', 'Роскошный люкс с отдельной гостиной и спальней', 35000.00, 2, 1),
(4, 'Президентский люкс', 'Эксклюзивный люкс с панорамным видом на Кремль', 80000.00, 4, 1),
(5, 'Номер с видом на Кремль', 'Уникальный номер с прямым видом на Красную площадь и Кремль', 45000.00, 2, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `seasonal_pricing`
--

CREATE TABLE `seasonal_pricing` (
  `pricing_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `price_multiplier` decimal(3,2) DEFAULT 1.00,
  `description` varchar(100) DEFAULT NULL
) ;

--
-- Дамп данных таблицы `seasonal_pricing`
--

INSERT INTO `seasonal_pricing` (`pricing_id`, `hotel_id`, `room_type_id`, `start_date`, `end_date`, `price_multiplier`, `description`) VALUES
(1, 1, 1, '2025-07-01', '2025-09-30', 1.20, 'Высокий сезон'),
(2, 1, 2, '2025-07-01', '2025-09-30', 1.20, 'Высокий сезон'),
(3, 1, 3, '2025-07-01', '2025-09-30', 1.20, 'Высокий сезон'),
(4, 1, 4, '2025-07-01', '2025-09-30', 1.20, 'Высокий сезон'),
(5, 1, 5, '2025-07-01', '2025-09-30', 1.20, 'Высокий сезон'),
(6, 1, 1, '2025-12-20', '2026-01-10', 1.50, 'Новогодние праздники'),
(7, 1, 2, '2025-12-20', '2026-01-10', 1.50, 'Новогодние праздники'),
(8, 1, 3, '2025-12-20', '2026-01-10', 1.50, 'Новогодние праздники'),
(9, 1, 4, '2025-12-20', '2026-01-10', 1.50, 'Новогодние праздники'),
(10, 1, 5, '2025-12-20', '2026-01-10', 1.50, 'Новогодние праздники');

-- --------------------------------------------------------

--
-- Структура таблицы `security_audit`
--

CREATE TABLE `security_audit` (
  `audit_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action_type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `hotel_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `services`
--

INSERT INTO `services` (`service_id`, `service_name`, `description`, `price`, `is_active`, `hotel_id`) VALUES
(1, 'Завтрак \"шведский стол\"', 'Богатый выбор блюд на завтрак', 2500.00, 1, 1),
(2, 'Трансфер из/в аэропорт', 'Индивидуальный трансфер на Mercedes S-class', 5000.00, 1, 1),
(3, 'Спа-процедуры', '60-минутный массаж всего тела', 8000.00, 1, 1),
(4, 'Экскурсия по Москве', 'Индивидуальная экскурсия с гидом', 10000.00, 1, 1),
(5, 'Ужин в ресторане O2', 'Ужин из 5 блюд с вином', 12000.00, 1, 1),
(6, 'Прачечная', 'Стирка и глажка одежды', 1500.00, 1, 1),
(7, 'Консьерж-сервис', 'Организация мероприятий и билетов', 0.00, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `service_orders`
--

CREATE TABLE `service_orders` (
  `order_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `position` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `hire_date` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `staff`
--

INSERT INTO `staff` (`staff_id`, `hotel_id`, `user_id`, `first_name`, `last_name`, `position`, `email`, `phone`, `hire_date`, `is_active`) VALUES
(1, 1, 1, 'Иван', 'Иванов', 'Менеджер отеля', 'i.ivanov@ritzcarlton.com', '+7 495 225-8881', '2015-06-15', 1),
(2, 1, 2, 'Ольга', 'Петрова', 'Администратор', 'o.petrova@ritzcarlton.com', '+7 495 225-8882', '2018-03-10', 1),
(3, 1, 3, 'Алексей', 'Сидоров', 'Шеф-повар', 'a.sidorov@ritzcarlton.com', '+7 495 225-8883', '2016-11-22', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `role` enum('user','manager','admin') DEFAULT 'user',
  `login_attempts` int(11) DEFAULT 0,
  `last_login_attempt` datetime DEFAULT NULL,
  `last_success_login` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`user_id`, `email`, `password_hash`, `first_name`, `last_name`, `role`, `login_attempts`, `last_login_attempt`, `last_success_login`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'admin@ritzcarlton.com', '$2y$12$8QJQJQJQJQJQJQJQJQJQJ.QJQJQJQJQJQJQJQJQJQJQJQJQJQJQJ', 'Admin', 'User', 'admin', 0, NULL, NULL, 1, '2025-11-08 20:40:33', '2025-11-09 14:33:52'),
(2, 'manager@ritzcarlton.com', '$2y$12$8QJQJQJQJQJQJQJQJQJQJ.QJQJQJQJQJQJQJQJQJQJQJQJQJQJQJ', 'Manager', 'User', 'manager', 0, NULL, NULL, 1, '2025-11-08 20:40:33', '2025-11-09 14:34:21'),
(3, 'user@example.com', '$2y$12$8QJQJQJQJQJQJQJQJQJQJ.QJQJQJQJQJQJQJQJQJQJQJQJQJQJQJ', 'Regular', 'User', 'user', 0, NULL, NULL, 1, '2025-11-08 20:40:33', '2025-11-08 20:40:33'),
(5, 'o.diop@yandex.ru', '$2y$12$w4KTmzum6SeWyZpeQTTUWuBrHmXjTG8f9qM.25UCqKPZDXttke8fW', 'Мария', 'Сумочкина', 'user', 0, NULL, '2025-11-09 14:54:44', 1, '2025-11-09 11:53:22', '2025-11-09 11:54:44');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_booking_dates` (`check_in_date`,`check_out_date`),
  ADD KEY `idx_booking_guest` (`guest_id`),
  ADD KEY `idx_booking_hotel` (`hotel_id`);

--
-- Индексы таблицы `booking_services`
--
ALTER TABLE `booking_services`
  ADD PRIMARY KEY (`booking_service_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Индексы таблицы `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`discount_id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `hotel_id` (`hotel_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Индексы таблицы `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`guest_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `passport_number` (`passport_number`),
  ADD KEY `idx_guest_name` (`last_name`,`first_name`),
  ADD KEY `fk_guest_user` (`user_id`);

--
-- Индексы таблицы `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`hotel_id`);

--
-- Индексы таблицы `hotel_amenities`
--
ALTER TABLE `hotel_amenities`
  ADD PRIMARY KEY (`amenity_id`);

--
-- Индексы таблицы `hotel_amenity_relations`
--
ALTER TABLE `hotel_amenity_relations`
  ADD PRIMARY KEY (`relation_id`),
  ADD UNIQUE KEY `hotel_id` (`hotel_id`,`amenity_id`),
  ADD KEY `amenity_id` (`amenity_id`);

--
-- Индексы таблицы `hotel_photos`
--
ALTER TABLE `hotel_photos`
  ADD PRIMARY KEY (`photo_id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Индексы таблицы `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`reset_id`),
  ADD UNIQUE KEY `unique_token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `processed_by` (`processed_by`);

--
-- Индексы таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `guest_id` (`guest_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_review_hotel` (`hotel_id`);

--
-- Индексы таблицы `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD UNIQUE KEY `room_number` (`room_number`),
  ADD KEY `type_id` (`type_id`),
  ADD KEY `idx_room_status` (`status`);

--
-- Индексы таблицы `room_discounts`
--
ALTER TABLE `room_discounts`
  ADD PRIMARY KEY (`room_discount_id`),
  ADD KEY `discount_id` (`discount_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Индексы таблицы `room_services`
--
ALTER TABLE `room_services`
  ADD PRIMARY KEY (`room_service_id`),
  ADD UNIQUE KEY `room_id` (`room_id`,`service_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Индексы таблицы `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`type_id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Индексы таблицы `seasonal_pricing`
--
ALTER TABLE `seasonal_pricing`
  ADD PRIMARY KEY (`pricing_id`),
  ADD KEY `hotel_id` (`hotel_id`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Индексы таблицы `security_audit`
--
ALTER TABLE `security_audit`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `idx_audit_user` (`user_id`),
  ADD KEY `idx_audit_date` (`created_at`);

--
-- Индексы таблицы `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `idx_service_hotel` (`hotel_id`);

--
-- Индексы таблицы `service_orders`
--
ALTER TABLE `service_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Индексы таблицы `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `hotel_id` (`hotel_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_user_email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `booking_services`
--
ALTER TABLE `booking_services`
  MODIFY `booking_service_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `discounts`
--
ALTER TABLE `discounts`
  MODIFY `discount_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `guests`
--
ALTER TABLE `guests`
  MODIFY `guest_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `hotels`
--
ALTER TABLE `hotels`
  MODIFY `hotel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `hotel_amenities`
--
ALTER TABLE `hotel_amenities`
  MODIFY `amenity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT для таблицы `hotel_amenity_relations`
--
ALTER TABLE `hotel_amenity_relations`
  MODIFY `relation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT для таблицы `hotel_photos`
--
ALTER TABLE `hotel_photos`
  MODIFY `photo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT для таблицы `room_discounts`
--
ALTER TABLE `room_discounts`
  MODIFY `room_discount_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `room_services`
--
ALTER TABLE `room_services`
  MODIFY `room_service_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `room_types`
--
ALTER TABLE `room_types`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `seasonal_pricing`
--
ALTER TABLE `seasonal_pricing`
  MODIFY `pricing_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `security_audit`
--
ALTER TABLE `security_audit`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `service_orders`
--
ALTER TABLE `service_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`),
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`),
  ADD CONSTRAINT `bookings_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `booking_services`
--
ALTER TABLE `booking_services`
  ADD CONSTRAINT `booking_services_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_services_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`);

--
-- Ограничения внешнего ключа таблицы `discounts`
--
ALTER TABLE `discounts`
  ADD CONSTRAINT `discounts_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`),
  ADD CONSTRAINT `discounts_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `guests`
--
ALTER TABLE `guests`
  ADD CONSTRAINT `fk_guest_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `guests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `hotel_amenity_relations`
--
ALTER TABLE `hotel_amenity_relations`
  ADD CONSTRAINT `hotel_amenity_relations_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hotel_amenity_relations_ibfk_2` FOREIGN KEY (`amenity_id`) REFERENCES `hotel_amenities` (`amenity_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `hotel_photos`
--
ALTER TABLE `hotel_photos`
  ADD CONSTRAINT `hotel_photos_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`processed_by`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`),
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`),
  ADD CONSTRAINT `reviews_ibfk_4` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `room_types` (`type_id`);

--
-- Ограничения внешнего ключа таблицы `room_discounts`
--
ALTER TABLE `room_discounts`
  ADD CONSTRAINT `room_discounts_ibfk_1` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`discount_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_discounts_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_discounts_ibfk_3` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`type_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `room_services`
--
ALTER TABLE `room_services`
  ADD CONSTRAINT `room_services_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_services_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `room_types`
--
ALTER TABLE `room_types`
  ADD CONSTRAINT `room_types_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`);

--
-- Ограничения внешнего ключа таблицы `seasonal_pricing`
--
ALTER TABLE `seasonal_pricing`
  ADD CONSTRAINT `seasonal_pricing_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`),
  ADD CONSTRAINT `seasonal_pricing_ibfk_2` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`type_id`);

--
-- Ограничения внешнего ключа таблицы `security_audit`
--
ALTER TABLE `security_audit`
  ADD CONSTRAINT `security_audit_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`);

--
-- Ограничения внешнего ключа таблицы `service_orders`
--
ALTER TABLE `service_orders`
  ADD CONSTRAINT `service_orders_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`),
  ADD CONSTRAINT `service_orders_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`);

--
-- Ограничения внешнего ключа таблицы `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`),
  ADD CONSTRAINT `staff_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
