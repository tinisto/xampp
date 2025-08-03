<?php
// Test page to debug header issues
session_start();

// Set test session data to simulate logged in user
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'testuser';
$_SESSION['email'] = 'test@example.com';
$_SESSION['logged_in'] = true;
$_SESSION['role'] = 'admin'; // Changed to admin to test Dashboard link
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header Debug Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        body {
            padding-top: 100px;
        }
        .debug-info {
            margin: 20px;
            padding: 20px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }
        .debug-info pre {
            margin: 0;
        }
    </style>
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
    
    <div class="container">
        <div class="debug-info">
            <h2>Header Debug Information</h2>
            <hr>
            <h3>Session Data:</h3>
            <pre><?php print_r($_SESSION); ?></pre>
            <hr>
            <h3>Test Instructions:</h3>
            <ol>
                <li>Check if you see the user avatar (green circle) in the header</li>
                <li>Try clicking on the user avatar</li>
                <li>Open browser console (F12) and check for JavaScript errors</li>
                <li>Check if "Dropdown clicked" appears in console when clicking avatar</li>
            </ol>
            <hr>
            <h3>Expected Behavior:</h3>
            <ul>
                <li>User avatar should be visible on desktop</li>
                <li>Clicking avatar should open dropdown menu</li>
                <li>Dropdown should contain: Profile, (Dashboard if admin), Logout</li>
            </ul>
            <hr>
            <button class="btn btn-primary" onclick="testDropdown()">Test Dropdown Manually</button>
        </div>
    </div>

    <script>
    function testDropdown() {
        const userAvatar = document.querySelector('.user-avatar.dropdown-toggle');
        if (userAvatar) {
            console.log('User avatar found:', userAvatar);
            userAvatar.click();
        } else {
            console.log('User avatar NOT found');
            console.log('All elements with class user-avatar:', document.querySelectorAll('.user-avatar'));
        }
    }
    
    // Debug on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Page loaded');
        console.log('User avatar element:', document.querySelector('.user-avatar.dropdown-toggle'));
        console.log('Dropdown menu element:', document.querySelector('.user-menu .dropdown-menu'));
        console.log('All dropdowns:', document.querySelectorAll('.dropdown'));
    });
    </script>
</body>
</html>