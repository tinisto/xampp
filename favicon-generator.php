<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favicon Generator - 11klassniki.ru</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            padding: 40px;
            background: #f5f5f5;
            text-align: center;
        }
        
        .favicon-container {
            background: white;
            padding: 40px;
            margin: 20px auto;
            max-width: 800px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .favicon-grid {
            display: flex;
            gap: 40px;
            justify-content: center;
            align-items: center;
            margin: 30px 0;
        }
        
        .favicon-preview {
            text-align: center;
        }
        
        .favicon-preview h3 {
            color: #666;
            margin-bottom: 10px;
        }
        
        /* Favicon styles */
        .favicon-16 {
            width: 16px;
            height: 16px;
            background: #0039A6;
            border-radius: 2px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 9px;
            font-weight: bold;
            font-family: Arial, sans-serif;
        }
        
        .favicon-32 {
            width: 32px;
            height: 32px;
            background: #0039A6;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            font-weight: bold;
            font-family: Arial, sans-serif;
        }
        
        .favicon-64 {
            width: 64px;
            height: 64px;
            background: #0039A6;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            font-weight: bold;
            font-family: Arial, sans-serif;
            box-shadow: 0 2px 8px rgba(0, 57, 166, 0.3);
        }
        
        .favicon-180 {
            width: 180px;
            height: 180px;
            background: #0039A6;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 90px;
            font-weight: bold;
            font-family: Arial, sans-serif;
            box-shadow: 0 4px 16px rgba(0, 57, 166, 0.3);
        }
        
        .code-block {
            background: #2d2d2d;
            color: #f8f8f8;
            padding: 20px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            margin-top: 20px;
            text-align: left;
            overflow-x: auto;
        }
        
        /* Browser preview */
        .browser-preview {
            background: #f0f0f0;
            border-radius: 8px;
            padding: 10px;
            margin: 20px 0;
        }
        
        .browser-tab {
            background: white;
            border-radius: 4px 4px 0 0;
            padding: 8px 16px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <h1>Favicon Implementation</h1>
    
    <div class="favicon-container">
        <h2>Favicon Sizes</h2>
        <div class="favicon-grid">
            <div class="favicon-preview">
                <h3>16x16</h3>
                <div class="favicon-16">11</div>
                <p>Browser tabs</p>
            </div>
            
            <div class="favicon-preview">
                <h3>32x32</h3>
                <div class="favicon-32">11</div>
                <p>Desktop</p>
            </div>
            
            <div class="favicon-preview">
                <h3>64x64</h3>
                <div class="favicon-64">11</div>
                <p>High DPI</p>
            </div>
            
            <div class="favicon-preview">
                <h3>180x180</h3>
                <div class="favicon-180">11</div>
                <p>Apple Touch</p>
            </div>
        </div>
        
        <h2>Browser Tab Preview</h2>
        <div class="browser-preview">
            <div class="browser-tab">
                <div class="favicon-16">11</div>
                <span>11klassniki.ru - Российское образование</span>
            </div>
        </div>
        
        <h2>HTML Implementation</h2>
        <div class="code-block">
&lt;!-- Place in &lt;head&gt; section --&gt;
&lt;link rel="icon" type="image/svg+xml" href="/favicon.svg"&gt;
&lt;link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png"&gt;
&lt;link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png"&gt;
&lt;link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png"&gt;
&lt;meta name="theme-color" content="#0039A6"&gt;</div>
        
        <h2>SVG Favicon Code</h2>
        <div class="code-block">
&lt;svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"&gt;
    &lt;rect width="32" height="32" rx="6" fill="#0039A6"/&gt;
    &lt;text x="16" y="23" text-anchor="middle" fill="white" 
          font-family="Arial, sans-serif" font-size="18" font-weight="700"&gt;11&lt;/text&gt;
&lt;/svg&gt;</div>
    </div>
</body>
</html>