<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/components/logo-component.php';
?>
<style>
    .header-dark {
        background: rgba(var(--bg-primary-rgb), 0.95);
        backdrop-filter: blur(20px);
        border-bottom: 1px solid var(--border-color);
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 0;
    }
    
    .nav-main {
        display: flex;
        align-items: center;
        gap: 2rem;
    }
    
    .nav-main a {
        color: var(--text-secondary);
        font-weight: 500;
        transition: color 0.2s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .nav-main a:hover {
        color: var(--text-primary);
    }
    
    .nav-main a.active {
        color: var(--accent-primary);
    }
    
    .nav-right {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .search-icon-link {
        color: var(--text-secondary);
        font-size: 1.25rem;
        transition: color 0.2s;
        padding: 0.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .search-icon-link:hover {
        color: var(--accent-primary);
    }
    
    .btn-header {
        padding: 0.5rem 1.25rem;
        border-radius: 24px;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    
    .btn-login {
        background: transparent;
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }
    
    .btn-login:hover {
        background: var(--bg-tertiary);
    }
    
    .btn-signup {
        background: var(--gradient);
        color: white;
    }
    
    .btn-signup:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .user-menu {
        position: relative;
    }
    
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid transparent;
    }
    
    .user-avatar:hover {
        border-color: var(--accent-primary);
        transform: scale(1.05);
    }
    
    .user-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .theme-toggle-header {
        background: var(--gradient);
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }
    
    .theme-toggle-header:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
    
    .dropdown-menu-dark {
        position: absolute;
        top: 100%;
        right: 0;
        margin-top: 0.5rem;
        background: var(--bg-primary);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        min-width: 220px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1000;
        overflow: hidden;
    }
    
    .dark-theme .dropdown-menu-dark {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }
    
    .user-menu:hover .dropdown-menu-dark,
    .dropdown-menu-dark:hover {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        color: var(--text-secondary);
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        font-weight: 500;
    }
    
    .dropdown-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 3px;
        height: 100%;
        background: var(--gradient);
        transform: scaleY(0);
        transform-origin: center;
        transition: transform 0.2s ease;
    }
    
    .dropdown-item:hover {
        background: var(--bg-secondary);
        color: var(--text-primary);
        text-decoration: none;
        transform: translateX(4px);
    }
    
    .dropdown-item:hover::before {
        transform: scaleY(1);
    }
    
    .dropdown-item i {
        width: 20px;
        font-size: 1rem;
        color: var(--accent-primary);
    }
    
    .dropdown-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--border-color), transparent);
        margin: 0.5rem 0;
    }
    
    @media (max-width: 768px) {
        .nav-main {
            display: none;
        }
        
        .search-box {
            display: none;
        }
        
        .header-content {
            padding: 0.75rem 0;
        }
    }
</style>

<header class="header-dark">
    <div class="container">
        <div class="header-content">
            <div class="nav-left">
                <?php renderLogo('normal', true); ?>
            </div>
            
            <nav class="nav-main">
                <a href="/vpo" <?php echo strpos($_SERVER['REQUEST_URI'], '/vpo') !== false ? 'class="active"' : ''; ?>>
                    ВУЗы
                </a>
                <a href="/spo" <?php echo strpos($_SERVER['REQUEST_URI'], '/spo') !== false ? 'class="active"' : ''; ?>>
                    Колледжи
                </a>
                <a href="/schools" <?php echo strpos($_SERVER['REQUEST_URI'], '/schools') !== false ? 'class="active"' : ''; ?>>
                    Школы
                </a>
                <a href="/news" <?php echo strpos($_SERVER['REQUEST_URI'], '/news') !== false ? 'class="active"' : ''; ?>>
                    Новости
                </a>
                <a href="/tests" <?php echo strpos($_SERVER['REQUEST_URI'], '/tests') !== false ? 'class="active"' : ''; ?>>
                    Тесты
                </a>
            </nav>
            
            <div class="nav-right">
                <a href="/search" class="search-icon-link" title="Поиск">
                    <i class="fas fa-search"></i>
                </a>
                
                <?php if (isset($_SESSION['email'])): ?>
                    <?php
                    // Get user info
                    $email = $_SESSION['email'];
                    $checkUserQuery = "SELECT * FROM users WHERE email=?";
                    $stmtCheckUser = mysqli_prepare($connection, $checkUserQuery);
                    
                    if ($stmtCheckUser) {
                        mysqli_stmt_bind_param($stmtCheckUser, "s", $email);
                        mysqli_stmt_execute($stmtCheckUser);
                        $resultCheckUser = mysqli_stmt_get_result($stmtCheckUser);
                        
                        if ($resultCheckUser && mysqli_num_rows($resultCheckUser) > 0) {
                            $user = mysqli_fetch_assoc($resultCheckUser);
                            ?>
                            <div class="user-menu">
                                <div class="user-avatar">
                                    <?php if (!empty($user['avatar'])): ?>
                                        <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar">
                                    <?php else: ?>
                                        <?php echo strtoupper(substr($user['name'] ?? $email, 0, 1)); ?>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="dropdown-menu-dark">
                                    <a href="/account" class="dropdown-item">
                                        <i class="fas fa-user"></i>
                                        Профиль
                                    </a>
                                    <?php if ($_SESSION['role'] == 'admin'): ?>
                                        <a href="/dashboard" class="dropdown-item">
                                            <i class="fas fa-tachometer-alt"></i>
                                            Панель управления
                                        </a>
                                    <?php endif; ?>
                                    <div class="dropdown-divider"></div>
                                    <a href="/pages/logout/logout.php" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt"></i>
                                        Выход
                                    </a>
                                </div>
                            </div>
                            <?php
                        }
                        mysqli_stmt_close($stmtCheckUser);
                    }
                    ?>
                <?php else: ?>
                    <a href="/login" class="btn-header btn-login">
                        Вход
                    </a>
                <?php endif; ?>
                
                <!-- Theme Toggle Button -->
                <button class="theme-toggle-header" id="themeToggleHeader" aria-label="Toggle theme">
                    <i class="fas fa-moon" id="themeIconHeader"></i>
                </button>
            </div>
        </div>
    </div>
</header>