<?php
$page_title = "Услуги отеля";
require_once 'includes/config.php';
require_once 'includes/functions.php';

$services = getAllActiveServices();
$hotel = getHotelDetails();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Услуги отеля</title>
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

        /* Services Grid */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100%, 1fr));
            gap: 30px;
            margin-bottom: 50px;
            max-width: 100%;
            margin-left: auto;
            margin-right: auto;
            width: calc(100% - 40px);
        }

        /* Service Card Styles */
        .service-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: grid;
            grid-template-columns: 400px 1fr;
            min-height: 400px;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }

        .service-image {
            height: 100%;
            overflow: hidden;
            position: relative;
        }

        .service-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .service-card:hover .service-image img {
            transform: scale(1.05);
        }

        .service-image-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: bold;
        }

        .service-details {
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .service-card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .service-price {
            font-size: 1.8rem;
            color: #c9a66b;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .service-description {
            color: #7f8c8d;
            line-height: 1.8;
            margin-bottom: 30px;
            flex-grow: 1;
        }

        .service-features {
            margin-bottom: 30px;
        }

        .service-features h4 {
            font-size: 1.2rem;
            color: #2c3e50;
            margin-bottom: 15px;
            font-family: 'Playfair Display', serif;
        }

        .service-features ul {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }

        .service-features li {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #555;
        }

        .service-features i {
            color: #c9a66b;
            width: 20px;
        }

        .service-buttons {
            display: flex;
            gap: 15px;
        }

        /* Button Styles */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #c9a66b, #8b6b3d);
            color: white;
            flex: 1;
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
            flex: 1;
        }

        .btn-secondary:hover {
            background: #c9a66b;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 107, 61, 0.2);
        }

        /* CTA Section */
        .service-cta {
            padding: 80px 0;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            text-align: center;
            margin-top: 50px;
        }

        .cta-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .cta-content h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .cta-content p {
            font-size: 1.2rem;
            margin-bottom: 40px;
            opacity: 0.9;
            line-height: 1.6;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .cta-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }

        .cta-btn-primary {
            background: linear-gradient(135deg, #c9a66b, #8b6b3d);
            color: white;
        }

        .cta-btn-primary:hover {
            background: linear-gradient(135deg, #8b6b3d, #6c5028);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(139, 107, 61, 0.3);
        }

        .cta-btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid #c9a66b;
        }

        .cta-btn-secondary:hover {
            background: #c9a66b;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(139, 107, 61, 0.2);
        }

        /* Service Categories Filter */
        .services-filter {
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
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 10px 20px;
            border: 1px solid #ddd;
            border-radius: 25px;
            background: #f8f9fa;
            color: #555;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .filter-btn.active,
        .filter-btn:hover {
            background: linear-gradient(135deg, #c9a66b, #8b6b3d);
            color: white;
            border-color: #c9a66b;
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .section-header,
            .services-filter,
            .services-grid {
                padding: 0 20px;
                width: 100%;
            }
        }

        @media (max-width: 992px) {
            .service-card {
                grid-template-columns: 300px 1fr;
                min-height: 350px;
            }
            
            .service-details {
                padding: 30px;
            }
            
            .service-card h3 {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 768px) {
            .service-card {
                grid-template-columns: 1fr;
                grid-template-rows: 250px 1fr;
            }
            
            .service-features ul {
                grid-template-columns: 1fr;
            }
            
            .section-header h2 {
                font-size: 2rem;
            }
            
            .service-buttons {
                flex-direction: column;
            }
            
            .cta-content h2 {
                font-size: 2rem;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .cta-btn {
                width: 250px;
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .service-card {
                grid-template-rows: 200px 1fr;
            }
            
            .service-details {
                padding: 20px;
            }
            
            .service-card h3 {
                font-size: 1.5rem;
            }
            
            .service-price {
                font-size: 1.5rem;
            }
            
            .services-grid {
                grid-template-columns: 1fr;
            }
            
            .section-header {
                padding-top: 30px;
            }
            
            .section-header,
            .services-filter,
            .services-grid {
                padding: 0 15px;
            }
            
            .service-cta {
                padding: 60px 0;
            }
            
            .cta-content h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
<?php require_once 'includes/header.php'; ?>

<main>
    <section class="services-section">
        <div class="section-header">
            <h2>Наши услуги</h2>
            <p>Дополнительные услуги для вашего комфорта во время пребывания</p>
        </div>
        
              
        <div class="services-grid">
    <?php foreach ($services as $service): ?>
    <div class="service-card" data-category="<?php echo strtolower($service['category'] ?? 'all'); ?>">
        <div class="service-image">
            <?php
            // Определяем название папки/файла для каждой услуги
            $imageMapping = [
                'Завтрак "шведский стол"' => 'shwedentable',
                'Консьерж-сервис' => 'conciergeservice',
                'Прачечная' => 'prachechnaya',
                'Спа-процедуры' => 'relax-zone',
                'Трансфер из/в аэропорт' => 'a51b4e2bc4347e0497e813d585d1551b',
                'Ужин в ресторане O2' => 'q2',
                'Экскурсия по Москве' => 'excursion'
            ];
            
            $imageName = $imageMapping[$service['service_name']] ?? $service['service_id'];
            $imagePath = 'C:\xampp\htdocs\hotel_management_system\assets\images\\' . $imageName . '.jpg';
            $webImagePath = $base_url . '/assets/images/' . $imageName . '.jpg';
            
            // Проверяем существование файла
            if (file_exists($imagePath)) {
                $serviceImage = $webImagePath;
            } else {
                $serviceImage = ''; // Если файла нет, оставляем пустую строку
            }
            ?>
            <?php if (!empty($serviceImage)): ?>
                <img src="<?php echo $serviceImage; ?>" alt="<?php echo $service['service_name']; ?>"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <?php endif; ?>
            <div class="service-image-placeholder" style="display: <?php echo empty($serviceImage) ? 'flex' : 'none'; ?>;">
                <i class="fas fa-<?php echo $service['icon'] ?? 'concierge-bell'; ?>"></i>
            </div>
        </div>
        
        <div class="service-details">
            <div>
                <h3><?php echo $service['service_name']; ?></h3>
                <p class="service-price"><?php echo number_format($service['price'], 0, ',', ' '); ?> ₽</p>
                <p class="service-description"><?php echo $service['description']; ?></p>
                
                <div class="service-features">
                    <h4>Включено в услугу:</h4>
                    <ul>
                        <li><i class="fas fa-check"></i> Профессиональное обслуживание</li>
                        <li><i class="fas fa-check"></i> Высокое качество</li>
                        <li><i class="fas fa-check"></i> Индивидуальный подход</li>
                        <li><i class="fas fa-check"></i> Быстрое выполнение</li>
                    </ul>
                </div>
            </div>
            
            <div class="service-buttons">
                <a href="tel:<?php echo $hotel['phone']; ?>" class="btn btn-primary">
                    <i class="fas fa-phone"></i> Заказать
                </a>
                <a href="#contact" class="btn btn-secondary">
                    <i class="fas fa-info-circle"></i> Подробнее
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

    <section class="service-cta" id="contact">
        <div class="cta-content">
            <h2>Хотите заказать дополнительные услуги?</h2>
            <p>Наш консьерж-сервис доступен 24/7 для бронирования любых услуг. Свяжитесь с нами удобным для вас способом.</p>
            <div class="cta-buttons">
                <a href="tel:<?php echo $hotel['phone']; ?>" class="cta-btn cta-btn-primary">
                    <i class="fas fa-phone"></i> Позвонить: <?php echo $hotel['phone']; ?>
                </a>
                <a href="mailto:<?php echo $hotel['email']; ?>" class="cta-btn cta-btn-secondary">
                    <i class="fas fa-envelope"></i> Написать на почту
                </a>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Фильтрация услуг по категориям
    const filterButtons = document.querySelectorAll('.filter-btn');
    const serviceCards = document.querySelectorAll('.service-card');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Убираем активный класс у всех кнопок
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Добавляем активный класс текущей кнопке
            this.classList.add('active');
            
            const category = this.getAttribute('data-category');
            
            // Показываем/скрываем карточки услуг
            serviceCards.forEach(card => {
                if (category === 'all' || card.getAttribute('data-category') === category) {
                    card.style.display = 'grid';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
    
    // Обработка ошибок загрузки изображений
    const serviceImages = document.querySelectorAll('.service-image img');
    serviceImages.forEach(img => {
        img.addEventListener('error', function() {
            this.style.display = 'none';
            const placeholder = this.nextElementSibling;
            if (placeholder && placeholder.classList.contains('service-image-placeholder')) {
                placeholder.style.display = 'flex';
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
</body>
</html>