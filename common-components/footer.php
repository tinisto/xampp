<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/components/logo-component.php';
?>
<style>
    .footer-dark {
        background: transparent;
        border-top: 1px solid var(--border-color);
        margin-top: auto;
        padding: 3rem 0 1.5rem;
    }
    
    .footer-content {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr;
        gap: 3rem;
        margin-bottom: 2rem;
    }
    
    .footer-brand h4 {
        font-size: 1.25rem;
        margin-bottom: 1rem;
        color: var(--text-primary);
    }
    
    .footer-brand p {
        color: var(--text-secondary);
        line-height: 1.8;
        margin-bottom: 1.5rem;
    }
    
    .social-links {
        display: flex;
        gap: 0.75rem;
    }
    
    .social-link {
        width: 40px;
        height: 40px;
        background: var(--bg-primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-secondary);
        transition: all 0.2s;
        border: 1px solid var(--border-color);
    }
    
    .social-link:hover {
        background: var(--gradient);
        color: white;
        border-color: transparent;
        transform: translateY(-2px);
    }
    
    .footer-column h5 {
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        margin-bottom: 1.25rem;
        font-weight: 600;
    }
    
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .footer-links li {
        margin-bottom: 0.75rem;
    }
    
    .footer-links a {
        color: var(--text-secondary);
        font-size: 0.9375rem;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .footer-links a:hover {
        color: var(--accent-primary);
        transform: translateX(4px);
    }
    
    .footer-bottom {
        padding-top: 2rem;
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .copyright {
        color: var(--text-muted);
        font-size: 0.875rem;
    }
    
    .footer-bottom-links {
        display: flex;
        gap: 2rem;
    }
    
    .footer-bottom-links a {
        color: var(--text-muted);
        font-size: 0.875rem;
        transition: color 0.2s;
    }
    
    .footer-bottom-links a:hover {
        color: var(--text-primary);
    }
    
    @media (max-width: 768px) {
        .footer-content {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        
        .footer-bottom {
            flex-direction: column;
            text-align: center;
        }
        
        .footer-bottom-links {
            flex-direction: column;
            gap: 0.75rem;
        }
    }
</style>

<footer class="footer-dark">
    <div class="container">
        <div class="footer-content">
            <div class="footer-brand">
                <?php renderLogo('normal', true); ?>
                <p style="margin-top: 1rem;">
                    Образовательная платформа для абитуриентов. 
                    Помогаем выбрать учебное заведение и подготовиться к поступлению.
                </p>
                <div class="social-links">
                    <a href="#" class="social-link" aria-label="VKontakte">
                        <i class="fab fa-vk"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Telegram">
                        <i class="fab fa-telegram-plane"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
            
            <div class="footer-column">
                <h5>Образование</h5>
                <ul class="footer-links">
                    <li><a href="/vpo">ВУЗы России</a></li>
                    <li><a href="/spo">Колледжи</a></li>
                    <li><a href="/schools">Школы</a></li>
                    <li><a href="/tests">Тесты</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h5>Информация</h5>
                <ul class="footer-links">
                    <li><a href="/news">Новости</a></li>
                    <li><a href="/about">О проекте</a></li>
                    <li><a href="/faq">Вопросы и ответы</a></li>
                    <li><a href="/contacts">Контакты</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h5>Личный кабинет</h5>
                <ul class="footer-links">
                    <?php if (isset($_SESSION['email'])): ?>
                        <li><a href="/account"><i class="fas fa-user"></i> Мой профиль</a></li>
                        <li><a href="/my-tests"><i class="fas fa-chart-line"></i> Мои результаты</a></li>
                        <li><a href="/favorites"><i class="fas fa-heart"></i> Избранное</a></li>
                        <li><a href="/pages/logout/logout.php"><i class="fas fa-sign-out-alt"></i> Выход</a></li>
                    <?php else: ?>
                        <li><a href="/login"><i class="fas fa-sign-in-alt"></i> Вход</a></li>
                        <li><a href="/registration"><i class="fas fa-user-plus"></i> Регистрация</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="copyright">
                &copy; <?php echo date('Y'); ?> 11классники. Все права защищены.
            </div>
            <div class="footer-bottom-links">
                <a href="/privacy">Политика конфиденциальности</a>
                <a href="/terms">Пользовательское соглашение</a>
                <a href="/sitemap">Карта сайта</a>
            </div>
        </div>
    </div>
</footer>