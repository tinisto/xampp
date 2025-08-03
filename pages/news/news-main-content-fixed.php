<?php
// Get news count for statistics
$countQuery = "SELECT COUNT(*) as total FROM news WHERE approved = 1";
$countResult = mysqli_query($connection, $countQuery);
$newsCount = mysqli_fetch_assoc($countResult)['total'];

// Get categories count
$catCountQuery = "SELECT COUNT(*) as total FROM news_categories";
$catCountResult = mysqli_query($connection, $catCountQuery);
$categoriesCount = mysqli_fetch_assoc($catCountResult)['total'];

// Get latest news from each category
$categoriesQuery = "SELECT * FROM news_categories ORDER BY title_category_news";
$categoriesResult = mysqli_query($connection, $categoriesQuery);

// Get featured news (latest 4 news items)
$featuredNewsQuery = "SELECT n.*, nc.title_category_news, nc.url_category_news 
                      FROM news n
                      LEFT JOIN news_categories nc ON n.category_news = nc.id_category_news
                      WHERE n.approved = 1 
                      ORDER BY n.date_news DESC 
                      LIMIT 4";
$featuredNewsResult = mysqli_query($connection, $featuredNewsQuery);

// Get news by categories
$newsByCategories = [];
mysqli_data_seek($categoriesResult, 0); // Reset result pointer
while ($category = mysqli_fetch_assoc($categoriesResult)) {
    $categoryId = $category['id_category_news'];
    $categoryNewsQuery = "SELECT n.*, nc.title_category_news, nc.url_category_news 
                          FROM news n
                          LEFT JOIN news_categories nc ON n.category_news = nc.id_category_news
                          WHERE n.category_news = '$categoryId' AND n.approved = 1 
                          ORDER BY n.date_news DESC 
                          LIMIT 6";
    $categoryNewsResult = mysqli_query($connection, $categoryNewsQuery);
    
    if ($categoryNewsResult) {
        $news = mysqli_fetch_all($categoryNewsResult, MYSQLI_ASSOC);
        if (!empty($news)) {
            $newsByCategories[$category['url_category_news']] = [
                'category' => $category,
                'news' => $news
            ];
        }
    }
}
?>

<!-- Page Header with Statistics -->
<?php 
// Include page header component
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/page-header.php';

// Format statistics for page header
$statsText = $newsCount . ' Новостей • ' . $categoriesCount . ' Категорий';
renderPageHeader(
    'Новости образования',
    $statsText,
    false
);
?>

<style>
    .category-section {
        margin-bottom: 60px;
    }
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding: 0 15px;
    }
    .section-title {
        font-size: 28px;
        font-weight: 700;
        color: #333;
        margin: 0;
        position: relative;
    }
    .section-title::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 0;
        width: 50px;
        height: 3px;
        background: #28a745;
    }
    .view-all-btn {
        color: #28a745;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .view-all-btn:hover {
        color: #218838;
        transform: translateX(5px);
    }
    .news-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        padding: 0 15px;
    }
    .featured-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        margin-bottom: 60px;
    }
    .news-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        position: relative;
    }
    .news-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .news-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        transition: transform 0.3s ease;
        position: relative;
    }
    .news-card:hover .news-image {
        transform: scale(1.05);
    }
    .news-content {
        padding: 25px;
    }
    .news-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 12px;
        line-height: 1.4;
        color: #333;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .news-title a {
        color: inherit;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    .news-title a:hover {
        color: #28a745;
    }
    .news-excerpt {
        color: #666;
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 15px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .news-meta {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        font-size: 13px;
        color: #888;
    }
    .featured-section {
        margin-bottom: 60px;
    }
    .featured-title {
        font-size: 36px;
        font-weight: 700;
        text-align: center;
        margin-bottom: 40px;
        color: #333;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }
    .empty-state i {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.5;
    }
    @media (max-width: 768px) {
        .section-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        .news-grid, .featured-grid {
            grid-template-columns: 1fr;
            padding: 0;
        }
        .section-title {
            font-size: 24px;
        }
        .news-content {
            padding: 20px 15px;
        }
    }
</style>

<div class="container">
    <!-- Featured News Section -->
    <section class="featured-section">
        <h2 class="featured-title">Последние новости</h2>
        <div class="featured-grid">
            <?php 
            $featuredNews = mysqli_fetch_all($featuredNewsResult, MYSQLI_ASSOC);
            if (!empty($featuredNews)): 
                foreach ($featuredNews as $news): 
                    $excerpt = strip_tags($news['text_news']);
                    $excerpt = mb_substr($excerpt, 0, 150) . '...';
                    // Check for images
                    $image = 'https://via.placeholder.com/400x200/28a745/ffffff?text=Новости';
                    if (!empty($news['image_news_1'])) {
                        $imagePath = "/images/news-images/{$news['id_news']}_1.jpg";
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                            $image = $imagePath;
                        }
                    }
            ?>
                <article class="news-card">
                    <div style="position: relative;">
                        <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($news['title_news']) ?>" class="news-image">
                        <?php if ($news['title_category_news']): 
                            renderCardBadge($news['title_category_news'], '/news/' . $news['url_category_news'], 'overlay', 'green');
                        endif; ?>
                    </div>
                    <div class="news-content">
                        <h3 class="news-title">
                            <a href="/news/<?= htmlspecialchars($news['url_news']) ?>">
                                <?= htmlspecialchars($news['title_news']) ?>
                            </a>
                        </h3>
                        <p class="news-excerpt"><?= htmlspecialchars($excerpt) ?></p>
                    </div>
                </article>
            <?php 
                endforeach; 
            else: 
            ?>
                <div class="empty-state">
                    <i class="fas fa-newspaper"></i>
                    <h3>Новости скоро появятся</h3>
                    <p>Мы работаем над наполнением этого раздела актуальными новостями.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Category Sections -->
    <?php foreach ($newsByCategories as $categorySlug => $categoryData): 
        $category = $categoryData['category'];
        $categoryNews = $categoryData['news'];
        if (empty($categoryNews)) continue;
    ?>
        <section class="category-section">
            <div class="section-header">
                <h2 class="section-title"><?= htmlspecialchars($category['title_category_news']) ?></h2>
                <a href="/news/<?= htmlspecialchars($category['url_category_news']) ?>" class="view-all-btn">
                    Все новости <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="news-grid">
                <?php foreach (array_slice($categoryNews, 0, 6) as $news): 
                    $excerpt = strip_tags($news['text_news']);
                    $excerpt = mb_substr($excerpt, 0, 120) . '...';
                    // Check for images
                    $image = 'https://via.placeholder.com/400x200/28a745/ffffff?text=Новости';
                    if (!empty($news['image_news_1'])) {
                        $imagePath = "/images/news-images/{$news['id_news']}_1.jpg";
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                            $image = $imagePath;
                        }
                    }
                ?>
                    <article class="news-card">
                        <div style="position: relative;">
                            <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($news['title_news']) ?>" class="news-image">
                            <?php renderCardBadge($news['title_category_news'], '/news/' . $news['url_category_news'], 'overlay', 'green'); ?>
                        </div>
                        <div class="news-content">
                            <h3 class="news-title">
                                <a href="/news/<?= htmlspecialchars($news['url_news']) ?>">
                                    <?= htmlspecialchars($news['title_news']) ?>
                                </a>
                            </h3>
                            <p class="news-excerpt"><?= htmlspecialchars($excerpt) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
</div>