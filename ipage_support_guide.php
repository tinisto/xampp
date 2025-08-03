<!DOCTYPE html>
<html>
<head>
    <title>iPage Support Guide - PHP Restart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            line-height: 1.6;
        }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .info { color: blue; }
        .code { 
            background: #f5f5f5; 
            padding: 15px; 
            border: 1px solid #ddd; 
            font-family: monospace; 
            margin: 10px 0;
            border-radius: 5px;
        }
        .support-box {
            background: #e3f2fd;
            border: 2px solid #2196f3;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .chat-template {
            background: white;
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            font-style: italic;
        }
        h2 {
            color: #2196f3;
            border-bottom: 2px solid #2196f3;
            padding-bottom: 10px;
        }
        .button {
            display: inline-block;
            background: #2196f3;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        .button:hover {
            background: #1976d2;
        }
    </style>
</head>
<body>
    <h1>📞 iPage Support Guide - Restart PHP</h1>
    
    <div class="support-box">
        <h2>🎯 Quick Summary</h2>
        <p><strong>Problem:</strong> Your website's PHP is caching old database settings</p>
        <p><strong>Solution:</strong> iPage support needs to restart PHP for your account</p>
        <p><strong>Time Required:</strong> 2-5 minutes with support</p>
    </div>
    
    <h2>📱 Contact iPage Support</h2>
    
    <h3>Option 1: Live Chat (Fastest)</h3>
    <ol>
        <li>Go to <a href="https://www.ipage.com/help/contact" target="_blank">iPage Support</a></li>
        <li>Click "Chat Now"</li>
        <li>Use this message:</li>
    </ol>
    
    <div class="chat-template">
        Hi, I need help with my hosting account for 11klassniki.ru. I've updated my database configuration in the .env file, but PHP is caching the old settings. Could you please restart PHP-FPM or clear the PHP opcache for my account? This will make my site use the new database configuration. Thank you!
    </div>
    
    <h3>Option 2: Phone Support</h3>
    <p><strong>iPage Support Phone:</strong> 1-877-472-4399 (US)</p>
    <p>Tell them:</p>
    <div class="chat-template">
        "I need PHP restarted for my hosting account. My domain is 11klassniki.ru. I've changed my database configuration but PHP is caching the old values."
    </div>
    
    <h2>🔍 If Support Asks Questions</h2>
    
    <h3>Q: "What exactly do you need?"</h3>
    <p class="chat-template">
        I need PHP-FPM restarted or the PHP opcache cleared. My .env file has been updated with new database credentials, but PHP is still using cached values from the old configuration.
    </p>
    
    <h3>Q: "What error are you getting?"</h3>
    <p class="chat-template">
        My application is connecting to the wrong database. The .env file shows DB_NAME=11klassniki_claude but PHP is still using the old database 11klassniki_ru.
    </p>
    
    <h3>Q: "Have you tried clearing your browser cache?"</h3>
    <p class="chat-template">
        This isn't a browser cache issue. It's server-side PHP caching. The PHP process needs to be restarted to read the new environment variables.
    </p>
    
    <h2>✅ After Support Restarts PHP</h2>
    <p>Once support confirms they've restarted PHP:</p>
    <ol>
        <li>Wait 1-2 minutes</li>
        <li>Visit: <a href="https://11klassniki.ru/test_new_structure.php" target="_blank">Test New Structure</a></li>
        <li>You should see "Current database: 11klassniki_claude"</li>
        <li>All your pages will start working with the new database!</li>
    </ol>
    
    <h2>🚀 Alternative Solutions</h2>
    <p>If support can't help immediately:</p>
    <ul>
        <li><strong>Wait Option:</strong> PHP usually auto-restarts within 2-6 hours on shared hosting</li>
        <li><strong>Control Panel:</strong> Check if your iPage control panel has a "Restart Application" option</li>
        <li><strong>PHP Version:</strong> Sometimes changing PHP version and changing back forces a restart</li>
    </ul>
    
    <div class="support-box" style="background: #f8f9fa; border-color: #6c757d;">
        <h3>📋 Technical Details for Support</h3>
        <p>If support needs technical details:</p>
        <ul>
            <li>Old database: 11klassniki_ru</li>
            <li>New database: 11klassniki_claude</li>
            <li>Using: PHP with Dotenv for environment variables</li>
            <li>Issue: PHP opcache holding old DB_NAME constant</li>
            <li>Need: Clear opcache or restart PHP-FPM</li>
        </ul>
    </div>
    
    <p style="text-align: center; margin-top: 40px;">
        <a href="/" class="button">← Back to Home</a>
        <a href="/test_new_structure.php" class="button">Test Structure</a>
    </p>
</body>
</html>