<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Logo Implementation - 11klassniki.ru</title>
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
        
        .showcase {
            background: white;
            padding: 60px;
            margin-bottom: 40px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        /* Final Logo Style */
        .logo-final {
            font-size: 42px;
            position: relative;
            display: inline-block;
            font-family: Arial, sans-serif;
            font-weight: 400;
            color: #333;
        }
        
        .logo-final .eleven {
            font-weight: 700;
            color: #0039A6;
        }
        
        .logo-final .ru {
            color: #D52B1E;
            font-weight: 500;
        }
        
        .logo-final svg {
            position: absolute;
            bottom: -8px;
            left: -5px;
            width: 65px;
            height: 20px;
        }
        
        /* Header version */
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
            font-size: 28px;
            position: relative;
            display: inline-block;
            font-family: Arial, sans-serif;
            font-weight: 400;
            color: #333;
        }
        
        .header-logo .eleven {
            font-weight: 700;
            color: #0039A6;
        }
        
        .header-logo .ru {
            color: #D52B1E;
            font-weight: 500;
        }
        
        .header-logo svg {
            position: absolute;
            bottom: -5px;
            left: -3px;
            width: 45px;
            height: 15px;
        }
        
        /* Implementation code */
        .code-block {
            background: #2d2d2d;
            color: #f8f8f8;
            padding: 20px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            margin-top: 10px;
            overflow-x: auto;
            text-align: left;
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
        
        .usage-example {
            background: #f8f8f8;
            padding: 30px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="text-align: center;">Final Logo: Clean Swoosh</h1>
        
        <!-- Main showcase -->
        <div class="showcase">
            <h2>Primary Logo</h2>
            <div class="logo-final">
                <span class="eleven">11</span>klassniki<span class="ru">.ru</span>
                <svg>
                    <path d="M 5 15 Q 35 8 60 12" 
                          stroke="#0039A6" stroke-width="2.5" fill="none" 
                          stroke-linecap="round" opacity="0.8"/>
                </svg>
            </div>
            <p style="color: #666; margin-top: 40px;">Clean, modern, with subtle movement</p>
        </div>
        
        <!-- Header implementation -->
        <div class="showcase">
            <h2>Header Implementation</h2>
            <div class="header-demo">
                <div class="header-logo">
                    <span class="eleven">11</span>klassniki<span class="ru">.ru</span>
                    <svg>
                        <path d="M 3 12 Q 25 7 42 10" 
                              stroke="#0039A6" stroke-width="2" fill="none" 
                              stroke-linecap="round" opacity="0.8"/>
                    </svg>
                </div>
                <nav style="display: flex; gap: 30px;">
                    <a href="#" style="color: #666; text-decoration: none;">Главная</a>
                    <a href="#" style="color: #666; text-decoration: none;">Школы</a>
                    <a href="#" style="color: #666; text-decoration: none;">События</a>
                    <a href="#" style="color: #666; text-decoration: none;">Войти</a>
                </nav>
            </div>
        </div>
        
        <!-- Implementation -->
        <div class="showcase">
            <h2>Implementation Code</h2>
            
            <div class="usage-example">
                <h3>HTML Structure</h3>
                <div class="code-block">
&lt;div class="logo"&gt;
    &lt;span class="eleven"&gt;11&lt;/span&gt;klassniki&lt;span class="ru"&gt;.ru&lt;/span&gt;
    &lt;svg&gt;
        &lt;path d="M 5 15 Q 35 8 60 12" 
              stroke="#0039A6" stroke-width="2.5" fill="none" 
              stroke-linecap="round" opacity="0.8"/&gt;
    &lt;/svg&gt;
&lt;/div&gt;</div>
            </div>
            
            <div class="usage-example">
                <h3>CSS Styles</h3>
                <div class="code-block">
.logo {
    font-size: 42px;
    position: relative;
    display: inline-block;
    font-family: Arial, sans-serif;
    font-weight: 400;
    color: #333;
}

.logo .eleven {
    font-weight: 700;
    color: #0039A6; /* Russian Blue */
}

.logo .ru {
    color: #D52B1E; /* Russian Red */
    font-weight: 500;
}

.logo svg {
    position: absolute;
    bottom: -8px;
    left: -5px;
    width: 65px;
    height: 20px;
}</div>
            </div>
            
            <div class="usage-example">
                <h3>Color Values</h3>
                <div style="display: flex; gap: 20px; justify-content: center; margin-top: 20px;">
                    <div style="text-align: center;">
                        <div style="width: 80px; height: 80px; background: #0039A6; border-radius: 8px;"></div>
                        <p style="margin-top: 10px;">Russian Blue<br><code>#0039A6</code></p>
                    </div>
                    <div style="text-align: center;">
                        <div style="width: 80px; height: 80px; background: #D52B1E; border-radius: 8px;"></div>
                        <p style="margin-top: 10px;">Russian Red<br><code>#D52B1E</code></p>
                    </div>
                    <div style="text-align: center;">
                        <div style="width: 80px; height: 80px; background: #333; border-radius: 8px;"></div>
                        <p style="margin-top: 10px;">Text Color<br><code>#333333</code></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- SVG Download -->
        <div class="showcase">
            <h2>Download Options</h2>
            <p>SVG file available: <code>logo-final-swoosh.svg</code></p>
            <img src="logo-final-swoosh.svg" alt="Final Logo" style="margin: 20px 0;">
        </div>
    </div>
</body>
</html>