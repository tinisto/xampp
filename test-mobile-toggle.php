<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Toggle Test</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; }
        
        .header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: relative;
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
            font-weight: 400;
            color: #333;
            text-decoration: none;
        }
        
        .logo .eleven { font-weight: 700; color: #0039A6; }
        .logo .ru { color: #D52B1E; font-weight: 500; }
        
        .nav {
            display: flex;
            gap: 30px;
        }
        
        .nav a {
            color: #555;
            text-decoration: none;
            font-weight: 500;
        }
        
        .btn-primary {
            background: #0039A6;
            color: white;
            padding: 8px 20px;
            border-radius: 6px;
            text-decoration: none;
        }
        
        /* Mobile Toggle - ALWAYS visible for testing */
        .mobile-toggle {
            display: block;
            font-size: 24px;
            color: #555;
            cursor: pointer;
            border: 2px solid red; /* Red border to make it visible */
            padding: 10px;
            background: yellow; /* Yellow background to make it obvious */
        }
        
        .mobile-nav {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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
            border-bottom: 1px solid #eee;
        }
        
        /* Desktop styles */
        @media (min-width: 769px) {
            .mobile-toggle {
                /* Still show on desktop for testing */
                opacity: 0.5;
            }
        }
        
        /* Mobile styles */
        @media (max-width: 768px) {
            .nav {
                display: none;
            }
            
            .mobile-toggle {
                opacity: 1;
                border: 2px solid green; /* Green on mobile */
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="/" class="logo">
                <span class="eleven">11</span>klassniki<span class="ru">.ru</span>
            </a>
            
            <nav class="nav">
                <a href="#">Школы</a>
                <a href="#">События</a>
                <a href="#">СПО</a>
                <a href="#">ВПО</a>
            </nav>
            
            <a href="#" class="btn-primary">Войти</a>
            
            <div class="mobile-toggle" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
                TOGGLE
            </div>
        </div>
        
        <div class="mobile-nav" id="mobileNav">
            <a href="#">Школы</a>
            <a href="#">События</a>
            <a href="#">СПО</a>
            <a href="#">ВПО</a>
            <a href="#" class="btn-primary">Войти</a>
        </div>
    </header>
    
    <main style="padding: 40px;">
        <h1>Mobile Toggle Test</h1>
        <p>The toggle button should be visible with a colored border.</p>
        <p>Current screen width: <span id="width"></span>px</p>
        <p>Toggle button should be:</p>
        <ul>
            <li>Yellow background with red border on desktop</li>
            <li>Yellow background with green border on mobile (< 768px)</li>
        </ul>
        
        <button onclick="testResize()">Simulate Mobile (400px)</button>
        <button onclick="testDesktop()">Simulate Desktop (1200px)</button>
    </main>
    
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
        
        function updateWidth() {
            document.getElementById('width').textContent = window.innerWidth;
        }
        
        function testResize() {
            window.resizeTo(400, 600);
            updateWidth();
        }
        
        function testDesktop() {
            window.resizeTo(1200, 800);
            updateWidth();
        }
        
        window.addEventListener('resize', updateWidth);
        updateWidth();
    </script>
</body>
</html>