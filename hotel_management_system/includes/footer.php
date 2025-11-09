<?php
// footer.php - Футер сайта отеля The Ritz-Carlton Moscow

// Переменные для динамического контента
$hotel_name = "The Ritz-Carlton Moscow";
$phone_number = "+7 (495) 225-8888";
$email = "info@ritzcarltonmoscow.com";
$address = "Тверская ул., 3, Москва, 125009";
$base_url = "/hotel_management_system";

// Массив быстрых ссылок
$quick_links = [
    'index.php' => 'Главная',
    'rooms.php' => 'Номера',
    'services.php' => 'Услуги',
    'about.php' => 'Об отеле',
    'contact.php' => 'Контакты'
];
?>
<style>
/* Стили для footer */
.footer {
    background: #2c3e50;
    color: #ecf0f1;
    padding: 50px 0 20px;
    margin-top: 50px;
}

.footer-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
    margin-bottom: 30px;
}

.footer-column h3 {
    color: #e74c3c;
    margin-bottom: 20px;
    font-size: 18px;
}

.footer-column p {
    line-height: 1.6;
    margin-bottom: 15px;
}

.footer-column address p {
    margin-bottom: 10px;
}

.footer-column ul {
    list-style: none;
    padding: 0;
}

.footer-column ul li {
    margin-bottom: 8px;
}

.footer-column a {
    color: #bdc3c7;
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-column a:hover {
    color: #e74c3c;
}

.social-links {
    margin-top: 15px;
}

.social-links a {
    display: inline-block;
    margin-right: 15px;
    font-size: 18px;
    width: 36px;
    height: 36px;
    line-height: 36px;
    text-align: center;
    background: #34495e;
    border-radius: 50%;
    transition: background 0.3s ease;
}

.social-links a:hover {
    background: #e74c3c;
}

.newsletter-form input {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: none;
    border-radius: 4px;
}

.newsletter-form button {
    width: 100%;
    padding: 10px;
    background: #e74c3c;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.newsletter-form button:hover {
    background: #c0392b;
}

.footer-bottom {
    border-top: 1px solid #34495e;
    padding-top: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.footer-links a {
    margin-left: 20px;
    color: #bdc3c7;
    text-decoration: none;
}

.footer-links a:hover {
    color: #e74c3c;
}

@media (max-width: 768px) {
    .footer-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .footer-bottom {
        flex-direction: column;
        text-align: center;
    }
    
    .footer-links {
        margin-top: 15px;
    }
    
    .footer-links a {
        margin: 0 10px;
    }
}
</style>

</div><!-- Закрываем container из header.php -->
</main>
        
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <!-- Колонка 1: Информация об отеле -->
            <div class="footer-column">
                <h3>The Ritz-Carlton Moscow</h3>
                <p>Роскошный пятизвездочный отель в самом центре Москвы с видом на Красную площадь и Кремль.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
            
            <!-- Колонка 2: Контакты -->
            <div class="footer-column">
                <h3>Контакты</h3>
                <address>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo $address; ?></p>
                    <p><i class="fas fa-phone"></i> 
                        <a href="tel:<?php echo str_replace(' ', '', $phone_number); ?>">
                            <?php echo $phone_number; ?>
                        </a>
                    </p>
                    <p><i class="fas fa-envelope"></i> 
                        <a href="mailto:<?php echo $email; ?>">
                            <?php echo $email; ?>
                        </a>
                    </p>
                </address>
            </div>
            
            <!-- Колонка 3: Быстрые ссылки -->
            <div class="footer-column">
                <h3>Быстрые ссылки</h3>
                <ul>
                    <?php foreach ($quick_links as $url => $title): ?>
                        <li>
                            <a href="<?php echo $base_url . '/' . $url; ?>">
                                <?php echo $title; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <!-- Колонка 4: Подписка на новости -->
            <div class="footer-column">
                <h3>Подписаться на новости</h3>
                <form class="newsletter-form" action="<?php echo $base_url; ?>/subscribe.php" method="POST">
                    <input type="email" name="email" placeholder="Ваш email" required>
                    <button type="submit" class="btn btn-primary">Подписаться</button>
                </form>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> The Ritz-Carlton Moscow. Все права защищены.</p>
            <div class="footer-links">
                <a href="<?php echo $base_url; ?>/privacy.php">Политика конфиденциальности</a>
                <a href="<?php echo $base_url; ?>/terms.php">Условия использования</a>
            </div>
        </div>
    </div>
</footer>

<script src="<?php echo $base_url; ?>/assets/js/main.js"></script>
</body>
</html>