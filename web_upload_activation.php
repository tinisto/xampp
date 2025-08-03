<?php
/**
 * Web-based Activation File Uploader
 * Upload this single file first, then use it to upload the activation tools
 */

session_start();

// Simple authentication - change this password!
$UPLOAD_PASSWORD = 'upload123!';

$message = '';
$success = false;

// Handle login
if (isset($_POST['password'])) {
    if ($_POST['password'] === $UPLOAD_PASSWORD) {
        $_SESSION['upload_authenticated'] = true;
        $message = 'Authentication successful!';
        $success = true;
    } else {
        $message = 'Invalid password!';
    }
}

// Handle file upload
if (isset($_SESSION['upload_authenticated']) && $_SESSION['upload_authenticated'] && isset($_POST['upload_code'])) {
    $files_to_create = [];
    
    // Activation manual tool
    $files_to_create['activate-user-manual.php'] = '<?php
/**
 * Manual User Activation Tool
 * Allows activation of user accounts by email address
 */

session_start();
require_once "config/loadEnv.php";
require_once "database/db_connections.php";

// Simple security check
if ($_SERVER["REQUEST_METHOD"] !== "POST" && !isset($_GET["email"])) {
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Активация аккаунта - 11klassniki.ru</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Активация аккаунта вручную</h4>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Введите email адрес для активации аккаунта:</p>
                            
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email адрес</label>
                                    <input type="email" class="form-control" id="email" name="email" required 
                                           placeholder="your-email@example.com">
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Активировать аккаунт</button>
                                <a href="/login" class="btn btn-secondary">Вернуться к входу</a>
                            </form>
                            
                            <div class="alert alert-warning mt-3">
                                <strong>Внимание:</strong> Этот инструмент временный и будет удален после исправления системы активации.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Process activation
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"])) {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("<div class=\'alert alert-danger\'>Неверный формат email адреса!</div>");
    }
    
    try {
        // Check if user exists
        $checkStmt = $connection->prepare("SELECT id, email, is_active FROM users WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result->num_rows === 0) {
            ?>
            <!DOCTYPE html>
            <html lang="ru">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Пользователь не найден</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            </head>
            <body>
                <div class="container mt-5">
                    <div class="alert alert-danger">
                        <h4>Пользователь не найден!</h4>
                        <p>Email адрес <strong><?php echo htmlspecialchars($email); ?></strong> не зарегистрирован в системе.</p>
                        <a href="activate-user-manual.php" class="btn btn-primary">Попробовать снова</a>
                        <a href="/registration" class="btn btn-secondary">Регистрация</a>
                    </div>
                </div>
            </body>
            </html>
            <?php
            exit;
        }
        
        $user = $result->fetch_assoc();
        
        if ($user["is_active"] == 1) {
            ?>
            <!DOCTYPE html>
            <html lang="ru">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Аккаунт уже активирован</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            </head>
            <body>
                <div class="container mt-5">
                    <div class="alert alert-info">
                        <h4>Аккаунт уже активирован!</h4>
                        <p>Email адрес <strong><?php echo htmlspecialchars($email); ?></strong> уже активирован.</p>
                        <a href="/login" class="btn btn-primary">Войти в систему</a>
                    </div>
                </div>
            </body>
            </html>
            <?php
            exit;
        }
        
        // Activate the user
        $updateStmt = $connection->prepare("UPDATE users SET is_active = 1, activation_token = NULL, activation_link = NULL WHERE email = ?");
        $updateStmt->bind_param("s", $email);
        
        if ($updateStmt->execute()) {
            ?>
            <!DOCTYPE html>
            <html lang="ru">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Аккаунт активирован!</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            </head>
            <body>
                <div class="container mt-5">
                    <div class="alert alert-success">
                        <h4>Успешно!</h4>
                        <p>Аккаунт <strong><?php echo htmlspecialchars($email); ?></strong> успешно активирован!</p>
                        <p>Теперь вы можете войти в систему.</p>
                        <a href="/login" class="btn btn-primary btn-lg">Войти в систему</a>
                    </div>
                    
                    <div class="alert alert-warning mt-3">
                        <strong>Безопасность:</strong> Пожалуйста, сообщите администратору об удалении этого файла после использования.
                    </div>
                </div>
            </body>
            </html>
            <?php
        } else {
            throw new Exception("Failed to update user status");
        }
        
    } catch (Exception $e) {
        ?>
        <!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Ошибка активации</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
            <div class="container mt-5">
                <div class="alert alert-danger">
                    <h4>Ошибка активации!</h4>
                    <p>Произошла ошибка при активации аккаунта. Пожалуйста, попробуйте позже или обратитесь к администратору.</p>
                    <p class="small text-muted">Ошибка: <?php echo htmlspecialchars($e->getMessage()); ?></p>
                    <a href="activate-user-manual.php" class="btn btn-primary">Попробовать снова</a>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
    
    exit;
}
?>';

    // Create login content update
    $files_to_create['pages/login/login_content.php'] = file_get_contents('/Applications/XAMPP/xamppfiles/htdocs/pages/login/login_content.php');
    
    $uploaded = 0;
    $errors = [];
    
    foreach ($files_to_create as $filename => $content) {
        try {
            // Create directory if needed
            $dir = dirname($filename);
            if ($dir !== '.' && !is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            
            // Write file
            if (file_put_contents($filename, $content) !== false) {
                $uploaded++;
            } else {
                $errors[] = "Failed to write $filename";
            }
        } catch (Exception $e) {
            $errors[] = "Error with $filename: " . $e->getMessage();
        }
    }
    
    if ($uploaded > 0) {
        $message = "Successfully created $uploaded files!";
        $success = true;
        
        // Self-destruct option
        if (isset($_POST['self_destruct'])) {
            unlink(__FILE__);
            $message .= " This uploader has been deleted.";
        }
    } else {
        $message = "Failed to create files: " . implode(', ', $errors);
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activation File Uploader</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Activation System File Uploader</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?>">
                                <?php echo htmlspecialchars($message); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!isset($_SESSION['upload_authenticated']) || !$_SESSION['upload_authenticated']): ?>
                            <!-- Login Form -->
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Upload Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <div class="form-text">Default: upload123!</div>
                                </div>
                                <button type="submit" class="btn btn-primary">Login</button>
                            </form>
                        <?php else: ?>
                            <!-- Upload Form -->
                            <p>Click the button below to create the activation system files:</p>
                            
                            <form method="POST">
                                <input type="hidden" name="upload_code" value="1">
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="self_destruct" name="self_destruct" checked>
                                    <label class="form-check-label" for="self_destruct">
                                        Delete this uploader after creating files (recommended)
                                    </label>
                                </div>
                                
                                <button type="submit" class="btn btn-success">Create Activation Files</button>
                                <a href="?logout=1" class="btn btn-secondary">Logout</a>
                            </form>
                            
                            <hr>
                            
                            <h5>Files to be created:</h5>
                            <ul>
                                <li><code>/activate-user-manual.php</code> - Manual activation tool</li>
                                <li><code>/pages/login/login_content.php</code> - Updated login page</li>
                            </ul>
                            
                            <div class="alert alert-info mt-3">
                                <strong>After creating files:</strong>
                                <ol>
                                    <li>Visit <a href="/activate-user-manual.php" target="_blank">/activate-user-manual.php</a></li>
                                    <li>Enter your email to activate your account</li>
                                    <li>Try logging in</li>
                                    <li>Delete the activation files for security</li>
                                </ol>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($success && $uploaded > 0): ?>
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5>Quick Links:</h5>
                            <a href="/activate-user-manual.php" class="btn btn-primary" target="_blank">Go to Activation Tool</a>
                            <a href="/login" class="btn btn-secondary" target="_blank">Go to Login Page</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>