<?php
$page_title = "Подтверждение бронирования";
require_once 'includes/config.php';
require_once 'includes/functions.php';

$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$booking_id) {
    header("Location: booking.php");
    exit;
}

$booking = getBookingDetails($booking_id);
if (!$booking) {
    header("Location: booking.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    /* Все предыдущие стили остаются без изменений */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .confirmation-section {
        width: 100%;
        padding: 40px 0;
    }

    .confirmation-box {
        background: white;
        border-radius: 20px;
        padding: 50px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        text-align: center;
        max-width: 600px;
        margin: 0 auto;
        position: relative;
        overflow: hidden;
    }

    .confirmation-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #4CAF50, #45a049);
    }

    .success-icon {
        font-size: 80px;
        color: #4CAF50;
        margin-bottom: 30px;
        animation: bounce 1s ease;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-20px);
        }
        60% {
            transform: translateY(-10px);
        }
    }

    h2 {
        color: #2c3e50;
        font-size: 32px;
        margin-bottom: 15px;
        font-weight: 700;
    }

    .confirmation-box > p {
        color: #7f8c8d;
        font-size: 18px;
        margin-bottom: 40px;
        line-height: 1.6;
    }

    .booking-summary {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 40px;
        text-align: left;
        border: 1px solid #e3e3e3;
    }

    .booking-summary h3 {
        color: #2c3e50;
        font-size: 22px;
        margin-bottom: 25px;
        text-align: center;
        font-weight: 600;
        position: relative;
        padding-bottom: 10px;
    }

    .booking-summary h3::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, #3498db, #2980b9);
        border-radius: 2px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #e3e3e3;
        font-size: 16px;
    }

    .summary-item:last-child {
        border-bottom: none;
    }

    .summary-item strong {
        color: #2c3e50;
        font-weight: 600;
        min-width: 120px;
    }

    .summary-item:last-child {
        font-size: 18px;
        font-weight: 700;
        color: #27ae60;
        padding-top: 20px;
        margin-top: 10px;
        border-top: 2px solid #3498db;
    }

    .summary-item:last-child strong {
        color: #2c3e50;
        font-size: 18px;
    }

    .confirmation-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-top: 30px;
    }

    .btn {
        padding: 16px 30px;
        border: none;
        border-radius: 50px;
        font-size: 16px;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #2980b9, #1c6ea4);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(52, 152, 219, 0.4);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #95a5a6, #7f8c8d);
        color: white;
        box-shadow: 0 4px 15px rgba(149, 165, 166, 0.3);
    }

    .btn-secondary:hover {
        background: linear-gradient(135deg, #7f8c8d, #6c7b7d);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(149, 165, 166, 0.4);
    }

    /* Анимации */
    .confirmation-box {
        animation: slideUp 0.8s ease;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .summary-item {
        animation: fadeIn 0.6s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Адаптивность */
    @media (max-width: 768px) {
        .container {
            padding: 10px;
        }
        
        .confirmation-box {
            padding: 30px 20px;
            margin: 20px;
        }
        
        h2 {
            font-size: 28px;
        }
        
        .confirmation-box > p {
            font-size: 16px;
        }
        
        .booking-summary {
            padding: 20px;
        }
        
        .summary-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
            padding: 12px 0;
        }
        
        .summary-item strong {
            min-width: auto;
        }
        
        .confirmation-actions {
            grid-template-columns: 1fr;
            gap: 15px;
        }
        
        .btn {
            padding: 14px 25px;
            font-size: 15px;
        }
    }

    @media (max-width: 480px) {
        .success-icon {
            font-size: 60px;
        }
        
        h2 {
            font-size: 24px;
        }
        
        .booking-summary h3 {
            font-size: 20px;
        }
        
        .summary-item {
            font-size: 14px;
        }
        
        .summary-item:last-child {
            font-size: 16px;
        }
    }

    /* Дополнительные украшения */
    .confetti {
        position: absolute;
        width: 10px;
        height: 10px;
        background: linear-gradient(45deg, #4CAF50, #3498db, #e74c3c, #f39c12);
        border-radius: 50%;
        animation: confetti 3s ease-out;
        opacity: 0;
    }

    @keyframes confetti {
        from {
            transform: translateY(0) rotate(0deg);
            opacity: 1;
        }
        to {
            transform: translateY(100px) rotate(360deg);
            opacity: 0;
        }
    }

    /* Иконки */
    .btn i {
        font-size: 18px;
    }

    /* Ховер эффекты */
    .btn:hover {
        transform: translateY(-2px);
    }

    .btn:active {
        transform: translateY(0);
    }

    /* Стили для fallback окна email - ИСПРАВЛЕННЫЕ */
    .email-fallback {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .email-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.7);
    }

    .email-modal {
        position: relative;
        background: white;
        padding: 30px;
        border-radius: 15px;
        max-width: 500px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
        z-index: 10001;
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    }

    .email-modal h3 {
        color: #2c3e50;
        margin-bottom: 15px;
        text-align: center;
        font-size: 24px;
    }

    .email-content {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin: 15px 0;
        border: 1px solid #e9ecef;
    }

    .email-content p {
        margin: 8px 0;
        color: #2c3e50;
    }

    .email-content textarea {
        width: 100%;
        height: 120px;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 10px;
        font-family: inherit;
        font-size: 14px;
        line-height: 1.4;
        margin-top: 10px;
        resize: vertical;
    }

    .email-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 20px;
    }

    .btn-copy, .btn-close {
        padding: 12px 24px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-copy {
        background: #3498db;
        color: white;
    }

    .btn-copy:hover {
        background: #2980b9;
        transform: translateY(-2px);
    }

    .btn-close {
        background: #95a5a6;
        color: white;
    }

    .btn-close:hover {
        background: #7f8c8d;
        transform: translateY(-2px);
    }

    .email-success {
        background: #d4edda;
        color: #155724;
        padding: 10px;
        border-radius: 5px;
        margin-top: 10px;
        text-align: center;
        display: none;
    }
    </style>
</head>
<body>

<section class="confirmation-section">
    <div class="container">
        <div class="confirmation-box">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2>Бронирование подтверждено!</h2>
            <p>Ваш номер успешно забронирован. ID бронирования: #<?= $booking['booking_id']; ?></p>
            
            <div class="booking-summary">
                <h3>Детали бронирования</h3>
                <div class="summary-item">
                    <strong>Номер:</strong> <?= $booking['room_number']; ?> (<?= $booking['type_name']; ?>)
                </div>
                <div class="summary-item">
                    <strong>Даты:</strong> <?= date('d.m.Y', strtotime($booking['check_in_date'])); ?> - <?= date('d.m.Y', strtotime($booking['check_out_date'])); ?>
                </div>
                <div class="summary-item">
                    <strong>Гость:</strong> <?= $booking['guest_name']; ?>
                </div>
                <div class="summary-item">
                    <strong>Сумма:</strong> <?= number_format($booking['total_amount'], 0, ',', ' '); ?> ₽
                </div>
            </div>
            
            <div class="confirmation-actions">
                <a href="/hotel_management_system/index.php" class="btn btn-primary">
                    <i class="fas fa-home"></i>На главную
                </a>
                <?php if (!empty($booking['email'])): ?>
                    <button onclick="sendEmailConfirmation()" class="btn btn-secondary">
                        <i class="fas fa-envelope"></i>Отправить email
                    </button>
                <?php else: ?>
                    <button class="btn btn-secondary" disabled title="Email не указан">
                        <i class="fas fa-envelope"></i>Отправить email
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
// Добавляем конфетти для праздничного эффекта
document.addEventListener('DOMContentLoaded', function() {
    const colors = ['#4CAF50', '#3498db', '#e74c3c', '#f39c12', '#9b59b6', '#1abc9c'];
    const box = document.querySelector('.confirmation-box');
    
    for (let i = 0; i < 30; i++) {
        const confetti = document.createElement('div');
        confetti.className = 'confetti';
        confetti.style.left = Math.random() * 100 + '%';
        confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.animationDelay = Math.random() * 2 + 's';
        confetti.style.width = Math.random() * 8 + 4 + 'px';
        confetti.style.height = confetti.style.width;
        box.appendChild(confetti);
    }
});

function sendEmailConfirmation() {
    const email = '<?= $booking['email'] ?>';
    const bookingId = '<?= $booking['booking_id'] ?>';
    const guestName = '<?= $booking['guest_name'] ?>';
    const roomNumber = '<?= $booking['room_number'] ?>';
    const roomType = '<?= $booking['type_name'] ?>';
    const checkIn = '<?= date('d.m.Y', strtotime($booking['check_in_date'])); ?>';
    const checkOut = '<?= date('d.m.Y', strtotime($booking['check_out_date'])); ?>';
    const amount = '<?= number_format($booking['total_amount'], 0, ',', ' '); ?>';
    const nights = '<?= round((strtotime($booking['check_out_date']) - strtotime($booking['check_in_date'])) / (60 * 60 * 24)); ?>';

    const subject = 'Подтверждение бронирования #' + bookingId;

    const body = `
Уважаемый(ая) ${guestName},

Ваше бронирование в отеле The Ritz-Carlton Moscow подтверждено!

🎉 ДЕТАЛИ БРОНИРОВАНИЯ:
────────────────────
• ID бронирования: #${bookingId}
• Номер: ${roomNumber} (${roomType})
• Даты проживания: ${checkIn} - ${checkOut}
• Количество ночей: ${nights}
• Итоговая сумма: ${amount} ₽

📍 АДРЕС ОТЕЛЯ:
The Ritz-Carlton Moscow
Тверская ул., 3, Москва

📞 КОНТАКТНАЯ ИНФОРМАЦИЯ:
Телефон: +7 (495) 225-8888
Email: info@ritzcarltonmoscow.ru

⏰ ВРЕМЯ ЗАЕЗДА/ВЫЕЗДА:
Заезд: после 14:00
Выезд: до 12:00

Благодарим за выбор нашего отеля! Ждём вас с нетерпением.

С уважением,
Команда The Ritz-Carlton Moscow
`.trim();

    const encodedSubject = encodeURIComponent(subject);
    const encodedBody = encodeURIComponent(body);

    const mailtoLink = `mailto:${email}?subject=${encodedSubject}&body=${encodedBody}`;

    // Создаём временную ссылку и кликаем по ней
    const tempLink = document.createElement('a');
    tempLink.href = mailtoLink;
    tempLink.style.display = 'none';
    document.body.appendChild(tempLink);
    tempLink.click();
    document.body.removeChild(tempLink);

    // Резервный вариант, если почтовый клиент не открылся
    setTimeout(() => {
        showEmailFallback(body, email, subject);
    }, 1000);
}

function showEmailFallback(body, email, subject) {
    const fallbackHTML = `
        <div class="email-fallback">
            <div class="email-overlay" onclick="closeFallback()"></div>
            <div class="email-modal">
                <h3>✉️ Информация для отправки</h3>
                <p>Почтовый клиент не открылся автоматически. Скопируйте текст ниже:</p>
                
                <div class="email-content">
                    <p><strong>Кому:</strong> ${email}</p>
                    <p><strong>Тема:</strong> ${subject}</p>
                    <textarea id="emailText" readonly>${body}</textarea>
                    <div id="copySuccess" class="email-success">
                        ✓ Текст скопирован в буфер обмена!
                    </div>
                </div>
                
                <div class="email-actions">
                    <button onclick="copyEmailContent()" class="btn-copy">
                        <i class="fas fa-copy"></i> Скопировать текст
                    </button>
                    <button onclick="closeFallback()" class="btn-close">
                        <i class="fas fa-times"></i> Закрыть
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', fallbackHTML);
}

function copyEmailContent() {
    const textarea = document.getElementById('emailText');
    const successMessage = document.getElementById('copySuccess');
    
    textarea.select();
    textarea.setSelectionRange(0, 99999); // Для мобильных устройств
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            successMessage.style.display = 'block';
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 3000);
        }
    } catch (err) {
        alert('Не удалось скопировать текст. Попробуйте выделить и скопировать вручную.');
    }
}

function closeFallback() {
    const fallback = document.querySelector('.email-fallback');
    if (fallback) {
        fallback.style.opacity = '0';
        fallback.style.transition = 'opacity 0.3s ease';
        setTimeout(() => {
            fallback.remove();
        }, 300);
    }
}

// Закрытие по ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeFallback();
    }
});
</script>

</body>
</html>