<div class="container">
    <style>
        /* Breadcrumb navigation */
        .breadcrumb-nav {
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
            display: flex;
            flex-wrap: wrap;
            list-style: none;
        }
        
        .breadcrumb-item {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            content: "/";
            padding: 0 0.5rem;
            color: var(--text-muted);
        }
        
        .breadcrumb-item a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .breadcrumb-item a:hover {
            color: var(--accent-primary);
        }
        
        .breadcrumb-item.active {
            color: var(--text-primary);
            font-weight: 500;
        }
        
        /* Page header */
        .page-header {
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .page-description {
            font-size: 1.125rem;
            color: var(--text-secondary);
        }
        
        /* News card styling */
        .news-card {
            height: 220px;
            background: transparent;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }
        .news-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            border-color: var(--accent-primary, #667eea);
        }
        .news-card-img {
            height: 150px;
            overflow: hidden;
            position: relative;
        }
        .news-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .news-category-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(102, 126, 234, 0.9);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            backdrop-filter: blur(10px);
        }
        .news-card-body {
            padding: 1.25rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .news-card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-primary, #fff);
            line-height: 1.4;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .news-card-description {
            font-size: 0.875rem;
            color: var(--text-secondary, #999);
            margin-bottom: 1rem;
            line-height: 1.5;
            flex-grow: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
        .news-card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            color: var(--text-muted, #666);
        }
        .news-card-date {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .news-card-date i {
            font-size: 0.75rem;
        }
        
        /* Ensure Bootstrap grid works properly */
        .row {
            display: flex !important;
            flex-wrap: wrap !important;
            margin-right: -0.75rem !important;
            margin-left: -0.75rem !important;
        }
        
        .row > * {
            flex-shrink: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            padding-right: 0.75rem !important;
            padding-left: 0.75rem !important;
        }
        
        @media (min-width: 576px) {
            .col-sm-6 {
                flex: 0 0 auto !important;
                width: 50% !important;
            }
        }
        
        @media (min-width: 768px) {
            .col-md-6 {
                flex: 0 0 auto !important;
                width: 50% !important;
            }
        }
        
        @media (min-width: 992px) {
            .col-lg-3 {
                flex: 0 0 auto !important;
                width: 25% !important;
            }
        }
    </style>

    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb-nav" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/">
                    <i class="fas fa-home"></i> Главная
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Новости</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Новости образования</h1>
        <p class="page-description">Последние новости о ЕГЭ, поступлении в вузы и школьные события</p>
    </div>

    <div class="row">
        <?php
        // Check if database connection exists
        if ($connection && !$connection->connect_error) {
            // Get all news with category names
            $stmt = $connection->prepare("
                SELECT n.id_news, n.url_news, n.title_news, n.description_news, n.date_news, 
                       nc.title_category_news, n.image_news_1
                FROM news n
                LEFT JOIN news_categories nc ON n.category_news = nc.id_category_news
                WHERE n.approved = 1
                ORDER BY n.date_news DESC 
                LIMIT 24
            ");
            $stmt->execute();
            $resultNews = $stmt->get_result();
            
            if ($resultNews) {
                while ($rowNews = mysqli_fetch_assoc($resultNews)) {
        ?>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <a href="/news/<?php echo htmlspecialchars($rowNews['url_news']); ?>" class="text-decoration-none d-block">
                <div class="news-card">
                    <div class="news-card-img">
                        <?php if (!empty($rowNews['title_category_news'])): ?>
                            <span class="news-category-badge"><?php echo htmlspecialchars($rowNews['title_category_news'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endif; ?>
                        <?php
                        // Check for news image
                        $imagePath = '';
                        if (!empty($rowNews['image_news_1'])) {
                            $imagePath = "/images/news-images/" . $rowNews['image_news_1'];
                        } elseif (!empty($rowNews['id_news'])) {
                            // Try default pattern
                            $defaultImagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/news-images/{$rowNews['id_news']}_1.jpg";
                            if (file_exists($defaultImagePath)) {
                                $imagePath = "/images/news-images/{$rowNews['id_news']}_1.jpg";
                            }
                        }

                        if (!empty($imagePath) && file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)):
                        ?>
                            <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($rowNews['title_news']); ?>">
                        <?php else: ?>
                            <div style="height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                        <?php endif; ?>
                    </div>
                    <div class="news-card-body">
                        <h5 class="news-card-title"><?php echo htmlspecialchars($rowNews['title_news'], ENT_QUOTES, 'UTF-8'); ?></h5>
                    </div>
                </div>
            </a>
        </div>
        <?php
                }
            }
        } else {
            echo '<div class="col-12"><p class="text-center text-muted">База данных недоступна</p></div>';
        }
        ?>
    </div>
</div>