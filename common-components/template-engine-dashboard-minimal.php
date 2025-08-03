<?php
/**
 * Minimal Dashboard Template Engine
 * Clean dashboard layout without header/footer for admin interfaces
 */

function renderDashboardTemplate($pageTitle, $mainContent, $config = []) {
    // Default configuration
    $defaults = [
        'darkMode' => true,
        'customCSS' => '',
        'customJS' => '',
        'showNavigation' => true
    ];
    
    $config = array_merge($defaults, $config);
    
    // Include cookie consent system
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/components/cookie-consent.php';
    
    // Get theme preference
    $currentTheme = $_COOKIE['preferred-theme'] ?? 'light';
    ?>
<!DOCTYPE html>
<html lang="ru" data-theme="<?= htmlspecialchars($currentTheme) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Dashboard - 11классники</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Theme Variables -->
    <style>
        :root {
            --primary-color: #28a745;
            --primary-hover: #218838;
            --bg-color: #f8f9fa;
            --card-bg: #ffffff;
            --text-primary: #333;
            --text-secondary: #666;
            --border-color: #dee2e6;
        }
        
        [data-theme="dark"] {
            --bg-color: #1a1a1a;
            --card-bg: #2d2d2d;
            --text-primary: #ffffff;
            --text-secondary: #cccccc;
            --border-color: #404040;
        }
        
        body {
            background-color: var(--bg-color);
            color: var(--text-primary);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            transition: all 0.3s ease;
        }
        
        .dashboard-container {
            min-height: 100vh;
            padding: 20px;
        }
        
        .dashboard-header {
            background: var(--card-bg);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .dashboard-title {
            margin: 0;
            color: var(--primary-color);
            font-size: 24px;
            font-weight: 600;
        }
        
        .dashboard-nav {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .nav-btn {
            padding: 8px 16px;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .nav-btn:hover {
            background: var(--primary-hover);
            color: white;
            transform: translateY(-1px);
        }
        
        .theme-toggle {
            background: none;
            border: 2px solid var(--border-color);
            color: var(--text-primary);
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .theme-toggle:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .dashboard-content {
            background: var(--card-bg);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        /* Table styling for dark mode */
        [data-theme="dark"] .table {
            --bs-table-bg: var(--card-bg);
            --bs-table-color: var(--text-primary);
        }
        
        [data-theme="dark"] .table-dark {
            --bs-table-bg: #404040;
            --bs-table-color: var(--text-primary);
        }
        
        /* Alert styling */
        .alert {
            border-radius: 8px;
        }
        
        <?= $config['customCSS'] ?>
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php if ($config['showNavigation']): ?>
        <div class="dashboard-header">
            <h1 class="dashboard-title"><?= htmlspecialchars($pageTitle) ?></h1>
            <div class="dashboard-nav">
                <a href="/dashboard" class="nav-btn">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="/dashboard/users" class="nav-btn">
                    <i class="fas fa-users"></i> Пользователи
                </a>
                <button class="theme-toggle" onclick="toggleTheme()">
                    <i class="fas fa-moon" id="theme-icon"></i>
                </button>
                <a href="/logout" class="nav-btn" style="background: #dc3545;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="dashboard-content">
            <?php 
            if (is_file($_SERVER['DOCUMENT_ROOT'] . '/' . $mainContent)) {
                include $_SERVER['DOCUMENT_ROOT'] . '/' . $mainContent;
            } else {
                include $mainContent;
            }
            ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Theme Toggle Script -->
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('preferred-theme', newTheme);
            
            // Set secure cookie
            const isSecure = window.location.protocol === 'https:';
            document.cookie = `preferred-theme=${newTheme};path=/;max-age=31536000;${isSecure ? 'secure;' : ''}samesite=lax`;
            
            // Update icon
            const icon = document.getElementById('theme-icon');
            if (icon) {
                icon.className = newTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
            }
        }
        
        // Initialize theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('preferred-theme') || 'light';
            const html = document.documentElement;
            
            html.setAttribute('data-theme', savedTheme);
            
            // Set secure cookie on page load
            const isSecure = window.location.protocol === 'https:';
            document.cookie = `preferred-theme=${savedTheme};path=/;max-age=31536000;${isSecure ? 'secure;' : ''}samesite=lax`;
            
            // Update icon
            const icon = document.getElementById('theme-icon');
            if (icon) {
                icon.className = savedTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
            }
        });
    </script>
    
    <?= $config['customJS'] ?>
    
    <!-- Cookie Consent Banner -->
    <?= renderCookieConsent() ?>
</body>
</html>
<?php
}
?>