<?php
// Modern homepage
require_once __DIR__ . '/database/db_modern.php';

$pageTitle = '11-классники - Образовательный портал';

// Get statistics
$stats = [
    'schools' => db_fetch_column("SELECT COUNT(*) FROM schools") ?: 3318,
    'vpo' => db_fetch_column("SELECT COUNT(*) FROM vpo") ?: 2520,
    'spo' => db_fetch_column("SELECT COUNT(*) FROM spo") ?: 1850,
    'news' => db_fetch_column("SELECT COUNT(*) FROM news") ?: 496,
];

// Get latest posts (articles)
$latestPosts = db_fetch_all("
    SELECT p.*, c.category_name, c.url_slug as category_slug
    FROM posts p
    LEFT JOIN categories c ON p.category = c.id_category
    ORDER BY p.date_post DESC
    LIMIT 8
");

// Section 1: Hero
ob_start();
?>
<div style="text-align: center; padding: 60px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <h1 style="font-size: 48px; font-weight: 700; margin-bottom: 20px;">11-классники</h1>
    <p style="font-size: 20px; opacity: 0.9; max-width: 600px; margin: 0 auto;">
        Образовательный портал для школьников, абитуриентов и студентов
    </p>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: Statistics
ob_start();
?>
<div style="padding: 50px 20px; background: #f8f9fa;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <h2 style="text-align: center; font-size: 32px; font-weight: 700; margin-bottom: 40px;">Наша база данных</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
            <div style="text-align: center; background: white; padding: 40px 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="font-size: 48px; font-weight: 700; color: #007bff; margin-bottom: 10px;">
                    <?= number_format($stats['schools']) ?>
                </div>
                <div style="font-size: 18px; color: #666;">Школ</div>
                <a href="/schools" style="display: inline-block; margin-top: 15px; color: #007bff; text-decoration: none;">
                    Посмотреть все →
                </a>
            </div>
            
            <div style="text-align: center; background: white; padding: 40px 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="font-size: 48px; font-weight: 700; color: #28a745; margin-bottom: 10px;">
                    <?= number_format($stats['vpo']) ?>
                </div>
                <div style="font-size: 18px; color: #666;">ВУЗов</div>
                <a href="/vpo" style="display: inline-block; margin-top: 15px; color: #28a745; text-decoration: none;">
                    Посмотреть все →
                </a>
            </div>
            
            <div style="text-align: center; background: white; padding: 40px 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="font-size: 48px; font-weight: 700; color: #ffc107; margin-bottom: 10px;">
                    <?= number_format($stats['spo']) ?>
                </div>
                <div style="font-size: 18px; color: #666;">ССУЗов</div>
                <a href="/spo" style="display: inline-block; margin-top: 15px; color: #ffc107; text-decoration: none;">
                    Посмотреть все →
                </a>
            </div>
            
            <div style="text-align: center; background: white; padding: 40px 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="font-size: 48px; font-weight: 700; color: #dc3545; margin-bottom: 10px;">
                    <?= number_format($stats['news']) ?>
                </div>
                <div style="font-size: 18px; color: #666;">Новостей</div>
                <a href="/news" style="display: inline-block; margin-top: 15px; color: #dc3545; text-decoration: none;">
                    Читать все →
                </a>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Search
ob_start();
?>
<div style="padding: 60px 20px; background: white;">
    <div style="max-width: 800px; margin: 0 auto; text-align: center;">
        <h2 style="font-size: 28px; font-weight: 700; margin-bottom: 30px;">Поиск по сайту</h2>
        
        <form action="/search" method="get" style="display: flex; gap: 10px; max-width: 600px; margin: 0 auto;">
            <input type="text" 
                   name="q" 
                   placeholder="Введите название школы, ВУЗа или колледжа..." 
                   style="flex: 1; padding: 15px 20px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 16px;">
            <button type="submit" 
                    style="padding: 15px 30px; background: #007bff; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer;">
                Найти
            </button>
        </form>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();

// Section 4: Latest Articles
ob_start();
?>
<div style="padding: 60px 20px; background: #f8f9fa;">
    <div style="max-width: 1400px; margin: 0 auto;">
        <h2 style="text-align: center; font-size: 32px; font-weight: 700; margin-bottom: 40px;">Полезные статьи</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px;">
            <?php foreach ($latestPosts as $post): ?>
            <article style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s; cursor: pointer;"
                     onclick="window.location.href='/post/<?= htmlspecialchars($post['url_slug']) ?>'">
                <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-book-open" style="font-size: 64px; color: white; opacity: 0.8;"></i>
                </div>
                
                <div style="padding: 25px;">
                    <?php if ($post['category_name']): ?>
                    <a href="/posts?category=<?= htmlspecialchars($post['category_slug']) ?>" 
                       onclick="event.stopPropagation()"
                       style="display: inline-block; background: #e9ecef; color: #495057; padding: 6px 12px; border-radius: 20px; font-size: 12px; text-decoration: none; margin-bottom: 15px;">
                        <?= htmlspecialchars($post['category_name']) ?>
                    </a>
                    <?php endif; ?>
                    
                    <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 10px; line-height: 1.4; color: #333;">
                        <?= htmlspecialchars($post['title_post']) ?>
                    </h3>
                    
                    <div style="color: #666; font-size: 14px; display: flex; align-items: center; gap: 15px;">
                        <span><i class="far fa-calendar"></i> <?= date('d.m.Y', strtotime($post['date_post'])) ?></span>
                        <span><i class="far fa-eye"></i> <?= number_format($post['view_post']) ?></span>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
            <a href="/posts" 
               style="display: inline-block; background: #007bff; color: white; padding: 15px 40px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 16px;">
                Все статьи →
            </a>
        </div>
    </div>
</div>
<?php
$greyContent4 = ob_get_clean();

// Section 5: Features
ob_start();
?>
<div style="padding: 60px 20px; background: white;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <h2 style="text-align: center; font-size: 32px; font-weight: 700; margin-bottom: 40px;">Почему выбирают нас</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px;">
            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; background: #e3f2fd; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-database" style="font-size: 36px; color: #2196f3;"></i>
                </div>
                <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 15px;">Обширная база данных</h3>
                <p style="color: #666; line-height: 1.6;">
                    Более 7000 учебных заведений России с подробной информацией
                </p>
            </div>
            
            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; background: #e8f5e9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-sync-alt" style="font-size: 36px; color: #4caf50;"></i>
                </div>
                <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 15px;">Актуальная информация</h3>
                <p style="color: #666; line-height: 1.6;">
                    Регулярное обновление данных и свежие новости образования
                </p>
            </div>
            
            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; background: #fff3e0; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-search" style="font-size: 36px; color: #ff9800;"></i>
                </div>
                <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 15px;">Удобный поиск</h3>
                <p style="color: #666; line-height: 1.6;">
                    Быстрый поиск по регионам, городам и типам учебных заведений
                </p>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Personalized recommendations for logged-in users
if (isset($_SESSION['user_id'])) {
    ob_start();
    ?>
    <div style="padding: 60px 20px; background: var(--bg-secondary);">
        <div style="max-width: 1200px; margin: 0 auto;">
            <?php 
            require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/recommendations.php';
            include_recommendations_widget($_SESSION['user_id'], 'Рекомендуем к прочтению', 6);
            ?>
        </div>
    </div>
    <?php
    $greyContent6 = ob_get_clean();
} else {
    $greyContent6 = '';
}

// Include template
$blueContent = '';
include $_SERVER['DOCUMENT_ROOT'] . '/real_template_local.php';
?>