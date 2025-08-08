<?php
// Working category page - direct implementation with pagination
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get category
$categorySlug = $_GET['category_en'] ?? 'a-naposledok-ya-skazhu';
$query = "SELECT * FROM categories WHERE url_category = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $categorySlug);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if (!$category) {
    die("Category not found");
}

$categoryId = isset($category['id_category']) ? $category['id_category'] : $category['id'];

// Pagination setup
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$postsPerPage = 12; // Show 12 posts per page
$offset = ($page - 1) * $postsPerPage;

// Get total posts count
$countQuery = "SELECT COUNT(*) as total FROM posts WHERE category = ?";
$stmt = $connection->prepare($countQuery);
$stmt->bind_param("i", $categoryId);
$stmt->execute();
$countResult = $stmt->get_result();
$totalPosts = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalPosts / $postsPerPage);

// Get posts with pagination
$postsQuery = "SELECT id, title_post, text_post, url_slug, date_post 
               FROM posts 
               WHERE category = ? 
               ORDER BY date_post DESC
               LIMIT ? OFFSET ?";
$stmt = $connection->prepare($postsQuery);
$stmt->bind_param("iii", $categoryId, $postsPerPage, $offset);
$stmt->execute();
$postsResult = $stmt->get_result();

// Build posts array
$posts = [];
while ($row = $postsResult->fetch_assoc()) {
    $posts[] = [
        'id' => $row['id'],
        'title' => $row['title_post'],
        'url' => $row['url_slug'],
        'date' => $row['date_post'],
        'text' => $row['text_post']
    ];
}

// Start template sections with hash prevention script
$greyContent1 = '<script>
// Remove hash from URL on page load
if (window.location.hash) {
    history.replaceState(null, null, window.location.pathname + window.location.search);
}
// Prevent hash from being added to links
document.addEventListener("DOMContentLoaded", function() {
    // Override any click handlers that might add hash
    document.querySelectorAll("a").forEach(function(link) {
        if (link.href && link.href.endsWith("#")) {
            link.addEventListener("click", function(e) {
                if (this.href.endsWith("#")) {
                    e.preventDefault();
                }
            });
        }
    });
});
</script>
<div style="padding: 30px; text-align: center;">
    <style>
        .category-title-heading {
            color: #333;
            margin: 0 0 10px 0;
        }
        .category-subtitle {
            color: #666;
            margin: 0;
            opacity: 0.8;
        }
        
        /* Dark mode styles */
        html[data-theme="dark"] .category-title-heading,
        html[data-bs-theme="dark"] .category-title-heading,
        body.dark-mode .category-title-heading,
        .dark .category-title-heading,
        .dark-theme .category-title-heading {
            color: #ffffff !important;
        }
        
        html[data-theme="dark"] .category-subtitle,
        html[data-bs-theme="dark"] .category-subtitle,
        body.dark-mode .category-subtitle,
        .dark .category-subtitle,
        .dark-theme .category-subtitle {
            color: #cccccc !important;
        }
    </style>
    <h1 class="category-title-heading">' . htmlspecialchars($category['title_category']) . '</h1>
    <p class="category-subtitle">' . $totalPosts . ' статей</p>
</div>';

$greyContent2 = '';
$greyContent3 = '';
$greyContent4 = '';

// Main content - display posts
$greyContent5 = '<div style="padding: 20px;">';

if (!empty($posts)) {
    $greyContent5 .= '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">';
    
    foreach ($posts as $post) {
        $greyContent5 .= '
        <div style="background: white; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="padding: 20px;">
                <h3 style="margin: 0 0 10px 0;">
                    <a href="/post/' . htmlspecialchars($post['url']) . '" style="color: #333; text-decoration: none;">
                        ' . htmlspecialchars($post['title']) . '
                    </a>
                </h3>
                <p style="color: #666; font-size: 14px; margin: 10px 0;">
                    ' . date('d.m.Y', strtotime($post['date'])) . '
                </p>
                <p style="color: #555; font-size: 14px; line-height: 1.5;">
                    ' . htmlspecialchars(mb_substr(strip_tags($post['text']), 0, 150)) . '...
                </p>
                <a href="/post/' . htmlspecialchars($post['url']) . '" style="color: #0066cc; font-size: 14px;">
                    Читать далее →
                </a>
            </div>
        </div>';
    }
    
    $greyContent5 .= '</div>';
} else {
    $greyContent5 .= '<div style="text-align: center; padding: 40px; color: #666;">
        <p>В этой категории пока нет статей</p>
        <p>Категория ID: ' . $categoryId . '</p>
    </div>';
}

$greyContent5 .= '</div>';

// Section 6: Pagination
$greyContent6 = '';
if ($totalPages > 1) {
    ob_start();
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/pagination-modern.php';
    renderPaginationModern($page, $totalPages, '/category/' . $categorySlug . '?page=');
    $greyContent6 = ob_get_clean();
}

$blueContent = '';
$pageTitle = $category['title_category'] . ' - 11-классники';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>