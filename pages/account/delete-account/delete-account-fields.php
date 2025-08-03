<?php
// Check if user is admin
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

if ($isAdmin): ?>
    <div class="alert alert-warning">
        <h5 class="alert-heading">
            <i class="fas fa-shield-alt"></i> Защита администратора
        </h5>
        <p class="mb-0">Администраторы не могут удалить свой аккаунт в целях безопасности системы.</p>
    </div>
<?php else: ?>
    <div class="alert alert-danger">
        <h5 class="alert-heading">
            <i class="fas fa-exclamation-triangle"></i> Внимание!
        </h5>
        <p>Удаление аккаунта является необратимой операцией. Будут удалены:</p>
        <ul class="mb-0">
            <li>Все ваши личные данные</li>
            <li>История активности</li>
            <li>Все ваши комментарии</li>
            <li>Опубликованные новости (для представителей)</li>
        </ul>
    </div>
<?php endif; ?>

<?php if (!$isAdmin): ?>
<div class="card border-danger">
    <div class="card-body">
        <h5 class="card-title text-danger">Подтверждение удаления</h5>
        <p class="card-text">
            Если вы уверены, что хотите удалить свой аккаунт, введите ваш пароль для подтверждения.
        </p>
        
        <form action="/pages/account/delete-account/delete-account-process-simple.php" method="post" onsubmit="return confirm('Вы действительно хотите удалить свой аккаунт? Это действие необратимо!');">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            
            <div class="mb-3">
                <label for="password" class="form-label">Введите ваш пароль</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="confirm_delete" name="confirm_delete" required>
                <label class="form-check-label" for="confirm_delete">
                    Я понимаю, что удаление аккаунта необратимо
                </label>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Удалить аккаунт
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>