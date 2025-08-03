<?php
/**
 * Reusable form template for all forms on the site
 * 
 * Usage:
 * $formConfig = [
 *     'title' => 'Form Title',
 *     'action' => '/path/to/action',
 *     'submitText' => 'Submit Button Text',
 *     'bottomLink' => ['text' => 'Link text', 'url' => '/link', 'linkText' => 'Click here']
 * ];
 * 
 * Then include this template and add form fields in the content area
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
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - 11-классники</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        body {
            background-color: #ffffff;
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
            max-width: 500px; /* Standard width for all forms */
        }
        
        .form-card {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }
        
        .form-title {
            color: #28a745;
            font-weight: 600;
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .btn-submit {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
            font-weight: bold;
            padding: 10px 30px;
            display: block;
            width: 100%;
            margin-top: 1.5rem;
        }
        
        .btn-submit:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        
        .form-link {
            color: #28a745;
            text-decoration: none;
        }
        
        .form-link:hover {
            color: #218838;
            text-decoration: underline;
        }
        
        .input-group > .form-control {
            border-right: none;
        }
        
        .input-group-text {
            background: #ffffff;
            border-left: none;
            cursor: pointer;
        }
        
        .input-group-text:hover {
            background: #f8f9fa;
        }
        
        /* Ensure all form controls have consistent styling */
        .form-control, .form-select {
            border-color: #ced4da;
            padding: 0.5rem 0.75rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
    </style>
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