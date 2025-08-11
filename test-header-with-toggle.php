<?php
session_start();
$page_title = 'Test Header with Toggle - 11klassniki.ru';

// Force the header to include the toggle
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
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
        
        /* Mobile Menu Toggle - ALWAYS VISIBLE FOR TESTING */
        .mobile-toggle {
            display: block !important;
            font-size: 24px;
            color: #555;
            cursor: pointer;
            transition: color 0.3s;
            border: 2px solid red;
            padding: 10px;
            background: yellow;
        }
        
        .mobile-toggle:hover {
            color: #0039A6;
        }
        
        /* Mobile Navigation */
        .mobile-nav {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 0 0 12px 12px;
            padding: 20px;
            z-index: 999;
        }
        
        .mobile-nav.active {
            display: block;
        }
        
        .mobile-nav a {
            display: block;
            padding: 15px 0;
            color: #555;
            text-decoration: none;
            font-weight: 500;
            border-bottom: 1px solid #eee;
            transition: color 0.3s;
        }
        
        .mobile-nav a:last-child {
            border-bottom: none;
        }
        
        .mobile-nav a:hover {
            color: #0039A6;
        }
        
        .mobile-nav .btn-primary {
            background: #0039A6;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            text-align: center;
            margin-top: 10px;
            border-bottom: none;
        }
        
        /* Main Content */
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            min-height: calc(100vh - 200px);
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
            
            <div class="mobile-toggle" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
                TOGGLE
            </div>
        </div>
        
        <!-- Mobile Navigation Menu -->
        <div class="mobile-nav" id="mobileNav">
            <a href="/schools_modern.php">Школы</a>
            <a href="/events.php">События</a>
            <a href="/spo_modern.php">СПО</a>
            <a href="/vpo_modern.php">ВПО</a>
            <a href="/login_modern.php" class="btn-primary">Войти</a>
        </div>
    </header>
    
    <main class="main-content">
        <div style="padding: 40px; text-align: center;">
            <h1>Header with Toggle Test</h1>
            <p>The toggle button should be clearly visible with yellow background and red border.</p>
            <p>This proves the toggle functionality is working.</p>
            
            <div style="margin-top: 40px; padding: 30px; background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h2>✅ Toggle Button Features</h2>
                <ul style="list-style: none; padding: 0;">
                    <li>🍔 Hamburger icon (☰)</li>
                    <li>🔄 Changes to X when opened</li>
                    <li>📋 Shows dropdown menu</li>
                    <li>🖱️ Closes when clicking outside</li>
                    <li>📱 Responsive design</li>
                </ul>
            </div>
        </div>
    </main>
    
    <!-- JavaScript for mobile toggle -->
    <script>
        function toggleMobileMenu() {
            const mobileNav = document.getElementById('mobileNav');
            const toggle = document.querySelector('.mobile-toggle i');
            
            console.log('Toggle clicked!');
            
            if (mobileNav.classList.contains('active')) {
                mobileNav.classList.remove('active');
                toggle.className = 'fas fa-bars';
                console.log('Menu closed');
            } else {
                mobileNav.classList.add('active');
                toggle.className = 'fas fa-times';
                console.log('Menu opened');
            }
        }
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileNav = document.getElementById('mobileNav');
            const toggle = document.querySelector('.mobile-toggle');
            
            if (!toggle.contains(event.target) && !mobileNav.contains(event.target)) {
                mobileNav.classList.remove('active');
                document.querySelector('.mobile-toggle i').className = 'fas fa-bars';
            }
        });
    </script>
</body>
</html>