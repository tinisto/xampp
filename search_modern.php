<?php
// Modern search page
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

// Get search query
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$type = isset($_GET['type']) ? $_GET['type'] : 'all';

// Initialize results
$results = [
    'news' => [],
    'posts' => [],
    'vpo' => [],
    'spo' => [],
    'schools' => [],
];

$totalResults = 0;

if ($query && strlen($query) >= 2) {
    $searchTerm = '%' . $query . '%';
    
    // Search news (using correct schema)
    if ($type === 'all' || $type === 'news') {
        $results['news'] = db_fetch_all("
            SELECT 'news' as type, id, title_news as title, text_news as text, 
                   url_slug as slug, date_news as created_at, view_news as views
            FROM news 
            WHERE (title_news LIKE ? OR text_news LIKE ?) AND approved = 1
            ORDER BY date_news DESC
            LIMIT 8
        ", [$searchTerm, $searchTerm]);
    }
    
    // Search posts (using correct schema)
    if ($type === 'all' || $type === 'posts') {
        $results['posts'] = db_fetch_all("
            SELECT 'post' as type, id, title_post as title, text_post as text, 
                   url_slug as slug, date_post as created_at, view_post as views
            FROM posts 
            WHERE title_post LIKE ? OR text_post LIKE ?
            ORDER BY date_post DESC
            LIMIT 8
        ", [$searchTerm, $searchTerm]);
    }
    
    // Search VPO (using correct schema)
    if ($type === 'all' || $type === 'vpo') {
        $results['vpo'] = db_fetch_all("
            SELECT 'vpo' as type, id, name as title, full_name as text, 
                   url_slug as slug, NULL as created_at, 0 as views
            FROM vpo 
            WHERE name LIKE ? OR full_name LIKE ?
            ORDER BY name ASC
            LIMIT 6
        ", [$searchTerm, $searchTerm]);
    }
    
    // Search SPO (using correct schema)
    if ($type === 'all' || $type === 'spo') {
        $results['spo'] = db_fetch_all("
            SELECT 'spo' as type, id, name as title, full_name as text, 
                   url_slug as slug, NULL as created_at, 0 as views
            FROM spo 
            WHERE name LIKE ? OR full_name LIKE ?
            ORDER BY name ASC
            LIMIT 6
        ", [$searchTerm, $searchTerm]);
    }
    
    // Search Schools (using correct schema)
    if ($type === 'all' || $type === 'schools') {
        $results['schools'] = db_fetch_all("
            SELECT 'school' as type, id, name as title, full_name as text, 
                   url_slug as slug, NULL as created_at, 0 as views
            FROM schools 
            WHERE name LIKE ? OR full_name LIKE ?
            ORDER BY name ASC
            LIMIT 6
        ", [$searchTerm, $searchTerm]);
    }
    
    // Count total results
    foreach ($results as $section) {
        $totalResults += count($section);
    }
}

// Page title
$pageTitle = $query ? 'Результаты поиска: ' . htmlspecialchars($query) : 'Поиск';

// Section 1: Search form and title
ob_start();
?>
<div style="padding: 50px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h1 style="font-size: 36px; font-weight: 700; margin-bottom: 30px; text-align: center;">Поиск по сайту</h1>
        
        <form action="/search" method="get" style="display: flex; gap: 10px;">
            <input type="text" 
                   name="q" 
                   value="<?= htmlspecialchars($query) ?>"
                   placeholder="Введите запрос для поиска..." 
                   autofocus
                   style="flex: 1; padding: 15px 20px; border: none; border-radius: 8px; font-size: 16px;">
            <button type="submit" 
                    style="padding: 15px 30px; background: white; color: #667eea; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer;">
                <i class="fas fa-search"></i> Найти
            </button>
        </form>
        
        <?php if ($query): ?>
        <p style="text-align: center; margin-top: 20px; opacity: 0.9;">
            Найдено результатов: <?= $totalResults ?>
        </p>
        <?php endif; ?>
    </div>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: Filter tabs
ob_start();
if ($query):
?>
<div style="padding: 20px; background: white; border-bottom: 1px solid #e9ecef;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap; justify-content: center;">
            <a href="/search?q=<?= urlencode($query) ?>&type=all" 
               style="padding: 8px 16px; border-radius: 20px; text-decoration: none; transition: all 0.3s;
                      <?= $type === 'all' ? 'background: #667eea; color: white;' : 'background: #f8f9fa; color: #495057;' ?>">
                Все разделы
                <?php if ($type === 'all'): ?>
                <span style="background: rgba(255,255,255,0.3); padding: 2px 8px; border-radius: 10px; margin-left: 5px;">
                    <?= $totalResults ?>
                </span>
                <?php endif; ?>
            </a>
            
            <?php 
            $sections = [
                'news' => ['name' => 'Новости', 'icon' => 'newspaper'],
                'posts' => ['name' => 'Статьи', 'icon' => 'book-open'],
                'vpo' => ['name' => 'ВУЗы', 'icon' => 'university'],
                'spo' => ['name' => 'Колледжи', 'icon' => 'school'],
                'schools' => ['name' => 'Школы', 'icon' => 'graduation-cap'],
            ];
            
            foreach ($sections as $key => $section): 
                $count = count($results[$key]);
            ?>
            <a href="/search?q=<?= urlencode($query) ?>&type=<?= $key ?>" 
               style="padding: 8px 16px; border-radius: 20px; text-decoration: none; transition: all 0.3s;
                      <?= $type === $key ? 'background: #667eea; color: white;' : 'background: #f8f9fa; color: #495057;' ?>">
                <i class="fas fa-<?= $section['icon'] ?>"></i> <?= $section['name'] ?>
                <?php if ($count > 0): ?>
                <span style="background: <?= $type === $key ? 'rgba(255,255,255,0.3)' : '#e9ecef' ?>; padding: 2px 8px; border-radius: 10px; margin-left: 5px;">
                    <?= $count ?>
                </span>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php
endif;
$greyContent2 = ob_get_clean();

// Section 3: Search results
ob_start();
if ($query):
    if ($totalResults > 0):
?>
<div style="padding: 40px 20px; background: #f8f9fa;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <?php
        // Merge all results if showing all
        if ($type === 'all') {
            $allResults = [];
            foreach ($results as $sectionResults) {
                $allResults = array_merge($allResults, $sectionResults);
            }
            
            // Sort by date (newest first)
            usort($allResults, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
        } else {
            $allResults = $results[$type];
        }
        ?>
        
        <div style="display: grid; gap: 20px;">
            <?php foreach ($allResults as $result): ?>
            <article style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: all 0.3s;"
                     onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)';"
                     onmouseout="this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)';">
                <div style="display: flex; gap: 20px; align-items: start;">
                    <div style="width: 50px; height: 50px; background: #f8f9fa; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <?php
                        $icon = match($result['type']) {
                            'news' => 'fa-newspaper',
                            'post' => 'fa-book-open',
                            'vpo' => 'fa-university',
                            'spo' => 'fa-school',
                            'school' => 'fa-graduation-cap',
                            default => 'fa-file'
                        };
                        
                        $color = match($result['type']) {
                            'news' => '#007bff',
                            'post' => '#667eea',
                            'vpo' => '#1e3c72',
                            'spo' => '#00b09b',
                            'school' => '#f5576c',
                            default => '#6c757d'
                        };
                        ?>
                        <i class="fas <?= $icon ?>" style="font-size: 20px; color: <?= $color ?>;"></i>
                    </div>
                    
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <?php
                            $sectionName = match($result['type']) {
                                'news' => 'Новости',
                                'post' => 'Статьи',
                                'vpo' => 'ВУЗы',
                                'spo' => 'Колледжи',
                                'school' => 'Школы',
                                default => 'Другое'
                            };
                            ?>
                            <span style="background: <?= $color ?>; color: white; padding: 4px 12px; border-radius: 15px; font-size: 12px; font-weight: 500;">
                                <?= $sectionName ?>
                            </span>
                            
                            <?php if ($result['created_at']): ?>
                            <span style="color: #999; font-size: 14px;">
                                <?= date('d.m.Y', strtotime($result['created_at'])) ?>
                            </span>
                            <?php endif; ?>
                            
                            <?php if ($result['views'] > 0): ?>
                            <span style="color: #999; font-size: 14px;">
                                <i class="far fa-eye"></i> <?= number_format($result['views']) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 10px; line-height: 1.3;">
                            <?php
                            $url = match($result['type']) {
                                'news' => '/news/' . $result['slug'],
                                'post' => '/post/' . $result['slug'],
                                'vpo' => '/vpo/' . $result['slug'],
                                'spo' => '/spo/' . $result['slug'],
                                'school' => '/school/' . $result['slug'],
                                default => '#'
                            };
                            ?>
                            <a href="<?= $url ?>" style="color: #333; text-decoration: none;">
                                <?= preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark style="background: #fff59d; padding: 2px;">$1</mark>', htmlspecialchars($result['title'])) ?>
                            </a>
                        </h3>
                        
                        <?php if ($result['text']): ?>
                        <p style="color: #666; margin: 0; line-height: 1.6;">
                            <?php
                            $text = strip_tags($result['text']);
                            $text = mb_substr($text, 0, 200) . '...';
                            echo preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark style="background: #fff59d; padding: 2px;">$1</mark>', htmlspecialchars($text));
                            ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php
    else:
?>
<div style="padding: 80px 20px; background: white; text-align: center;">
    <i class="fas fa-search" style="font-size: 64px; color: #dee2e6; margin-bottom: 20px;"></i>
    <h2 style="color: #6c757d; margin-bottom: 10px;">Ничего не найдено</h2>
    <p style="color: #adb5bd; font-size: 18px; max-width: 500px; margin: 0 auto;">
        По запросу «<?= htmlspecialchars($query) ?>» ничего не найдено. 
        Попробуйте изменить запрос или воспользоваться другими разделами сайта.
    </p>
    
    <div style="margin-top: 40px; display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
        <a href="/news" style="padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 8px;">
            <i class="fas fa-newspaper"></i> Новости
        </a>
        <a href="/posts" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 8px;">
            <i class="fas fa-book-open"></i> Статьи
        </a>
        <a href="/vpo" style="padding: 12px 24px; background: #1e3c72; color: white; text-decoration: none; border-radius: 8px;">
            <i class="fas fa-university"></i> ВУЗы
        </a>
    </div>
</div>
<?php
    endif;
else:
?>
<div style="padding: 60px 20px; background: white;">
    <div style="max-width: 600px; margin: 0 auto; text-align: center;">
        <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px;">Популярные запросы</h2>
        <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
            <?php
            $popularQueries = [
                'ЕГЭ 2025', 'Поступление в ВУЗ', 'МГУ', 'СПбГУ', 
                'Медицинский университет', 'IT специальности', 
                'Стипендии', 'Общежитие', 'Бюджетные места'
            ];
            foreach ($popularQueries as $pq):
            ?>
            <a href="/search?q=<?= urlencode($pq) ?>" 
               style="padding: 8px 16px; background: #f8f9fa; color: #495057; text-decoration: none; border-radius: 20px; transition: all 0.3s;"
               onmouseover="this.style.background='#e9ecef';"
               onmouseout="this.style.background='#f8f9fa';">
                <?= $pq ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php
endif;
$greyContent3 = ob_get_clean();

// Other sections empty
$greyContent4 = '';
$greyContent5 = '';
$greyContent6 = '';
$blueContent = '';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template_local.php';
?>