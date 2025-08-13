<?php
/**
 * Header with working Categories dropdown for local development
 */

// Load session manager if not already loaded
if (!class_exists('SessionManager')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/SessionManager.php';
}

// Ensure session is started
SessionManager::start();

// Direct database connection for local development
$hasDatabase = false;
$connection = null;

try {
    // Use local database connection
    $connection = new mysqli('127.0.0.1', 'root', 'root', '11klassniki_claude');
    
    if ($connection->connect_error) {
        // Try without password
        $connection = new mysqli('127.0.0.1', 'root', '', '11klassniki_claude');
    }
    
    if (!$connection->connect_error) {
        $connection->set_charset("utf8mb4");
        $hasDatabase = true;
    }
} catch (Exception $e) {
    $hasDatabase = false;
}
?>

<style>
    /* Unified Header Styles - Modern, Clean, Responsive */
    :root {
        --primary-color: #28a745;
        --surface: white;
        --text-color: #333;
        --shadow-sm: 0 2px 10px rgba(0,0,0,0.08);
        --border-color: #e9ecef;
    }

    [data-theme="dark"], [data-bs-theme="dark"] {
        --surface: #1f2937;
        --text-color: #f8f9fa;
        --border-color: #374151;
        --shadow-sm: 0 2px 10px rgba(0,0,0,0.3);
    }

    .header {
        background: var(--surface);
        box-shadow: var(--shadow-sm);
        position: sticky;
        top: 0;
        z-index: 1000;
        width: 100%;
        transition: all 0.3s ease;
    }
    
    .header-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 70px;
    }
    
    .header-brand {
        font-size: 22px;
        font-weight: 700;
        color: var(--primary-color) !important;
        text-decoration: none;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }
    
    .header-brand:hover {
        opacity: 0.8;
        transform: scale(1.02);
    }
    
    .header-nav {
        display: flex;
        align-items: center;
        gap: 30px;
        flex: 1;
        justify-content: center;
    }
    
    .nav-link {
        color: var(--text-color);
        text-decoration: none;
        font-weight: 500;
        padding: 8px 15px;
        border-radius: 8px;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .nav-link:hover {
        background: rgba(40, 167, 69, 0.1);
        color: var(--primary-color);
        transform: translateY(-1px);
    }
    
    /* Dropdown Styles */
    .dropdown {
        position: relative;
        display: inline-block;
    }
    
    .dropdown-toggle {
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    /* Remove Bootstrap's dropdown arrow */
    .dropdown-toggle::after {
        display: none !important;
    }
    
    .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        background: var(--surface);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        min-width: 200px;
        padding: 4px 0;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        z-index: 1001;
        pointer-events: none;
    }
    
    .dropdown.show .dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
        pointer-events: auto;
    }
    
    .dropdown-item {
        display: block;
        padding: 10px 20px;
        color: var(--text-color);
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .dropdown-item:hover {
        background: rgba(40, 167, 69, 0.1);
        color: var(--primary-color);
    }
    
    .dropdown-item.disabled {
        color: #999;
        cursor: not-allowed;
    }
    
    .dropdown-item.disabled:hover {
        background: transparent;
        color: #999;
    }
    
    /* Mobile styles */
    @media (max-width: 768px) {
        .header-nav {
            display: none;
        }
    }
</style>

<header class="header">
    <div class="header-container">
        <!-- Brand -->
        <a href="/" class="header-brand">
            11-классники
        </a>
        
        <!-- Navigation -->
        <nav class="header-nav" id="headerNav">
            <a href="/" class="nav-link">Главная</a>
            
            <!-- Categories Dropdown -->
            <?php if ($hasDatabase): ?>
                <div class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle" onclick="toggleDropdown(event, this)">
                        Категории <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
                    </a>
                    <div class="dropdown-menu">
                        <?php
                        try {
                            $hasCategories = false;
                            
                            // Show general categories only
                            $queryCategories = "SELECT url_category, title_category FROM categories ORDER BY title_category";
                            if ($connection && !$connection->connect_error) {
                                $resultCategories = mysqli_query($connection, $queryCategories);
                                
                                if ($resultCategories && mysqli_num_rows($resultCategories) > 0) {
                                    while ($category = mysqli_fetch_assoc($resultCategories)) {
                                        echo '<a href="/category/' . htmlspecialchars($category['url_category']) . '" class="dropdown-item">' . 
                                             htmlspecialchars($category['title_category']) . '</a>';
                                        $hasCategories = true;
                                    }
                                }
                            }
                            
                            if (!$hasCategories) {
                                echo '<a href="#" class="dropdown-item disabled">Категории не найдены</a>';
                            }
                        } catch (Exception $e) {
                            echo '<a href="#" class="dropdown-item">Ошибка загрузки категорий</a>';
                        }
                        ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Show message if database not connected -->
                <a href="#" class="nav-link" style="color: #999; cursor: not-allowed;" title="База данных не подключена">
                    Категории <i class="fas fa-exclamation-circle" style="font-size: 12px;"></i>
                </a>
            <?php endif; ?>
            
            <a href="/vpo-all-regions" class="nav-link">ВУЗы</a>
            <a href="/spo-all-regions" class="nav-link">ССУЗы</a>
            <a href="/schools-all-regions" class="nav-link">Школы</a>
            <a href="/news" class="nav-link">Новости</a>
            <a href="/tests" class="nav-link">Тесты</a>
        </nav>
        
        <!-- Actions -->
        <div class="header-actions">
            <!-- Theme Toggle -->
            <button class="theme-toggle-btn" onclick="toggleTheme()" aria-label="Переключить тему" style="background: transparent; border: 1px solid var(--border-color); width: 40px; height: 40px; border-radius: 50%; cursor: pointer;">
                <i class="fas fa-moon" id="theme-icon"></i>
            </button>
            
            <!-- User Menu -->
            <?php if (SessionManager::isLoggedIn()): ?>
                <div class="dropdown user-menu">
                    <div class="user-avatar dropdown-toggle" onclick="toggleDropdown(event, this)" style="width: 40px; height: 40px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="dropdown-menu">
                        <a href="/account" class="dropdown-item">
                            <i class="fas fa-user" style="margin-right: 10px; width: 16px;"></i>Мой аккаунт
                        </a>
                        <?php if (SessionManager::get('role') === 'admin'): ?>
                        <a href="/dashboard" class="dropdown-item">
                            <i class="fas fa-tachometer-alt" style="margin-right: 10px; width: 16px;"></i>Dashboard
                        </a>
                        <?php endif; ?>
                        <hr class="dropdown-divider">
                        <a href="/logout" class="dropdown-item">
                            <i class="fas fa-sign-out-alt" style="margin-right: 10px; width: 16px;"></i>Выйти
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="/login" class="nav-link">Войти</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<script>
// Dropdown functionality
function toggleDropdown(event, element) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    // Close all other dropdowns
    document.querySelectorAll('.dropdown.show').forEach(dropdown => {
        if (dropdown !== element.closest('.dropdown')) {
            dropdown.classList.remove('show');
        }
    });
    
    // Toggle current dropdown
    const dropdownElement = element.closest('.dropdown');
    if (dropdownElement) {
        dropdownElement.classList.toggle('show');
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown.show').forEach(dropdown => {
            dropdown.classList.remove('show');
        });
    }
});

// Theme toggle
function toggleTheme() {
    const body = document.body;
    const currentTheme = body.getAttribute('data-bs-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    body.setAttribute('data-bs-theme', newTheme);
    
    // Update icon
    const icon = document.getElementById('theme-icon');
    icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    
    // Save preference
    localStorage.setItem('theme', newTheme);
}

// Load saved theme
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.body.setAttribute('data-bs-theme', savedTheme);
    const icon = document.getElementById('theme-icon');
    icon.className = savedTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
});
</script>

<?php
// Debug info - remove in production
if ($hasDatabase) {
    echo "<!-- Database connected: Yes -->";
} else {
    echo "<!-- Database connected: No -->";
}
?>