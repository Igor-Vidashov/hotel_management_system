<?php
$page_title = "Номера";
require_once 'includes/config.php';
require_once 'includes/functions.php';

$roomTypes = getRoomTypes();
$type_id = isset($_GET['type']) ? (int)$_GET['type'] : 0;
$selectedType = $type_id ? getRoomTypeById($type_id) : null;

// Добавляем проверку на существование типа номера
if ($type_id > 0 && !$selectedType) {
    header('Location: ' . $base_url . '/rooms.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Номера - Отель</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Full-width Room Gallery */
        .room-gallery-fullwidth {
            width: 100%;
            height: 500px;
            position: relative;
        }

        .main-image-full {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .main-image-full img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: opacity 0.3s ease;
        }

        .thumbnail-images-full {
            position: absolute;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }

        .thumbnail-images-full img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            cursor: pointer;
            transition: transform 0.3s ease;
            border: 2px solid white;
        }

        .thumbnail-images-full img:hover {
            transform: scale(1.1);
        }

        .thumbnail-images-full img.active {
            border-color: #c9a66b;
            box-shadow: 0 0 10px rgba(201, 166, 107, 0.5);
        }

        /* Full-width Room Info */
        .room-info-fullwidth {
            background: white;
            padding: 40px 0;
            width: 100%;
        }

        .room-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding: 0 20px;
        }

        .room-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: #2c3e50;
            margin: 0;
        }

        .room-price-large {
            font-size: 1.8rem;
            color: #c9a66b;
            font-weight: 600;
        }

        .room-price-large small {
            font-size: 1.1rem;
            color: #7f8c8d;
        }

        .room-content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding: 0 20px;
        }

        .room-description h3 {
            font-size: 1.5rem;
            color: #2c3e50;
            margin-bottom: 15px;
            font-family: 'Playfair Display', serif;
        }

        .room-description p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #555;
        }

        .room-details-sidebar {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            height: fit-content;
        }

        .room-amenities {
            margin-top: 40px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding: 0 20px;
        }

        .room-amenities h3 {
            font-size: 1.8rem;
            color: #2c3e50;
            margin-bottom: 25px;
            font-family: 'Playfair Display', serif;
            text-align: center;
        }

        .room-amenities ul {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            list-style: none;
        }

        .room-amenities li {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .room-amenities li:hover {
            background: #e9ecef;
        }

        .room-amenities i {
            color: #c9a66b;
            font-size: 1.2rem;
            width: 25px;
        }

        /* Header Styles */
        .section-header {
            text-align: center;
            margin-bottom: 40px;
            padding-top: 50px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding: 50px 20px 0;
        }

        .section-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: #2c3e50;
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
        }

        .section-header h2:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #c9a66b, #8b6b3d);
        }

        .section-header p {
            font-size: 1.1rem;
            color: #7f8c8d;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Filter Styles */
        .rooms-filter {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            width: calc(100% - 40px);
        }

        .filter-options {
            display: flex;
            justify-content: center;
        }

        .form-control {
            padding: 12px 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            width: 300px;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #c9a66b;
            box-shadow: 0 0 0 3px rgba(201, 166, 107, 0.2);
        }

        /* Rooms Grid */
        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100%, 1fr));
            gap: 30px;
            margin-bottom: 50px;
            max-width: 100%;
            margin-left: auto;
            margin-right: auto;
            width: calc(100% - 40px);
        }

        /* Room Card Styles */
        .room-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }

        .room-image {
            height: 550px;
            overflow: hidden;
        }

        .room-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .room-card:hover .room-image img {
            transform: scale(1.05);
        }

        .room-details {
            padding: 25px;
        }

        .room-card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .room-price {
            font-size: 1.25rem;
            color: #c9a66b;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .room-capacity {
            color: #7f8c8d;
            margin-bottom: 20px;
        }

        .room-buttons {
            display: flex;
            gap: 12px;
        }

        /* Button Styles */
        .btn {
            display: inline-block;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #c9a66b, #8b6b3d);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #8b6b3d, #6c5028);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 107, 61, 0.3);
        }

        .btn-secondary {
            background: transparent;
            color: #c9a66b;
            border: 1px solid #c9a66b;
        }

        .btn-secondary:hover {
            background: #c9a66b;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 107, 61, 0.2);
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .room-content-grid,
            .room-header,
            .room-amenities,
            .section-header,
            .rooms-filter,
            .rooms-grid {
                padding: 0 20px;
                width: 100%;
            }
        }

        @media (max-width: 992px) {
            .room-content-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .room-gallery-fullwidth {
                height: 400px;
            }
            
            .room-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .rooms-grid {
                grid-template-columns: repeat(auto-fill, minmax(100%, 1fr));
            }
        }

        @media (max-width: 768px) {
            .room-gallery-fullwidth {
                height: 300px;
            }
            
            .thumbnail-images-full {
                position: static;
                justify-content: center;
                margin-top: 15px;
            }
            
            .room-amenities ul {
                grid-template-columns: 1fr;
            }
            
            .room-header h2 {
                font-size: 2rem;
            }
            
            .section-header h2 {
                font-size: 2rem;
            }
            
            .room-buttons {
                flex-direction: column;
            }
        }

        @media (max-width: 576px) {
            .room-info-fullwidth {
                padding: 20px 0;
            }
            
            .room-gallery-fullwidth {
                height: 250px;
            }
            
            .thumbnail-images-full img {
                width: 60px;
                height: 45px;
            }
            
            .rooms-grid {
                grid-template-columns: 1fr;
            }
            
            .form-control {
                width: 100%;
            }
            
            .section-header {
                padding-top: 30px;
            }
            
            .room-content-grid,
            .room-header,
            .room-amenities,
            .section-header,
            .rooms-filter,
            .rooms-grid {
                padding: 0 15px;
            }
        }
    </style>
</head>
<body>
<?php require_once 'includes/header.php'; ?>

<main>
    <section class="rooms-section">
        <div class="section-header">
            <h2>Наши номера</h2>
            <p>Выберите идеальный вариант для вашего пребывания</p>
        </div>
        
        <div class="rooms-filter">
            <div class="filter-options">
                <select id="room-type-filter" class="form-control">
                    <option value="0">Все типы номеров</option>
                    <?php foreach ($roomTypes as $type): ?>
                    <option value="<?php echo $type['type_id']; ?>" <?php echo $type_id == $type['type_id'] ? 'selected' : ''; ?>>
                        <?php echo $type['type_name']; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="rooms-grid">
           <?php if ($selectedType): ?>
    <div class="room-details-page">
        <!-- Галерея изображений -->
        <div class="room-gallery-fullwidth">
            <div class="main-image-full">
                <img id="main-room-image" src="<?php echo $base_url; ?>/assets/images/rooms/<?php echo $selectedType['type_id']; ?>_main.jpg" alt="<?php echo $selectedType['type_name']; ?>">
            </div>
            <div class="thumbnail-images-full">
                <img src="<?php echo $base_url; ?>/assets/images/rooms/<?php echo $selectedType['type_id']; ?>_main.jpg" alt="<?php echo $selectedType['type_name']; ?>" data-image="<?php echo $base_url; ?>/assets/images/rooms/<?php echo $selectedType['type_id']; ?>_main.jpg" class="thumbnail active">
                <img src="<?php echo $base_url; ?>/assets/images/rooms/<?php echo $selectedType['type_id']; ?>_1.jpg" alt="<?php echo $selectedType['type_name']; ?> - Фото 1" data-image="<?php echo $base_url; ?>/assets/images/rooms/<?php echo $selectedType['type_id']; ?>_1.jpg" class="thumbnail">
                <img src="<?php echo $base_url; ?>/assets/images/rooms/<?php echo $selectedType['type_id']; ?>_2.jpg" alt="<?php echo $selectedType['type_name']; ?> - Фото 2" data-image="<?php echo $base_url; ?>/assets/images/rooms/<?php echo $selectedType['type_id']; ?>_2.jpg" class="thumbnail">
                <img src="<?php echo $base_url; ?>/assets/images/rooms/<?php echo $selectedType['type_id']; ?>_3.jpg" alt="<?php echo $selectedType['type_name']; ?> - Фото 3" data-image="<?php echo $base_url; ?>/assets/images/rooms/<?php echo $selectedType['type_id']; ?>_3.jpg" class="thumbnail">
            </div>
        </div>
        
        <!-- Информация о номере - растянута на всю ширину -->
        <div class="room-info-fullwidth">
            <div class="room-header">
                <h2><?php echo $selectedType['type_name']; ?></h2>
                <div class="room-price-large">
                    <span>от <?php echo number_format($selectedType['base_price'], 0, ',', ' '); ?> ₽</span>
                    <small>за ночь</small>
                </div>
            </div>
            
            <div class="room-content-grid">
                <div class="room-description">
                    <h3>Описание номера</h3>
                    <p><?php echo $selectedType['description']; ?></p>
                </div>
                
                <div class="room-details-sidebar">
                    <div class="room-capacity-detail">
                        <h3>Вместимость:</h3>
                        <p>До <?php echo $selectedType['capacity']; ?> гостей</p>
                    </div>
                    
                    <div class="room-booking">
                        <a href="<?php echo $base_url; ?>/booking.php?type=<?php echo $selectedType['type_id']; ?>" class="btn btn-primary">Забронировать</a>
                    </div>
                </div>
            </div>
            
            <div class="room-amenities">
                <h3>Удобства номера:</h3>
                <ul>
                    <li><i class="fas fa-wifi"></i> Бесплатный WiFi</li>
                    <li><i class="fas fa-snowflake"></i> Кондиционер</li>
                    <li><i class="fas fa-tv"></i> Телевизор</li>
                    <li><i class="fas fa-wine-bottle"></i> Мини-бар</li>
                    <li><i class="fas fa-lock"></i> Сейф</li>
                    <li><i class="fas fa-wind"></i> Фен</li>
                    <li><i class="fas fa-bath"></i> Ванная комната</li>
                    <li><i class="fas fa-coffee"></i> Чайная станция</li>
                    <li><i class="fas fa-phone"></i> Телефон</li>
                    <li><i class="fas fa-tshirt"></i> Гардероб</li>
                    <li><i class="fas fa-couch"></i> Зона отдыха</li>
                    <li><i class="fas fa-parking"></i> Парковка</li>
                </ul>
            </div>
        </div>
    </div>
<?php else: ?>
                    <?php foreach ($roomTypes as $roomType): ?>
                    <div class="room-card">
                        <div class="room-image">
                            <img src="<?php echo $base_url; ?>/assets/images/rooms/<?php echo $roomType['type_id']; ?>_thumb.jpg" alt="<?php echo $roomType['type_name']; ?>">
                        </div>
                        <div class="room-details">
                            <h3><?php echo $roomType['type_name']; ?></h3>
                            <p class="room-price">от <?php echo number_format($roomType['base_price'], 0, ',', ' '); ?> ₽ за ночь</p>
                            <p class="room-capacity">Вместимость: до <?php echo $roomType['capacity']; ?> гостей</p>
                            <div class="room-buttons">
                                <a href="<?php echo $base_url; ?>/rooms.php?type=<?php echo $roomType['type_id']; ?>" class="btn btn-secondary">Подробнее</a>
                                <a href="<?php echo $base_url; ?>/booking.php?type=<?php echo $roomType['type_id']; ?>" class="btn btn-primary">Забронировать</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roomTypeFilter = document.getElementById('room-type-filter');
    
    roomTypeFilter.addEventListener('change', function() {
        const typeId = this.value;
        if (typeId > 0) {
            window.location.href = `<?php echo $base_url; ?>/rooms.php?type=${typeId}`;
        } else {
            window.location.href = '<?php echo $base_url; ?>/rooms.php';
        }
    });

    // Функциональность для смены основного изображения при клике на миниатюру
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.getElementById('main-room-image');
    
    if (thumbnails.length > 0 && mainImage) {
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function() {
                // Меняем основное изображение
                mainImage.src = this.getAttribute('data-image');
                
                // Убираем активный класс у всех миниатюр
                thumbnails.forEach(t => t.classList.remove('active'));
                
                // Добавляем активный класс к текущей миниатюре
                this.classList.add('active');
            });
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
</body>
</html>