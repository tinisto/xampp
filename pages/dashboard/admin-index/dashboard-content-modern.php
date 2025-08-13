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
            <i class="fas fa-home"></i>Overview
        </a>
        <a href="/pages/dashboard/schools-dashboard/schools-index/schools-index.php" class="dashboard-nav-item">
            <i class="fas fa-school"></i>Schools
        </a>
        <a href="/pages/dashboard/vpo-dashboard/vpo-index/vpo-index.php" class="dashboard-nav-item">
            <i class="fas fa-university"></i>Universities
        </a>
        <a href="/pages/dashboard/spo-dashboard/spo-index/spo-index.php" class="dashboard-nav-item">
            <i class="fas fa-graduation-cap"></i>Colleges
        </a>
        <a href="/pages/dashboard/users-dashboard/users-index/users-index.php" class="dashboard-nav-item">
            <i class="fas fa-users"></i>Users
        </a>
        <a href="/pages/dashboard/comments-dashboard/comments-index/comments-index.php" class="dashboard-nav-item">
            <i class="fas fa-comments"></i>Comments
        </a>
        <a href="/pages/dashboard/news-dashboard/news-index/news-index.php" class="dashboard-nav-item">
            <i class="fas fa-newspaper"></i>News
        </a>
        <a href="/pages/dashboard/posts-dashboard/posts-index/posts-index.php" class="dashboard-nav-item">
            <i class="fas fa-file-alt"></i>Posts
        </a>
        <a href="/pages/dashboard/messages-dashboard/messages-index/messages-index.php" class="dashboard-nav-item">
            <i class="fas fa-envelope"></i>Messages
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
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 mb-1">Welcome back, Admin!</h1>
                <p class="text-muted">Here's what's happening with your educational platform today.</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="dashboard-stats-row">
            <!-- Schools Card -->
            <div class="dashboard-card">
                <div class="dashboard-card-body">
                    <div class="dashboard-card-icon bg-primary">
                        <i class="fas fa-school"></i>
                    </div>
                    <div class="dashboard-card-title">Schools</div>
                    <div class="dashboard-card-number"><?php echo number_format($stats['schools']); ?></div>
                    <a href="/pages/dashboard/schools-dashboard/schools-index/schools-index.php" class="dashboard-card-link">
                        View All Schools →
                    </a>
                </div>
            </div>

            <!-- Universities Card -->
            <div class="dashboard-card">
                <div class="dashboard-card-body">
                    <div class="dashboard-card-icon bg-success">
                        <i class="fas fa-university"></i>
                    </div>
                    <div class="dashboard-card-title">Universities</div>
                    <div class="dashboard-card-number"><?php echo number_format($stats['vpo']); ?></div>
                    <a href="/pages/dashboard/vpo-dashboard/vpo-index/vpo-index.php" class="dashboard-card-link">
                        View All Universities →
                    </a>
                </div>
            </div>

            <!-- Colleges Card -->
            <div class="dashboard-card">
                <div class="dashboard-card-body">
                    <div class="dashboard-card-icon bg-warning">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="dashboard-card-title">Colleges</div>
                    <div class="dashboard-card-number"><?php echo number_format($stats['spo']); ?></div>
                    <a href="/pages/dashboard/spo-dashboard/spo-index/spo-index.php" class="dashboard-card-link">
                        View All Colleges →
                    </a>
                </div>
            </div>

            <!-- Users Card -->
            <div class="dashboard-card">
                <div class="dashboard-card-body">
                    <div class="dashboard-card-icon bg-info">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="dashboard-card-title">Users</div>
                    <div class="dashboard-card-number"><?php echo number_format($stats['users']); ?></div>
                    <a href="/pages/dashboard/users-dashboard/users-index/users-index.php" class="dashboard-card-link">
                        Manage Users →
                    </a>
                </div>
            </div>
        </div>

        <!-- Second Row Stats -->
        <div class="dashboard-stats-row">
            <!-- Comments Card -->
            <div class="dashboard-card">
                <div class="dashboard-card-body">
                    <div class="dashboard-card-icon bg-primary">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="dashboard-card-title">Comments</div>
                    <div class="dashboard-card-number"><?php echo number_format($stats['comments']); ?></div>
                    <a href="/pages/dashboard/comments-dashboard/comments-index/comments-index.php" class="dashboard-card-link">
                        Moderate Comments →
                    </a>
                </div>
            </div>

            <!-- News Card -->
            <div class="dashboard-card">
                <div class="dashboard-card-body">
                    <div class="dashboard-card-icon bg-danger">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div class="dashboard-card-title">News Articles</div>
                    <div class="dashboard-card-number"><?php echo number_format($stats['news']); ?></div>
                    <a href="/pages/dashboard/news-dashboard/news-index/news-index.php" class="dashboard-card-link">
                        Manage News →
                    </a>
                </div>
            </div>

            <!-- Posts Card -->
            <div class="dashboard-card">
                <div class="dashboard-card-body">
                    <div class="dashboard-card-icon bg-success">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="dashboard-card-title">Posts</div>
                    <div class="dashboard-card-number"><?php echo number_format($stats['posts']); ?></div>
                    <a href="/pages/dashboard/posts-dashboard/posts-index/posts-index.php" class="dashboard-card-link">
                        View Posts →
                    </a>
                </div>
            </div>

            <!-- Messages Card -->
            <div class="dashboard-card">
                <div class="dashboard-card-body">
                    <div class="dashboard-card-icon bg-warning">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="dashboard-card-title">Messages</div>
                    <div class="dashboard-card-number"><?php echo number_format($stats['messages']); ?></div>
                    <a href="/pages/dashboard/messages-dashboard/messages-index/messages-index.php" class="dashboard-card-link">
                        View Messages →
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </div>
                    <div class="dashboard-card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <a href="/pages/dashboard/schools-dashboard/schools-create/schools-create.php" class="dashboard-btn dashboard-btn-primary w-100">
                                    <i class="fas fa-plus"></i>Add School
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="/pages/dashboard/vpo-dashboard/vpo-create/vpo-create.php" class="dashboard-btn dashboard-btn-success w-100">
                                    <i class="fas fa-plus"></i>Add University
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="/pages/dashboard/news-dashboard/news-create/news-create.php" class="dashboard-btn dashboard-btn-outline w-100">
                                    <i class="fas fa-newspaper"></i>Create News
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="/pages/dashboard/posts-dashboard/posts-create/posts-create.php" class="dashboard-btn dashboard-btn-outline w-100">
                                    <i class="fas fa-edit"></i>Create Post
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dashboard JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>