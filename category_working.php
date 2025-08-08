<?php
// Working category page with correct database credentials
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Use the correct production database credentials
$connection = new mysqli('11klassnikiru67871.ipagemysql.com', '11klone_user', 'K8HqqBV3hTf4mha', '11klassniki_ru');
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
$connection->set_charset('utf8mb4');

// Get category from URL
$urlCategory = isset($_GET['url_category']) ? $_GET['url_category'] : 'mir-uvlecheniy';
$urlCategory = $connection->real_escape_string($urlCategory);

// Fetch category data
$query = "SELECT * FROM categories WHERE url_category = '$urlCategory'";
$result = $connection->query($query);

if (!$result || $result->num_rows == 0) {
    die("Category not found");
}

$categoryData = $result->fetch_assoc();
$categoryId = $categoryData['id_category'];
$categoryTitle = $categoryData['title_category'];

// Count posts
$countQuery = "SELECT COUNT(*) as count FROM posts WHERE category = $categoryId";
$countResult = $connection->query($countQuery);
$postCount = $countResult->fetch_assoc()['count'];

// Pagination
$postsPerPage = 12;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $postsPerPage;
$totalPages = ceil($postCount / $postsPerPage);

// Fetch posts
$postsQuery = "SELECT * FROM posts WHERE category = $categoryId ORDER BY date_post DESC LIMIT $postsPerPage OFFSET $offset";
$postsResult = $connection->query($postsQuery);

// Category colors
$categoryColors = [
    'mir-uvlecheniy' => '#9333ea',
    'ege' => '#3b82f6',
    'oge' => '#14b8a6',
    'vpr' => '#f97316',
    'novosti' => '#22c55e',
    'olimpiady' => '#ef4444'
];
$categoryColor = $categoryColors[$urlCategory] ?? '#22c55e';
?>
<!DOCTYPE html>
<html lang="ru" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($categoryTitle) ?> - 11klassniki.ru</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f8f9fa;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #1a202c;
        }
        
        .page-subtitle {
            font-size: 1.125rem;
            color: #64748b;
        }
        
        .posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }
        
        .post-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
            position: relative;
        }
        
        .post-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border-color: <?= $categoryColor ?>;
        }
        
        .post-badge {
            position: absolute;
            top: 16px;
            left: 16px;
            background: <?= $categoryColor ?>;
            color: white;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            z-index: 1;
        }
        
        .post-image-placeholder {
            width: 100%;
            height: 180px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 48px;
        }
        
        .post-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        
        .post-content {
            padding: 20px;
        }
        
        .post-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 12px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .post-excerpt {
            color: #64748b;
            font-size: 14px;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .pagination a,
        .pagination span {
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .pagination a {
            background: white;
            border: 1px solid #e2e8f0;
            color: #374151;
        }
        
        .pagination a:hover {
            background: #f3f4f6;
            border-color: #d1d5db;
        }
        
        .pagination .current {
            background: <?= $categoryColor ?>;
            color: white;
        }
        
        .empty-message {
            text-align: center;
            padding: 60px 20px;
            color: #64748b;
        }
        
        /* Dark mode */
        [data-theme="dark"] body {
            background: #1e293b;
            color: #e2e8f0;
        }
        
        [data-theme="dark"] .page-title {
            color: #f1f5f9;
        }
        
        [data-theme="dark"] .post-card {
            background: #334155;
            border-color: #475569;
        }
        
        [data-theme="dark"] .post-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }
        
        [data-theme="dark"] .post-title {
            color: #f1f5f9;
        }
        
        [data-theme="dark"] .post-image-placeholder {
            background: #475569;
            color: #94a3b8;
        }
        
        [data-theme="dark"] .pagination a {
            background: #334155;
            border-color: #475569;
            color: #e2e8f0;
        }
        
        [data-theme="dark"] .pagination a:hover {
            background: #475569;
        }
        
        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }
            
            .posts-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1 class="page-title"><?= htmlspecialchars($categoryTitle) ?></h1>
            <p class="page-subtitle"><?= $postCount ?> <?= $postCount == 1 ? 'статья' : ($postCount < 5 ? 'статьи' : 'статей') ?></p>
        </div>
        
        <?php if ($postsResult && $postsResult->num_rows > 0): ?>
            <div class="posts-grid">
                <?php while ($post = $postsResult->fetch_assoc()): ?>
                    <a href="/post/<?= htmlspecialchars($post['url_post']) ?>" class="post-card">
                        <span class="post-badge"><?= htmlspecialchars($categoryTitle) ?></span>
                        
                        <?php
                        $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/{$post['id_post']}_1.jpg";
                        if (file_exists($imagePath)):
                        ?>
                            <img src="/images/posts-images/<?= $post['id_post'] ?>_1.jpg" 
                                 alt="<?= htmlspecialchars($post['title_post']) ?>" 
                                 class="post-image">
                        <?php else: ?>
                            <div class="post-image-placeholder">
                                <i class="fas fa-file-alt"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="post-content">
                            <h3 class="post-title"><?= htmlspecialchars($post['title_post']) ?></h3>
                            <?php if (!empty($post['text_post'])): ?>
                                <p class="post-excerpt"><?= htmlspecialchars(mb_substr(strip_tags($post['text_post']), 0, 150)) ?>...</p>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
            
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($currentPage > 1): ?>
                        <a href="?url_category=<?= $urlCategory ?>&page=<?= $currentPage - 1 ?>">←</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == $currentPage): ?>
                            <span class="current"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?url_category=<?= $urlCategory ?>&page=<?= $i ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?url_category=<?= $urlCategory ?>&page=<?= $currentPage + 1 ?>">→</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-message">
                <p>В этой категории пока нет статей.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Simple theme toggle
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
</body>
</html>
<?php
$connection->close();
?>