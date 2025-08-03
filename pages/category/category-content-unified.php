<?php
// Include the new reusable components
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/content-wrapper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/page-header-compact.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/card-badge.php';
// Typography is included after page-header to avoid conflicts
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/typography.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/image-lazy-load.php';
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/pagination.php";

// Define category colors
$categoryColors = [
    'mir-uvlecheniy' => 'purple',
    'ege' => 'blue', 
    'oge' => 'teal',
    'vpr' => 'orange',
    'novosti' => 'green',
    'olimpiady' => 'red'
];

renderContentWrapper('start');

// Check if categoryData is already set from category-data-fetch.php
if (isset($categoryData) && !empty($categoryData)) {
    // Category data is already loaded, use it directly
        
        // Count posts in this category
        $countQuery = "SELECT COUNT(*) as count FROM posts WHERE category = ?";
        $countStmt = mysqli_prepare($connection, $countQuery);
        mysqli_stmt_bind_param($countStmt, 'i', $categoryData['id_category']);
        mysqli_stmt_execute($countStmt);
        $countResult = mysqli_stmt_get_result($countStmt);
        $postCount = mysqli_fetch_assoc($countResult)['count'];
        mysqli_stmt_close($countStmt);
        
        // Format the subtitle with post count
        $subtitle = $postCount . ' ' . ($postCount == 1 ? 'статья' : ($postCount < 5 ? 'статьи' : 'статей'));
        
        // Get category color
        $categoryColor = $categoryColors[$categoryData['url_category']] ?? 'green';

        // Use the compact page header component
        renderPageHeaderCompact(
            $categoryData['title_category'],
            $subtitle,
            ['showSubtitle' => true]
        );

        // Pagination setup
        $postsPerPage = 12;
        $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($currentPage - 1) * $postsPerPage;

        // Fetch posts for this category
        $queryPosts = "SELECT * FROM posts WHERE category = ? ORDER BY date_post DESC LIMIT ? OFFSET ?";
        $postsStmt = mysqli_prepare($connection, $queryPosts);
        mysqli_stmt_bind_param($postsStmt, 'iii', $categoryData['id_category'], $postsPerPage, $offset);
        mysqli_stmt_execute($postsStmt);
        $postsResult = mysqli_stmt_get_result($postsStmt);
        
        if (mysqli_num_rows($postsResult) > 0) {
            ?>
            <style>
                .posts-grid {
                    display: grid;
                    grid-template-columns: repeat(4, 1fr);
                    gap: 24px;
                    margin-bottom: 30px;
                    padding-bottom: 0;
                }
                
                .post-card {
                    background: var(--surface, white);
                    border: 1px solid var(--border-color, #e2e8f0);
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
                    border-color: var(--primary-color, #28a745);
                    text-decoration: none;
                    color: inherit;
                }
                
                .post-image-wrapper {
                    width: 100%;
                    background: var(--surface-variant, #f8f9fa);
                }
                
                .post-image {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }
                
                .post-image-placeholder {
                    width: 100%;
                    height: 180px;
                    background: var(--surface-variant, #f8f9fa);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: var(--text-secondary, #6b7280);
                    font-size: 48px;
                }
                
                .post-content {
                    padding: 20px;
                }
                
                .post-title {
                    font-size: 18px;
                    font-weight: 600;
                    margin: 0 0 12px 0;
                    line-height: 1.4;
                    color: var(--text-primary, #1a202c);
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }
                
                .post-excerpt {
                    color: var(--text-secondary, #64748b);
                    font-size: 14px;
                    line-height: 1.5;
                    margin: 0;
                    display: -webkit-box;
                    -webkit-line-clamp: 3;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }
                
                /* Dark mode */
                [data-theme="dark"] .post-card {
                    background: var(--surface, #2d3748);
                    border-color: var(--border-color, #4a5568);
                }
                
                [data-theme="dark"] .post-card:hover {
                    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
                }
                
                [data-theme="dark"] .post-title {
                    color: var(--text-primary, #f7fafc);
                }
                
                [data-theme="dark"] .post-image-placeholder {
                    background: var(--surface-variant, #4a5568);
                }
                
                @media (max-width: 1200px) {
                    .posts-grid {
                        grid-template-columns: repeat(3, 1fr);
                    }
                }
                
                @media (max-width: 900px) {
                    .posts-grid {
                        grid-template-columns: repeat(2, 1fr);
                        gap: 20px;
                    }
                }
                
                @media (max-width: 600px) {
                    .posts-grid {
                        grid-template-columns: 1fr;
                        gap: 16px;
                        margin-left: 0;
                        margin-right: 0;
                    }
                }
            </style>
            
            <div class="posts-grid">
                <?php while ($post = mysqli_fetch_assoc($postsResult)): ?>
                    <a href="/post/<?= htmlspecialchars($post['url_post']) ?>" class="post-card">
                        <?php
                        // Add category badge with dynamic color
                        renderCardBadge($categoryData['title_category'], '', 'overlay', $categoryColor);
                        
                        $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/{$post['id_post']}_1.jpg";
                        if (file_exists($imagePath)):
                            renderLazyImage([
                                'src' => "/images/posts-images/" . htmlspecialchars($post['id_post']) . "_1.jpg",
                                'alt' => htmlspecialchars($post['title_post']),
                                'class' => 'post-image',
                                'aspectRatio' => '16:9'
                            ]);
                        else:
                        ?>
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
            
            <?php
            // Pagination
            if ($postCount > $postsPerPage) {
                $totalPages = ceil($postCount / $postsPerPage);
                $currentUrl = "/category/" . $categoryData['url_category'];
                
                echo '<div style="display: flex; justify-content: center; margin-top: 30px; margin-bottom: 0;">';
                renderPagination($currentUrl, $currentPage, $totalPages);
                echo '</div>';
            }
        } else {
            renderCallout('В этой категории пока нет статей.', 'info', 'Нет статей');
        }
        
        mysqli_stmt_close($postsStmt);
} else {
    renderCallout('Категория не найдена или не содержит статей.', 'error', 'Ошибка');
}

renderContentWrapper('end');
?>