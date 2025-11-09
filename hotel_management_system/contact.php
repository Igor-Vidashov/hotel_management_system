<?php
$page_title = "Контакты";
require_once 'includes/config.php';
require_once 'includes/functions.php';

$hotel = getHotelDetails();
?>

<style>
/* Стили для страницы контактов */
.contact-section {
    padding: 80px 0;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.contact-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
    margin-top: 40px;
}

.contact-info {
    background: var(--white);
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.contact-info h3 {
    color: var(--primary-color);
    font-size: 1.8rem;
    margin-bottom: 30px;
    text-align: center;
    font-family: 'Playfair Display', serif;
}

.info-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 25px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.info-item:hover {
    transform: translateX(10px);
    background: var(--white);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.info-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--primary-color) 0%, #a67c00 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
    flex-shrink: 0;
}

.info-icon i {
    color: var(--white);
    font-size: 1.2rem;
}

.info-content h4 {
    color: var(--dark-color);
    font-size: 1.2rem;
    margin-bottom: 5px;
    font-family: 'Playfair Display', serif;
}

.info-content p {
    color: var(--text-color);
    margin: 0;
    line-height: 1.6;
}

.info-content a {
    color: var(--primary-color);
    text-decoration: none;
    transition: color 0.3s ease;
}

.info-content a:hover {
    color: var(--secondary-color);
}

.contact-form {
    background: var(--white);
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.contact-form h3 {
    color: var(--primary-color);
    font-size: 1.8rem;
    margin-bottom: 30px;
    text-align: center;
    font-family: 'Playfair Display', serif;
}

.contact-form .form-group {
    margin-bottom: 20px;
}

.contact-form label {
    display: block;
    margin-bottom: 8px;
    color: var(--dark-color);
    font-weight: 500;
}

.contact-form .form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.contact-form .form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    background: var(--white);
    box-shadow: 0 0 0 3px rgba(179, 139, 89, 0.1);
}

.contact-form textarea.form-control {
    resize: vertical;
    min-height: 120px;
}

.contact-form .btn-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, #a67c00 100%);
    border: none;
    padding: 15px 40px;
    color: var(--white);
    border-radius: 25px;
    font-weight: bold;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    width: 100%;
    cursor: pointer;
}

.contact-form .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(179, 139, 89, 0.3);
}

.map-section {
    padding: 60px 0;
    background: var(--white);
}

.map-section h3 {
    color: var(--primary-color);
    font-size: 2rem;
    margin-bottom: 30px;
    text-align: center;
    font-family: 'Playfair Display', serif;
}

.map-container {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    border: 5px solid var(--white);
}

/* Анимации */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.contact-info,
.contact-form {
    animation: fadeInUp 0.6s ease-out;
}

.contact-form {
    animation-delay: 0.2s;
}

/* Адаптивность */
@media (max-width: 992px) {
    .contact-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .contact-info,
    .contact-form {
        padding: 30px;
    }
}

@media (max-width: 768px) {
    .contact-section {
        padding: 60px 0;
    }
    
    .info-item {
        flex-direction: column;
        text-align: center;
        padding: 15px;
    }
    
    .info-icon {
        margin-right: 0;
        margin-bottom: 15px;
    }
    
    .contact-info h3,
    .contact-form h3 {
        font-size: 1.5rem;
    }
    
    .map-section h3 {
        font-size: 1.5rem;
    }
}

@media (max-width: 480px) {
    .contact-info,
    .contact-form {
        padding: 20px;
    }
    
    .info-item {
        padding: 12px;
    }
}
</style>

<section class="contact-section">
    <div class="container">
        <div class="section-header">
            <h2>Свяжитесь с нами</h2>
            <p>Мы всегда рады ответить на ваши вопросы</p>
        </div>
        
        <div class="contact-grid">
            <div class="contact-info">
                <h3>Контактная информация</h3>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-content">
                        <h4>Адрес</h4>
                        <p><?= htmlspecialchars($hotel['address']); ?>, <?= htmlspecialchars($hotel['city']); ?>, <?= htmlspecialchars($hotel['country']); ?></p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="info-content">
                        <h4>Телефон</h4>
                        <p><a href="tel:<?= htmlspecialchars($hotel['phone']); ?>"><?= htmlspecialchars($hotel['phone']); ?></a></p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-content">
                        <h4>Email</h4>
                        <p><a href="mailto:<?= htmlspecialchars($hotel['email']); ?>"><?= htmlspecialchars($hotel['email']); ?></a></p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="info-content">
                        <h4>Часы работы</h4>
                        <p>Круглосуточно, 7 дней в неделю</p>
                        <p>Заезд: <?= htmlspecialchars($hotel['check_in_time']); ?> | Выезд: <?= htmlspecialchars($hotel['check_out_time']); ?></p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="info-content">
                        <h4>Рейтинг</h4>
                        <p>
                            <?php for ($i = 0; $i < $hotel['star_rating']; $i++): ?>
                                <i class="fas fa-star" style="color: #ffd700;"></i>
                            <?php endfor; ?>
                            <?= htmlspecialchars($hotel['star_rating']); ?>-звездочный отель
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="contact-form">
                <h3>Напишите нам</h3>
                <form id="contactForm" method="POST" action="<?= $base_url; ?>/includes/contact_handler.php">
                    <div class="form-group">
                        <label for="name">Ваше имя *</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Телефон</label>
                        <input type="tel" id="phone" name="phone" class="form-control" placeholder="+7 (XXX) XXX-XX-XX">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Тема *</label>
                        <input type="text" id="subject" name="subject" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Сообщение *</label>
                        <textarea id="message" name="message" class="form-control" rows="5" 
                                  placeholder="Расскажите нам, чем мы можем вам помочь..." required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Отправить сообщение
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="map-section">
    <div class="container">
        <h3>Мы на карте</h3>
        <div class="map-container">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2244.4303074593986!2d37.608860415931104!3d55.76139798055578!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46b54a5a738fa419%3A0x7c347d506f52311f!2sThe%20Ritz-Carlton%2C%20Moscow!5e0!3m2!1sen!2sru!4v1620000000000!5m2!1sen!2sru" 
                width="100%" 
                height="450" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>