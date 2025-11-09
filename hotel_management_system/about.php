<?php
$page_title = "Об отеле";
require_once 'includes/config.php';
require_once 'includes/functions.php';

$hotel = getHotelDetails();
$amenities = getHotelAmenities();
$photos = getHotelPhotos();
?>

<style>
/* Стили для страницы "Об отеле" */
.about-section {
    padding: 80px 0;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

/* Заголовок на всю ширину */
.full-width-header {
    text-align: center;
    margin-bottom: 50px;
    padding: 0 20px;
}

.full-width-header h2 {
    color: var(--primary-color);
    font-size: 3rem;
    margin-bottom: 15px;
    font-family: 'Playfair Display', serif;
}

.full-width-header p {
    color: var(--text-light);
    font-size: 1.3rem;
    max-width: 600px;
    margin: 0 auto;
}

.about-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: start;
}

.about-text {
    background: var(--white);
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.about-text h3 {
    color: var(--primary-color);
    font-size: 1.8rem;
    margin: 30px 0 20px 0;
    font-family: 'Playfair Display', serif;
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 10px;
}

.about-text h3:first-child {
    margin-top: 0;
}

.about-text p {
    color: var(--text-color);
    line-height: 1.8;
    font-size: 1.1rem;
    margin-bottom: 20px;
}

.amenities-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    list-style: none;
    padding: 0;
}

.amenities-list li {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    background: #f8f9fa;
    border-radius: 8px;
    transition: all 0.3s ease;
    border-left: 4px solid var(--primary-color);
}

.amenities-list li:hover {
    background: var(--white);
    transform: translateX(5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.amenities-list i {
    color: var(--primary-color);
    margin-right: 12px;
    font-size: 1.1rem;
    width: 20px;
    text-align: center;
}

/* Исправленные стили для контактов - БЕЛЫЙ фон */
.contact-info {
    background: var(--white);
    padding: 25px;
    border-radius: 10px;
    border: 2px solid var(--primary-color);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.contact-info p {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    color: var(--text-color);
}

.contact-info p:last-child {
    margin-bottom: 0;
}

.contact-info i {
    margin-right: 15px;
    font-size: 1.2rem;
    width: 20px;
    text-align: center;
    color: var(--primary-color);
}

.contact-info a {
    color: var(--text-color) !important;
    text-decoration: none;
    transition: color 0.3s ease;
}

.contact-info a:hover {
    color: var(--primary-color) !important;
    text-decoration: underline;
}

.about-gallery {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.main-photo {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    height: 400px;
}

.main-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.main-photo:hover img {
    transform: scale(1.05);
}

.thumbnail-photos {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
}

.thumbnail-photos img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.thumbnail-photos img:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.hotel-stats {
    padding: 60px 0;
    background: linear-gradient(135deg, var(--primary-color) 0%, #a67c00 100%);
    color: var(--white);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 40px;
    text-align: center;
}

.stat-item {
    padding: 30px;
    background: rgba(255,255,255,0.1);
    border-radius: 15px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    transition: transform 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-10px);
    background: rgba(255,255,255,0.15);
}

.stat-number {
    font-size: 3rem;
    font-weight: bold;
    margin-bottom: 10px;
    font-family: 'Playfair Display', serif;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.stat-label {
    font-size: 1.1rem;
    opacity: 0.9;
    font-weight: 500;
}

/* Анимации */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.about-text,
.about-gallery,
.stat-item {
    animation: fadeIn 0.8s ease-out;
}

.stat-item:nth-child(1) { animation-delay: 0.1s; }
.stat-item:nth-child(2) { animation-delay: 0.2s; }
.stat-item:nth-child(3) { animation-delay: 0.3s; }

/* Дополнительные стили для улучшения читаемости */
.hotel-description {
    font-size: 1.2rem;
    line-height: 1.8;
    color: var(--text-color);
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 25px;
    border-radius: 10px;
    border-left: 4px solid var(--primary-color);
    margin-bottom: 30px;
}

.star-rating {
    display: inline-flex;
    gap: 5px;
    margin-left: 15px;
}

.star-rating i {
    color: #ffd700;
    font-size: 1.3rem;
}

/* Адаптивность */
@media (max-width: 992px) {
    .about-content {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    
    .about-text {
        padding: 30px;
    }
    
    .amenities-list {
        grid-template-columns: 1fr;
    }
    
    .main-photo {
        height: 300px;
    }
    
    .full-width-header h2 {
        font-size: 2.5rem;
    }
}

@media (max-width: 768px) {
    .about-section {
        padding: 60px 0;
    }
    
    .about-text h3 {
        font-size: 1.5rem;
    }
    
    .thumbnail-photos {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .thumbnail-photos img {
        height: 100px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .stat-item {
        padding: 20px;
    }
    
    .stat-number {
        font-size: 2.5rem;
    }
    
    .full-width-header h2 {
        font-size: 2rem;
    }
    
    .full-width-header p {
        font-size: 1.1rem;
    }
}

@media (max-width: 480px) {
    .about-text {
        padding: 20px;
    }
    
    .thumbnail-photos {
        grid-template-columns: 1fr;
    }
    
    .contact-info {
        padding: 20px;
    }
    
    .contact-info p {
        flex-direction: column;
        text-align: center;
        gap: 8px;
    }
    
    .contact-info i {
        margin-right: 0;
    }
    
    .hotel-description {
        padding: 20px;
    }
}
</style>

<section class="about-section">
    <div class="container">
        <!-- Заголовок на всю ширину -->
        <div class="full-width-header">
            <h2>О нашем отеле</h2>
            <p>Роскошь и комфорт в самом сердце Москвы</p>
        </div>
        
        <!-- Основное описание на всю ширину -->
        <div class="hotel-description">
            <strong>The Ritz-Carlton Moscow</strong> - <?= htmlspecialchars($hotel['description']); ?>
            <div class="star-rating">
                <?php for ($i = 0; $i < $hotel['star_rating']; $i++): ?>
                    <i class="fas fa-star"></i>
                <?php endfor; ?>
            </div>
        </div>
        
        <!-- Двухколоночный контент -->
        <div class="about-content">
            <div class="about-text">
                <h3>Наши удобства</h3>
                <ul class="amenities-list">
                    <?php foreach ($amenities as $amenity): ?>
                    <li>
                        <i class="fas <?= htmlspecialchars($amenity['icon_class']); ?>"></i> 
                        <?= htmlspecialchars($amenity['amenity_name']); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                
                <h3>Контакты</h3>
                <div class="contact-info">
                    <p>
                        <i class="fas fa-map-marker-alt"></i> 
                        <span><?= htmlspecialchars($hotel['address']); ?>, <?= htmlspecialchars($hotel['city']); ?>, <?= htmlspecialchars($hotel['country']); ?></span>
                    </p>
                    <p>
                        <i class="fas fa-phone"></i> 
                        <span><a href="tel:<?= htmlspecialchars($hotel['phone']); ?>"><?= htmlspecialchars($hotel['phone']); ?></a></span>
                    </p>
                    <p>
                        <i class="fas fa-envelope"></i> 
                        <span><a href="mailto:<?= htmlspecialchars($hotel['email']); ?>"><?= htmlspecialchars($hotel['email']); ?></a></span>
                    </p>
                </div>
            </div>
            
            <div class="about-gallery">
                <?php if (!empty($photos)): ?>
                <div class="main-photo">
                    <img src="<?= htmlspecialchars($photos[0]['photo_url']); ?>" alt="<?= htmlspecialchars($hotel['name']); ?>">
                </div>
                <div class="thumbnail-photos">
                    <?php for ($i = 1; $i < min(4, count($photos)); $i++): ?>
                    <img src="<?= htmlspecialchars($photos[$i]['photo_url']); ?>" alt="Фото отеля <?= $i + 1; ?>">
                    <?php endfor; ?>
                </div>
                <?php else: ?>
                <div class="main-photo">
                    <img src="<?= $base_url; ?>/assets/images/hotel-default.jpg" alt="The Ritz-Carlton Moscow">
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="hotel-stats">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number"><?= htmlspecialchars($hotel['star_rating']); ?></div>
                <div class="stat-label">Звездный рейтинг</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">150+</div>
                <div class="stat-label">Довольных гостей ежедневно</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Служба поддержки</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">50+</div>
                <div class="stat-label">Роскошных номеров</div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>