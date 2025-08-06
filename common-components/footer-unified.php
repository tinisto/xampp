<style>
    .unified-footer {
        background-color: var(--surface, #f8f9fa);
        color: var(--text-secondary, #6c757d);
        padding: 30px 0; /* SAME as reduced header height */
        margin-top: auto; /* Push footer to bottom */
        border-top: 1px solid var(--border-color, #dee2e6);
        flex-shrink: 0; /* Prevent footer from shrinking */
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
        gap: 20px;
        padding: 0; /* Keep minimal internal padding */
    }
    
    .footer-brand {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .footer-brand-text p {
        margin: 0;
        font-size: 14px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
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
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
        font-weight: 500;
        transition: color 0.3s ease;
    }
    
    .footer-nav-link:hover {
        color: var(--primary-color, #28a745) !important;
    }
    
    
    .footer-copyright {
        font-size: 14px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
        color: var(--text-secondary, #6c757d);
        text-align: center;
        flex: 1;
    }
    
    
    @media (max-width: 768px) {
        .footer-main {
            flex-direction: column;
            text-align: center;
        }
        
        .footer-nav {
            justify-content: center;
        }
    }
</style>

<footer class="unified-footer">
    <div class="footer-content">
        <div class="footer-main">
            <div class="footer-brand">
                <?php 
                include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/site-icon.php';
                renderSiteIcon('small', '/', 'footer-brand-icon');
                ?>
            </div>
            <div class="footer-copyright">
                <span>&copy; <?= date('Y') ?> 11-классники. Все права защищены.</span>
            </div>
            <nav class="footer-nav">
                <a href="/about" class="footer-nav-link">О проекте</a>
                <a href="/write" class="footer-nav-link">Связаться с нами</a>
                <a href="/privacy" class="footer-nav-link">Конфиденциальность</a>
                <a href="/terms" class="footer-nav-link">Условия использования</a>
            </nav>
        </div>
    </div>
</footer>