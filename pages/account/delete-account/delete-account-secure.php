<?php
require_once __DIR__ . '/../../../includes/init.php';
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/check_user.php';

// Check if user is admin trying to delete themselves
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$isSelfDeletion = true; // Account deletion is always for self

// Prevent admin self-deletion
if ($isAdmin && $isSelfDeletion) {
    $_SESSION['error'] = 'Администраторы не могут удалить свой собственный аккаунт. Обратитесь к другому администратору.';
    header('Location: /account');
    exit();
}
?>

<h1 class="mb-3">Удаление профиля</h1>

<div class="col-lg-6">
    <div class="warning-box">
        <p class="text-danger">
            <strong>Внимание!</strong> Удаление профиля приведет к полной потере всех ваших данных. 
            Это действие необратимо.
        </p>
        <p>Будут удалены:</p>
        <ul>
            <li>Ваш профиль и личная информация</li>
            <li>Все ваши комментарии</li>
            <li>Ваши новости и статьи</li>
            <li>Загруженные файлы и изображения</li>
        </ul>
    </div>

    <form method="post" action="/account/delete-account/delete-account-process.php" onsubmit="return confirm('Вы уверены, что хотите удалить свой аккаунт? Это действие необратимо!');">
        <?php echo csrf_field(); ?>
        
        <div class="mb-3">
            <label for="password" class="form-label">Введите пароль для подтверждения</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="confirm" name="confirm" required>
                <label class="form-check-label" for="confirm">
                    Я понимаю, что удаление аккаунта необратимо
                </label>
            </div>
        </div>
        
        <button type="submit" class="btn btn-danger">Удалить аккаунт навсегда</button>
        <a href="/account" class="btn btn-secondary ms-2">Отмена</a>
    </form>
</div>

<style>
.warning-box {
    background-color: #fff3cd;
    border: 1px solid #ffeeba;
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 20px;
}
</style>