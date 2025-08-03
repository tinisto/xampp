<style>
    .unified-footer {
        background-color: var(--surface, #f8f9fa);
        color: var(--text-secondary, #6c757d);
        padding: 40px 0 20px;
        margin-top: auto;
        border-top: 1px solid var(--border-color, #dee2e6);
    }
    
    .footer-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .footer-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 30px;
        margin-bottom: 20px;
    }
    
    .footer-brand h5 {
        color: var(--primary-color, #28a745);
        font-size: 24px;
        font-weight: 700;
        margin: 0 0 10px 0;
    }
    
    .footer-brand p {
        margin: 0;
        font-size: 14px;
    }
    
    .footer-nav {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }
    
    .footer-nav-link {
        color: var(--text-secondary, #6c757d) !important;
        text-decoration: none;
        font-size: 14px;
        transition: color 0.3s ease;
    }
    
    .footer-nav-link:hover {
        color: var(--primary-color, #28a745) !important;
    }
    
    .footer-bottom {
        border-top: 1px solid var(--border-color, #dee2e6);
        padding-top: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .footer-copyright {
        display: flex;
        gap: 10px;
        font-size: 14px;
    }
    
    .footer-contact a {
        color: var(--text-secondary, #6c757d);
        text-decoration: none;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    
    .footer-contact a:hover {
        color: var(--primary-color, #28a745);
        text-decoration: underline;
    }
    
    @media (max-width: 768px) {
        .footer-main {
            flex-direction: column;
            text-align: center;
        }
        
        .footer-nav {
            justify-content: center;
        }
        
        .footer-bottom {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<footer class="unified-footer">
    <div class="footer-content">
        <div class="footer-main">
            <div class="footer-brand">
                <h5>11-классники</h5>
                <p>Образовательный портал России</p>
            </div>
            <nav class="footer-nav">
                <a href="/about" class="footer-nav-link">О проекте</a>
                <a href="/write" class="footer-nav-link">Связаться с нами</a>
                <a href="/tests" class="footer-nav-link">Тесты</a>
                <a href="/news" class="footer-nav-link">Новости</a>
            </nav>
        </div>
        <div class="footer-bottom">
            <div class="footer-copyright">
                <span>&copy; <?= date('Y') ?> 11-классники.</span>
                <span>Все права защищены.</span>
            </div>
            <div class="footer-contact">
                <a href="mailto:support@11klassniki.ru">
                    <i class="fas fa-envelope"></i> support@11klassniki.ru
                </a>
            </div>
        </div>
    </div>
</footer>