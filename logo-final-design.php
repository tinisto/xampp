<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Logo Design - 11klassniki.ru</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            padding: 40px;
            background: #f5f5f5;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .logo-showcase {
            background: white;
            padding: 60px;
            margin-bottom: 40px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        /* Primary Logo Design */
        .logo-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 20px 0;
        }
        
        .logo-icon {
            width: 50px;
            height: 50px;
            background: #0039A6;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: 600;
            font-family: 'Arial', sans-serif;
            box-shadow: 0 3px 10px rgba(0, 57, 166, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .logo-icon::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 35%;
            background: #D52B1E;
            opacity: 0.2;
        }
        
        .logo-text {
            font-size: 32px;
            font-weight: 400;
            color: #333;
            font-family: 'Arial', sans-serif;
            letter-spacing: -0.5px;
        }
        
        .logo-text .domain {
            color: #D52B1E;
            font-weight: 500;
        }
        
        /* Header Implementation */
        .header-demo {
            background: white;
            padding: 20px 40px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .header-logo {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .header-logo .icon {
            width: 40px;
            height: 40px;
            background: #0039A6;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            font-weight: 600;
        }
        
        .header-logo .text {
            font-size: 24px;
            color: #333;
        }
        
        .header-logo .domain {
            color: #D52B1E;
            font-weight: 500;
        }
        
        /* Favicon Preview */
        .favicon-preview {
            display: inline-block;
            margin: 20px;
        }
        
        .favicon-16 {
            width: 16px;
            height: 16px;
            background: #0039A6;
            border-radius: 3px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 10px;
            font-weight: bold;
        }
        
        .favicon-32 {
            width: 32px;
            height: 32px;
            background: #0039A6;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            font-weight: bold;
        }
        
        /* Color Guide */
        .color-palette {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 40px;
        }
        
        .color-swatch {
            text-align: center;
        }
        
        .color-box {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        /* Usage Examples */
        .usage-example {
            background: #f8f8f8;
            padding: 30px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .code-block {
            background: #2d2d2d;
            color: #f8f8f8;
            padding: 20px;
            border-radius: 6px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            margin-top: 10px;
        }
        
        h2 {
            color: #333;
            margin-top: 40px;
            margin-bottom: 20px;
        }
        
        h3 {
            color: #666;
            margin-top: 30px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="text-align: center;">Final Logo Design - 11klassniki.ru</h1>
        
        <!-- Primary Logo Showcase -->
        <div class="logo-showcase">
            <h2>Primary Logo - Integrated Design</h2>
            <div class="logo-primary">
                <div class="logo-icon">11</div>
                <div class="logo-text">klassniki<span class="domain">.ru</span></div>
            </div>
            <p style="color: #666; margin-top: 20px;">Unified design with rounded square icon</p>
            
            <hr style="margin: 40px auto; width: 200px; border: none; border-top: 1px solid #e0e0e0;">
            
            <h2>Alternative: Text-Based Logo</h2>
            <div style="font-size: 36px; font-weight: 400; color: #333; font-family: Arial, sans-serif; letter-spacing: -1px;">
                <span style="font-weight: 700; color: #0039A6;">11</span>klassniki<span style="color: #D52B1E; font-weight: 500;">.ru</span>
            </div>
            <p style="color: #666; margin-top: 20px;">Seamless text integration</p>
        </div>
        
        <!-- Header Implementation -->
        <div class="logo-showcase">
            <h2>Header Implementation</h2>
            <div class="header-demo">
                <div class="header-logo">
                    <div class="icon">11</div>
                    <div class="text">klassniki<span class="domain">.ru</span></div>
                </div>
                <nav style="display: flex; gap: 30px;">
                    <a href="#" style="color: #666; text-decoration: none;">Главная</a>
                    <a href="#" style="color: #666; text-decoration: none;">Школы</a>
                    <a href="#" style="color: #666; text-decoration: none;">События</a>
                    <a href="#" style="color: #666; text-decoration: none;">Войти</a>
                </nav>
            </div>
        </div>
        
        <!-- Favicon Versions -->
        <div class="logo-showcase">
            <h2>Favicon Versions</h2>
            <div style="display: flex; gap: 40px; justify-content: center; align-items: center;">
                <div>
                    <h3>16x16</h3>
                    <div class="favicon-16">11</div>
                </div>
                <div>
                    <h3>32x32</h3>
                    <div class="favicon-32">11</div>
                </div>
                <div>
                    <h3>Logo Icon Only</h3>
                    <div class="logo-icon">11</div>
                </div>
            </div>
        </div>
        
        <!-- Color Palette -->
        <div class="logo-showcase">
            <h2>Official Color Palette</h2>
            <div class="color-palette">
                <div class="color-swatch">
                    <div class="color-box" style="background: #0039A6;">
                        #0039A6
                    </div>
                    <p>Primary Blue<br><small>Russian Blue</small></p>
                </div>
                <div class="color-swatch">
                    <div class="color-box" style="background: #D52B1E;">
                        #D52B1E
                    </div>
                    <p>Accent Red<br><small>Russian Red</small></p>
                </div>
                <div class="color-swatch">
                    <div class="color-box" style="background: #333333;">
                        #333333
                    </div>
                    <p>Text Color<br><small>Dark Gray</small></p>
                </div>
                <div class="color-swatch">
                    <div class="color-box" style="background: #FFFFFF; color: #333; border: 1px solid #e0e0e0;">
                        #FFFFFF
                    </div>
                    <p>Background<br><small>White</small></p>
                </div>
            </div>
        </div>
        
        <!-- Implementation Code -->
        <div class="logo-showcase">
            <h2>Implementation Code</h2>
            
            <div class="usage-example">
                <h3>HTML Structure</h3>
                <div class="code-block">
&lt;!-- Logo for header --&gt;
&lt;div class="site-logo"&gt;
    &lt;div class="logo-icon"&gt;11&lt;/div&gt;
    &lt;div class="logo-text"&gt;klassniki&lt;span class="logo-domain"&gt;.ru&lt;/span&gt;&lt;/div&gt;
&lt;/div&gt;</div>
            </div>
            
            <div class="usage-example">
                <h3>CSS Styles</h3>
                <div class="code-block">
.site-logo {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.logo-icon {
    width: 40px;
    height: 40px;
    background: #0039A6;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    font-weight: 600;
    font-family: Arial, sans-serif;
    box-shadow: 0 2px 6px rgba(0, 57, 166, 0.2);
}

.logo-text {
    font-size: 24px;
    font-weight: 400;
    color: #333;
    font-family: Arial, sans-serif;
}

.logo-domain {
    color: #D52B1E;
    font-weight: 500;
}</div>
            </div>
            
            <div class="usage-example">
                <h3>Favicon HTML</h3>
                <div class="code-block">
&lt;link rel="icon" type="image/svg+xml" href="/favicon.svg"&gt;
&lt;link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png"&gt;
&lt;link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png"&gt;</div>
            </div>
        </div>
        
        <!-- SVG Version -->
        <div class="logo-showcase">
            <h2>SVG Logo Code</h2>
            <div class="usage-example">
                <div class="code-block">
&lt;svg width="200" height="60" viewBox="0 0 200 60" xmlns="http://www.w3.org/2000/svg"&gt;
    &lt;!-- Icon --&gt;
    &lt;rect x="10" y="10" width="40" height="40" rx="10" fill="#0039A6"/&gt;
    &lt;text x="30" y="35" text-anchor="middle" fill="white" 
          font-family="Arial" font-size="20" font-weight="600"&gt;11&lt;/text&gt;
    
    &lt;!-- Text --&gt;
    &lt;text x="58" y="35" font-family="Arial" font-size="24" fill="#333"&gt;
        klassniki&lt;tspan fill="#D52B1E" font-weight="500"&gt;.ru&lt;/tspan&gt;
    &lt;/text&gt;
&lt;/svg&gt;</div>
            </div>
        </div>
    </div>
</body>
</html>