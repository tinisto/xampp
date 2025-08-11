<?php
// Logo implementation examples for 11klassniki.ru
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logo Concepts - 11klassniki.ru</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            padding: 40px;
            background: #f5f5f5;
        }
        
        .logo-container {
            background: white;
            padding: 40px;
            margin-bottom: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        /* Logo Style 1: Gradient Circle */
        .logo-style1 {
            display: inline-flex;
            align-items: center;
            gap: 15px;
            font-size: 28px;
            font-weight: 300;
            color: #333;
        }
        
        .logo-style1 .logo-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #00D4FF 0%, #0066CC 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 400;
            font-size: 24px;
            box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
        }
        
        .logo-style1 .ru {
            color: #0099FF;
            font-weight: 500;
        }
        
        /* Logo Style 2: Book Pages */
        .logo-style2 {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-size: 24px;
        }
        
        .logo-style2 .logo-icon {
            display: flex;
            gap: 3px;
        }
        
        .logo-style2 .page {
            width: 8px;
            height: 40px;
            background: linear-gradient(180deg, #0066CC 0%, #003D7A 100%);
            border-radius: 2px;
            position: relative;
        }
        
        .logo-style2 .page::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            border-radius: 2px 2px 0 0;
        }
        
        /* Logo Style 3: Modern Minimal */
        .logo-style3 {
            font-size: 32px;
            font-weight: 200;
            color: #333;
            letter-spacing: -1px;
        }
        
        .logo-style3 .eleven {
            font-weight: 700;
            background: linear-gradient(135deg, #0099FF 0%, #0066CC 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Logo Style 4: Academic */
        .logo-style4 {
            display: inline-flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo-style4 .logo-icon {
            position: relative;
            width: 60px;
            height: 50px;
        }
        
        .logo-style4 .cap {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 30px solid transparent;
            border-right: 30px solid transparent;
            border-bottom: 25px solid #0066CC;
        }
        
        .logo-style4 .cap::after {
            content: '11';
            position: absolute;
            top: 8px;
            left: -10px;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }
        
        .logo-style4 .text {
            font-size: 26px;
            color: #333;
        }
        
        /* Slogan styles */
        .slogan {
            margin-top: 10px;
            font-size: 14px;
            color: #666;
            font-weight: 300;
        }
        
        .slogan-alt {
            font-size: 12px;
            color: #999;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        
        /* Russian Flag Styles */
        .logo-russian1 {
            display: inline-flex;
            align-items: center;
            gap: 15px;
            font-size: 28px;
            color: #333;
        }
        
        .logo-russian1 .flag-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            background: white;
        }
        
        .logo-russian1 .flag-circle::before,
        .logo-russian1 .flag-circle::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            height: 33.33%;
        }
        
        .logo-russian1 .flag-circle::before {
            top: 33.33%;
            background: #0039A6;
        }
        
        .logo-russian1 .flag-circle::after {
            bottom: 0;
            background: #D52B1E;
        }
        
        .logo-russian1 .flag-number {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 32px;
            font-weight: bold;
            color: #333;
            text-shadow: 2px 2px 4px rgba(255,255,255,0.9);
            z-index: 10;
        }
        
        .logo-russian1 .ru {
            color: #D52B1E;
            font-weight: 500;
        }
        
        /* Russian Style 2 */
        .logo-russian2 {
            display: inline-flex;
            align-items: center;
            gap: 12px;
        }
        
        .logo-russian2 .flag-pages {
            display: flex;
            gap: 4px;
        }
        
        .logo-russian2 .page {
            width: 12px;
            height: 50px;
            position: relative;
            border-radius: 2px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .logo-russian2 .page1 {
            background: linear-gradient(to bottom, white 33.33%, #0039A6 33.33%, #0039A6 66.66%, #D52B1E 66.66%);
        }
        
        .logo-russian2 .page2 {
            background: linear-gradient(to bottom, #D52B1E 0%, #0039A6 50%, white 100%);
        }
        
        .logo-russian2 .text {
            font-size: 30px;
            font-weight: 400;
            color: #333;
        }
        
        .logo-russian2 .text .ru {
            color: #D52B1E;
            font-weight: 500;
        }
        
        /* Patriotic slogan */
        .slogan-patriotic {
            margin-top: 10px;
            font-size: 13px;
        }
        
        .slogan-patriotic .blue {
            color: #0039A6;
        }
        
        .slogan-patriotic .red {
            color: #D52B1E;
        }
    </style>
</head>
<body>
    <h1>Logo Concepts for 11klassniki.ru</h1>
    
    <!-- Concept 1: Modern Circle -->
    <div class="logo-container">
        <h2>Concept 1: Modern Circle</h2>
        <div class="logo-style1">
            <div class="logo-icon">11</div>
            <span>klassniki<span class="ru">.ru</span></span>
        </div>
        <div class="slogan">Твой путь к успеху начинается здесь</div>
    </div>
    
    <!-- Concept 2: Book Pages -->
    <div class="logo-container">
        <h2>Concept 2: Educational Books</h2>
        <div class="logo-style2">
            <div class="logo-icon">
                <div class="page"></div>
                <div class="page"></div>
            </div>
            <span>klassniki.ru</span>
        </div>
        <div class="slogan">Образование. Вдохновение. Будущее.</div>
    </div>
    
    <!-- Concept 3: Minimal -->
    <div class="logo-container">
        <h2>Concept 3: Minimal Modern</h2>
        <div class="logo-style3">
            <span class="eleven">11</span>klassniki.ru
        </div>
        <div class="slogan-alt">Учись • Развивайся • Достигай</div>
    </div>
    
    <!-- Concept 4: Academic -->
    <div class="logo-container">
        <h2>Concept 4: Academic Excellence</h2>
        <div class="logo-style4">
            <div class="logo-icon">
                <div class="cap"></div>
            </div>
            <span class="text">klassniki.ru</span>
        </div>
        <div class="slogan">Вместе к вершинам знаний</div>
    </div>
    
    <!-- Russian Flag Concepts -->
    <div class="logo-container">
        <h2>🇷🇺 Russian Flag Inspired Logos</h2>
        
        <!-- Russian Concept 1 -->
        <h3>Concept 5: Tricolor Circle</h3>
        <div class="logo-russian1">
            <div class="flag-circle">
                <div class="flag-number">11</div>
            </div>
            <span>klassniki<span class="ru">.ru</span></span>
        </div>
        <div class="slogan-patriotic">
            <span class="blue">Российское образование</span> • 
            <span>Мировые стандарты</span>
        </div>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
        
        <!-- Russian Concept 2 -->
        <h3>Concept 6: Flag Pages</h3>
        <div class="logo-russian2">
            <div class="flag-pages">
                <div class="page page1"></div>
                <div class="page page2"></div>
            </div>
            <span class="text">klassniki<span class="ru">.ru</span></span>
        </div>
        <div class="slogan-patriotic">
            <span class="blue">Образование</span> • 
            <span>Вдохновение</span> • 
            <span class="red">Будущее</span>
        </div>
    </div>
    
    <!-- SVG Versions -->
    <div class="logo-container">
        <h2>SVG Logo Concepts</h2>
        <img src="logo-concept.svg" alt="Logo Concept 1" style="margin: 20px;">
        <br>
        <img src="logo-modern.svg" alt="Logo Concept 2" style="margin: 20px;">
        <br>
        <img src="logo-russian-flag.svg" alt="Russian Flag Logo 1" style="margin: 20px;">
        <br>
        <img src="logo-russian-modern.svg" alt="Russian Flag Logo 2" style="margin: 20px;">
    </div>
    
    <!-- Color Palette -->
    <div class="logo-container">
        <h2>Recommended Color Palette</h2>
        
        <h3>Russian Flag Colors</h3>
        <div style="display: flex; gap: 20px; margin-top: 20px;">
            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; background: #FFFFFF; border: 1px solid #ddd; border-radius: 8px;"></div>
                <p>White<br>#FFFFFF</p>
            </div>
            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; background: #0039A6; border-radius: 8px;"></div>
                <p>Russian Blue<br>#0039A6</p>
            </div>
            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; background: #D52B1E; border-radius: 8px;"></div>
                <p>Russian Red<br>#D52B1E</p>
            </div>
            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; background: #333333; border-radius: 8px;"></div>
                <p>Text Dark<br>#333333</p>
            </div>
        </div>
        
        <h3 style="margin-top: 30px;">Alternative Educational Colors</h3>
        <div style="display: flex; gap: 20px; margin-top: 20px;">
            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; background: #0066CC; border-radius: 8px;"></div>
                <p>Primary Blue<br>#0066CC</p>
            </div>
            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; background: #0099FF; border-radius: 8px;"></div>
                <p>Light Blue<br>#0099FF</p>
            </div>
            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; background: #FFD700; border-radius: 8px;"></div>
                <p>Gold Accent<br>#FFD700</p>
            </div>
        </div>
    </div>
    
    <!-- Implementation Code -->
    <div class="logo-container">
        <h2>HTML Implementation (Recommended)</h2>
        <pre style="background: #f5f5f5; padding: 20px; border-radius: 8px; overflow-x: auto;">
&lt;!-- Simple HTML/CSS Logo --&gt;
&lt;div class="site-logo"&gt;
    &lt;span class="logo-number"&gt;11&lt;/span&gt;
    &lt;span class="logo-text"&gt;klassniki&lt;span class="logo-domain"&gt;.ru&lt;/span&gt;&lt;/span&gt;
&lt;/div&gt;
&lt;div class="site-slogan"&gt;Твой путь к успеху начинается здесь&lt;/div&gt;

&lt;style&gt;
.site-logo {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    font-size: 28px;
    font-weight: 300;
    color: #333;
}

.logo-number {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #00D4FF 0%, #0066CC 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 500;
    box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
}

.logo-domain {
    color: #0099FF;
    font-weight: 500;
}

.site-slogan {
    font-size: 14px;
    color: #666;
    margin-top: 5px;
}
&lt;/style&gt;
        </pre>
    </div>
</body>
</html>