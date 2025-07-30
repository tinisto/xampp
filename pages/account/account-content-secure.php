<?php
require_once __DIR__ . '/../../includes/init.php';

// Get user information
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$userOccupation = $_SESSION['occupation'] ?? '';
$userName = h($_SESSION['firstname'] ?? '') . ' ' . h($_SESSION['lastname'] ?? '');
$userEmail = h($_SESSION['email'] ?? '');
?>

<div class="container my-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Личный кабинет</h5>
                    <?php if ($isAdmin): ?>
                        <span class="badge bg-warning text-dark">Администратор</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <p class="small text-muted">
                        <?= $userName ?><br>
                        <?= $userEmail ?>
                    </p>
                </div>
                <div class="list-group list-group-flush">
                    <a href="/account" class="list-group-item list-group-item-action">
                        <i class="fa fa-user"></i> Мой профиль
                    </a>
                    <a href="/account/personal-data-change" class="list-group-item list-group-item-action">
                        <i class="fa fa-edit"></i> Изменить данные
                    </a>
                    <a href="/account/password-change" class="list-group-item list-group-item-action">
                        <i class="fa fa-key"></i> Изменить пароль
                    </a>
                    <a href="/account/avatar" class="list-group-item list-group-item-action">
                        <i class="fa fa-image"></i> Изменить аватар
                    </a>
                    
                    <?php if ($userOccupation === 'Представитель ВУЗа' || 
                              $userOccupation === 'Представитель ССУЗа' || 
                              $userOccupation === 'Представитель школы'): ?>
                    <a href="/account/contact-info" class="list-group-item list-group-item-action">
                        <i class="fa fa-phone"></i> Контактная информация
                    </a>
                    <?php endif; ?>
                    
                    <a href="/account/comments-user" class="list-group-item list-group-item-action">
                        <i class="fa fa-comments"></i> Мои комментарии
                    </a>
                    <a href="/account/news-user" class="list-group-item list-group-item-action">
                        <i class="fa fa-newspaper"></i> Мои новости
                    </a>
                    
                    <?php if ($isAdmin): ?>
                    <!-- Admin-only sections -->
                    <div class="dropdown-divider"></div>
                    <h6 class="dropdown-header text-danger">Администрирование</h6>
                    <a href="/dashboard" class="list-group-item list-group-item-action text-danger">
                        <i class="fa fa-tachometer-alt"></i> Панель управления
                    </a>
                    <a href="/dashboard/users" class="list-group-item list-group-item-action text-danger">
                        <i class="fa fa-users"></i> Управление пользователями
                    </a>
                    <a href="/dashboard/logs" class="list-group-item list-group-item-action text-danger">
                        <i class="fa fa-file-alt"></i> Системные логи
                    </a>
                    <?php endif; ?>
                    
                    <div class="dropdown-divider"></div>
                    
                    <?php if (!$isAdmin): ?>
                    <a href="/account/delete-account" class="list-group-item list-group-item-action text-danger">
                        <i class="fa fa-trash"></i> Удалить аккаунт
                    </a>
                    <?php else: ?>
                    <div class="list-group-item disabled text-muted">
                        <i class="fa fa-trash"></i> Удалить аккаунт
                        <small class="d-block">Администраторы не могут удалить свой аккаунт</small>
                    </div>
                    <?php endif; ?>
                    
                    <a href="/logout" class="list-group-item list-group-item-action">
                        <i class="fa fa-sign-out-alt"></i> Выйти
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= h($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <?php unset($_SESSION['error']); ?>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= h($_SESSION['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <?php unset($_SESSION['success']); ?>
            </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <h4>Информация о профиле</h4>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Имя:</dt>
                        <dd class="col-sm-9"><?= $userName ?></dd>
                        
                        <dt class="col-sm-3">Email:</dt>
                        <dd class="col-sm-9"><?= $userEmail ?></dd>
                        
                        <dt class="col-sm-3">Роль:</dt>
                        <dd class="col-sm-9">
                            <?php if ($isAdmin): ?>
                                <span class="badge bg-danger">Администратор</span>
                            <?php else: ?>
                                <span class="badge bg-primary">Пользователь</span>
                            <?php endif; ?>
                        </dd>
                        
                        <?php if ($userOccupation): ?>
                        <dt class="col-sm-3">Занятость:</dt>
                        <dd class="col-sm-9"><?= h($userOccupation) ?></dd>
                        <?php endif; ?>
                        
                        <dt class="col-sm-3">Дата регистрации:</dt>
                        <dd class="col-sm-9"><?= h($_SESSION['created_at'] ?? 'Неизвестно') ?></dd>
                    </dl>
                    
                    <?php if ($isAdmin): ?>
                    <div class="alert alert-info mt-3">
                        <h5><i class="fa fa-info-circle"></i> Права администратора</h5>
                        <p>Как администратор, вы имеете доступ к:</p>
                        <ul class="mb-0">
                            <li>Панели управления сайтом</li>
                            <li>Управлению пользователями</li>
                            <li>Модерации контента</li>
                            <li>Системным логам и статистике</li>
                        </ul>
                        <p class="mb-0 mt-2"><strong>Примечание:</strong> Администраторы не могут удалить свой собственный аккаунт для безопасности системы.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>