<?php
// Note: Current database doesn't have avatar field
// This is a placeholder for future implementation
?>

<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> 
    Функция загрузки аватара будет доступна в следующей версии системы.
</div>

<div class="text-center mb-4">
    <div class="avatar-placeholder" style="width: 150px; height: 150px; background-color: #e9ecef; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
        <i class="fas fa-user" style="font-size: 60px; color: #6c757d;"></i>
    </div>
</div>

<p class="text-center text-muted">
    Аватар позволит другим пользователям узнавать вас на сайте.
</p>

<!-- Future implementation
<form action="/pages/account/avatar/avatar-upload.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
    
    <div class="mb-3">
        <label for="avatar" class="form-label">Выберите изображение</label>
        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*" required>
        <div class="form-text">Поддерживаются форматы: JPG, PNG, GIF. Максимальный размер: 2MB</div>
    </div>
    
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-upload"></i> Загрузить аватар
        </button>
        <a href="/account" class="btn btn-secondary">
            Отмена
        </a>
    </div>
</form>
-->