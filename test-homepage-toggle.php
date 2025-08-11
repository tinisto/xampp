<?php
// Test what header is being used on homepage
session_start();
$page_title = 'Test Homepage Toggle - 11klassniki.ru';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Homepage Toggle Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .test-box { 
            background: #f0f0f0; 
            padding: 20px; 
            margin: 20px 0; 
            border-radius: 8px; 
        }
        .toggle-visible {
            display: block !important;
            background: yellow;
            border: 3px solid red;
            padding: 10px;
            font-size: 24px;
            color: #333;
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>Homepage Toggle Debug</h1>
    
    <div class="test-box">
        <h3>1. Let's check which template is being used:</h3>
        <?php 
        echo "Current page: " . basename($_SERVER['PHP_SELF']) . "<br>";
        echo "Request URI: " . $_SERVER['REQUEST_URI'] . "<br>";
        ?>
    </div>
    
    <div class="test-box">
        <h3>2. Include the header directly:</h3>
        <?php
        echo "Including header.php...<br>";
        if (file_exists(__DIR__ . '/includes/header.php')) {
            echo "✅ Header file exists<br>";
            // Let's see what gets rendered
            ob_start();
            include __DIR__ . '/includes/header.php';
            $header_content = ob_get_clean();
            
            // Check if toggle is in the content
            if (strpos($header_content, 'mobile-toggle') !== false) {
                echo "✅ Toggle found in header content<br>";
            } else {
                echo "❌ Toggle NOT found in header content<br>";
            }
            
            // Display the header
            echo $header_content;
        } else {
            echo "❌ Header file not found<br>";
        }
        ?>
    </div>
    
    <div class="test-box">
        <h3>3. Manual toggle button (should always be visible):</h3>
        <div class="toggle-visible" onclick="alert('Toggle clicked!')">
            <i class="fas fa-bars"></i> TOGGLE BUTTON TEST
        </div>
        <p>This toggle should be visible with yellow background and red border.</p>
    </div>
    
    <div class="test-box">
        <h3>4. Screen width test:</h3>
        <p>Current window width: <span id="width">Unknown</span>px</p>
        <p>Toggle should be visible when width < 768px</p>
        <button onclick="document.body.style.width='400px'">Simulate Mobile (400px)</button>
        <button onclick="document.body.style.width='100%'">Reset Width</button>
    </div>

    <script>
        // Update width display
        function updateWidth() {
            document.getElementById('width').textContent = window.innerWidth;
        }
        window.addEventListener('resize', updateWidth);
        updateWidth();
    </script>
    
    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html>