<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/unified-template.php';

// Database connection
if (!isset($connection)) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db_connect.php';
}

// Check if the URL parameter is set
if (isset($_GET['url_category'])) {
    // Sanitize the input
    $urlCategory = mysqli_real_escape_string($connection, $_GET['url_category']);

    // Fetch category data
    $queryCategory = "SELECT * FROM categories WHERE url_category = '$urlCategory'";
    $resultCategory = mysqli_query($connection, $queryCategory);

    if ($resultCategory && mysqli_num_rows($resultCategory) > 0) {
        $categoryData = mysqli_fetch_assoc($resultCategory);
        $pageTitle = $categoryData['title_category'];
        $metaD = $categoryData['meta_d_category'];
        $metaK = $categoryData['meta_k_category'];

        // Fetch posts for this category
        $queryPosts = "SELECT * FROM posts WHERE category = {$categoryData['id_category']} AND approved = 1 ORDER BY date_post DESC LIMIT 20";
        $resultPosts = mysqli_query($connection, $queryPosts);
        $posts = $resultPosts ? mysqli_fetch_all($resultPosts, MYSQLI_ASSOC) : [];

        // Count total posts
        $totalPosts = count($posts);

        // Free the result sets
        mysqli_free_result($resultCategory);
        if ($resultPosts) {
            mysqli_free_result($resultPosts);
        }
    } else {
        // Redirect to 404 page
        header("Location: /404");
        exit();
    }
} else {
    // Redirect to 404 page
    header("Location: /404");
    exit();
}

// Prepare page content
ob_start();
?>

<div class="news-grid">
    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>
            <article class="news-card">
                <div class="news-image-container card-image-container">
                    <?php 
                    // Check for image
                    $image = '';
                    if (!empty($post['image_post']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/images/' . $post['image_post'])) {
                        $image = '/images/' . $post['image_post'];
                    }
                    ?>
                    <?php if ($image): ?>
                        <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($post['title_post']) ?>" class="news-image">
                    <?php else: ?>
                        <div class="news-image">
                            <i class="fas fa-newspaper fa-2x"></i>
                        </div>
                    <?php endif; ?>
                    <?php renderCardBadge($categoryData['title_category'], '', 'overlay', 'blue'); ?>
                </div>
                <div class="news-content">
                    <h3 class="news-title">
                        <a href="/post/<?= htmlspecialchars($post['url_post']) ?>">
                            <?= htmlspecialchars($post['title_post']) ?>
                        </a>
                    </h3>
                </div>
            </article>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-newspaper"></i>
            <h3>Статьи скоро появятся</h3>
            <p>Мы работаем над наполнением этой категории актуальными статьями.</p>
        </div>
    <?php endif; ?>
</div>

<?php
$pageContent = ob_get_clean();

// Render the unified page
$options = [
    'headerStats' => [
        ['number' => $totalPosts, 'label' => 'Статей']
    ],
    'showSearch' => true,
    'searchPlaceholder' => 'Поиск статей...',
    'searchId' => 'categorySearch',
    'metaDescription' => $metaD,
    'metaKeywords' => $metaK
];

renderUnifiedPage($pageTitle, $pageContent, $options);
?>

<script>
// Category search functionality
document.getElementById('categorySearch').addEventListener('input', function(e) {
    const search = e.target.value.toLowerCase();
    document.querySelectorAll('.news-card').forEach(card => {
        const title = card.querySelector('.news-title').textContent.toLowerCase();
        const isVisible = title.includes(search);
        card.style.display = isVisible ? '' : 'none';
    });
});
</script>