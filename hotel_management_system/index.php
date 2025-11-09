<?php
// index.php
$page_title = "Главная";
require_once 'includes/config.php';
require_once 'includes/functions.php';

$hotel = getHotelDetails();
$amenities = getHotelAmenities();
$photos = getHotelPhotos();
$roomTypes = getRoomTypes();

require_once 'includes/header.php';
?>

<style>
/* Общие стили */
.welcome-section {
    position: relative;
    padding: 60px 0;
    text-align: center;
    overflow: hidden;
}

.welcome-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    right: -100%;
    bottom: 0;
    background: linear-gradient(135deg, rgba(248, 249, 250, 0.9) 0%, rgba(233, 236, 239, 0.9) 100%), 
                url('/hotel_management_system/assets/images/вход.jpg') center/cover no-repeat;
    background-attachment: fixed;
    z-index: 1;
}

.welcome-content {
    max-width: 100%;
    margin: 0 auto;
    padding: 0 20px;
    position: relative;
    z-index: 2;
}

.hotel-name {
    color: #c29b40;
    font-size: 3.5rem;
    margin-bottom: 25px;
    font-weight: 300;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.hotel-description {
    font-size: 1.3rem;
    color: #2c3e50;
    line-height: 1.7;
    margin-bottom: 35px;
    font-weight: 300;
}

.hotel-info {
    background: rgba(255, 255, 255, 0.95);
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    margin: 40px auto;
    max-width: 700px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
}

.hotel-address {
    font-size: 1.2rem;
    color: #2c3e50;
    margin-bottom: 20px;
    font-weight: 400;
}

.star-rating {
    margin-top: 20px;
}

.star-rating i {
    color: #ffd700;
    font-size: 2.5rem;
    margin: 0 3px;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
}

/* Стили для блока авторизации */
.auth-section {
    background: rgba(255, 255, 255, 0.95);
    padding: 25px;
    border-radius: 12px;
    margin: 25px auto;
    max-width: 500px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(194, 155, 64, 0.3);
}

.auth-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 10px;
}

.auth-section .btn {
    display: inline-block;
    padding: 12px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    text-align: center;
    min-width: 160px;
    font-size: 1rem;
    border: 2px solid transparent;
}

.auth-section .btn-login {
    background: linear-gradient(135deg, #c29b40 0%, #a67c00 100%);
    color: white;
    border-color: #c29b40;
}

.auth-section .btn-login:hover {
    background: linear-gradient(135deg, #a67c00 0%, #8c6b00 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(194, 155, 64, 0.4);
}

.auth-section .btn-register {
    background: transparent;
    color: #2c3e50;
    border: 2px solid #2c3e50;
}

.auth-section .btn-register:hover {
    background: #2c3e50;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(44, 62, 80, 0.3);
}

.user-welcome {
    text-align: center;
    color: #2c3e50;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid rgba(194, 155, 64, 0.3);
}

.user-welcome p {
    margin-bottom: 12px;
    font-size: 1.1em;
    font-weight: 500;
}

.user-welcome .btn {
    margin: 0 5px;
    padding: 8px 20px;
    font-size: 0.9rem;
    min-width: 140px;
}

.auth-links {
    text-align: center;
    margin-top: 15px;
    font-size: 0.9rem;
    color: #666;
}

.auth-links a {
    color: #c29b40;
    text-decoration: none;
    font-weight: 500;
}

.auth-links a:hover {
    color: #a67c00;
    text-decoration: underline;
}

@media (max-width: 768px) {
    .welcome-section {
        padding: 60px 0;
    }
    
    .welcome-section::before {
        background-attachment: scroll;
    }
    
    .hotel-name {
        font-size: 2.5rem;
    }
    
    .hotel-description {
        font-size: 1.1rem;
    }
    
    .hotel-info {
        padding: 20px;
        margin: 25px 15px;
    }
    
    .star-rating i {
        font-size: 2rem;
    }
    
    .auth-section {
        margin: 20px 15px;
        padding: 20px;
    }
    
    .auth-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .auth-section .btn {
        width: 100%;
        max-width: 250px;
    }
}

/* Секции с двумя колонками */
.two-columns-section {
    padding: 60px 0;
    background: white;
}

.section-header {
    text-align: center;
    margin-bottom: 40px;
}

.section-header h2 {
    color: #c29b40;
    font-size: 2.5rem;
    margin-bottom: 15px;
}

.section-header p {
    color: #666;
    font-size: 1.1rem;
}

.amenities-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.amenity-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    transition: transform 0.3s ease;
}

.amenity-card:hover {
    transform: translateY(-5px);
}

.amenity-icon {
    font-size: 2.5rem;
    color: #c29b40;
    margin-bottom: 15px;
}

.amenity-card h3 {
    color: #333;
    font-size: 1.1rem;
    margin: 0;
}

.rooms-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.room-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.room-card:hover {
    transform: translateY(-10px);
}

.room-image {
    height: 100px;
    background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    font-weight: bold;
}

.room-details {
    padding: 25px;
}

.room-details h3 {
    color: #c29b40;
    font-size: 1.5rem;
    margin-bottom: 10px;
}

.room-price {
    font-size: 1.3rem;
    color: #2c3e50;
    font-weight: bold;
    margin-bottom: 10px;
}

.room-capacity {
    color: #666;
    margin-bottom: 20px;
}

.btn-primary {
    background: linear-gradient(135deg, #c29b40 0%, #a67c00 100%);
    border: none;
    padding: 12px 30px;
    color: white;
    text-decoration: none;
    border-radius: 25px;
    font-weight: bold;
    transition: all 0.3s ease;
    display: inline-block;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(198, 155, 64, 0.3);
}

.btn-secondary {
    background: transparent;
    border: 2px solid #c29b40;
    color: #c29b40;
    padding: 12px 30px;
    text-decoration: none;
    border-radius: 25px;
    font-weight: bold;
    transition: all 0.3s ease;
    display: inline-block;
}

.btn-secondary:hover {
    background: #c29b40;
    color: white;
    transform: translateY(-2px);
}

/* Отзывы и CTA секция */
.testimonials-cta-section {
    padding: 60px 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.testimonials-grid {
    display: grid;
    gap: 30px;
}

.testimonial-card {
    background: transparent;
    padding: 30px;
    border-radius: 15px;
    border: 1px solid rgba(255,255,255,0.2);
}

.testimonial-content .rating {
    color: #ffd700;
    margin-bottom: 15px;
}

.testimonial-content p {
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 20px;
    font-style: italic;
}

.guest-info h4 {
    color: #fff;
    margin-bottom: 5px;
    font-size: 1.2rem;
}

.guest-info p {
    color: rgba(255,255,255,0.8);
    margin: 0;
    font-style: normal;
}

.cta-content {
    text-align: center;
    padding: 40px;
}

.cta-content h2 {
    font-size: 2.5rem;
    margin-bottom: 20px;
}

.cta-content p {
    font-size: 1.2rem;
    margin-bottom: 30px;
    opacity: 0.9;
}

.cta-btn {
    background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
    color: #2c3e50;
    padding: 18px 45px;
    font-size: 1.2rem;
    font-weight: bold;
    border: none;
    border-radius: 50px;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
    box-shadow: 0 10px 30px rgba(255, 215, 0, 0.3);
}

.cta-btn:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(255, 215, 0, 0.4);
}

/* Адаптивность */
@media (max-width: 768px) {
    .hotel-name {
        font-size: 2.2rem;
    }
    
    .hotel-description {
        font-size: 1.1rem;
    }
    
    .section-header h2 {
        font-size: 2rem;
    }
    
    .rooms-grid,
    .amenities-grid {
        grid-template-columns: 1fr;
    }
    
    .cta-content h2 {
        font-size: 2rem;
    }
    
    .cta-btn {
        padding: 15px 35px;
        font-size: 1.1rem;
    }
}
</style>

<!-- Приветственная секция -->
<section class="welcome-section">
    <div class="container">
        <div class="welcome-content">
            <h1 class="hotel-name">Добро пожаловать в The Ritz-Carlton Moscow</h1>
            <p class="hotel-description">
                Роскошный пятизвездочный отель в самом центре Москвы с видом на Красную площадь и Кремль. 
                Отель предлагает элегантные номера, рестораны высокой кухни, спа-центр мирового класса 
                и исключительный сервис.
            </p>
            
          <!-- Блок авторизации -->
<div class="auth-section">
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="user-welcome">
            <p>Добро пожаловать, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Гость'); ?>!</p>
            <a href="client_login.php" class="btn btn-login">Личный кабинет</a>
            <a href="logout.php" class="btn btn-register">Выйти</a>
        </div>
    <?php else: ?>
        <div class="auth-buttons">
            <a href="client_login.php" class="btn btn-login">Войти в аккаунт</a>
            <a href="client_register.php" class="btn btn-register">Зарегистрироваться</a>
        </div>
        <div class="auth-links">
            <p>Вы сотрудник? <a href="login.php">Вход для персонала</a></p>
        </div>
    <?php endif; ?>
</div>

            <div class="hotel-info">
                <p class="hotel-address">
                    Отель расположен по адресу: Тверская ул., 3, Москва, Россия.
                </p>
                <div class="star-rating">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                        <i class="fas fa-star"></i>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Удобства и номера -->
<section class="two-columns-section">
    <div class="container">
        <div class="row">
            <!-- Левая колонка - Удобства -->
            <div class="col-md-6">
                <div class="section-header">
                    <h2>Наши удобства</h2>
                    <p>Все для вашего комфортного пребывания</p>
                </div>
                
                <div class="amenities-grid">
                    <div class="amenity-card">
                        <div class="amenity-icon"><i class="fas fa-wifi"></i></div>
                        <h3>Бесплатный WiFi</h3>
                    </div>
                    <div class="amenity-card">
                        <div class="amenity-icon"><i class="fas fa-swimming-pool"></i></div>
                        <h3>Бассейн</h3>
                    </div>
                    <div class="amenity-card">
                        <div class="amenity-icon"><i class="fas fa-spa"></i></div>
                        <h3>Спа-центр</h3>
                    </div>
                    <div class="amenity-card">
                        <div class="amenity-icon"><i class="fas fa-dumbbell"></i></div>
                        <h3>Фитнес-центр</h3>
                    </div>
                    <div class="amenity-card">
                        <div class="amenity-icon"><i class="fas fa-utensils"></i></div>
                        <h3>Ресторан</h3>
                    </div>
                    <div class="amenity-card">
                        <div class="amenity-icon"><i class="fas fa-glass-martini-alt"></i></div>
                        <h3>Бар</h3>
                    </div>
                    <div class="amenity-card">
                        <div class="amenity-icon"><i class="fas fa-clock"></i></div>
                        <h3>Круглосуточная стойка регистрации</h3>
                    </div>
                    <div class="amenity-card">
                        <div class="amenity-icon"><i class="fas fa-concierge-bell"></i></div>
                        <h3>Обслуживание в номерах</h3>
                    </div>
                    <div class="amenity-card">
                        <div class="amenity-icon"><i class="fas fa-tshirt"></i></div>
                        <h3>Прачечная</h3>
                    </div>
                    <div class="amenity-card">
                        <div class="amenity-icon"><i class="fas fa-briefcase"></i></div>                     
                        <h3>Бизнес-центр</h3>
                    </div>
                    <div class="amenity-card">
                        <div class="amenity-icon"><i class="fas fa-parking"></i></div>  
			<h3>Парковка</h3>
                    </div>
                    <div class="amenity-card">
                        <div class="amenity-icon"><i class="fas fa-wheelchair"></i></div>  
			<h3>Номера для гостей с ограниченными возможностями</h3>
                    </div>
                    <div class="amenity-card">
                        <div class="amenity-icon"><i class="fas fa-bell-concierge"></i></div>  
			<h3>Консьерж-сервис</h3>
                    </div>
                    <div class="amenity-card">
                        <div class="amenity-icon"><i class="fas fa-umbrella-beach"></i></div>  
			<h3>Терраса на крыше</h3>
                    </div>
                    <div class="amenity-card">
                        <div class="amenity-icon"><i class="fas fa-hot-tub-person"></i></div>  
			<h3>Джакузи</h3>
                    </div>
                    </div>
                </div>
            </div>

            <!-- Правая колонка - Номера -->
            <div class="col-md-6">
                <div class="section-header">
                    <h2>Наши номера</h2>
                    <p>Выберите идеальный вариант для вашего пребывания</p>
                </div>
                
                <div class="rooms-grid">
                    <div class="room-card">
<img src="<?php echo $base_url; ?>/assets/images/фото3.jpg" height="300" class="logo-img" alt="<?php echo $hotel_name; ?>" />
                        <div class="room-image">Стандартный номер</div>

                        <div class="room-details">
                            <h3>Стандартный номер</h3>
                            <p class="room-price">от 15 000 ₽ за ночь</p>
                            <p class="room-capacity">Вместимость: до 2 гостей</p>
                        <a href="/hotel_management_system/rooms.php" class="btn-primary">Подробнее</a>    
                        </div>
                    </div>
                    
                    <div class="room-card">
<img src="<?php echo $base_url; ?>/assets/images/фото2.jpg" height="300" class="logo-img" alt="<?php echo $hotel_name; ?>" />
                        <div class="room-image">Делюкс</div>
                        <div class="room-details">
                            <h3>Делюкс</h3>
                            <p class="room-price">от 22 000 ₽ за ночь</p>
                            <p class="room-capacity">Вместимость: до 2 гостей</p>
                            <a href="/hotel_management_system/rooms.php" class="btn-primary">Подробнее</a>
                        </div>
                    </div>
                    
                    <div class="room-card">
<img src="<?php echo $base_url; ?>/assets/images/фото6.jpg" height="300" class="logo-img" alt="<?php echo $hotel_name; ?>" />
                        <div class="room-image">Люкс</div>
                        <div class="room-details">
                            <h3>Люкс</h3>
                            <p class="room-price">от 35 000 ₽ за ночь</p>
                            <p class="room-capacity">Вместимость: до 2 гостей</p>
                            <a href="/hotel_management_system/rooms.php" class="btn-primary">Подробнее</a>
                        </div>
                    </div>

		   <div class="room-card">
<img src="<?php echo $base_url; ?>/assets/images/фото5.jpeg" height="300" class="logo-img" alt="<?php echo $hotel_name; ?>" />
                        <div class="room-image">Президентский люкс</div>
                        <div class="room-details">
                            <h3>Президентский люкс</h3>
                            <p class="room-price">от 80 000 ₽ за ночь</p>
                            <p class="room-capacity">Вместимость: до 4 гостей</p>
                            <a href="/hotel_management_system/rooms.php" class="btn-primary">Подробнее</a>
                        </div>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 30px;">
                    <a href="/hotel_management_system/rooms.php" class="btn-secondary">Посмотреть все номера</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Отзывы и CTA -->
<section class="testimonials-cta-section">
    <div class="container">
        <div class="row">
            <!-- Левая колонка - Отзывы -->
            <div class="col-md-6">
                <div class="section-header">
                    <h2>Отзывы наших гостей</h2>
                    <p>Что говорят о нас наши клиенты</p>
                </div>
                
                <div class="testimonials-grid">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <div class="rating">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                <i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                            <p>"Отличный отель с прекрасным видом на Кремль. Обслуживание на высшем уровне!"</p>
                            <div class="guest-info">
                                <h4>Владимир Путин</h4>
                                <p>Президент России</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <div class="rating">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                <i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                            <p>"Роскошный люкс, впечатляющий сервис. Особенно понравился спа-центр."</p>
                            <div class="guest-info">
                                <h4>Лев Толстой</h4>
                                <p>Писатель</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Правая колонка - CTA -->
            <div class="col-md-6">
                <div class="cta-content">
                    <h2>Готовы к незабываемому отдыху?</h2>
                    <p>Забронируйте номер прямо сейчас и получите лучшие условия!</p>
                    <a href="/hotel_management_system/booking.php" class="cta-btn">Забронировать</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>