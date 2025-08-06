<style>
    .unified-footer {
        background-color: var(--surface, #f8f9fa);
        color: var(--text-secondary, #6c757d);
        padding: 15px 20px; /* Reduced vertical padding on mobile */
        margin: 0; /* No margins */
        margin-top: auto; /* Push footer to bottom */
        border-top: 1px solid var(--border-color, #dee2e6);
        flex-shrink: 0; /* Prevent footer from shrinking */
        box-sizing: border-box;
    }
    
    /* Desktop - no change in spacing approach */
    @media (min-width: 769px) {
        .unified-footer {
            padding: 30px; /* Slightly more padding on desktop */
            margin: 0; /* No margins */
            margin-top: auto; /* Keep margin-top auto */
        }
    }
    
    .footer-content {
        max-width: none; /* Remove max-width like other sections */
        margin: 0;
        padding: 0; /* Remove padding - it's on the parent */
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
        .unified-footer {
            padding: 10px 15px; /* Less padding on mobile */
        }
        
        .footer-main {
            flex-direction: column;
            text-align: center;
            gap: 8px; /* Reduced gap between sections */
        }
        
        .footer-brand {
            margin-bottom: 0;
        }
        
        .footer-copyright {
            line-height: 1.3; /* Tighter line height */
            margin: 0;
        }
        
        .footer-nav {
            justify-content: center;
            gap: 15px; /* Consistent spacing between links */
            flex-wrap: nowrap; /* Keep all links on one line */
        }
        
        .footer-nav-link {
            font-size: 13px; /* Slightly smaller on mobile */
            white-space: nowrap; /* Prevent wrapping */
        }
        
        /* Hide "О проекте" on mobile to fit privacy and terms on one line */
        .footer-nav-link:first-child {
            display: none;
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