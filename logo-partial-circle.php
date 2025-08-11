<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partial Circle Logo Designs - 11klassniki.ru</title>
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
        
        /* Style 1: Top Arc */
        .logo-arc1 {
            position: relative;
            display: inline-block;
            padding: 25px 10px 10px 10px;
        }
        
        .logo-arc1 svg {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 120%;
            height: 50px;
        }
        
        .logo-arc1 .text {
            font-size: 36px;
            font-weight: 400;
            color: #333;
            position: relative;
            z-index: 1;
        }
        
        .logo-arc1 .eleven {
            font-weight: 700;
            color: #0039A6;
        }
        
        .logo-arc1 .ru {
            color: #D52B1E;
            font-weight: 500;
        }
        
        /* Style 2: Side Brackets */
        .logo-arc2 {
            position: relative;
            display: inline-block;
            padding: 10px 25px;
        }
        
        .logo-arc2 svg {
            position: absolute;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            width: 100%;
            height: 100%;
        }
        
        .logo-arc2 .text {
            font-size: 36px;
            font-weight: 400;
            color: #333;
            position: relative;
            z-index: 1;
        }
        
        .logo-arc2 .eleven {
            font-weight: 700;
            color: #0039A6;
        }
        
        .logo-arc2 .ru {
            color: #D52B1E;
            font-weight: 500;
        }
        
        /* Style 3: Three-Quarter Circle */
        .logo-arc3 {
            position: relative;
            display: inline-block;
            padding: 30px;
        }
        
        .logo-arc3 svg {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 120%;
            height: 120%;
        }
        
        .logo-arc3 .text {
            font-size: 36px;
            font-weight: 400;
            color: #333;
            position: relative;
            z-index: 1;
        }
        
        .logo-arc3 .eleven {
            font-weight: 700;
            color: #0039A6;
        }
        
        .logo-arc3 .ru {
            color: #D52B1E;
            font-weight: 500;
        }
        
        /* Style 4: Bottom Arc */
        .logo-arc4 {
            position: relative;
            display: inline-block;
            padding: 10px 10px 25px 10px;
        }
        
        .logo-arc4 svg {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 110%;
            height: 40px;
        }
        
        .logo-arc4 .text {
            font-size: 36px;
            font-weight: 400;
            color: #333;
            position: relative;
            z-index: 1;
        }
        
        .logo-arc4 .eleven {
            font-weight: 700;
            color: #0039A6;
        }
        
        .logo-arc4 .ru {
            color: #D52B1E;
            font-weight: 500;
        }
        
        /* Style 5: Corner Accent */
        .logo-arc5 {
            position: relative;
            display: inline-block;
            padding: 20px 20px 20px 30px;
        }
        
        .logo-arc5 svg {
            position: absolute;
            top: 0;
            left: 0;
            width: 80px;
            height: 80px;
        }
        
        .logo-arc5 .text {
            font-size: 36px;
            font-weight: 400;
            color: #333;
            position: relative;
            z-index: 1;
        }
        
        .logo-arc5 .eleven {
            font-weight: 700;
            color: #0039A6;
        }
        
        .logo-arc5 .ru {
            color: #D52B1E;
            font-weight: 500;
        }
        
        /* Style 6: Embrace */
        .logo-arc6 {
            position: relative;
            display: inline-block;
            padding: 25px 30px;
        }
        
        .logo-arc6 svg {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            height: 100%;
        }
        
        .logo-arc6 .text {
            font-size: 36px;
            font-weight: 400;
            color: #333;
            position: relative;
            z-index: 1;
        }
        
        .logo-arc6 .eleven {
            font-weight: 700;
            color: #0039A6;
        }
        
        .logo-arc6 .ru {
            color: #D52B1E;
            font-weight: 500;
        }
        
        /* Style 7: Dynamic Swoosh */
        .logo-arc7 {
            position: relative;
            display: inline-block;
            padding: 20px;
        }
        
        .logo-arc7 svg {
            position: absolute;
            top: 0;
            left: -10px;
            width: 110%;
            height: 100%;
        }
        
        .logo-arc7 .text {
            font-size: 36px;
            font-weight: 400;
            color: #333;
            position: relative;
            z-index: 1;
        }
        
        .logo-arc7 .eleven {
            font-weight: 700;
            color: #0039A6;
        }
        
        .logo-arc7 .ru {
            color: #D52B1E;
            font-weight: 500;
        }
        
        /* Style 8: Shield Shape */
        .logo-arc8 {
            position: relative;
            display: inline-block;
            padding: 30px;
        }
        
        .logo-arc8 svg {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 110%;
            height: 110%;
        }
        
        .logo-arc8 .text {
            font-size: 36px;
            font-weight: 400;
            color: #333;
            position: relative;
            z-index: 1;
        }
        
        .logo-arc8 .eleven {
            font-weight: 700;
            color: #0039A6;
        }
        
        .logo-arc8 .ru {
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
    <h1 style="text-align: center;">Partial Circle Logo Designs</h1>
    
    <!-- Style 1 -->
    <div class="logo-container">
        <h2>Style 1: Top Arc Protection</h2>
        <div class="logo-arc1">
            <svg>
                <path d="M 10 45 Q 50% 5 90% 45" 
                      stroke="#0039A6" stroke-width="3" fill="none" 
                      stroke-linecap="round"/>
            </svg>
            <div class="text">
                <span class="eleven">11</span>klassniki<span class="ru">.ru</span>
            </div>
        </div>
    </div>
    
    <!-- Style 2 -->
    <div class="logo-container">
        <h2>Style 2: Side Embrace</h2>
        <div class="logo-arc2">
            <svg>
                <path d="M 15 10 Q 5 50% 15 90%" 
                      stroke="#0039A6" stroke-width="2.5" fill="none" 
                      stroke-linecap="round"/>
                <path d="M 85% 10 Q 95% 50% 85% 90%" 
                      stroke="#D52B1E" stroke-width="2.5" fill="none" 
                      stroke-linecap="round"/>
            </svg>
            <div class="text">
                <span class="eleven">11</span>klassniki<span class="ru">.ru</span>
            </div>
        </div>
    </div>
    
    <!-- Style 3 -->
    <div class="logo-container">
        <h2>Style 3: Three-Quarter Circle</h2>
        <div class="logo-arc3">
            <svg>
                <circle cx="50%" cy="50%" r="45%" 
                        stroke="#0039A6" stroke-width="2.5" fill="none"
                        stroke-dasharray="270 90"
                        transform="rotate(-45 50% 50%)"/>
            </svg>
            <div class="text">
                <span class="eleven">11</span>klassniki<span class="ru">.ru</span>
            </div>
        </div>
    </div>
    
    <!-- Style 4 -->
    <div class="logo-container">
        <h2>Style 4: Bottom Support Arc</h2>
        <div class="logo-arc4">
            <svg>
                <path d="M 10 10 Q 50% 35 90% 10" 
                      stroke="#D52B1E" stroke-width="3" fill="none" 
                      stroke-linecap="round"/>
            </svg>
            <div class="text">
                <span class="eleven">11</span>klassniki<span class="ru">.ru</span>
            </div>
        </div>
    </div>
    
    <!-- Style 5 -->
    <div class="logo-container">
        <h2>Style 5: Corner Accent</h2>
        <div class="logo-arc5">
            <svg>
                <path d="M 10 60 Q 10 10 60 10" 
                      stroke="#0039A6" stroke-width="3" fill="none" 
                      stroke-linecap="round"/>
            </svg>
            <div class="text">
                <span class="eleven">11</span>klassniki<span class="ru">.ru</span>
            </div>
        </div>
    </div>
    
    <!-- Style 6 -->
    <div class="logo-container">
        <h2>Style 6: Gentle Embrace</h2>
        <div class="logo-arc6">
            <svg>
                <path d="M 5 30 Q 5 10 25 10 L 75% 10 Q 95% 10 95% 30 L 95% 70% Q 95% 90% 75% 90% L 25 90% Q 5 90% 5 70% Z" 
                      stroke="#0039A6" stroke-width="2" fill="none" 
                      stroke-linecap="round"
                      stroke-dasharray="150 500"/>
            </svg>
            <div class="text">
                <span class="eleven">11</span>klassniki<span class="ru">.ru</span>
            </div>
        </div>
    </div>
    
    <!-- Style 7 -->
    <div class="logo-container">
        <h2>Style 7: Dynamic Swoosh Circle</h2>
        <div class="logo-arc7">
            <svg>
                <path d="M 20 50% Q 10 20 40 15 Q 70 10 90 30 Q 100 50% 90 70" 
                      stroke="#0039A6" stroke-width="3" fill="none" 
                      stroke-linecap="round"
                      opacity="0.8"/>
            </svg>
            <div class="text">
                <span class="eleven">11</span>klassniki<span class="ru">.ru</span>
            </div>
        </div>
    </div>
    
    <!-- Style 8 -->
    <div class="logo-container">
        <h2>Style 8: Shield Protection</h2>
        <div class="logo-arc8">
            <svg>
                <path d="M 50% 5 Q 85% 15% 85% 50% Q 85% 85% 50% 95% Q 15% 85% 15% 50% Q 15% 15% 50% 5" 
                      stroke="#0039A6" stroke-width="2.5" fill="none" 
                      stroke-linecap="round"
                      stroke-dasharray="120 80"
                      transform="rotate(45 50% 50%)"/>
            </svg>
            <div class="text">
                <span class="eleven">11</span>klassniki<span class="ru">.ru</span>
            </div>
        </div>
    </div>
    
    <!-- Recommended -->
    <div class="logo-container" style="background: #f0f8ff; border: 2px solid #0039A6;">
        <h2 style="color: #0039A6;">✓ Recommended: Elegant Partial Circle</h2>
        <div style="position: relative; display: inline-block; padding: 30px 35px;">
            <svg style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 100%; height: 100%;">
                <circle cx="50%" cy="50%" r="42%" 
                        stroke="#0039A6" stroke-width="2" fill="none"
                        stroke-dasharray="220 140"
                        transform="rotate(-30 50% 50%)"
                        opacity="0.6"/>
                <circle cx="50%" cy="50%" r="42%" 
                        stroke="#D52B1E" stroke-width="2" fill="none"
                        stroke-dasharray="60 300"
                        transform="rotate(150 50% 50%)"
                        opacity="0.5"/>
            </svg>
            <div style="font-size: 40px; position: relative; z-index: 1;">
                <span style="font-weight: 700; color: #0039A6;">11</span><span style="color: #333;">klassniki</span><span style="color: #D52B1E; font-weight: 500;">.ru</span>
            </div>
        </div>
        <p style="color: #666; margin-top: 30px; font-size: 14px;">
            Partial circles in Russian flag colors create dynamic protection around the text
        </p>
    </div>
</body>
</html>