<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test - 11klassniki.ru</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <meta name="theme-color" content="#0039A6">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f7fa; }
        
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
        
        .logo {
            font-size: 28px;
            position: relative;
            display: inline-block;
            font-family: Arial, sans-serif;
            font-weight: 400;
            color: #333;
            text-decoration: none;
        }
        
        .logo .eleven { font-weight: 700; color: #0039A6; }
        .logo .ru { color: #D52B1E; font-weight: 500; }
        .logo svg { position: absolute; bottom: -5px; left: -3px; width: 45px; height: 15px; }
        
        .nav { display: flex; gap: 30px; }
        .nav a { color: #555; text-decoration: none; font-weight: 500; transition: color 0.3s; }
        .nav a:hover { color: #0039A6; }
        
        .btn-primary {
            background: #0039A6;
            color: white;
            padding: 8px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
        }
        
        .mobile-toggle { display: none; font-size: 24px; color: #555; cursor: pointer; }
        
        @media (max-width: 768px) {
            .nav { display: none; }
            .mobile-toggle { display: block; }
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
            
            <a href="/login_modern.php" class="btn-primary">Войти</a>
            <div class="mobile-toggle"><i class="fas fa-bars"></i></div>
        </div>
    </header>
    
    <main style="padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
        <h1>New Logo Test</h1>
        <p>Check if you can see the new logo with the curved swoosh under "11".</p>
        
        <div style="margin: 40px 0; padding: 30px; background: white; border-radius: 12px;">
            <h2>✅ What you should see:</h2>
            <ul>
                <li>Logo: "11klassniki.ru" with blue "11" and red ".ru"</li>
                <li>Curved line under "11"</li>
                <li>Blue favicon in browser tab</li>
                <li>Navigation without "Главная"</li>
                <li>Single "Войти" button</li>
            </ul>
        </div>
    </main>
    
    <footer style="background: #2d3748; color: white; padding: 15px 20px; margin-top: 60px;">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; gap: 30px;">
                <a href="/contact.php" style="color: rgba(255,255,255,0.7); text-decoration: none;">Контакты</a>
                <a href="/privacy_modern.php" style="color: rgba(255,255,255,0.7); text-decoration: none;">Политика конфиденциальности</a>
                <a href="/terms.php" style="color: rgba(255,255,255,0.7); text-decoration: none;">Условия использования</a>
                <a href="/about.php" style="color: rgba(255,255,255,0.7); text-decoration: none;">О проекте</a>
            </div>
            <div style="opacity: 0.6; font-size: 14px;">
                Одиннадцать шагов к большому будущему • © 2025 11klassniki.ru
            </div>
        </div>
    </footer>
</body>
</html>