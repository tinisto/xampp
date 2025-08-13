<?php
// Check if the URL parameter is set
if (isset($_GET['url_post'])) {
    // Sanitize the input
    $urlPost = mysqli_real_escape_string($connection, $_GET['url_post']);
    
    // Fetch posts with category information
    $queryPosts = "SELECT p.*, c.title_category 
                   FROM posts p 
                   LEFT JOIN categories c ON p.category = c.id_category 
                   WHERE p.url_post = '$urlPost'";
    $resultPosts = mysqli_query($connection, $queryPosts);
    
    if ($resultPosts) {
        // Display posts
        while ($rowPost = mysqli_fetch_assoc($resultPosts)) {
            
            // Check if the user has not visited the page during the current session
            if (!isset($_SESSION['visited'])) {
                // Increase view count
                $updatedViews = $rowPost['view_post'] + 1;
                $queryUpdateViews = "UPDATE posts SET view_post = $updatedViews WHERE url_post = '$urlPost'";
                mysqli_query($connection, $queryUpdateViews);
                // Set the session variable to indicate that the user has visited the page
                $_SESSION['visited'] = true;
            }
            
            require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/getEntityIdFromURL.php';
            $entity_type = 'post';
            $id_entity = getEntityIdFromPostURL($connection);
?>

<style>
    /* CNN-style News Article */
    .article-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1rem;
    }
    
    .article-grid {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 2rem;
    }
    
    .article-main {
        background: transparent;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        overflow: hidden;
    }
    
    .article-category {
        background: #c00;
        color: white;
        padding: 0.5rem 1rem;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.875rem;
        letter-spacing: 0.05em;
    }
    
    .article-header {
        padding: 2rem;
        border-bottom: 1px solid var(--border-color);
    }
    
    .article-title {
        font-size: 2.5rem;
        font-weight: 900;
        color: var(--text-primary);
        margin-bottom: 1rem;
        line-height: 1.1;
    }
    
    .article-subtitle {
        font-size: 1.25rem;
        color: var(--text-secondary);
        line-height: 1.4;
        margin-bottom: 1rem;
    }
    
    .article-meta {
        display: flex;
        gap: 1rem;
        color: var(--text-muted);
        font-size: 0.875rem;
        flex-wrap: wrap;
    }
    
    .article-meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .article-meta-item::after {
        content: "•";
        margin-left: 1rem;
        color: var(--text-muted);
    }
    
    .article-meta-item:last-child::after {
        display: none;
    }
    
    .article-hero-image {
        width: 100%;
        max-height: 500px;
        object-fit: cover;
    }
    
    .article-image-caption {
        padding: 1rem 2rem;
        background: rgba(0,0,0,0.1);
        font-size: 0.875rem;
        color: var(--text-secondary);
        border-bottom: 1px solid var(--border-color);
    }
    
    .article-body {
        padding: 2rem;
    }
    
    .article-content {
        font-size: 1.125rem;
        line-height: 1.75;
        color: var(--text-primary);
    }
    
    .article-content p {
        margin-bottom: 1.5rem;
    }
    
    .article-content p:first-child::first-letter {
        float: left;
        font-size: 4rem;
        line-height: 3rem;
        font-weight: 700;
        margin-right: 0.5rem;
        color: var(--accent-primary);
    }
    
    .article-images-inline {
        margin: 2rem 0;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }
    
    .article-image-inline {
        border-radius: 4px;
        overflow: hidden;
    }
    
    .article-image-inline img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    
    /* Sidebar */
    .article-sidebar {
        position: sticky;
        top: 1rem;
        height: fit-content;
    }
    
    .sidebar-section {
        background: transparent;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    
    .sidebar-header {
        background: #222;
        color: white;
        padding: 1rem;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.875rem;
    }
    
    .sidebar-content {
        padding: 1rem;
    }
    
    .related-item {
        padding: 1rem 0;
        border-bottom: 1px solid var(--border-color);
    }
    
    .related-item:last-child {
        border-bottom: none;
    }
    
    .related-title {
        color: var(--text-primary);
        font-weight: 600;
        line-height: 1.3;
        margin-bottom: 0.5rem;
        display: block;
        text-decoration: none;
        transition: color 0.2s;
    }
    
    .related-title:hover {
        color: var(--accent-primary);
    }
    
    .related-meta {
        font-size: 0.75rem;
        color: var(--text-muted);
    }
    
    /* Admin Bar */
    .admin-bar {
        background: #333;
        color: white;
        padding: 0.75rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .admin-bar a {
        color: #ffd700;
        text-decoration: none;
    }
    
    /* Comments Section */
    .comments-section {
        background: transparent;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        margin-top: 2rem;
        padding: 2rem;
    }
    
    .comments-header {
        border-bottom: 3px solid var(--accent-primary);
        padding-bottom: 1rem;
        margin-bottom: 2rem;
    }
    
    .comments-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
    }
    
    @media (max-width: 968px) {
        .article-grid {
            grid-template-columns: 1fr;
        }
        
        .article-title {
            font-size: 2rem;
        }
        
        .article-sidebar {
            position: static;
        }
    }
    
    @media (max-width: 768px) {
        .article-header,
        .article-body {
            padding: 1rem;
        }
        
        .article-title {
            font-size: 1.75rem;
        }
        
        .article-content {
            font-size: 1rem;
        }
    }
</style>

<div class="article-container">
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
        <div class="admin-bar">
            <span>Post ID: <?php echo $rowPost['id_post']; ?></span>
            <a href="/pages/dashboard/posts-dashboard/posts-view/posts-view-edit-form.php?id_post=<?php echo $rowPost['id_post']; ?>">
                <i class="fas fa-edit"></i> Edit Article
            </a>
        </div>
    <?php endif; ?>
    
    <div class="article-grid">
        <!-- Main Article -->
        <div class="article-main">
            <?php if (!empty($rowPost['title_category'])): ?>
                <div class="article-category"><?php echo htmlspecialchars($rowPost['title_category']); ?></div>
            <?php endif; ?>
            
            <div class="article-header">
                <h1 class="article-title"><?php echo htmlspecialchars($rowPost['title_post']); ?></h1>
                
                <?php if (!empty($rowPost['description_post'])): ?>
                    <p class="article-subtitle"><?php echo htmlspecialchars($rowPost['description_post']); ?></p>
                <?php endif; ?>
                
                <div class="article-meta">
                    <div class="article-meta-item">
                        <i class="far fa-clock"></i>
                        <?php echo date('d M Y, H:i', strtotime($rowPost['date_post'])); ?>
                    </div>
                    <div class="article-meta-item">
                        <i class="far fa-eye"></i>
                        <?php echo number_format($rowPost['view_post']); ?> просмотров
                    </div>
                </div>
            </div>
            
            <?php if (!empty($rowPost['image_post_1'])): 
                $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/" . $rowPost['image_post_1'];
                if (file_exists($imagePath)): ?>
                    <img src="/images/posts-images/<?php echo htmlspecialchars($rowPost['image_post_1']); ?>" 
                         alt="<?php echo htmlspecialchars($rowPost['title_post']); ?>" 
                         class="article-hero-image">
                    <?php if (!empty($rowPost['bio_post'])): ?>
                        <div class="article-image-caption">
                            <?php echo htmlspecialchars($rowPost['bio_post']); ?>
                        </div>
                    <?php endif; ?>
            <?php endif; endif; ?>
            
            <div class="article-body">
                <div class="article-content">
                    <?php 
                    // Display main text
                    echo nl2br($rowPost['text_post']); 
                    ?>
                </div>
                
                <?php 
                // Check for additional images
                $hasAdditionalImages = false;
                for ($i = 2; $i <= 3; $i++) {
                    if (!empty($rowPost["image_post_$i"])) {
                        $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/" . $rowPost["image_post_$i"];
                        if (file_exists($imagePath)) {
                            $hasAdditionalImages = true;
                            break;
                        }
                    }
                }
                
                if ($hasAdditionalImages): ?>
                    <div class="article-images-inline">
                        <?php for ($i = 2; $i <= 3; $i++) : ?>
                            <?php if (!empty($rowPost["image_post_$i"])) : 
                                $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/" . $rowPost["image_post_$i"];
                                if (file_exists($imagePath)): ?>
                                    <div class="article-image-inline">
                                        <img src="/images/posts-images/<?php echo htmlspecialchars($rowPost["image_post_$i"]); ?>" 
                                             alt="<?php echo htmlspecialchars($rowPost['title_post']); ?> - Image <?php echo $i; ?>">
                                    </div>
                            <?php endif; endif; ?>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Sidebar -->
        <aside class="article-sidebar">
            <!-- Related Articles -->
            <div class="sidebar-section">
                <div class="sidebar-header">Похожие статьи</div>
                <div class="sidebar-content">
                    <?php
                    // Fetch related articles from same category
                    $relatedQuery = "SELECT id_post, title_post, url_post, date_post 
                                   FROM posts 
                                   WHERE category = {$rowPost['category']} 
                                   AND id_post != {$rowPost['id_post']}
                                   ORDER BY date_post DESC 
                                   LIMIT 5";
                    $relatedResult = mysqli_query($connection, $relatedQuery);
                    
                    if ($relatedResult && mysqli_num_rows($relatedResult) > 0):
                        while ($related = mysqli_fetch_assoc($relatedResult)): ?>
                            <div class="related-item">
                                <a href="/post/<?php echo htmlspecialchars($related['url_post']); ?>" class="related-title">
                                    <?php echo htmlspecialchars($related['title_post']); ?>
                                </a>
                                <div class="related-meta">
                                    <?php echo date('d M Y', strtotime($related['date_post'])); ?>
                                </div>
                            </div>
                        <?php endwhile;
                    else: ?>
                        <p style="color: var(--text-muted); text-align: center;">Нет похожих статей</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Latest News -->
            <div class="sidebar-section">
                <div class="sidebar-header">Последние новости</div>
                <div class="sidebar-content">
                    <?php
                    // Fetch latest articles
                    $latestQuery = "SELECT id_post, title_post, url_post, date_post 
                                  FROM posts 
                                  WHERE id_post != {$rowPost['id_post']}
                                  ORDER BY date_post DESC 
                                  LIMIT 5";
                    $latestResult = mysqli_query($connection, $latestQuery);
                    
                    if ($latestResult && mysqli_num_rows($latestResult) > 0):
                        while ($latest = mysqli_fetch_assoc($latestResult)): ?>
                            <div class="related-item">
                                <a href="/post/<?php echo htmlspecialchars($latest['url_post']); ?>" class="related-title">
                                    <?php echo htmlspecialchars($latest['title_post']); ?>
                                </a>
                                <div class="related-meta">
                                    <?php echo date('d M Y', strtotime($latest['date_post'])); ?>
                                </div>
                            </div>
                        <?php endwhile;
                    endif; ?>
                </div>
            </div>
        </aside>
    </div>
    
    <!-- Comments Section -->
    <div class="comments-section">
        <div class="comments-header">
            <h2 class="comments-title">Комментарии</h2>
        </div>
        
        <?php
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/getEntityIdFromURL.php';
        $result = getEntityIdFromPostURL($connection);
        $id_entity = $result['id_entity'];
        $entity_type = $result['entity_type'];
        
        // Check if the user is logged in
        if (isset($_SESSION['email']) && isset($_SESSION['avatar'])) {
            $user = $_SESSION['email'];
            $avatar = $_SESSION['avatar'];
        }
        
        require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/get_avatar.php";
        include $_SERVER['DOCUMENT_ROOT'] . '/comments/user_comments_modern.php';
        ?>
    </div>
</div>

<?php
        }
        
        // Free the result set for posts
        mysqli_free_result($resultPosts);
    } else {
        echo '<div class="article-container"><p style="color: var(--text-muted); text-align: center;">Error loading article</p></div>';
    }
} else {
    echo '<div class="article-container"><p style="color: var(--text-muted); text-align: center;">Invalid URL</p></div>';
}
?>