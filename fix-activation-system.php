<?php
/**
 * Account Activation System Fix
 * Comprehensive tool to diagnose and fix activation issues
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$action = $_GET['action'] ?? 'overview';
$message = '';
$messageType = 'info';

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔧 Account Activation System Fix</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-tools me-2"></i>Account Activation System Fix</h4>
                    </div>
                    <div class="card-body">
                        
                        <?php if ($action === 'activate_user' && isset($_POST['email'])): ?>
                            <?php
                            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
                            
                            if ($email) {
                                try {
                                    $stmt = $connection->prepare("UPDATE users SET is_active = 1, activation_token = NULL, activation_link = NULL WHERE email = ?");
                                    $stmt->bind_param("s", $email);
                                    
                                    if ($stmt->execute() && $stmt->affected_rows > 0) {
                                        $message = "✅ Пользователь $email успешно активирован!";
                                        $messageType = 'success';
                                    } else {
                                        $message = "❌ Пользователь не найден или уже активирован.";
                                        $messageType = 'warning';
                                    }
                                    $stmt->close();
                                } catch (Exception $e) {
                                    $message = "❌ Ошибка: " . $e->getMessage();
                                    $messageType = 'danger';
                                }
                            } else {
                                $message = "❌ Неверный формат email.";
                                $messageType = 'danger';
                            }
                            ?>
                        <?php endif; ?>

                        <?php if ($action === 'activate_all'): ?>
                            <?php
                            try {
                                $stmt = $connection->prepare("UPDATE users SET is_active = 1 WHERE is_active = 0");
                                $stmt->execute();
                                $affected = $stmt->affected_rows;
                                $stmt->close();
                                
                                if ($affected > 0) {
                                    $message = "✅ Активировано $affected пользователей!";
                                    $messageType = 'success';
                                } else {
                                    $message = "ℹ️ Все пользователи уже активированы.";
                                    $messageType = 'info';
                                }
                            } catch (Exception $e) {
                                $message = "❌ Ошибка: " . $e->getMessage();
                                $messageType = 'danger';
                            }
                            ?>
                        <?php endif; ?>

                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?= $messageType ?>" role="alert">
                                <?= $message ?>
                            </div>
                        <?php endif; ?>

                        <!-- Navigation -->
                        <div class="mb-4">
                            <div class="btn-group" role="group">
                                <a href="?action=overview" class="btn btn-outline-primary <?= $action === 'overview' ? 'active' : '' ?>">
                                    <i class="fas fa-chart-bar me-1"></i>Overview
                                </a>
                                <a href="?action=users" class="btn btn-outline-primary <?= $action === 'users' ? 'active' : '' ?>">
                                    <i class="fas fa-users me-1"></i>Users
                                </a>
                                <a href="?action=tools" class="btn btn-outline-primary <?= $action === 'tools' ? 'active' : '' ?>">
                                    <i class="fas fa-tools me-1"></i>Tools
                                </a>
                            </div>
                        </div>

                        <?php if ($action === 'overview'): ?>
                            <!-- System Overview -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-success text-white">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <i class="fas fa-check-circle me-2"></i>Активированы
                                            </h5>
                                            <?php
                                            $result = $connection->query("SELECT COUNT(*) as count FROM users WHERE is_active = 1");
                                            $activeCount = $result ? $result->fetch_assoc()['count'] : 0;
                                            ?>
                                            <p class="card-text display-6"><?= $activeCount ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <i class="fas fa-clock me-2"></i>Не активированы
                                            </h5>
                                            <?php
                                            $result = $connection->query("SELECT COUNT(*) as count FROM users WHERE is_active = 0");
                                            $inactiveCount = $result ? $result->fetch_assoc()['count'] : 0;
                                            ?>
                                            <p class="card-text display-6"><?= $inactiveCount ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-info text-white">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <i class="fas fa-users me-2"></i>Всего
                                            </h5>
                                            <?php
                                            $result = $connection->query("SELECT COUNT(*) as count FROM users");
                                            $totalCount = $result ? $result->fetch_assoc()['count'] : 0;
                                            ?>
                                            <p class="card-text display-6"><?= $totalCount ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <h5>🔧 Quick Actions</h5>
                                <div class="d-grid gap-2 d-md-block">
                                    <a href="?action=activate_all" class="btn btn-warning" 
                                       onclick="return confirm('Активировать всех неактивированных пользователей?')">
                                        <i class="fas fa-magic me-1"></i>Активировать всех
                                    </a>
                                    <a href="/activate-user-manual.php" class="btn btn-success">
                                        <i class="fas fa-user-check me-1"></i>Активировать одного
                                    </a>
                                    <a href="/pages/registration/resend_activation/resend_activation.php" class="btn btn-primary">
                                        <i class="fas fa-envelope me-1"></i>Отправить активацию
                                    </a>
                                </div>
                            </div>

                        <?php elseif ($action === 'users'): ?>
                            <!-- Users List -->
                            <h5><i class="fas fa-users me-2"></i>Список пользователей</h5>
                            
                            <?php
                            $limit = 20;
                            $page = max(1, intval($_GET['page'] ?? 1));
                            $offset = ($page - 1) * $limit;
                            
                            $usersQuery = "SELECT id, email, is_active, created_at, activation_token 
                                          FROM users 
                                          ORDER BY created_at DESC 
                                          LIMIT $limit OFFSET $offset";
                            $usersResult = $connection->query($usersQuery);
                            
                            if ($usersResult && $usersResult->num_rows > 0):
                            ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Email</th>
                                                <th>Статус</th>
                                                <th>Дата создания</th>
                                                <th>Токен</th>
                                                <th>Действия</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($user = $usersResult->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($user['id']) ?></td>
                                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                                    <td>
                                                        <?php if ($user['is_active'] == 1): ?>
                                                            <span class="badge bg-success">✅ Активирован</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning">❌ Не активирован</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                                                    <td>
                                                        <?php if (!empty($user['activation_token'])): ?>
                                                            <small class="text-muted">Есть токен</small>
                                                        <?php else: ?>
                                                            <small class="text-muted">Без токена</small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($user['is_active'] == 0): ?>
                                                            <form method="POST" action="?action=activate_user" class="d-inline">
                                                                <input type="hidden" name="email" value="<?= htmlspecialchars($user['email']) ?>">
                                                                <button type="submit" class="btn btn-sm btn-success" 
                                                                        onclick="return confirm('Активировать пользователя?')">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">Пользователи не найдены.</div>
                            <?php endif; ?>

                        <?php elseif ($action === 'tools'): ?>
                            <!-- Tools -->
                            <h5><i class="fas fa-tools me-2"></i>Инструменты активации</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">Активировать по email</h6>
                                        </div>
                                        <div class="card-body">
                                            <form method="POST" action="?action=activate_user">
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email пользователя:</label>
                                                    <input type="email" class="form-control" id="email" name="email" required>
                                                </div>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-user-check me-1"></i>Активировать
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">Системные ссылки</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-grid gap-2">
                                                <a href="/pages/registration/activate_account/activate_account.php" class="btn btn-outline-primary">
                                                    <i class="fas fa-link me-1"></i>Страница активации
                                                </a>
                                                <a href="/pages/registration/resend_activation/resend_activation.php" class="btn btn-outline-info">
                                                    <i class="fas fa-envelope me-1"></i>Повторная отправка
                                                </a>
                                                <a href="/login" class="btn btn-outline-success">
                                                    <i class="fas fa-sign-in-alt me-1"></i>Страница входа
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                    <div class="card-footer text-muted">
                        <div class="d-flex justify-content-between align-items-center">
                            <small>
                                <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                                Удалите этот файл после использования!
                            </small>
                            <a href="/" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-home me-1"></i>На главную
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>