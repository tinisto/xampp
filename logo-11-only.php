<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>11 - Refined Logo Concepts</title>
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
        
        /* Style 1: Academic Excellence */
        .logo-1 {
            font-size: 120px;
            font-weight: 300;
            color: #0039A6;
            margin: 20px;
            display: inline-block;
            font-family: 'Georgia', serif;
            position: relative;
        }
        
        .logo-1::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #0039A6 0%, #FFFFFF 50%, #D52B1E 100%);
        }
        
        /* Style 2: Graduation Cap */
        .logo-2 {
            position: relative;
            display: inline-block;
            width: 150px;
            height: 150px;
        }
        
        .logo-2 .cap {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 60px solid transparent;
            border-right: 60px solid transparent;
            border-bottom: 40px solid #0039A6;
        }
        
        .logo-2 .number {
            position: absolute;
            top: 60px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 60px;
            font-weight: 700;
            color: #333;
            font-family: 'Arial', sans-serif;
        }
        
        /* Style 3: Book Symbol */
        .logo-3 {
            position: relative;
            display: inline-block;
            width: 120px;
            height: 100px;
        }
        
        .logo-3 .book {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 60px;
            background: #0039A6;
            border-radius: 0 0 40px 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logo-3 .book::before {
            content: '';
            position: absolute;
            top: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 90px;
            height: 4px;
            background: #D52B1E;
        }
        
        .logo-3 .pages {
            color: white;
            font-size: 36px;
            font-weight: 700;
            font-family: 'Arial', sans-serif;
        }
        
        /* Style 4: Overlapping */
        .logo-4 {
            position: relative;
            display: inline-block;
            font-size: 100px;
            font-family: 'Georgia', serif;
        }
        
        .logo-4 .first {
            color: #0039A6;
            position: absolute;
            left: 0;
            font-weight: 700;
        }
        
        .logo-4 .second {
            color: #D52B1E;
            position: absolute;
            left: 40px;
            font-weight: 700;
            opacity: 0.8;
        }
        
        /* Style 5: Modern Square */
        .logo-5 {
            width: 120px;
            height: 120px;
            background: #333;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 60px;
            font-weight: 200;
            font-family: 'Arial', sans-serif;
            border-radius: 20px;
            position: relative;
            overflow: hidden;
        }
        
        .logo-5::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 40%;
            background: rgba(0, 57, 166, 0.2);
        }
        
        .logo-5::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 40%;
            background: rgba(213, 43, 30, 0.2);
        }
        
        /* Style 6: Outline */
        .logo-6 {
            font-size: 120px;
            font-weight: 900;
            color: transparent;
            -webkit-text-stroke: 3px #0039A6;
            text-stroke: 3px #0039A6;
            font-family: 'Arial Black', sans-serif;
            display: inline-block;
            position: relative;
        }
        
        .logo-6::after {
            content: '11';
            position: absolute;
            left: 4px;
            top: 4px;
            color: transparent;
            -webkit-text-stroke: 3px #D52B1E;
            text-stroke: 3px #D52B1E;
            opacity: 0.5;
        }
        
        /* Style 7: Minimal Lines */
        .logo-7 {
            display: inline-block;
            position: relative;
            width: 80px;
            height: 100px;
        }
        
        .logo-7::before,
        .logo-7::after {
            content: '';
            position: absolute;
            width: 8px;
            height: 100%;
            background: #0039A6;
            border-radius: 4px;
        }
        
        .logo-7::before {
            left: 20px;
        }
        
        .logo-7::after {
            right: 20px;
            background: #D52B1E;
        }
        
        /* Style 8: Bold Shadow */
        .logo-8 {
            font-size: 140px;
            font-weight: 900;
            color: #0039A6;
            font-family: 'Arial Black', sans-serif;
            display: inline-block;
            position: relative;
            text-shadow: 8px 8px 0px #D52B1E;
        }
        
        /* Style 9: Dot Pattern */
        .logo-9 {
            font-size: 100px;
            font-weight: 700;
            background-image: radial-gradient(circle, #0039A6 30%, transparent 30%);
            background-size: 10px 10px;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: inline-block;
            font-family: 'Arial Black', sans-serif;
        }
        
        /* Style 10: Growth Symbol */
        .logo-10 {
            position: relative;
            display: inline-block;
            width: 120px;
            height: 120px;
        }
        
        .logo-10 .growth {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 60px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            gap: 8px;
        }
        
        .logo-10 .bar {
            width: 30px;
            background: #0039A6;
            border-radius: 4px 4px 0 0;
        }
        
        .logo-10 .bar:first-child {
            height: 30px;
        }
        
        .logo-10 .bar:last-child {
            height: 60px;
            background: #D52B1E;
        }
        
        .logo-10 .number {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 40px;
            font-weight: 700;
            color: #333;
        }
        
        h2 {
            font-size: 16px;
            color: #666;
            margin-top: 40px;
            margin-bottom: 20px;
        }
        
        .description {
            font-size: 14px;
            color: #999;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Refined "11" Logo Concepts</h1>
    <p style="text-align: center; color: #666; margin-bottom: 40px;">Focus on educational symbolism and Russian identity</p>
    
    <div class="logo-container">
        <h2>Concept 1: Academic Excellence</h2>
        <div class="logo-1">11</div>
        <p class="description">Classic serif font symbolizing tradition and knowledge</p>
    </div>
    
    <div class="logo-container">
        <h2>Concept 2: Graduation Cap</h2>
        <div class="logo-2">
            <div class="cap"></div>
            <div class="number">11</div>
        </div>
        <p class="description">Symbolizing 11th grade achievement and graduation</p>
    </div>
    
    <div class="logo-container">
        <h2>Concept 3: Open Book</h2>
        <div class="logo-3">
            <div class="book">
                <div class="pages">11</div>
            </div>
        </div>
        <p class="description">Knowledge and learning represented as an open book</p>
    </div>
    
    <div class="logo-container">
        <h2>Concept 4: Overlapping</h2>
        <div class="logo-4">
            <span class="first">1</span>
            <span class="second">1</span>
        </div>
        <p class="description">Two colors creating depth</p>
    </div>
    
    <div class="logo-container">
        <h2>Concept 5: Modern Square</h2>
        <div class="logo-5">11</div>
        <p class="description">Dark background with subtle flag hints</p>
    </div>
    
    <div class="logo-container">
        <h2>Concept 6: Outline Style</h2>
        <div class="logo-6">11</div>
        <p class="description">Double outline effect</p>
    </div>
    
    <div class="logo-container">
        <h2>Concept 7: Abstract Lines</h2>
        <div class="logo-7"></div>
        <p class="description">Minimalist line representation</p>
    </div>
    
    <div class="logo-container">
        <h2>Concept 8: Bold Shadow</h2>
        <div class="logo-8">11</div>
        <p class="description">Blue with red shadow</p>
    </div>
    
    <div class="logo-container">
        <h2>Concept 9: Dot Pattern</h2>
        <div class="logo-9">11</div>
        <p class="description">Modern dot texture</p>
    </div>
    
    <div class="logo-container">
        <h2>Concept 10: Growth & Progress</h2>
        <div class="logo-10">
            <div class="growth">
                <div class="bar"></div>
                <div class="bar"></div>
            </div>
            <div class="number">11</div>
        </div>
        <p class="description">Ascending bars symbolizing academic growth and achievement</p>
    </div>
</body>
</html>