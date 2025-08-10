<?php
// Favorites page
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = '/favorites';
    header('Location: /login');
    exit;
}

// Get filter type
$type = isset($_GET['type']) ? $_GET['type'] : 'all';

// Fetch favorites based on type
$favorites = [];

if ($type === 'all' || $type === 'vpo') {
    $vpoFavorites = db_fetch_all("
        SELECT f.*, v.name_vpo as title, v.url_slug, v.description, 'vpo' as type
        FROM favorites f
        JOIN vpo v ON f.item_id = v.id_university
        WHERE f.user_id = ? AND f.item_type = 'vpo'
        ORDER BY f.created_at DESC
    ", [$_SESSION['user_id']]);
    $favorites = array_merge($favorites, $vpoFavorites);
}

if ($type === 'all' || $type === 'spo') {
    $spoFavorites = db_fetch_all("
        SELECT f.*, s.name_spo as title, s.url_slug, s.description, 'spo' as type
        FROM favorites f
        JOIN spo s ON f.item_id = s.id_college
        WHERE f.user_id = ? AND f.item_type = 'spo'
        ORDER BY f.created_at DESC
    ", [$_SESSION['user_id']]);
    $favorites = array_merge($favorites, $spoFavorites);
}

if ($type === 'all' || $type === 'schools') {
    $schoolFavorites = db_fetch_all("
        SELECT f.*, sc.name_school as title, sc.url_slug, sc.description, 'school' as type
        FROM favorites f
        JOIN schools sc ON f.item_id = sc.id_school
        WHERE f.user_id = ? AND f.item_type = 'school'
        ORDER BY f.created_at DESC
    ", [$_SESSION['user_id']]);
    $favorites = array_merge($favorites, $schoolFavorites);
}

if ($type === 'all' || $type === 'news') {
    $newsFavorites = db_fetch_all("
        SELECT f.*, n.title_news as title, n.url_news as url_slug, n.text_news as description, 'news' as type
        FROM favorites f
        JOIN news n ON f.item_id = n.id_news
        WHERE f.user_id = ? AND f.item_type = 'news'
        ORDER BY f.created_at DESC
    ", [$_SESSION['user_id']]);
    $favorites = array_merge($favorites, $newsFavorites);
}

if ($type === 'all' || $type === 'posts') {
    $postFavorites = db_fetch_all("
        SELECT f.*, p.title_post as title, p.url_slug, p.text_post as description, 'post' as type
        FROM favorites f
        JOIN posts p ON f.item_id = p.id
        WHERE f.user_id = ? AND f.item_type = 'post'
        ORDER BY f.created_at DESC
    ", [$_SESSION['user_id']]);
    $favorites = array_merge($favorites, $postFavorites);
}

// Sort all favorites by date
usort($favorites, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

// Page title
$pageTitle = 'Избранное';

// Section 1: Header
ob_start();
?>
<div style="padding: 50px 20px; background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%); color: white;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <h1 style="font-size: 36px; font-weight: 700; margin-bottom: 20px;">Избранное</h1>
        <p style="font-size: 18px; opacity: 0.9;">
            Ваши сохраненные учебные заведения и публикации
        </p>
    </div>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: Filter tabs
ob_start();
?>
<div style="padding: 20px; background: white; border-bottom: 1px solid #e9ecef;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap;">
            <a href="/favorites?type=all" 
               style="padding: 8px 16px; border-radius: 20px; text-decoration: none; transition: all 0.3s;
                      <?= $type === 'all' ? 'background: #f5576c; color: white;' : 'background: #f8f9fa; color: #495057;' ?>">
                Все
                <span style="background: <?= $type === 'all' ? 'rgba(255,255,255,0.3)' : '#e9ecef' ?>; 
                            padding: 2px 8px; border-radius: 10px; margin-left: 5px;">
                    <?= count($favorites) ?>
                </span>
            </a>
            
            <a href="/favorites?type=vpo" 
               style="padding: 8px 16px; border-radius: 20px; text-decoration: none; transition: all 0.3s;
                      <?= $type === 'vpo' ? 'background: #1e3c72; color: white;' : 'background: #f8f9fa; color: #495057;' ?>">
                <i class="fas fa-university"></i> ВУЗы
            </a>
            
            <a href="/favorites?type=spo" 
               style="padding: 8px 16px; border-radius: 20px; text-decoration: none; transition: all 0.3s;
                      <?= $type === 'spo' ? 'background: #00b09b; color: white;' : 'background: #f8f9fa; color: #495057;' ?>">
                <i class="fas fa-school"></i> Колледжи
            </a>
            
            <a href="/favorites?type=schools" 
               style="padding: 8px 16px; border-radius: 20px; text-decoration: none; transition: all 0.3s;
                      <?= $type === 'schools' ? 'background: #f5576c; color: white;' : 'background: #f8f9fa; color: #495057;' ?>">
                <i class="fas fa-graduation-cap"></i> Школы
            </a>
            
            <a href="/favorites?type=news" 
               style="padding: 8px 16px; border-radius: 20px; text-decoration: none; transition: all 0.3s;
                      <?= $type === 'news' ? 'background: #007bff; color: white;' : 'background: #f8f9fa; color: #495057;' ?>">
                <i class="fas fa-newspaper"></i> Новости
            </a>
            
            <a href="/favorites?type=posts" 
               style="padding: 8px 16px; border-radius: 20px; text-decoration: none; transition: all 0.3s;
                      <?= $type === 'posts' ? 'background: #667eea; color: white;' : 'background: #f8f9fa; color: #495057;' ?>">
                <i class="fas fa-book-open"></i> Статьи
            </a>
        </div>
    </div>
</div>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Favorites list
ob_start();
?>
<div style="padding: 40px 20px; background: #f8f9fa; min-height: 60vh;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <?php if (empty($favorites)): ?>
        <div style="text-align: center; padding: 80px 20px;">
            <i class="fas fa-heart" style="font-size: 64px; color: #dee2e6; margin-bottom: 20px;"></i>
            <h2 style="color: #6c757d; margin-bottom: 10px;">Список избранного пуст</h2>
            <p style="color: #adb5bd; font-size: 18px; margin-bottom: 30px;">
                Добавляйте интересные учебные заведения и публикации в избранное,<br>
                чтобы быстро к ним возвращаться
            </p>
            <a href="/" 
               style="display: inline-block; padding: 12px 30px; background: #f5576c; color: white; 
                      border-radius: 8px; text-decoration: none; font-weight: 600;">
                <i class="fas fa-home"></i> На главную
            </a>
        </div>
        <?php else: ?>
        <div style="display: grid; gap: 20px;">
            <?php foreach ($favorites as $favorite): ?>
            <article style="background: white; border-radius: 12px; padding: 25px; 
                           box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: all 0.3s; position: relative;"
                     onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)';"
                     onmouseout="this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)';">
                
                <!-- Remove from favorites button -->
                <button onclick="removeFromFavorites('<?= $favorite['item_type'] ?>', <?= $favorite['item_id'] ?>)"
                        style="position: absolute; top: 20px; right: 20px; background: #fff; 
                               border: 1px solid #dee2e6; border-radius: 50%; width: 36px; height: 36px; 
                               cursor: pointer; display: flex; align-items: center; justify-content: center;
                               transition: all 0.2s;"
                        onmouseover="this.style.background='#f8f9fa'"
                        onmouseout="this.style.background='#fff'">
                    <i class="fas fa-heart" style="color: #f5576c;"></i>
                </button>
                
                <div style="display: flex; gap: 20px; align-items: start;">
                    <div style="width: 60px; height: 60px; background: #f8f9fa; border-radius: 10px; 
                                display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <?php
                        $icon = match($favorite['type']) {
                            'vpo' => 'fa-university',
                            'spo' => 'fa-school',
                            'school' => 'fa-graduation-cap',
                            'news' => 'fa-newspaper',
                            'post' => 'fa-book-open',
                            default => 'fa-file'
                        };
                        
                        $color = match($favorite['type']) {
                            'vpo' => '#1e3c72',
                            'spo' => '#00b09b',
                            'school' => '#f5576c',
                            'news' => '#007bff',
                            'post' => '#667eea',
                            default => '#6c757d'
                        };
                        ?>
                        <i class="fas <?= $icon ?>" style="font-size: 24px; color: <?= $color ?>;"></i>
                    </div>
                    
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <?php
                            $typeName = match($favorite['type']) {
                                'vpo' => 'ВУЗ',
                                'spo' => 'Колледж',
                                'school' => 'Школа',
                                'news' => 'Новость',
                                'post' => 'Статья',
                                default => 'Другое'
                            };
                            ?>
                            <span style="background: <?= $color ?>; color: white; padding: 4px 12px; 
                                        border-radius: 15px; font-size: 12px; font-weight: 500;">
                                <?= $typeName ?>
                            </span>
                            
                            <span style="color: #999; font-size: 14px;">
                                Добавлено <?= date('d.m.Y', strtotime($favorite['created_at'])) ?>
                            </span>
                        </div>
                        
                        <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 10px; line-height: 1.3;">
                            <?php
                            $url = match($favorite['type']) {
                                'vpo' => '/vpo/' . $favorite['url_slug'],
                                'spo' => '/spo/' . $favorite['url_slug'],
                                'school' => '/school/' . $favorite['url_slug'],
                                'news' => '/news/' . $favorite['url_slug'],
                                'post' => '/post/' . $favorite['url_slug'],
                                default => '#'
                            };
                            ?>
                            <a href="<?= $url ?>" style="color: #333; text-decoration: none;">
                                <?= htmlspecialchars($favorite['title']) ?>
                            </a>
                        </h3>
                        
                        <?php if ($favorite['description']): ?>
                        <p style="color: #666; margin: 0; line-height: 1.6;">
                            <?= htmlspecialchars(mb_substr(strip_tags($favorite['description']), 0, 150)) ?>...
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function removeFromFavorites(type, id) {
    if (confirm('Удалить из избранного?')) {
        // Here you would make an AJAX call to remove the item
        fetch('/api/favorites/remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ type: type, id: id })
        }).then(() => {
            location.reload();
        });
    }
}
</script>
<?php
$greyContent3 = ob_get_clean();

// Other sections empty
$greyContent4 = '';
$greyContent5 = '';
$greyContent6 = '';
$blueContent = '';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template_local.php';
?>