<?php
/**
 * Manual User Activation Script
 * Use this to manually activate a user account
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Configuration - UPDATE THESE VALUES
$userEmail = 'your-email@example.com'; // Replace with your actual email
$activateUser = false; // Set to true to actually activate

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual User Activation</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .alert { padding: 15px; margin: 10px 0; border-radius: 4px; }
        .alert-info { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        .alert-success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .alert-warning { background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
        .form-group { margin: 15px 0; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .btn { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <h1>🔧 Manual User Activation</h1>
    
    <?php if (isset($_POST['activate_email'])): ?>
        <?php
        $emailToActivate = filter_input(INPUT_POST, 'activate_email', FILTER_VALIDATE_EMAIL);
        
        if ($emailToActivate) {
            try {
                // Check if user exists
                $checkStmt = $connection->prepare("SELECT id, email, is_active FROM users WHERE email = ?");
                $checkStmt->bind_param("s", $emailToActivate);
                $checkStmt->execute();
                $result = $checkStmt->get_result();
                
                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    
                    if ($user['is_active'] == 1) {
                        echo '<div class="alert alert-info">✅ Пользователь уже активирован!</div>';
                    } else {
                        // Activate the user
                        $updateStmt = $connection->prepare("UPDATE users SET is_active = 1, activation_token = NULL, activation_link = NULL WHERE email = ?");
                        $updateStmt->bind_param("s", $emailToActivate);
                        
                        if ($updateStmt->execute()) {
                            echo '<div class="alert alert-success">✅ Пользователь успешно активирован! Теперь вы можете войти в систему.</div>';
                        } else {
                            echo '<div class="alert alert-warning">❌ Ошибка при активации пользователя.</div>';
                        }
                        $updateStmt->close();
                    }
                } else {
                    echo '<div class="alert alert-warning">❌ Пользователь с таким email не найден.</div>';
                }
                $checkStmt->close();
                
            } catch (Exception $e) {
                echo '<div class="alert alert-warning">❌ Ошибка: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        } else {
            echo '<div class="alert alert-warning">❌ Неверный формат email.</div>';
        }
        ?>
    <?php endif; ?>

    <div class="alert alert-info">
        <h4>📋 Инструкции:</h4>
        <ol>
            <li>Введите email аккаунта, который нужно активировать</li>
            <li>Нажмите "Активировать пользователя"</li>
            <li>После активации вы сможете войти в систему</li>
            <li>Удалите этот файл после использования для безопасности</li>
        </ol>
    </div>

    <form method="POST" action="">
        <div class="form-group">
            <label for="activate_email">Email пользователя для активации:</label>
            <input type="email" id="activate_email" name="activate_email" class="form-control" 
                   placeholder="user@example.com" required>
        </div>
        <button type="submit" class="btn">🚀 Активировать пользователя</button>
    </form>

    <h3>📊 Информация о пользователях</h3>
    <?php
    try {
        $usersQuery = "SELECT id, email, is_active, created_at FROM users ORDER BY created_at DESC LIMIT 10";
        $usersResult = $connection->query($usersQuery);
        
        if ($usersResult && $usersResult->num_rows > 0) {
            echo '<table>';
            echo '<tr><th>ID</th><th>Email</th><th>Статус</th><th>Дата создания</th></tr>';
            
            while ($row = $usersResult->fetch_assoc()) {
                $status = $row['is_active'] == 1 ? '✅ Активирован' : '❌ Не активирован';
                $statusClass = $row['is_active'] == 1 ? 'style="color: green;"' : 'style="color: red;"';
                
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                echo '<td ' . $statusClass . '>' . $status . '</td>';
                echo '<td>' . htmlspecialchars($row['created_at']) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<div class="alert alert-info">Пользователи не найдены.</div>';
        }
    } catch (Exception $e) {
        echo '<div class="alert alert-warning">Ошибка при получении списка пользователей: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    ?>

    <div class="alert alert-warning">
        <h4>⚠️ Безопасность:</h4>
        <p>После использования обязательно удалите этот файл из корневой директории сайта!</p>
        <p>Команда для удаления: <code>rm activate-user-manual.php</code></p>
    </div>

</body>
</html>