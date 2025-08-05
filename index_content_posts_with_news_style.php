<?php
// Include news card component for styling, but we'll adapt it for posts
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/card-badge.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/search-box.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/image-lazy-load.php';

// Function to render post card using news card styling
function renderPostCard($post, $categoryName, $categoryUrl, $badgeColor = 'green') {
    // Check for image
    $hasImage = false;
    $image = '';
    
    $imagePath = "/images/posts-images/{$post['id']}_1.jpg";
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
        $hasImage = true;
        $image = $imagePath;
    }
    
    // Prepare excerpt
    $excerpt = strip_tags($post['text_post'] ?? '');
    $excerpt = mb_strlen($excerpt) > 120 ? mb_substr($excerpt, 0, 120) . '...' : $excerpt;
    
    // Post URL
    $postUrl = "/post/" . htmlspecialchars($post['url_slug']);
    ?>
    
    <article class="news-card">
        <a href="<?= $postUrl ?>" class="news-card-link">
            <div class="news-image-container">
                <?php if ($hasImage): 
                    renderLazyImage([
                        'src' => htmlspecialchars($image),
                        'alt' => htmlspecialchars($post['title_post']),
                        'class' => 'news-image',
                        'aspectRatio' => '16:9'
                    ]);
                else: ?>
                    <div class="news-image-placeholder"><i class="fas fa-file-alt fa-3x"></i></div>
                <?php endif; ?>
            </div>
            <div class="news-content">
                <h3 class="news-title">
                    <?= htmlspecialchars($post['title_post']) ?>
                </h3>
                <?php if (!empty($excerpt)): ?>
                    <p class="news-excerpt"><?= htmlspecialchars($excerpt) ?></p>
                <?php endif; ?>
            </div>
        </a>
        <?php 
        renderCardBadge($categoryName, $categoryUrl, 'overlay', $badgeColor);
        ?>
    </article>
    <?php
}
?>

<style>
    .hero-section {
        background: linear-gradient(135deg, var(--hero-bg-start, #667eea) 0%, var(--hero-bg-end, #764ba2) 100%);
        color: white;
        padding: 30px 20px;
        margin-bottom: 30px;
    }
    
    .text-center {
        text-align: center;
    }
    
    /* Dark mode hero section */
    [data-theme="dark"] .hero-section {
        background: linear-gradient(135deg, var(--hero-bg-start, #374151) 0%, var(--hero-bg-end, #1f2937) 100%);
    }
    .hero-title {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 20px;
    }
    /* Direct styling for search box in hero */
    .hero-section .search-box-wrapper {
        max-width: 600px;
        margin: 0 auto;
        position: relative;
    }
    
    @media (max-width: 768px) {
        .hero-title {
            font-size: 24px;
        }
        .hero-section {
            padding: 25px 0;
        }
    }
    
    .section-header {
        text-align: center;
        margin-bottom: 30px;
    }
    .section-title {
        font-size: 28px;
        font-weight: 600;
        color: var(--text-primary, #333);
        margin-bottom: 8px;
    }
    .section-subtitle {
        font-size: 16px;
        color: var(--text-secondary, #666);
    }
    
    /* News card styling (reused for posts) */
    .news-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        padding: 0;
        margin-bottom: 40px;
    }
    .news-card {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        height: 100%;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .news-card * {
        box-sizing: border-box;
    }
    .news-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    }
    .news-card-link {
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative;
    }
    .news-image-container {
        position: relative;
        width: 100%;
        height: 200px;
        background: #e9ecef;
        overflow: hidden;
        flex-shrink: 0;
        margin: 0;
        padding: 0;
        line-height: 0;
        font-size: 0;
    }
    .news-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
        display: block;
        margin: 0;
        padding: 0;
    }
    .news-card:hover .news-image {
        transform: scale(1.02);
    }
    .news-image-placeholder {
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        width: 100%;
        background: #dee2e6;
        margin: 0;
        padding: 0;
    }
    .news-image-placeholder i {
        opacity: 0.7;
    }
    .news-content {
        padding: 15px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .news-title {
        font-size: 14px;
        font-weight: 600;
        margin: 0 0 10px 0;
        line-height: 1.3;
        color: #333;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .news-card:hover .news-title {
        color: #28a745;
    }
    .news-excerpt {
        color: #666;
        font-size: 13px;
        line-height: 1.4;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Ensure badge is clickable above card link */
    .card-badge-overlay {
        position: relative;
        z-index: 10;
    }
    
    /* Responsive design */
    @media (max-width: 1200px) {
        .news-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 900px) {
        .news-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
    }
    @media (max-width: 600px) {
        .news-grid {
            grid-template-columns: 1fr;
        }
    }
    
    /* Dark mode styles */
    [data-theme="dark"] .news-card {
        background: #1a202c;
        color: #e4e6eb;
        border: 1px solid rgba(255,255,255,0.1);
    }
    [data-theme="dark"] .news-card:hover {
        border-color: rgba(255,255,255,0.2);
        box-shadow: 0 4px 16px rgba(0,0,0,0.25);
    }
    [data-theme="dark"] .news-title {
        color: #f7fafc;
    }
    [data-theme="dark"] .news-excerpt {
        color: #a0aec0;
    }
    [data-theme="dark"] .news-image-container {
        background: #2d3748;
    }
    
    .stats-section {
        background: var(--surface-variant, #f8f9fa);
        padding: 30px 0;
        margin: 40px 0;
    }
    .stat-card {
        text-align: center;
        padding: 15px;
    }
    .stat-number {
        font-size: 32px;
        font-weight: 700;
        color: var(--primary-color, #28a745);
        margin-bottom: 5px;
    }
    .stat-label {
        font-size: 16px;
        color: var(--text-secondary, #666);
    }
    
    /* Dark mode support */
    [data-theme="dark"] .stats-section {
        background: var(--surface-variant, #2d3748);
    }
    [data-theme="dark"] .stat-label {
        color: var(--text-secondary, #a0aec0);
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -10px;
    }
    
    .col-md-3 {
        flex: 0 0 25%;
        max-width: 25%;
        padding: 0 10px;
    }
    
    @media (max-width: 768px) {
        .col-md-3 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
    
    @media (max-width: 576px) {
        .col-md-3 {
            flex: 0 0 100%;
            max-width: 100%;
        }
        .section-title {
            font-size: 24px;
        }
        .stat-number {
            font-size: 28px;
        }
    }
</style>

<!-- Hero Section -->
<div class="hero-section text-center">
    <h1 class="hero-title">Поиск школ, вузов, статей</h1>
    <?php 
    renderSearchBox([
        'id' => 'homeSearch',
        'placeholder' => 'Поиск школ, вузов, статей...',
        'action' => '/search-process',
        'method' => 'GET',
        'inputName' => 'query',
        'size' => 'large',
        'showButton' => false,
        'autofocus' => false
    ]);
    ?>
</div>

<div class="container">
    
    <!-- 11-классники Posts Section -->
    <div class="news-grid">
        <?php
        $query11 = "SELECT * FROM posts WHERE category = 1 ORDER BY date_post DESC LIMIT 8";
        $result11 = mysqli_query($connection, $query11);
        
        if ($result11 && mysqli_num_rows($result11) > 0) {
            while ($row11 = mysqli_fetch_assoc($result11)) {
                renderPostCard($row11, '11-классники', '/category/11-klassniki', 'teal');
            }
        }
        ?>
    </div>
    
    <!-- Абитуриентам Posts Section -->
    <div class="news-grid">
        <?php
        $queryAbiturient = "SELECT * FROM posts WHERE category = 6 ORDER BY date_post DESC LIMIT 8";
        $resultAbiturient = mysqli_query($connection, $queryAbiturient);
        
        if ($resultAbiturient && mysqli_num_rows($resultAbiturient) > 0) {
            while ($rowAbiturient = mysqli_fetch_assoc($resultAbiturient)) {
                renderPostCard($rowAbiturient, 'Абитуриентам', '/category/abiturientam', 'orange');
            }
        }
        ?>
    </div>
    
</div>

<!-- Statistics Section -->
<div class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <?php
                        $countSchools = mysqli_query($connection, "SELECT COUNT(*) as count FROM schools");
                        echo number_format(mysqli_fetch_assoc($countSchools)['count']);
                        ?>
                    </div>
                    <div class="stat-label">Школ в базе</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <?php
                        $countVPO = mysqli_query($connection, "SELECT COUNT(*) as count FROM vpo");
                        echo number_format(mysqli_fetch_assoc($countVPO)['count']);
                        ?>
                    </div>
                    <div class="stat-label">ВУЗов</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <?php
                        $countSPO = mysqli_query($connection, "SELECT COUNT(*) as count FROM spo");
                        echo number_format(mysqli_fetch_assoc($countSPO)['count']);
                        ?>
                    </div>
                    <div class="stat-label">ССУЗов</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <?php
                        $countPosts = mysqli_query($connection, "SELECT COUNT(*) as count FROM posts");
                        echo number_format(mysqli_fetch_assoc($countPosts)['count']);
                        ?>
                    </div>
                    <div class="stat-label">Статей</div>
                </div>
            </div>
        </div>
    </div>
</div>