<?php
// Include news card component
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/news-card.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/search-box.php';
?>

<style>
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px 0;
        margin-bottom: 30px;
    }
    .hero-title {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 12px;
    }
    .hero-subtitle {
        font-size: 16px;
        opacity: 0.9;
        margin-bottom: 20px;
    }
    .hero-search {
        max-width: 600px;
        margin: 0 auto;
        position: relative;
    }
    
    @media (max-width: 768px) {
        .hero-title {
            font-size: 24px;
        }
        .hero-subtitle {
            display: none;
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
    
    /* Custom news grid for homepage - smaller than main news page */
    .homepage-news-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 40px;
    }
    
    @media (max-width: 1200px) {
        .homepage-news-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 900px) {
        .homepage-news-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
    }
    @media (max-width: 600px) {
        .homepage-news-grid {
            grid-template-columns: 1fr;
        }
    }
    
    .view-all-btn {
        text-align: center;
        margin-top: 30px;
    }
    .btn-outline {
        display: inline-block;
        padding: 12px 30px;
        border: 2px solid var(--primary-color, #28a745);
        color: var(--primary-color, #28a745);
        text-decoration: none;
        border-radius: 50px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn-outline:hover {
        background: var(--primary-color, #28a745);
        color: white;
        transform: translateY(-2px);
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
<div class="hero-section">
    <div class="container text-center">
        <h1 class="hero-title">Поиск школ, вузов, статей</h1>
        <p class="hero-subtitle">Образовательный портал для школьников и абитуриентов</p>
        <div class="hero-search">
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
    </div>
</div>

<div class="container">
    
    <!-- Latest News Section -->
    <div class="section-header">
        <h2 class="section-title">Последние новости</h2>
        <p class="section-subtitle">Актуальные новости образования</p>
    </div>
    
    <div class="homepage-news-grid">
        <?php
        // Fetch latest news with category information
        $newsQuery = "SELECT n.*, nc.title_category_news, nc.url_category_news 
                      FROM news n
                      LEFT JOIN news_categories nc ON n.category_news = nc.id_category_news
                      WHERE n.approved = 1 
                      ORDER BY n.date_news DESC 
                      LIMIT 8";
        $newsResult = mysqli_query($connection, $newsQuery);
        
        if ($newsResult && mysqli_num_rows($newsResult) > 0) {
            while ($newsItem = mysqli_fetch_assoc($newsResult)) {
                renderNewsCard($newsItem, true);
            }
        } else {
            // Fallback to posts if no news available
            $postsQuery = "SELECT id_post as id_news, title_post as title_news, text_post as text_news, 
                          url_post as url_news, date_post as date_news, 
                          'Статьи' as title_category_news, 'stati' as url_category_news
                          FROM posts 
                          ORDER BY date_post DESC 
                          LIMIT 8";
            $postsResult = mysqli_query($connection, $postsQuery);
            
            if ($postsResult && mysqli_num_rows($postsResult) > 0) {
                while ($postItem = mysqli_fetch_assoc($postsResult)) {
                    renderNewsCard($postItem, true);
                }
            } else {
                echo '<div class="empty-state">
                        <i class="fas fa-newspaper"></i>
                        <h3>Пока нет новостей</h3>
                        <p>Новости появятся здесь в ближайшее время</p>
                      </div>';
            }
        }
        ?>
    </div>
    
    <div class="view-all-btn">
        <a href="/news" class="btn-outline">Все новости</a>
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
                        $countNews = mysqli_query($connection, "SELECT COUNT(*) as count FROM news WHERE approved = 1");
                        echo number_format(mysqli_fetch_assoc($countNews)['count']);
                        ?>
                    </div>
                    <div class="stat-label">Новостей</div>
                </div>
            </div>
        </div>
    </div>
</div>