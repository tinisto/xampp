<!DOCTYPE html>
<html lang="ru" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Theme Test Inline</title>
    <style>
        [data-bs-theme="light"] {
            background: white;
            color: black;
        }
        [data-bs-theme="dark"] {
            background: #212529;
            color: white;
        }
    </style>
</head>
<body>
    <h1>Direct Theme Test</h1>
    <button onclick="toggleTheme()">Toggle Theme</button>
    <p id="status"></p>
    
    <script>
    function toggleTheme() {
        var current = document.documentElement.getAttribute('data-bs-theme');
        var newTheme = current === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-bs-theme', newTheme);
        document.getElementById('status').innerHTML = 'Theme changed to: ' + newTheme;
    }
    
    // Show function exists
    document.getElementById('status').innerHTML = 'toggleTheme exists: ' + (typeof toggleTheme !== 'undefined');
    </script>
</body>
</html>