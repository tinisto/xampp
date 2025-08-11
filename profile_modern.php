<?php
// User profile page
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// Fetch user data
$user = db_fetch_one("
    SELECT * FROM users 
    WHERE id = ?
", [$_SESSION['user_id']]);

if (!$user) {
    session_destroy();
    header('Location: /login');
    exit;
}

// Fetch user statistics
$newsCount = db_fetch_column("
    SELECT COUNT(*) FROM news 
    WHERE author_id = ?
", [$_SESSION['user_id']]) ?? 0;

$postsCount = db_fetch_column("
    SELECT COUNT(*) FROM posts 
    WHERE author_id = ?
", [$_SESSION['user_id']]) ?? 0;

// Page title
$pageTitle = 'Мой профиль';

// Section 1: Profile header
ob_start();
?>
<div style="padding: 60px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <div style="max-width: 1000px; margin: 0 auto;">
        <div style="display: flex; align-items: center; gap: 40px; flex-wrap: wrap;">
            <div style="width: 150px; height: 150px; background: white; border-radius: 50%; 
                        display: flex; align-items: center; justify-content: center; 
                        box-shadow: 0 8px 30px rgba(0,0,0,0.2);">
                <i class="fas fa-user" style="font-size: 80px; color: #667eea;"></i>
            </div>
            
            <div style="flex: 1;">
                <h1 style="font-size: 36px; font-weight: 700; margin-bottom: 10px;">
                    <?php 
                        $displayName = '';
                        if (!empty($user['first_name']) || !empty($user['last_name'])) {
                            $displayName = trim($user['first_name'] . ' ' . $user['last_name']);
                        }
                        if (empty($displayName)) {
                            $displayName = explode('@', $user['email'])[0];
                        }
                        echo htmlspecialchars($displayName);
                    ?>
                </h1>
                <p style="font-size: 18px; opacity: 0.9; margin-bottom: 20px;">
                    <?= htmlspecialchars($user['email']) ?>
                </p>
                
                <div style="display: flex; gap: 30px; flex-wrap: wrap;">
                    <div>
                        <div style="font-size: 28px; font-weight: 700;"><?= $newsCount ?></div>
                        <div style="font-size: 14px; opacity: 0.8;">Новостей</div>
                    </div>
                    <div>
                        <div style="font-size: 28px; font-weight: 700;"><?= $postsCount ?></div>
                        <div style="font-size: 14px; opacity: 0.8;">Статей</div>
                    </div>
                    <div>
                        <div style="font-size: 28px; font-weight: 700;">
                            <?= date('d.m.Y', strtotime($user['created_at'])) ?>
                        </div>
                        <div style="font-size: 14px; opacity: 0.8;">Дата регистрации</div>
                    </div>
                </div>
            </div>
            
            <div>
                <a href="/profile/edit" 
                   style="display: inline-block; padding: 12px 30px; background: white; color: #667eea; 
                          border-radius: 8px; text-decoration: none; font-weight: 600; transition: transform 0.2s;"
                   onmouseover="this.style.transform='translateY(-2px)'"
                   onmouseout="this.style.transform='translateY(0)'">
                    <i class="fas fa-edit"></i> Редактировать профиль
                </a>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: Profile navigation
ob_start();
?>
<div style="padding: 20px; background: white; border-bottom: 1px solid #e9ecef;">
    <div style="max-width: 1000px; margin: 0 auto;">
        <div style="display: flex; gap: 30px; overflow-x: auto;">
            <a href="/profile" 
               style="padding: 10px 0; color: #667eea; text-decoration: none; font-weight: 600; 
                      border-bottom: 3px solid #667eea;">
                Обзор
            </a>
            <a href="/profile/favorites" 
               style="padding: 10px 0; color: #666; text-decoration: none; font-weight: 500;">
                Избранное
            </a>
            <a href="/profile/settings" 
               style="padding: 10px 0; color: #666; text-decoration: none; font-weight: 500;">
                Настройки
            </a>
            <a href="/profile/security" 
               style="padding: 10px 0; color: #666; text-decoration: none; font-weight: 500;">
                Безопасность
            </a>
        </div>
    </div>
</div>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Profile content
ob_start();
?>
<div style="padding: 40px 20px; background: #f8f9fa;">
    <div style="max-width: 1000px; margin: 0 auto;">
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
            
            <!-- Left sidebar -->
            <div>
                <!-- Quick actions -->
                <div style="background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px;">
                    <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 20px;">Быстрые действия</h3>
                    <div style="display: grid; gap: 10px;">
                        <a href="/news/create" 
                           style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; 
                                  background: #e3f2fd; color: #1976d2; border-radius: 8px; 
                                  text-decoration: none; transition: all 0.2s;"
                           onmouseover="this.style.background='#bbdefb'"
                           onmouseout="this.style.background='#e3f2fd'">
                            <i class="fas fa-plus"></i> Создать новость
                        </a>
                        <a href="/post/create" 
                           style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; 
                                  background: #f3e5f5; color: #7b1fa2; border-radius: 8px; 
                                  text-decoration: none; transition: all 0.2s;"
                           onmouseover="this.style.background='#e1bee7'"
                           onmouseout="this.style.background='#f3e5f5'">
                            <i class="fas fa-pen"></i> Написать статью
                        </a>
                        <a href="/favorites" 
                           style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; 
                                  background: #fce4ec; color: #c2185b; border-radius: 8px; 
                                  text-decoration: none; transition: all 0.2s;"
                           onmouseover="this.style.background='#f8bbd0'"
                           onmouseout="this.style.background='#fce4ec'">
                            <i class="fas fa-heart"></i> Избранное
                        </a>
                    </div>
                </div>
                
                <!-- Account info -->
                <div style="background: white; border-radius: 12px; padding: 25px;">
                    <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 20px;">Информация</h3>
                    <div style="display: grid; gap: 15px; font-size: 14px;">
                        <div>
                            <div style="color: #999;">Статус</div>
                            <div style="color: #333; font-weight: 600;">
                                <?= $user['role'] === 'admin' ? 'Администратор' : 'Пользователь' ?>
                            </div>
                        </div>
                        <div>
                            <div style="color: #999;">Последний вход</div>
                            <div style="color: #333; font-weight: 600;">
                                <?php 
                                    $lastLogin = $user['last_login_at'] ?? $user['last_login'] ?? null;
                                    echo $lastLogin ? date('d.m.Y H:i', strtotime($lastLogin)) : 'Сейчас';
                                ?>
                            </div>
                        </div>
                        <div>
                            <div style="color: #999;">Аккаунт активен</div>
                            <div style="color: #333; font-weight: 600;">
                                <i class="fas fa-check-circle" style="color: #4caf50;"></i> Да
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right content -->
            <div>
                <!-- Recent activity -->
                <div style="background: white; border-radius: 12px; padding: 30px; margin-bottom: 20px;">
                    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 25px;">Последняя активность</h2>
                    
                    <?php if ($newsCount > 0 || $postsCount > 0): ?>
                        <div style="display: grid; gap: 20px;">
                            <!-- Sample activity items -->
                            <div style="display: flex; gap: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                                <div style="width: 40px; height: 40px; background: #e3f2fd; border-radius: 50%; 
                                            display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fas fa-newspaper" style="color: #1976d2;"></i>
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; margin-bottom: 5px;">Опубликована новость</div>
                                    <div style="color: #666; font-size: 14px;">
                                        "Изменения в правилах приема в ВУЗы" • 2 часа назад
                                    </div>
                                </div>
                            </div>
                            
                            <div style="display: flex; gap: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                                <div style="width: 40px; height: 40px; background: #f3e5f5; border-radius: 50%; 
                                            display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fas fa-book-open" style="color: #7b1fa2;"></i>
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; margin-bottom: 5px;">Написана статья</div>
                                    <div style="color: #666; font-size: 14px;">
                                        "Как выбрать университет: полное руководство" • Вчера
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 40px; color: #999;">
                            <i class="fas fa-info-circle" style="font-size: 48px; margin-bottom: 20px;"></i>
                            <p>У вас пока нет активности</p>
                            <a href="/news/create" style="color: #667eea; text-decoration: none;">
                                Создать первую публикацию →
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Profile completion -->
                <div style="background: white; border-radius: 12px; padding: 30px;">
                    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 25px;">Заполнение профиля</h2>
                    
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span>Прогресс</span>
                            <span style="font-weight: 600;">60%</span>
                        </div>
                        <div style="background: #e9ecef; height: 8px; border-radius: 4px; overflow: hidden;">
                            <div style="background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); 
                                        height: 100%; width: 60%; transition: width 0.3s;"></div>
                        </div>
                    </div>
                    
                    <div style="display: grid; gap: 15px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-check-circle" style="color: #4caf50;"></i>
                            <span>Основная информация заполнена</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-check-circle" style="color: #4caf50;"></i>
                            <span>Email подтвержден</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="far fa-circle" style="color: #ccc;"></i>
                            <span>Добавить фото профиля</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="far fa-circle" style="color: #ccc;"></i>
                            <span>Указать интересы</span>
                        </div>
                    </div>
                    
                    <a href="/profile/edit" 
                       style="display: inline-block; margin-top: 20px; color: #667eea; 
                              text-decoration: none; font-weight: 600;">
                        Заполнить профиль →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();

// Other sections empty
$greyContent4 = '';
$greyContent5 = '';
$greyContent6 = '';
$blueContent = '';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/template.php';
?>