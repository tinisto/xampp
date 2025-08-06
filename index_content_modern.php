<?php
// Include loading placeholders and card badge component
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/loading-placeholders.php';
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/card-badge.php';
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
    @media (max-width: 576px) {
        .section-title {
            font-size: 24px;
        }
        .stat-card {
            padding: 15px 10px;
        }
        .stat-number {
            font-size: 28px;
        }
        .post-card {
            margin-bottom: 20px;
        }
    }
    .section-header {
        text-align: center;
        margin-bottom: 40px;
    }
    .section-title {
        font-size: 32px;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
    }
    .section-subtitle {
        font-size: 16px;
        color: #666;
    }
    .post-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        border: 1px solid rgba(0,0,0,0.06);
        position: relative;
    }
    .post-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        border-color: rgba(40, 167, 69, 0.2);
    }
    .post-image {
        width: 100%;
        height: 180px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .post-card:hover .post-image {
        transform: scale(1.02);
    }
    .post-image-container {
        position: relative;
        width: 100%;
        height: 180px;
        overflow: hidden;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .post-image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(0,0,0,0.7) 0%, transparent 50%);
        z-index: 1;
    }
    .post-content {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .post-title {
        font-size: 16px;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0;
        line-height: 1.5;
        flex: 1;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        transition: color 0.3s ease;
    }
    /* Dark mode styles */
    [data-bs-theme="dark"] .post-card {
        background: #1a202c;
        color: #e4e6eb;
        border-color: rgba(255,255,255,0.1);
    }
    [data-bs-theme="dark"] .post-card:hover {
        border-color: rgba(40, 167, 69, 0.3);
        box-shadow: 0 8px 25px rgba(0,0,0,0.25);
    }
    [data-bs-theme="dark"] .post-title {
        color: #f7fafc !important;
    }
    [data-bs-theme="dark"] .post-content {
        color: #e4e6eb;
    }
    [data-bs-theme="dark"] .post-card:hover .post-title {
        color: #28a745 !important;
    }
    [data-bs-theme="dark"] .post-image-container {
        background: linear-gradient(135deg, #2d3748, #1a202c);
    }
    [data-bs-theme="dark"] .stats-section {
        background: #2d3748;
    }
    [data-bs-theme="dark"] .stat-label {
        color: #718096;
    }
    .view-all-btn {
        text-align: center;
        margin-top: 40px;
    }
    .btn-outline {
        display: inline-block;
        padding: 12px 30px;
        border: 2px solid #28a745;
        color: #28a745;
        text-decoration: none;
        border-radius: 50px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn-outline:hover {
        background: #28a745;
        color: white;
        transform: translateY(-2px);
    }
    .stats-section {
        background: #f8f9fa;
        padding: 30px 0;
        margin: 30px 0;
    }
    .stat-card {
        text-align: center;
        padding: 15px;
    }
    .stat-number {
        font-size: 32px;
        font-weight: 700;
        color: #28a745;
        margin-bottom: 5px;
    }
    .stat-label {
        font-size: 16px;
        color: #666;
    }
</style>

<?php 
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/page-section-header.php';
renderPageSectionHeader([
    'title' => 'Поиск школ, вузов, статей',
    'showSearch' => true,
    'searchPlaceholder' => 'Поиск школ, вузов, статей...',
    'searchAction' => '/search-process',
    'searchName' => 'query'
]);
?>

<div class="container">
    
    
    <div class="row mb-5">
        <?php
        $query11 = "SELECT * FROM posts WHERE category = 1 ORDER BY date_post DESC LIMIT 6";
        $result11 = mysqli_query($connection, $query11);
        
        while ($row11 = mysqli_fetch_assoc($result11)) {
            $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/{$row11['id_post']}_1.jpg";
            $imageUrl = file_exists($imagePath) 
                ? "/images/posts-images/{$row11['id_post']}_1.jpg" 
                : "/images/posts-images/default.png";
            
            $date = new DateTime($row11['date_post']);
            $formattedDate = $date->format('d.m.Y');
        ?>
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="/post/<?= htmlspecialchars($row11['url_post']) ?>" class="text-decoration-none">
                <div class="post-card">
                    <div class="post-image-container">
                        <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($row11['title_post']) ?>" class="post-image">
                        <div class="post-image-overlay"></div>
                        <?php renderCardBadge('11-классники', '/category/11-klassniki', 'overlay', 'teal'); ?>
                    </div>
                    <div class="post-content">
                        <h3 class="post-title"><?= htmlspecialchars($row11['title_post']) ?></h3>
                    </div>
                </div>
            </a>
        </div>
        <?php } ?>
    </div>
    
    
    
    <div class="row mb-5">
        <?php
        $queryAbiturient = "SELECT * FROM posts WHERE category = 6 ORDER BY date_post DESC LIMIT 6";
        $resultAbiturient = mysqli_query($connection, $queryAbiturient);
        
        while ($rowAbiturient = mysqli_fetch_assoc($resultAbiturient)) {
            $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/{$rowAbiturient['id_post']}_1.jpg";
            $imageUrl = file_exists($imagePath) 
                ? "/images/posts-images/{$rowAbiturient['id_post']}_1.jpg" 
                : "/images/posts-images/default.png";
            
            $date = new DateTime($rowAbiturient['date_post']);
            $formattedDate = $date->format('d.m.Y');
        ?>
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="/post/<?= htmlspecialchars($rowAbiturient['url_post']) ?>" class="text-decoration-none">
                <div class="post-card">
                    <div class="post-image-container">
                        <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($rowAbiturient['title_post']) ?>" class="post-image">
                        <div class="post-image-overlay"></div>
                        <?php renderCardBadge('Абитуриентам', '/category/abiturientam', 'overlay', 'orange'); ?>
                    </div>
                    <div class="post-content">
                        <h3 class="post-title"><?= htmlspecialchars($rowAbiturient['title_post']) ?></h3>
                    </div>
                </div>
            </a>
        </div>
        <?php } ?>
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
                        echo mysqli_fetch_assoc($countSchools)['count'];
                        ?>
                    </div>
                    <div class="stat-label">Школ</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <?php
                        $countVPO = mysqli_query($connection, "SELECT COUNT(*) as count FROM vpo");
                        echo mysqli_fetch_assoc($countVPO)['count'];
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
                        echo mysqli_fetch_assoc($countSPO)['count'];
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
                        echo mysqli_fetch_assoc($countPosts)['count'];
                        ?>
                    </div>
                    <div class="stat-label">Статей</div>
                </div>
            </div>
        </div>
    </div>
</div>