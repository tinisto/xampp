<?php
/**
 * Account page template - consistent layout for all account pages
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

// Get page config
$pageTitle = $pageConfig['title'] ?? 'Мой аккаунт';
$showBackButton = $pageConfig['showBackButton'] ?? true;

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - 11-классники</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .account-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 15px;
        }
        
        .account-card {
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .account-header {
            border-bottom: 2px solid #28a745;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        
        .account-title {
            color: #28a745;
            font-size: 1.75rem;
            font-weight: 600;
            margin: 0;
        }
        
        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
        }
        
        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        
        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin-bottom: 1rem;
        }
        
        .breadcrumb-item a {
            color: #28a745;
            text-decoration: none;
        }
        
        .breadcrumb-item a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="account-container">
        <!-- Breadcrumb navigation -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Главная</a></li>
                <li class="breadcrumb-item"><a href="/account">Мой аккаунт</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($pageTitle) ?></li>
            </ol>
        </nav>
        
        <div class="account-card">
            <div class="account-header">
                <h1 class="account-title"><?= htmlspecialchars($pageTitle) ?></h1>
            </div>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <!-- Page content will be included here -->
            <?php if (isset($pageContent)): ?>
                <?php include $pageContent; ?>
            <?php endif; ?>
            
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>