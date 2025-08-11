<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Text Logo Variations - 11klassniki.ru</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            padding: 40px;
            background: #f5f5f5;
        }
        
        .logo-container {
            background: white;
            padding: 60px;
            margin-bottom: 40px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        /* Variation 1: Underline Curve */
        .logo-v1 {
            font-size: 36px;
            font-weight: 400;
            color: #333;
            font-family: Arial, sans-serif;
            position: relative;
            display: inline-block;
        }
        
        .logo-v1 .eleven {
            font-weight: 700;
            color: #0039A6;
        }
        
        .logo-v1 .ru {
            color: #D52B1E;
            font-weight: 500;
        }
        
        .logo-v1::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 45px;
            height: 3px;
            background: #0039A6;
            border-radius: 50% 50% 0 0 / 100% 100% 0 0;
            transform: scaleY(2);
        }
        
        /* Variation 2: Arc Above */
        .logo-v2 {
            font-size: 36px;
            font-weight: 400;
            color: #333;
            font-family: Arial, sans-serif;
            position: relative;
            display: inline-block;
            padding-top: 15px;
        }
        
        .logo-v2 .eleven {
            font-weight: 700;
            color: #0039A6;
        }
        
        .logo-v2 .ru {
            color: #D52B1E;
            font-weight: 500;
        }
        
        .logo-v2 svg {
            position: absolute;
            top: 0;
            left: -5px;
            width: 55px;
            height: 20px;
        }
        
        /* Variation 3: Circle Background on 11 */
        .logo-v3 {
            font-size: 36px;
            font-weight: 400;
            color: #333;
            font-family: Arial, sans-serif;
            display: inline-flex;
            align-items: center;
        }
        
        .logo-v3 .eleven {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 55px;
            height: 55px;
            background: #0039A6;
            color: white;
            border-radius: 50%;
            font-weight: 700;
            font-size: 28px;
            margin-right: 5px;
            box-shadow: 0 3px 10px rgba(0, 57, 166, 0.2);
        }
        
        .logo-v3 .ru {
            color: #D52B1E;
            font-weight: 500;
        }
        
        /* Variation 4: Wave Underline */
        .logo-v4 {
            font-size: 36px;
            font-weight: 400;
            color: #333;
            font-family: Arial, sans-serif;
            position: relative;
            display: inline-block;
        }
        
        .logo-v4 .eleven {
            font-weight: 700;
            color: #0039A6;
        }
        
        .logo-v4 .ru {
            color: #D52B1E;
            font-weight: 500;
        }
        
        .logo-v4 svg {
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100%;
            height: 12px;
        }
        
        /* Variation 5: Bracket Style */
        .logo-v5 {
            font-size: 36px;
            font-weight: 400;
            color: #333;
            font-family: Arial, sans-serif;
            position: relative;
            display: inline-block;
        }
        
        .logo-v5 .eleven {
            font-weight: 700;
            color: #0039A6;
            position: relative;
            padding: 0 15px;
        }
        
        .logo-v5 .eleven::before {
            content: '{';
            position: absolute;
            left: -5px;
            font-weight: 300;
            color: #D52B1E;
            font-size: 42px;
            top: -4px;
        }
        
        .logo-v5 .eleven::after {
            content: '}';
            position: absolute;
            right: -5px;
            font-weight: 300;
            color: #D52B1E;
            font-size: 42px;
            top: -4px;
        }
        
        .logo-v5 .ru {
            color: #D52B1E;
            font-weight: 500;
        }
        
        /* Variation 6: Swoosh */
        .logo-v6 {
            font-size: 36px;
            font-weight: 400;
            color: #333;
            font-family: Arial, sans-serif;
            position: relative;
            display: inline-block;
        }
        
        .logo-v6 .eleven {
            font-weight: 700;
            color: #0039A6;
            position: relative;
        }
        
        .logo-v6 .ru {
            color: #D52B1E;
            font-weight: 500;
        }
        
        .logo-v6 svg {
            position: absolute;
            bottom: -5px;
            left: -5px;
            width: 60px;
            height: 25px;
        }
        
        /* Variation 7: Gradient Background */
        .logo-v7 {
            font-size: 36px;
            font-weight: 400;
            color: #333;
            font-family: Arial, sans-serif;
            display: inline-block;
            position: relative;
        }
        
        .logo-v7 .eleven {
            font-weight: 700;
            background: linear-gradient(135deg, #0039A6 0%, #D52B1E 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            display: inline-block;
            padding-right: 5px;
        }
        
        .logo-v7 .eleven::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #0039A6 0%, #D52B1E 100%);
            border-radius: 1px;
        }
        
        .logo-v7 .ru {
            color: #D52B1E;
            font-weight: 500;
        }
        
        /* Variation 8: Ribbon */
        .logo-v8 {
            font-size: 36px;
            font-weight: 400;
            color: #333;
            font-family: Arial, sans-serif;
            position: relative;
            display: inline-block;
            padding: 0 20px;
        }
        
        .logo-v8::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 45px;
            background: #0039A6;
            transform: translateY(-50%) skewX(-5deg);
            opacity: 0.1;
            z-index: -1;
        }
        
        .logo-v8 .eleven {
            font-weight: 700;
            color: #0039A6;
            position: relative;
            z-index: 1;
        }
        
        .logo-v8 .ru {
            color: #D52B1E;
            font-weight: 500;
        }
        
        h2 {
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Text Logo Variations with Curves</h1>
    
    <!-- Variation 1 -->
    <div class="logo-container">
        <h2>Variation 1: Curved Underline</h2>
        <div class="logo-v1">
            <span class="eleven">11</span>klassniki<span class="ru">.ru</span>
        </div>
    </div>
    
    <!-- Variation 2 -->
    <div class="logo-container">
        <h2>Variation 2: Arc Above</h2>
        <div class="logo-v2">
            <svg>
                <path d="M 5 15 Q 27 0 50 15" stroke="#0039A6" stroke-width="3" fill="none"/>
            </svg>
            <span class="eleven">11</span>klassniki<span class="ru">.ru</span>
        </div>
    </div>
    
    <!-- Variation 3 -->
    <div class="logo-container">
        <h2>Variation 3: Circle Integration</h2>
        <div class="logo-v3">
            <span class="eleven">11</span>klassniki<span class="ru">.ru</span>
        </div>
    </div>
    
    <!-- Variation 4 -->
    <div class="logo-container">
        <h2>Variation 4: Wave Underline</h2>
        <div class="logo-v4">
            <span class="eleven">11</span>klassniki<span class="ru">.ru</span>
            <svg>
                <path d="M 0 6 Q 40 0 80 6 T 160 6 T 240 6" 
                      stroke="#0039A6" stroke-width="2" fill="none" opacity="0.6"/>
            </svg>
        </div>
    </div>
    
    <!-- Variation 5 -->
    <div class="logo-container">
        <h2>Variation 5: Bracket Accent</h2>
        <div class="logo-v5">
            <span class="eleven">11</span>klassniki<span class="ru">.ru</span>
        </div>
    </div>
    
    <!-- Variation 6 -->
    <div class="logo-container">
        <h2>Variation 6: Swoosh</h2>
        <div class="logo-v6">
            <span class="eleven">11</span>klassniki<span class="ru">.ru</span>
            <svg>
                <path d="M 0 20 Q 30 15 55 5" 
                      stroke="#D52B1E" stroke-width="3" fill="none" 
                      stroke-linecap="round"/>
            </svg>
        </div>
    </div>
    
    <!-- Variation 7 -->
    <div class="logo-container">
        <h2>Variation 7: Gradient with Underline</h2>
        <div class="logo-v7">
            <span class="eleven">11</span>klassniki<span class="ru">.ru</span>
        </div>
    </div>
    
    <!-- Variation 8 -->
    <div class="logo-container">
        <h2>Variation 8: Ribbon Background</h2>
        <div class="logo-v8">
            <span class="eleven">11</span>klassniki<span class="ru">.ru</span>
        </div>
    </div>
    
    <!-- Recommended -->
    <div class="logo-container" style="background: #f0f8ff; border: 2px solid #0039A6;">
        <h2 style="color: #0039A6;">✓ Recommended: Clean Swoosh</h2>
        <div style="font-size: 42px; position: relative; display: inline-block;">
            <span style="font-weight: 700; color: #0039A6;">11</span><span style="color: #333;">klassniki</span><span style="color: #D52B1E; font-weight: 500;">.ru</span>
            <svg style="position: absolute; bottom: -8px; left: -5px; width: 65px; height: 20px;">
                <path d="M 5 15 Q 35 8 60 12" 
                      stroke="#0039A6" stroke-width="2.5" fill="none" 
                      stroke-linecap="round" opacity="0.8"/>
            </svg>
        </div>
        <p style="color: #666; margin-top: 30px; font-size: 14px;">
            Simple curved accent under "11" adds movement without complexity
        </p>
    </div>
</body>
</html>