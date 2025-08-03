<?php
// Check if post data is already available from post-data-fetch.php
if (isset($postData)) {
  // Use the data already fetched
  $rowPost = $postData;

  // Check if the user has not visited the page during the current session
  if (!isset($_SESSION['visited'])) {
    // Increase view count
    $updatedViews = $rowPost['view_post'] + 1;
    $queryUpdateViews = "UPDATE posts SET view_post = $updatedViews WHERE id_post = " . (int)$rowPost['id_post'];
    mysqli_query($connection, $queryUpdateViews);
    // Set the session variable to indicate that the user has visited the page
    $_SESSION['visited'] = true;
  }
?>

<style>
  .post-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 60px 0 40px;
    margin-bottom: 40px;
  }
  .post-category-badge {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    color: white !important;
    padding: 6px 16px;
    border-radius: 25px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: 1px solid rgba(255,255,255,0.3);
    transition: all 0.3s ease;
  }
  .post-category-badge:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-1px);
  }
  .post-title {
    font-size: 42px;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 30px;
  }
  .post-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    opacity: 0.9;
    font-size: 14px;
  }
  .post-meta-left {
    display: flex;
    align-items: center;
    gap: 20px;
  }
  .post-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .post-content-wrapper {
    margin: 0 auto;
    padding: 0 20px;
  }
  .post-description {
    font-size: 20px;
    line-height: 1.6;
    color: var(--text-secondary, #666);
    margin-bottom: 30px;
    font-weight: 400;
  }
  .post-images {
    margin: 30px 0;
  }
  .post-image {
    width: 100%;
    max-width: 400px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    margin-bottom: 15px;
  }
  .post-bio {
    background: var(--surface-variant, #f8f9fa);
    padding: 20px;
    border-radius: 12px;
    margin: 20px 0;
    border-left: 4px solid var(--primary-color, #28a745);
    font-style: italic;
    color: var(--text-primary, #333);
  }
  .post-text {
    font-size: 16px;
    line-height: 1.6;
    color: var(--text-primary, #333);
  }
  .post-text p {
    margin-bottom: 15px;
  }
  .post-text strong {
    color: var(--primary-color, #28a745);
  }
  .admin-edit {
    position: absolute;
    top: 20px;
    right: 20px;
    background: rgba(255,255,255,0.2);
    padding: 8px 12px;
    border-radius: 8px;
    transition: all 0.3s ease;
  }
  .admin-edit:hover {
    background: rgba(255,255,255,0.3);
    transform: scale(1.05);
  }
  
  /* Dark mode support */
  [data-theme="dark"] .post-header {
    background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
  }
  
  [data-theme="dark"] .post-bio {
    background: var(--surface-variant, #2d3748);
    color: var(--text-primary, #f7fafc);
    border-left-color: var(--primary-color, #68d391);
  }
  
  [data-theme="dark"] .post-text {
    color: var(--text-primary, #f7fafc);
  }
  
  [data-theme="dark"] .post-description {
    color: var(--text-secondary, #cbd5e0);
  }
  
  [data-theme="dark"] .post-text strong {
    color: var(--primary-color, #68d391);
  }
  
  [data-theme="dark"] .post-image {
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
  }

  @media (max-width: 768px) {
    .post-title {
      font-size: 28px;
    }
    .post-header {
      padding: 40px 0 30px;
    }
    .post-meta {
      flex-direction: column;
      align-items: flex-start;
      gap: 15px;
    }
    .post-meta-left {
      flex-direction: column;
      align-items: flex-start;
      gap: 10px;
    }
    .post-text {
      font-size: 15px;
    }
  }
</style>

<div class="post-header">
  <div class="container position-relative">
      <?php 
      // Skip the problematic getEntityIdFromURL.php include
      // We already have the post data in $rowPost
      $entity_type = 'post';
      $id_entity = $rowPost['id_post'];
      
      // Get category name from database
      $categoryName = 'Статьи';
      $categoryUrl = '/';
      if (!empty($rowPost['category'])) {
        // Map category IDs to names (since categories table might not exist)
        $categoryMap = [
          1 => ['name' => '11-классники', 'url' => '11-klassniki'],
          2 => ['name' => 'Абитуриенты', 'url' => 'abiturient'],
          3 => ['name' => 'Статьи', 'url' => 'statyi']
        ];
        
        if (isset($categoryMap[$rowPost['category']])) {
          $categoryName = $categoryMap[$rowPost['category']]['name'];
          $categoryUrl = '/category/' . $categoryMap[$rowPost['category']]['url'];
        }
      }
      ?>

      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="/pages/dashboard/posts-dashboard/posts-view/posts-view-edit-form.php?id_post=<?= $rowPost['id_post'] ?>" class="admin-edit text-white">
          <i class="fas fa-edit"></i>
        </a>
      <?php endif; ?>

      <h1 class="post-title"><?= htmlspecialchars($rowPost['title_post']) ?></h1>
      
      <div class="post-meta">
        <div class="post-meta-left">
          <div class="post-meta-item">
            <i class="fas fa-user"></i>
            <span><?= htmlspecialchars($rowPost['author_post']) ?></span>
          </div>
          <div class="post-meta-item">
            <i class="fas fa-calendar"></i>
            <span><?= date('d F, Y', strtotime($rowPost['date_post'])) ?></span>
          </div>
          <div class="post-meta-item">
            <i class="fas fa-eye"></i>
            <span><?= number_format($rowPost['view_post']) ?></span>
          </div>
        </div>
        <div>
          <a href="<?= $categoryUrl ?>" class="post-category-badge text-decoration-none" style="position: static;"><?= $categoryName ?></a>
        </div>
  </div>
</div>

<div class="container">
    <?php if (!empty($rowPost['description_post'])): ?>
      <div class="post-description">
        <?= htmlspecialchars($rowPost['description_post']) ?>
      </div>
    <?php endif; ?>
    
    <div class="post-images">
      <?php
      // Image 1
      $imageFileName = isset($rowPost['image_post_1']) ? $rowPost['image_post_1'] : null;
      $imageUrl = $imageFileName ? "/images/posts-images/" . $imageFileName : null;
      $imagePath = $imageFileName ? $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/" . $imageFileName : null;

      if (!empty($rowPost['image_post_1']) && file_exists($imagePath)) {
        echo '<img src="' . $imageUrl . '" alt="Post Image 1" class="post-image">';
      }
      
      // Image 2
      $imageFileName = isset($rowPost['image_post_2']) ? $rowPost['image_post_2'] : null;
      $imageUrl = $imageFileName ? "/images/posts-images/" . $imageFileName : null;
      $imagePath = $imageFileName ? $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/" . $imageFileName : null;

      if (!empty($rowPost['image_post_2']) && file_exists($imagePath)) {
        echo '<img src="' . $imageUrl . '" alt="Post Image 2" class="post-image">';
      }
      
      // Image 3
      $imageFileName = isset($rowPost['image_post_3']) ? $rowPost['image_post_3'] : null;
      $imageUrl = $imageFileName ? "/images/posts-images/" . $imageFileName : null;
      $imagePath = $imageFileName ? $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/" . $imageFileName : null;

      if (!empty($rowPost['image_post_3']) && file_exists($imagePath)) {
        echo '<img src="' . $imageUrl . '" alt="Post Image 3" class="post-image">';
      }
      ?>
    </div>

    <?php if (!empty($rowPost['bio_post']) && 
              ((isset($rowPost['image_post_1']) && $rowPost['image_post_1']) ||
               (isset($rowPost['image_post_2']) && $rowPost['image_post_2']) ||
               (isset($rowPost['image_post_3']) && $rowPost['image_post_3']))): ?>
      <div class="post-bio">
        <?= $rowPost['bio_post'] ?>
      </div>
    <?php endif; ?>
    
    <div class="post-text">
      <?= $rowPost['text_post'] ?>
    </div>
</div>

<?php 
} else {
  echo '<div class="container"><p class="text-danger">Post not found or invalid URL</p></div>';
}
?>

<br clear="both">
<?php
include $_SERVER['DOCUMENT_ROOT'] . '/comments/user_comments.php';
?>