<?php
// Get current user data
$userId = $_SESSION['user_id'];

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$connection->set_charset("utf8mb4");

// Get user's current occupation
$stmt = $connection->prepare("SELECT occupation FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();
$connection->close();

// Note: firstname and lastname are not in the current database structure
// They will need to be added to the users table or stored elsewhere
?>

<form action="/pages/account/personal-data-change/personal-data-change-process-simple.php" method="post">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> 
        В текущей версии системы можно изменить только род деятельности. 
        Для изменения имени и фамилии обратитесь к администратору.
    </div>
    
    <div class="mb-4">
        <label for="occupation" class="form-label">Род деятельности</label>
        <select id="occupation" name="occupation" class="form-select" required>
            <option value="">Выберите род деятельности</option>
            <option value="Представитель ВУЗа" <?= $userData['occupation'] === 'Представитель ВУЗа' ? 'selected' : '' ?>>Представитель ВУЗа</option>
            <option value="Представитель ССУЗа" <?= $userData['occupation'] === 'Представитель ССУЗа' ? 'selected' : '' ?>>Представитель ССУЗа</option>
            <option value="Представитель школы" <?= $userData['occupation'] === 'Представитель школы' ? 'selected' : '' ?>>Представитель школы</option>
            <option value="Родитель" <?= $userData['occupation'] === 'Родитель' ? 'selected' : '' ?>>Родитель</option>
            <option value="Учащийся/учащаяся" <?= $userData['occupation'] === 'Учащийся/учащаяся' ? 'selected' : '' ?>>Учащийся</option>
            <option value="Другое" <?= $userData['occupation'] === 'Другое' ? 'selected' : '' ?>>Другое</option>
        </select>
    </div>
    
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Сохранить изменения
        </button>
    </div>
</form>