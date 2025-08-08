<?php
// Test page for dropdown functionality
$pageTitle = 'Test Dropdown';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Categories Dropdown</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            padding-top: 100px;
            background: #f5f5f5;
        }
        
        .test-info {
            background: white;
            padding: 20px;
            margin: 20px auto;
            max-width: 800px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .test-info h2 {
            margin-top: 20px;
            color: #333;
        }
        
        .console-output {
            background: #1e1e1e;
            color: #fff;
            padding: 15px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 12px;
            margin-top: 10px;
            max-height: 300px;
            overflow-y: auto;
        }
        
        .console-output .log {
            color: #fff;
        }
        
        .console-output .error {
            color: #f48771;
        }
        
        .console-output .success {
            color: #4ec9b0;
        }
    </style>
</head>
<body>
    <!-- Include the header -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_header.php'; ?>
    
    <div class="test-info">
        <h1>Dropdown Test Page</h1>
        <p>This page tests the Categories dropdown functionality. Click on "Категории" in the header above.</p>
        
        <h2>Console Output:</h2>
        <div class="console-output" id="consoleOutput">
            <div class="log">Waiting for page to load...</div>
        </div>
        
        <h2>Instructions:</h2>
        <ol>
            <li>Click on "Категории" in the header</li>
            <li>Check if the dropdown menu appears</li>
            <li>Watch the console output below for debugging information</li>
            <li>Try clicking outside to close the dropdown</li>
        </ol>
        
        <h2>Expected Behavior:</h2>
        <ul>
            <li>Dropdown menu should appear when clicking "Категории"</li>
            <li>Menu should contain category links</li>
            <li>Menu should close when clicking outside</li>
            <li>Bootstrap events should fire (check console)</li>
        </ul>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Capture console logs
        const consoleOutput = document.getElementById('consoleOutput');
        const originalLog = console.log;
        const originalError = console.error;
        
        function addToConsole(message, type = 'log') {
            const div = document.createElement('div');
            div.className = type;
            div.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
            consoleOutput.appendChild(div);
            consoleOutput.scrollTop = consoleOutput.scrollHeight;
            
            // Also log to real console
            if (type === 'error') {
                originalError(message);
            } else {
                originalLog(message);
            }
        }
        
        console.log = function(...args) {
            addToConsole(args.join(' '), 'log');
        };
        
        console.error = function(...args) {
            addToConsole(args.join(' '), 'error');
        };
        
        // Check Bootstrap on load
        window.addEventListener('load', function() {
            if (typeof bootstrap !== 'undefined') {
                addToConsole('Bootstrap loaded successfully! Version: ' + bootstrap.Dropdown.VERSION, 'success');
                
                // Check for dropdown elements
                const dropdowns = document.querySelectorAll('[data-bs-toggle="dropdown"]');
                addToConsole('Found ' + dropdowns.length + ' dropdown(s) on the page', 'log');
                
                const categoriesDropdown = document.getElementById('categoriesDropdown');
                if (categoriesDropdown) {
                    addToConsole('Categories dropdown found!', 'success');
                    const menu = categoriesDropdown.querySelector('.dropdown-menu');
                    const items = menu ? menu.querySelectorAll('.dropdown-item').length : 0;
                    addToConsole('Dropdown menu has ' + items + ' items', 'log');
                } else {
                    addToConsole('Categories dropdown not found!', 'error');
                }
            } else {
                addToConsole('Bootstrap not loaded!', 'error');
            }
        });
    </script>
</body>
</html>