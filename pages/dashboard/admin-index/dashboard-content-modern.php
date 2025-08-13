<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

// Get statistics for dashboard cards
$stats = [];

// Get schools count
$result = mysqli_query($connection, "SELECT COUNT(*) as count FROM schools");
$stats['schools'] = $result ? mysqli_fetch_assoc($result)['count'] : 0;

// Get VPO count
$result = mysqli_query($connection, "SELECT COUNT(*) as count FROM vpo");
$stats['vpo'] = $result ? mysqli_fetch_assoc($result)['count'] : 0;

// Get SPO count
$result = mysqli_query($connection, "SELECT COUNT(*) as count FROM spo");
$stats['spo'] = $result ? mysqli_fetch_assoc($result)['count'] : 0;

// Get users count
$result = mysqli_query($connection, "SELECT COUNT(*) as count FROM users");
$stats['users'] = $result ? mysqli_fetch_assoc($result)['count'] : 0;

// Get comments count
$result = mysqli_query($connection, "SELECT COUNT(*) as count FROM comments");
$stats['comments'] = $result ? mysqli_fetch_assoc($result)['count'] : 0;

// Get news count
$result = mysqli_query($connection, "SELECT COUNT(*) as count FROM news");
$stats['news'] = $result ? mysqli_fetch_assoc($result)['count'] : 0;

// Get posts count
$result = mysqli_query($connection, "SELECT COUNT(*) as count FROM posts");
$stats['posts'] = $result ? mysqli_fetch_assoc($result)['count'] : 0;

// Get messages count
$result = mysqli_query($connection, "SELECT COUNT(*) as count FROM messages");
$stats['messages'] = $result ? mysqli_fetch_assoc($result)['count'] : 0;
?>

<!-- Dashboard Sidebar -->
<div class="dashboard-sidebar" id="dashboardSidebar">
    <div class="dashboard-sidebar-header">
        <h4><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h4>
    </div>
    <nav class="dashboard-sidebar-nav">
        <a href="/dashboard" class="dashboard-nav-item active">
            <i class="fas fa-home"></i><span>Overview</span>
        </a>
        <a href="/pages/dashboard/schools-dashboard/schools-index/schools-index.php" class="dashboard-nav-item">
            <i class="fas fa-school"></i><span>Schools</span>
        </a>
        <a href="/pages/dashboard/vpo-dashboard/vpo-index/vpo-index.php" class="dashboard-nav-item">
            <i class="fas fa-university"></i><span>Universities</span>
        </a>
        <a href="/pages/dashboard/spo-dashboard/spo-index/spo-index.php" class="dashboard-nav-item">
            <i class="fas fa-graduation-cap"></i><span>Colleges</span>
        </a>
        <a href="/pages/dashboard/users-dashboard/users-index/users-index.php" class="dashboard-nav-item">
            <i class="fas fa-users"></i><span>Users</span>
        </a>
        <a href="/pages/dashboard/comments-dashboard/comments-index/comments-index.php" class="dashboard-nav-item">
            <i class="fas fa-comments"></i><span>Comments</span>
        </a>
        <a href="/pages/dashboard/news-dashboard/news-index/news-index.php" class="dashboard-nav-item">
            <i class="fas fa-newspaper"></i><span>News</span>
        </a>
        <a href="/pages/dashboard/posts-dashboard/posts-index/posts-index.php" class="dashboard-nav-item">
            <i class="fas fa-file-alt"></i><span>Posts</span>
        </a>
        <a href="/pages/dashboard/messages-dashboard/messages-index/messages-index.php" class="dashboard-nav-item">
            <i class="fas fa-envelope"></i><span>Messages</span>
        </a>
    </nav>
</div>

<!-- Dashboard Overlay for mobile -->
<div class="dashboard-overlay" id="dashboardOverlay"></div>

<!-- Main Dashboard Content -->
<div class="dashboard-main" id="dashboardMain">
    <!-- Dashboard Topbar -->
    <div class="dashboard-topbar">
        <button class="dashboard-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <nav aria-label="breadcrumb" class="dashboard-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fas fa-home me-1"></i>Dashboard
                </li>
                <li class="breadcrumb-item active">Overview</li>
            </ol>
        </nav>
    </div>

    <!-- Dashboard Content -->
    <div class="dashboard-content">
        <!-- Welcome Header -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="welcome-header">
                    <div class="welcome-content">
                        <h1 class="welcome-title">
                            Welcome back, Admin! 
                            <span class="wave">👋</span>
                        </h1>
                        <p class="welcome-subtitle">Here's what's happening with your educational platform today.</p>
                        <div class="welcome-stats">
                            <div class="welcome-stat">
                                <div class="stat-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-number">
                                        <?php echo number_format($stats['schools'] + $stats['vpo'] + $stats['spo']); ?>
                                    </div>
                                    <div class="stat-label">Total Institutions</div>
                                </div>
                            </div>
                            <div class="welcome-stat">
                                <div class="stat-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-number"><?php echo number_format($stats['users']); ?></div>
                                    <div class="stat-label">Active Users</div>
                                </div>
                            </div>
                            <div class="welcome-stat">
                                <div class="stat-icon">
                                    <i class="fas fa-newspaper"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-number">
                                        <?php echo number_format($stats['news'] + $stats['posts']); ?>
                                    </div>
                                    <div class="stat-label">Published Content</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="welcome-decoration">
                        <div class="decoration-circle circle-1"></div>
                        <div class="decoration-circle circle-2"></div>
                        <div class="decoration-circle circle-3"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="dashboard-stats-row">
            <!-- Schools Card -->
            <div class="dashboard-card stat-card" data-aos="fade-up" data-aos-delay="100">
                <div class="dashboard-card-body">
                    <div class="card-header-mini">
                        <div class="dashboard-card-icon bg-primary">
                            <i class="fas fa-school"></i>
                        </div>
                        <div class="card-trend">
                            <i class="fas fa-arrow-up trend-icon trend-up"></i>
                            <span class="trend-text">+12%</span>
                        </div>
                    </div>
                    <div class="dashboard-card-title">Schools</div>
                    <div class="dashboard-card-number" data-count="<?php echo $stats['schools']; ?>">0</div>
                    <div class="card-description">Educational institutions registered</div>
                    <a href="/pages/dashboard/schools-dashboard/schools-index/schools-index.php" class="dashboard-card-link">
                        View All Schools
                    </a>
                </div>
                <div class="card-glow"></div>
            </div>

            <!-- Universities Card -->
            <div class="dashboard-card stat-card" data-aos="fade-up" data-aos-delay="200">
                <div class="dashboard-card-body">
                    <div class="card-header-mini">
                        <div class="dashboard-card-icon bg-success">
                            <i class="fas fa-university"></i>
                        </div>
                        <div class="card-trend">
                            <i class="fas fa-arrow-up trend-icon trend-up"></i>
                            <span class="trend-text">+8%</span>
                        </div>
                    </div>
                    <div class="dashboard-card-title">Universities</div>
                    <div class="dashboard-card-number" data-count="<?php echo $stats['vpo']; ?>">0</div>
                    <div class="card-description">Higher education institutions</div>
                    <a href="/pages/dashboard/vpo-dashboard/vpo-index/vpo-index.php" class="dashboard-card-link">
                        View All Universities
                    </a>
                </div>
                <div class="card-glow"></div>
            </div>

            <!-- Colleges Card -->
            <div class="dashboard-card stat-card" data-aos="fade-up" data-aos-delay="300">
                <div class="dashboard-card-body">
                    <div class="card-header-mini">
                        <div class="dashboard-card-icon bg-warning">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="card-trend">
                            <i class="fas fa-arrow-up trend-icon trend-up"></i>
                            <span class="trend-text">+15%</span>
                        </div>
                    </div>
                    <div class="dashboard-card-title">Colleges</div>
                    <div class="dashboard-card-number" data-count="<?php echo $stats['spo']; ?>">0</div>
                    <div class="card-description">Professional education centers</div>
                    <a href="/pages/dashboard/spo-dashboard/spo-index/spo-index.php" class="dashboard-card-link">
                        View All Colleges
                    </a>
                </div>
                <div class="card-glow"></div>
            </div>

            <!-- Users Card -->
            <div class="dashboard-card stat-card" data-aos="fade-up" data-aos-delay="400">
                <div class="dashboard-card-body">
                    <div class="card-header-mini">
                        <div class="dashboard-card-icon bg-info">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="card-trend">
                            <i class="fas fa-arrow-up trend-icon trend-up"></i>
                            <span class="trend-text">+24%</span>
                        </div>
                    </div>
                    <div class="dashboard-card-title">Active Users</div>
                    <div class="dashboard-card-number" data-count="<?php echo $stats['users']; ?>">0</div>
                    <div class="card-description">Platform registered users</div>
                    <a href="/pages/dashboard/users-dashboard/users-index/users-index.php" class="dashboard-card-link">
                        Manage Users
                    </a>
                </div>
                <div class="card-glow"></div>
            </div>
        </div>

        <!-- Second Row Stats -->
        <div class="dashboard-stats-row">
            <!-- Comments Card -->
            <div class="dashboard-card stat-card" data-aos="fade-up" data-aos-delay="500">
                <div class="dashboard-card-body">
                    <div class="card-header-mini">
                        <div class="dashboard-card-icon bg-purple">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="card-trend">
                            <i class="fas fa-arrow-up trend-icon trend-up"></i>
                            <span class="trend-text">+18%</span>
                        </div>
                    </div>
                    <div class="dashboard-card-title">Comments</div>
                    <div class="dashboard-card-number" data-count="<?php echo $stats['comments']; ?>">0</div>
                    <div class="card-description">User engagement interactions</div>
                    <a href="/pages/dashboard/comments-dashboard/comments-index/comments-index.php" class="dashboard-card-link">
                        Moderate Comments
                    </a>
                </div>
                <div class="card-glow"></div>
            </div>

            <!-- News Card -->
            <div class="dashboard-card stat-card" data-aos="fade-up" data-aos-delay="600">
                <div class="dashboard-card-body">
                    <div class="card-header-mini">
                        <div class="dashboard-card-icon bg-danger">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        <div class="card-trend">
                            <i class="fas fa-arrow-up trend-icon trend-up"></i>
                            <span class="trend-text">+22%</span>
                        </div>
                    </div>
                    <div class="dashboard-card-title">News Articles</div>
                    <div class="dashboard-card-number" data-count="<?php echo $stats['news']; ?>">0</div>
                    <div class="card-description">Published news content</div>
                    <a href="/pages/dashboard/news-dashboard/news-index/news-index.php" class="dashboard-card-link">
                        Manage News
                    </a>
                </div>
                <div class="card-glow"></div>
            </div>

            <!-- Posts Card -->
            <div class="dashboard-card stat-card" data-aos="fade-up" data-aos-delay="700">
                <div class="dashboard-card-body">
                    <div class="card-header-mini">
                        <div class="dashboard-card-icon bg-success">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="card-trend">
                            <i class="fas fa-arrow-up trend-icon trend-up"></i>
                            <span class="trend-text">+9%</span>
                        </div>
                    </div>
                    <div class="dashboard-card-title">Posts</div>
                    <div class="dashboard-card-number" data-count="<?php echo $stats['posts']; ?>">0</div>
                    <div class="card-description">Educational articles published</div>
                    <a href="/pages/dashboard/posts-dashboard/posts-index/posts-index.php" class="dashboard-card-link">
                        View Posts
                    </a>
                </div>
                <div class="card-glow"></div>
            </div>

            <!-- Messages Card -->
            <div class="dashboard-card stat-card" data-aos="fade-up" data-aos-delay="800">
                <div class="dashboard-card-body">
                    <div class="card-header-mini">
                        <div class="dashboard-card-icon bg-warning">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="card-trend">
                            <i class="fas fa-arrow-down trend-icon trend-down"></i>
                            <span class="trend-text">-3%</span>
                        </div>
                    </div>
                    <div class="dashboard-card-title">Messages</div>
                    <div class="dashboard-card-number" data-count="<?php echo $stats['messages']; ?>">0</div>
                    <div class="card-description">User inquiries received</div>
                    <a href="/pages/dashboard/messages-dashboard/messages-index/messages-index.php" class="dashboard-card-link">
                        View Messages
                    </a>
                </div>
                <div class="card-glow"></div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="dashboard-card" data-aos="fade-up" data-aos-delay="900">
                    <div class="dashboard-card-header">
                        <div class="header-content-flex">
                            <div class="header-title">
                                <i class="fas fa-bolt me-2"></i>Quick Actions
                            </div>
                            <div class="header-subtitle">Manage your platform efficiently</div>
                        </div>
                    </div>
                    <div class="dashboard-card-body">
                        <div class="quick-actions-grid">
                            <a href="/pages/dashboard/schools-dashboard/schools-create/schools-create.php" class="quick-action-card primary">
                                <div class="action-icon">
                                    <i class="fas fa-school"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">Add School</div>
                                    <div class="action-subtitle">Register new educational institution</div>
                                </div>
                                <div class="action-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </a>
                            
                            <a href="/pages/dashboard/vpo-dashboard/vpo-create/vpo-create.php" class="quick-action-card success">
                                <div class="action-icon">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">Add University</div>
                                    <div class="action-subtitle">Register higher education institution</div>
                                </div>
                                <div class="action-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </a>
                            
                            <a href="/pages/dashboard/news-dashboard/news-create/news-create.php" class="quick-action-card warning">
                                <div class="action-icon">
                                    <i class="fas fa-newspaper"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">Create News</div>
                                    <div class="action-subtitle">Publish educational news article</div>
                                </div>
                                <div class="action-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </a>
                            
                            <a href="/pages/dashboard/posts-dashboard/posts-create/posts-create.php" class="quick-action-card info">
                                <div class="action-icon">
                                    <i class="fas fa-edit"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">Create Post</div>
                                    <div class="action-subtitle">Write informative content</div>
                                </div>
                                <div class="action-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AOS Animation Library -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<!-- Dashboard JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS
    AOS.init({
        duration: 800,
        easing: 'ease-out-cubic',
        once: true,
        offset: 100
    });

    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('dashboardSidebar');
    const overlay = document.getElementById('dashboardOverlay');
    const main = document.getElementById('dashboardMain');

    // Toggle sidebar
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        main.classList.toggle('sidebar-open');
    });

    // Close sidebar when clicking overlay
    overlay.addEventListener('click', function() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        main.classList.remove('sidebar-open');
    });

    // Auto-open sidebar on larger screens
    function checkScreenSize() {
        if (window.innerWidth >= 992) {
            sidebar.classList.add('active');
            main.classList.add('sidebar-open');
            overlay.classList.remove('active');
        } else {
            sidebar.classList.remove('active');
            main.classList.remove('sidebar-open');
            overlay.classList.remove('active');
        }
    }

    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);

    // Animated Number Counters
    function animateNumbers() {
        const numbers = document.querySelectorAll('.dashboard-card-number[data-count]');
        
        numbers.forEach(number => {
            const target = parseInt(number.dataset.count);
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                number.textContent = Math.floor(current).toLocaleString();
            }, 16);
        });
    }

    // Start number animation after a delay
    setTimeout(animateNumbers, 500);

    // Add hover effects to stat cards
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Add pulse effect to trend indicators
    const trendIcons = document.querySelectorAll('.trend-icon');
    trendIcons.forEach(icon => {
        setInterval(() => {
            icon.style.animation = 'pulse 0.6s ease-in-out';
            setTimeout(() => {
                icon.style.animation = '';
            }, 600);
        }, 3000);
    });

    // Quick action cards hover effects
    const quickActionCards = document.querySelectorAll('.quick-action-card');
    quickActionCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const arrow = this.querySelector('.action-arrow');
            arrow.style.transform = 'translateX(5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            const arrow = this.querySelector('.action-arrow');
            arrow.style.transform = 'translateX(0)';
        });
    });
});
</script>