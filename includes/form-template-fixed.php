<?php
/**
 * Fixed form template with CSS variables support
 */

// Ensure we have required config
if (!isset($formConfig)) {
    $formConfig = [];
}

// Set defaults
$title = $formConfig['title'] ?? '';
$action = $formConfig['action'] ?? '#';
$submitText = $formConfig['submitText'] ?? 'Submit';
$bottomLink = $formConfig['bottomLink'] ?? null;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if needed
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get current theme
$currentTheme = $_COOKIE['preferred-theme'] ?? 'light';
?>

<!DOCTYPE html>
<html lang="ru" data-theme="<?= htmlspecialchars($currentTheme) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - 11-классники</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    
    <!-- Theme Variables -->
    <link rel="stylesheet" href="/css/theme-variables.css">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Login Fix CSS -->
    <link rel="stylesheet" href="/css/login-fix.css">
    
    <style>
        /* Base styles using CSS variables */
        body {
            background-color: var(--color-surface-primary);
            color: var(--color-text-primary);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        
        .form-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        
        .form-wrapper {
            width: 100%;
            max-width: 500px;
        }
        
        .form-card {
            background-color: var(--color-card-bg);
            border: 1px solid var(--color-border-primary);
            border-radius: 0.375rem;
            padding: 1.5rem;
            box-shadow: 0 0.125rem 0.25rem var(--color-shadow-sm);
        }
        
        .form-title {
            color: var(--color-primary);
            font-weight: 600;
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .btn-submit {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
            color: white;
            font-weight: bold;
            padding: 10px 30px;
            display: block;
            width: 100%;
            margin-top: 1.5rem;
        }
        
        .btn-submit:hover {
            background-color: var(--color-primary-hover);
            border-color: var(--color-primary-hover);
        }
        
        .form-link {
            color: var(--color-link);
            text-decoration: none;
        }
        
        .form-link:hover {
            color: var(--color-link-hover);
            text-decoration: underline;
        }
        
        /* Form controls */
        .form-control, .form-select {
            background-color: var(--color-surface-secondary);
            color: var(--color-text-primary);
            border-color: var(--color-border-primary);
            padding: 0.5rem 0.75rem;
        }
        
        .form-control:focus, .form-select:focus {
            background-color: var(--color-surface-primary);
            color: var(--color-text-primary);
            border-color: var(--color-primary);
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        
        .form-control::placeholder {
            color: var(--color-text-tertiary);
            opacity: 1;
        }
        
        /* Input group */
        .input-group > .form-control {
            border-right: none;
        }
        
        .input-group-text {
            background: var(--color-surface-secondary);
            color: var(--color-text-secondary);
            border-left: none;
            border-color: var(--color-border-primary);
            cursor: pointer;
        }
        
        .input-group-text:hover {
            background: var(--color-bg-hover);
        }
    </style>
    
    <!-- Theme initialization -->
    <script>
        // Apply saved theme immediately
        (function() {
            const savedTheme = localStorage.getItem('preferred-theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
</head>
<body>
    <div class="form-container">
        <div class="form-wrapper">
            <div class="form-card">
                <?php if ($title): ?>
                    <h1 class="form-title"><?= htmlspecialchars($title) ?></h1>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                        <?php unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['errors']) && is_array($_SESSION['errors'])): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <p class="mb-1"><?= htmlspecialchars($error) ?></p>
                        <?php endforeach; ?>
                        <?php unset($_SESSION['errors']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($_SESSION['success']) ?>
                        <?php unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" action="<?= htmlspecialchars($action) ?>" <?= isset($formConfig['enctype']) ? 'enctype="' . $formConfig['enctype'] . '"' : '' ?>>
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <!-- Form fields will be included here -->
                    <?php if (isset($formFields)): ?>
                        <?php include $formFields; ?>
                    <?php endif; ?>
                    
                    <button type="submit" class="btn btn-submit"><?= htmlspecialchars($submitText) ?></button>
                </form>
                
                <?php if ($bottomLink): ?>
                    <p class="text-center mt-3">
                        <?= htmlspecialchars($bottomLink['text']) ?> 
                        <a href="<?= htmlspecialchars($bottomLink['url']) ?>" class="form-link">
                            <?= htmlspecialchars($bottomLink['linkText']) ?>
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Include any additional scripts -->
    <?php if (isset($formScripts)): ?>
        <?php include $formScripts; ?>
    <?php endif; ?>
</body>
</html>