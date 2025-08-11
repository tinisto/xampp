<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Header - 11klassniki.ru</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <meta name="theme-color" content="#0039A6">
    
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f5f7fa;
        }
        
        /* Header Styles */
        .header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
        }
        
        /* Logo Styles */
        .logo {
            font-size: 28px;
            position: relative;
            display: inline-block;
            font-family: Arial, sans-serif;
            font-weight: 400;
            color: #333;
            text-decoration: none;
        }
        
        .logo:hover {
            opacity: 0.9;
        }
        
        .logo .eleven {
            font-weight: 700;
            color: #0039A6;
        }
        
        .logo .ru {
            color: #D52B1E;
            font-weight: 500;
        }
        
        .logo svg {
            position: absolute;
            bottom: -5px;
            left: -3px;
            width: 45px;
            height: 15px;
        }
        
        /* Navigation */
        .nav {
            display: flex;
            align-items: center;
            gap: 30px;
        }
        
        .nav a {
            color: #555;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            position: relative;
        }
        
        .nav a:hover,
        .nav a.active {
            color: #0039A6;
        }
        
        .nav a.active::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 3px;
            background: #0039A6;
            border-radius: 3px 3px 0 0;
        }
        
        /* User Menu */
        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-menu a {
            color: #555;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .user-menu a:hover {
            color: #0039A6;
        }
        
        .btn-primary {
            background: #0039A6;
            color: white;
            padding: 8px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: #002D87;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 57, 166, 0.2);
        }
        
        /* Main Content */
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            min-height: calc(100vh - 200px);
        }
        
        /* Mobile toggles */
        .mobile-toggle {
            display: none;
            font-size: 24px;
            color: #555;
            cursor: pointer;
        }
        
        .mobile-footer-toggle {
            display: none;
            font-size: 18px;
            color: rgba(255,255,255,0.7);
            cursor: pointer;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .nav {
                display: none;
            }
            
            .user-menu {
                display: none;
            }
            
            .mobile-toggle {
                display: block;
            }
            
            .mobile-footer-toggle {
                display: block;
            }
            
            .logo {
                font-size: 24px;
            }
            
            .logo svg {
                width: 38px;
                height: 12px;
            }
            
            footer > div {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="/" class="logo">
                <span class="eleven">11</span>klassniki<span class="ru">.ru</span>
                <svg>
                    <path d="M 3 12 Q 25 7 42 10" 
                          stroke="#0039A6" stroke-width="2" fill="none" 
                          stroke-linecap="round" opacity="0.8"/>
                </svg>
            </a>
            
            <nav class="nav">
                <a href="/schools_modern.php">Школы</a>
                <a href="/events.php">События</a>
                <a href="/spo_modern.php">СПО</a>
                <a href="/vpo_modern.php">ВПО</a>
            </nav>
            
            <div class="user-menu">
                <a href="/login_modern.php" class="btn-primary">Войти</a>
            </div>
            
            <div class="mobile-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>
    
    <main class="main-content">
        <div style="padding: 40px; text-align: center;">
            <h1>Header Test Page</h1>
            <p>This page demonstrates the new header with logo and favicon implementation.</p>
            
            <div style="margin-top: 40px; padding: 40px; background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h2>Logo Implementation Complete ✓</h2>
                <p>The header now includes:</p>
                <ul style="list-style: none; padding: 0; margin-top: 20px; text-align: left; max-width: 400px; margin-left: auto; margin-right: auto;">
                    <li style="padding: 10px 0;">✓ Clean swoosh logo design</li>
                    <li style="padding: 10px 0;">✓ Russian flag colors (blue #0039A6, red #D52B1E)</li>
                    <li style="padding: 10px 0;">✓ Responsive navigation</li>
                    <li style="padding: 10px 0;">✓ Favicon implementation</li>
                </ul>
            </div>
            
            <div style="margin-top: 30px;">
                <h3>Favicon Preview</h3>
                <p>Check your browser tab to see the new "11" favicon in action!</p>
                <div style="margin-top: 20px;">
                    <div style="display: inline-block; padding: 10px; background: #f0f0f0; border-radius: 8px;">
                        <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                            <rect width="32" height="32" rx="6" fill="#0039A6"/>
                            <text x="16" y="23" text-anchor="middle" fill="white" 
                                  font-family="Arial, sans-serif" font-size="18" font-weight="700">11</text>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <footer style="background: #2d3748; color: white; padding: 15px 20px; margin-top: 60px; box-shadow: 0 -2px 10px rgba(0,0,0,0.05);">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;">
            <!-- Footer Links -->
            <div style="display: flex; gap: 30px; flex-wrap: wrap;">
                <a href="/contact.php" style="color: rgba(255,255,255,0.7); text-decoration: none; transition: color 0.3s; font-weight: 500;">Контакты</a>
                <a href="/privacy_modern.php" style="color: rgba(255,255,255,0.7); text-decoration: none; transition: color 0.3s; font-weight: 500;">Политика конфиденциальности</a>
                <a href="/terms.php" style="color: rgba(255,255,255,0.7); text-decoration: none; transition: color 0.3s; font-weight: 500;">Условия использования</a>
                <a href="/about.php" style="color: rgba(255,255,255,0.7); text-decoration: none; transition: color 0.3s; font-weight: 500;">О проекте</a>
            </div>
            
            <!-- Footer Info -->
            <div style="opacity: 0.6; font-size: 14px;">
                Одиннадцать шагов к большому будущему • © 2025 11klassniki.ru
            </div>
            
            <!-- Mobile Footer Toggle -->
            <div class="mobile-footer-toggle" style="display: none; font-size: 18px; color: rgba(255,255,255,0.7); cursor: pointer;">
                <i class="fas fa-chevron-up"></i>
            </div>
        </div>
    </footer>
</body>
</html>