    <footer class="footer" style="background: #2d3748; color: white; padding: 12px 20px; margin-top: 40px; box-shadow: 0 -1px 4px rgba(0,0,0,0.05);">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;">
            <!-- Footer Links -->
            <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                <a href="/contact.php" style="color: rgba(255,255,255,0.7); text-decoration: none; transition: color 0.3s; font-weight: 500;">Контакты</a>
                <a href="/privacy_modern.php" style="color: rgba(255,255,255,0.7); text-decoration: none; transition: color 0.3s; font-weight: 500;">Политика конфиденциальности</a>
                <a href="/terms.php" style="color: rgba(255,255,255,0.7); text-decoration: none; transition: color 0.3s; font-weight: 500;">Условия использования</a>
                <a href="/about.php" style="color: rgba(255,255,255,0.7); text-decoration: none; transition: color 0.3s; font-weight: 500;">О проекте</a>
            </div>
            
            <!-- Footer Info -->
            <div style="opacity: 0.6; font-size: 13px;">
                Одиннадцать шагов к большому будущему • © <?php echo date('Y'); ?> 11klassniki.ru
            </div>
            
            <!-- Mobile Footer Toggle -->
            <div class="mobile-footer-toggle" style="display: none; font-size: 18px; color: rgba(255,255,255,0.7); cursor: pointer;">
                <i class="fas fa-chevron-up"></i>
            </div>
        </div>
    </footer>
    
    <style>
        .mobile-footer-toggle {
            display: none;
            font-size: 18px;
            color: rgba(255,255,255,0.7);
            cursor: pointer;
        }
        
        @media (max-width: 768px) {
            .mobile-footer-toggle {
                display: block;
            }
            
            footer > div > div:first-child {
                display: none;
            }
            
            footer > div {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }
    </style>
</body>
</html>