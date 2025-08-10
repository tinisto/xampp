<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/upload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

$message = '';
$error = '';

// Get user data
$user = db_fetch_one("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        // Update profile
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        if ($name && $email) {
            // Check if email is already taken by another user
            $emailExists = db_fetch_one("
                SELECT id FROM users 
                WHERE email = ? AND id != ?
            ", [$email, $_SESSION['user_id']]);
            
            if ($emailExists) {
                $error = 'Этот email уже используется';
            } else {
                // Handle avatar upload
                $avatarPath = $user['avatar'];
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $uploadResult = ImageUpload::handleUpload($_FILES['avatar'], 'avatar', $_SESSION['user_id']);
                    if ($uploadResult['success']) {
                        // Delete old avatar if exists
                        if ($avatarPath) {
                            ImageUpload::deleteFile($avatarPath);
                        }
                        $avatarPath = $uploadResult['path'];
                    } else {
                        $error = $uploadResult['error'];
                    }
                }
                
                if (!$error) {
                    db()->update('users', [
                        'name' => $name,
                        'email' => $email,
                        'avatar' => $avatarPath
                    ], 'id = ?', [$_SESSION['user_id']]);
                    
                    // Update session
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                    
                    $message = 'Профиль успешно обновлен';
                    
                    // Refresh user data
                    $user = db_fetch_one("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
                }
            }
        } else {
            $error = 'Заполните все обязательные поля';
        }
        
    } elseif ($action === 'change_password') {
        // Change password
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if ($currentPassword && $newPassword && $confirmPassword) {
            if (!password_verify($currentPassword, $user['password'])) {
                $error = 'Неверный текущий пароль';
            } elseif ($newPassword !== $confirmPassword) {
                $error = 'Пароли не совпадают';
            } elseif (strlen($newPassword) < 6) {
                $error = 'Пароль должен содержать минимум 6 символов';
            } else {
                db()->update('users', [
                    'password' => password_hash($newPassword, PASSWORD_DEFAULT)
                ], 'id = ?', [$_SESSION['user_id']]);
                
                $message = 'Пароль успешно изменен';
            }
        } else {
            $error = 'Заполните все поля';
        }
        
    } elseif ($action === 'delete_avatar') {
        // Delete avatar
        if ($user['avatar']) {
            ImageUpload::deleteFile($user['avatar']);
            db()->update('users', ['avatar' => null], 'id = ?', [$_SESSION['user_id']]);
            $message = 'Аватар удален';
            $user['avatar'] = null;
        }
    }
}

// Page title
$pageTitle = 'Настройки профиля';

// Section 1: Header
ob_start();
?>
<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 60px 20px; color: white; text-align: center;">
    <h1 style="font-size: 36px; font-weight: 700; margin-bottom: 10px;">Настройки</h1>
    <p style="font-size: 18px; opacity: 0.9;">Управление профилем и настройками аккаунта</p>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: Settings forms
ob_start();
?>
<div style="padding: 40px 20px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <?php if ($message): ?>
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; 
                    padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; 
                    padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>
        
        <div style="display: grid; gap: 30px;">
            <!-- Profile settings -->
            <div style="background: var(--bg-primary); border: 1px solid var(--border-color); 
                        border-radius: 12px; padding: 30px;">
                <h2 style="font-size: 24px; margin-bottom: 25px;">
                    <i class="fas fa-user-edit"></i> Настройки профиля
                </h2>
                
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_profile">
                    
                    <div style="display: grid; gap: 20px;">
                        <!-- Avatar -->
                        <div>
                            <label style="display: block; margin-bottom: 10px; font-weight: 600;">
                                Аватар
                            </label>
                            <div style="display: flex; align-items: center; gap: 20px;">
                                <img src="<?= htmlspecialchars(get_user_avatar($_SESSION['user_id'], $user['name'], $user['avatar'])) ?>" 
                                     alt="Avatar" 
                                     style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
                                <div>
                                    <input type="file" 
                                           name="avatar" 
                                           accept="image/jpeg,image/png,image/gif,image/webp"
                                           style="margin-bottom: 10px;">
                                    <p style="font-size: 14px; color: var(--text-secondary); margin: 0;">
                                        JPG, PNG, GIF или WebP. Макс. размер: 5MB
                                    </p>
                                    <?php if ($user['avatar']): ?>
                                    <button type="submit" name="action" value="delete_avatar" 
                                            style="margin-top: 10px; padding: 5px 10px; background: #dc3545; 
                                                   color: white; border: none; border-radius: 4px; 
                                                   font-size: 14px; cursor: pointer;">
                                        Удалить аватар
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Name -->
                        <div>
                            <label for="name" style="display: block; margin-bottom: 8px; font-weight: 600;">
                                Имя <span style="color: #dc3545;">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="<?= htmlspecialchars($user['name']) ?>"
                                   required
                                   style="width: 100%; padding: 12px; border: 1px solid var(--border-color); 
                                          border-radius: 8px; font-size: 16px;">
                        </div>
                        
                        <!-- Email -->
                        <div>
                            <label for="email" style="display: block; margin-bottom: 8px; font-weight: 600;">
                                Email <span style="color: #dc3545;">*</span>
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="<?= htmlspecialchars($user['email']) ?>"
                                   required
                                   style="width: 100%; padding: 12px; border: 1px solid var(--border-color); 
                                          border-radius: 8px; font-size: 16px;">
                        </div>
                        
                        <button type="submit" 
                                style="padding: 12px 30px; background: #007bff; color: white; 
                                       border: none; border-radius: 8px; font-size: 16px; 
                                       font-weight: 600; cursor: pointer;">
                            <i class="fas fa-save"></i> Сохранить изменения
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Password change -->
            <div style="background: var(--bg-primary); border: 1px solid var(--border-color); 
                        border-radius: 12px; padding: 30px;">
                <h2 style="font-size: 24px; margin-bottom: 25px;">
                    <i class="fas fa-lock"></i> Изменить пароль
                </h2>
                
                <form method="POST">
                    <input type="hidden" name="action" value="change_password">
                    
                    <div style="display: grid; gap: 20px;">
                        <div>
                            <label for="current_password" style="display: block; margin-bottom: 8px; font-weight: 600;">
                                Текущий пароль
                            </label>
                            <input type="password" 
                                   id="current_password" 
                                   name="current_password" 
                                   required
                                   style="width: 100%; padding: 12px; border: 1px solid var(--border-color); 
                                          border-radius: 8px; font-size: 16px;">
                        </div>
                        
                        <div>
                            <label for="new_password" style="display: block; margin-bottom: 8px; font-weight: 600;">
                                Новый пароль
                            </label>
                            <input type="password" 
                                   id="new_password" 
                                   name="new_password" 
                                   required
                                   minlength="6"
                                   style="width: 100%; padding: 12px; border: 1px solid var(--border-color); 
                                          border-radius: 8px; font-size: 16px;">
                        </div>
                        
                        <div>
                            <label for="confirm_password" style="display: block; margin-bottom: 8px; font-weight: 600;">
                                Подтвердите новый пароль
                            </label>
                            <input type="password" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   required
                                   minlength="6"
                                   style="width: 100%; padding: 12px; border: 1px solid var(--border-color); 
                                          border-radius: 8px; font-size: 16px;">
                        </div>
                        
                        <button type="submit" 
                                style="padding: 12px 30px; background: #28a745; color: white; 
                                       border: none; border-radius: 8px; font-size: 16px; 
                                       font-weight: 600; cursor: pointer;">
                            <i class="fas fa-key"></i> Изменить пароль
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Email notifications -->
            <div style="background: var(--bg-primary); border: 1px solid var(--border-color); 
                        border-radius: 12px; padding: 30px;">
                <h2 style="font-size: 24px; margin-bottom: 25px;">
                    <i class="fas fa-bell"></i> Уведомления
                </h2>
                
                <div style="display: grid; gap: 15px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" checked style="width: 20px; height: 20px;">
                        <span>Новые комментарии к моим материалам</span>
                    </label>
                    
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" checked style="width: 20px; height: 20px;">
                        <span>Еженедельный дайджест новостей</span>
                    </label>
                    
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" style="width: 20px; height: 20px;">
                        <span>Специальные предложения и акции</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent2 = ob_get_clean();

// Include template
include 'real_template_local.php';
?>