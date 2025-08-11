<?php
// Admin dashboard
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /login');
    exit;
}

// Get statistics
$stats = [
    'users' => db_fetch_column("SELECT COUNT(*) FROM users"),
    'news' => db_fetch_column("SELECT COUNT(*) FROM news"),
    'posts' => db_fetch_column("SELECT COUNT(*) FROM posts"),
    'comments' => db_fetch_column("SELECT COUNT(*) FROM comments"),
    'vpo' => db_fetch_column("SELECT COUNT(*) FROM vpo"),
    'spo' => db_fetch_column("SELECT COUNT(*) FROM spo"),
    'schools' => db_fetch_column("SELECT COUNT(*) FROM schools"),
    'favorites' => db_fetch_column("SELECT COUNT(*) FROM favorites"),
];

// Recent activity
$recentUsers = db_fetch_all("
    SELECT id, name, email, created_at 
    FROM users 
    ORDER BY created_at DESC 
    LIMIT 5
");

$recentComments = db_fetch_all("
    SELECT c.*, u.name as user_name
    FROM comments c
    JOIN users u ON c.user_id = u.id
    ORDER BY c.created_at DESC
    LIMIT 5
");

// Page title
$pageTitle = 'Панель администратора';

// Section 1: Header
ob_start();
?>
<div style="padding: 40px 20px; background: linear-gradient(135deg, #1e272e 0%, #2d3436 100%); color: white;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 10px;">Панель администратора</h1>
        <p style="font-size: 18px; opacity: 0.8;">
            Добро пожаловать, <?= htmlspecialchars($_SESSION['user_name']) ?>!
        </p>
    </div>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: Statistics
ob_start();
?>
<div style="padding: 40px 20px; background: white;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 30px;">Статистика</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; 
                        padding: 30px; border-radius: 12px; text-align: center;">
                <i class="fas fa-users" style="font-size: 36px; margin-bottom: 15px;"></i>
                <div style="font-size: 36px; font-weight: 700;"><?= number_format($stats['users']) ?></div>
                <div style="font-size: 14px; opacity: 0.9;">Пользователей</div>
            </div>
            
            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; 
                        padding: 30px; border-radius: 12px; text-align: center;">
                <i class="fas fa-newspaper" style="font-size: 36px; margin-bottom: 15px;"></i>
                <div style="font-size: 36px; font-weight: 700;"><?= number_format($stats['news']) ?></div>
                <div style="font-size: 14px; opacity: 0.9;">Новостей</div>
            </div>
            
            <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; 
                        padding: 30px; border-radius: 12px; text-align: center;">
                <i class="fas fa-book-open" style="font-size: 36px; margin-bottom: 15px;"></i>
                <div style="font-size: 36px; font-weight: 700;"><?= number_format($stats['posts']) ?></div>
                <div style="font-size: 14px; opacity: 0.9;">Статей</div>
            </div>
            
            <div style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; 
                        padding: 30px; border-radius: 12px; text-align: center;">
                <i class="fas fa-comments" style="font-size: 36px; margin-bottom: 15px;"></i>
                <div style="font-size: 36px; font-weight: 700;"><?= number_format($stats['comments']) ?></div>
                <div style="font-size: 14px; opacity: 0.9;">Комментариев</div>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
            <div style="background: #f8f9fa; padding: 25px; border-radius: 12px; text-align: center;">
                <i class="fas fa-university" style="font-size: 32px; color: #1e3c72; margin-bottom: 10px;"></i>
                <div style="font-size: 28px; font-weight: 700; color: #333;"><?= number_format($stats['vpo']) ?></div>
                <div style="font-size: 14px; color: #666;">ВУЗов</div>
            </div>
            
            <div style="background: #f8f9fa; padding: 25px; border-radius: 12px; text-align: center;">
                <i class="fas fa-school" style="font-size: 32px; color: #00b09b; margin-bottom: 10px;"></i>
                <div style="font-size: 28px; font-weight: 700; color: #333;"><?= number_format($stats['spo']) ?></div>
                <div style="font-size: 14px; color: #666;">Колледжей</div>
            </div>
            
            <div style="background: #f8f9fa; padding: 25px; border-radius: 12px; text-align: center;">
                <i class="fas fa-graduation-cap" style="font-size: 32px; color: #f5576c; margin-bottom: 10px;"></i>
                <div style="font-size: 28px; font-weight: 700; color: #333;"><?= number_format($stats['schools']) ?></div>
                <div style="font-size: 14px; color: #666;">Школ</div>
            </div>
            
            <div style="background: #f8f9fa; padding: 25px; border-radius: 12px; text-align: center;">
                <i class="fas fa-heart" style="font-size: 32px; color: #e91e63; margin-bottom: 10px;"></i>
                <div style="font-size: 28px; font-weight: 700; color: #333;"><?= number_format($stats['favorites']) ?></div>
                <div style="font-size: 14px; color: #666;">Избранных</div>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Quick actions
ob_start();
?>
<div style="padding: 40px 20px; background: #f8f9fa;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 30px;">Быстрые действия</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <a href="/admin/users" 
               style="background: white; padding: 25px; border-radius: 12px; text-decoration: none; 
                      color: #333; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: all 0.3s;"
               onmouseover="this.style.transform='translateY(-5px)'"
               onmouseout="this.style.transform='translateY(0)'">
                <i class="fas fa-users" style="font-size: 32px; color: #667eea; margin-bottom: 15px;"></i>
                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 10px;">Управление пользователями</h3>
                <p style="color: #666; font-size: 14px;">Просмотр и редактирование пользователей</p>
            </a>
            
            <a href="/admin/content" 
               style="background: white; padding: 25px; border-radius: 12px; text-decoration: none; 
                      color: #333; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: all 0.3s;"
               onmouseover="this.style.transform='translateY(-5px)'"
               onmouseout="this.style.transform='translateY(0)'">
                <i class="fas fa-file-alt" style="font-size: 32px; color: #f5576c; margin-bottom: 15px;"></i>
                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 10px;">Управление контентом</h3>
                <p style="color: #666; font-size: 14px;">Новости, статьи и страницы</p>
            </a>
            
            <a href="/admin/institutions" 
               style="background: white; padding: 25px; border-radius: 12px; text-decoration: none; 
                      color: #333; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: all 0.3s;"
               onmouseover="this.style.transform='translateY(-5px)'"
               onmouseout="this.style.transform='translateY(0)'">
                <i class="fas fa-building" style="font-size: 32px; color: #00b09b; margin-bottom: 15px;"></i>
                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 10px;">Учебные заведения</h3>
                <p style="color: #666; font-size: 14px;">ВУЗы, колледжи и школы</p>
            </a>
            
            <a href="/admin/comments" 
               style="background: white; padding: 25px; border-radius: 12px; text-decoration: none; 
                      color: #333; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: all 0.3s;"
               onmouseover="this.style.transform='translateY(-5px)'"
               onmouseout="this.style.transform='translateY(0)'">
                <i class="fas fa-comments" style="font-size: 32px; color: #4facfe; margin-bottom: 15px;"></i>
                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 10px;">Модерация комментариев</h3>
                <p style="color: #666; font-size: 14px;">Просмотр и модерация комментариев</p>
            </a>
            
            <a href="/analytics" 
               style="background: white; padding: 25px; border-radius: 12px; text-decoration: none; 
                      color: #333; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: all 0.3s;"
               onmouseover="this.style.transform='translateY(-5px)'"
               onmouseout="this.style.transform='translateY(0)'">
                <i class="fas fa-chart-line" style="font-size: 32px; color: #6f42c1; margin-bottom: 15px;"></i>
                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 10px;">Аналитика и статистика</h3>
                <p style="color: #666; font-size: 14px;">Подробная аналитика использования портала</p>
            </a>
        </div>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();

// Section 4: Recent activity
ob_start();
?>
<div style="padding: 40px 20px; background: white;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
            <!-- Recent users -->
            <div>
                <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 20px;">Новые пользователи</h3>
                <div style="background: #f8f9fa; border-radius: 12px; overflow: hidden;">
                    <?php foreach ($recentUsers as $user): ?>
                    <div style="padding: 15px 20px; border-bottom: 1px solid #e9ecef;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong><?= htmlspecialchars($user['name']) ?></strong>
                                <div style="color: #666; font-size: 14px;"><?= htmlspecialchars($user['email']) ?></div>
                            </div>
                            <div style="color: #999; font-size: 14px;">
                                <?= date('d.m.Y', strtotime($user['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Recent comments -->
            <div>
                <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 20px;">Последние комментарии</h3>
                <div style="background: #f8f9fa; border-radius: 12px; overflow: hidden;">
                    <?php foreach ($recentComments as $comment): ?>
                    <div style="padding: 15px 20px; border-bottom: 1px solid #e9ecef;">
                        <div style="margin-bottom: 5px;">
                            <strong><?= htmlspecialchars($comment['user_name']) ?></strong>
                            <span style="color: #999; font-size: 14px; margin-left: 10px;">
                                <?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?>
                            </span>
                        </div>
                        <div style="color: #666; font-size: 14px;">
                            <?= htmlspecialchars(mb_substr($comment['comment_text'], 0, 100)) ?>...
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent4 = ob_get_clean();

// Other sections empty
$greyContent5 = '';
$greyContent6 = '';
$blueContent = '';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/template.php';
?>