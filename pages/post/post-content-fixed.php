<?php
// Fixed version of post-content.php that handles HTML entities
if (isset($postData)) {
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
    flex-wrap: wrap;
    gap: 20px;
  }
  .post-meta-left {
    display: flex;
    align-items: center;
    gap: 25px;
    flex-wrap: wrap;
  }
  .post-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    opacity: 0.9;
  }
  .post-meta-item i {
    font-size: 14px;
    opacity: 0.8;
  }
  .post-description {
    font-size: 18px;
    line-height: 1.7;
    color: #555;
    margin-bottom: 30px;
    padding: 20px;
    background: #f8f9fa;
    border-left: 4px solid #28a745;
  }
  .post-content {
    font-size: 16px;
    line-height: 1.8;
    color: #333;
  }
  .post-content p {
    margin-bottom: 20px;
  }
  .post-content h2, .post-content h3 {
    margin-top: 30px;
    margin-bottom: 20px;
    color: #28a745;
  }
  .post-images {
    margin: 30px 0;
    text-align: center;
  }
  .post-image {
    max-width: 100%;
    height: auto;
    margin: 20px 0;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  }
  .admin-edit {
    position: absolute;
    top: 20px;
    right: 20px;
    background: rgba(0,0,0,0.5);
    color: white;
    padding: 8px 16px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 14px;
  }
  .admin-edit:hover {
    background: rgba(0,0,0,0.7);
  }
  @media (max-width: 768px) {
    .post-title {
      font-size: 28px;
    }
    .post-meta {
      flex-direction: column;
      align-items: flex-start;
    }
    .post-meta-left {
      flex-direction: column;
      align-items: flex-start;
      gap: 10px;
    }
  }
</style>

<div class="post-header">
  <div class="container position-relative">
      <?php 
      // Skip the problematic getEntityIdFromURL.php include
      $entity_type = 'post';
      $id_entity = $rowPost['id_post'];
      
      // Get category name
      $categoryName = 'Статьи';
      $categoryUrl = '/';
      if (!empty($rowPost['category'])) {
        // Map category IDs to names
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
          Редактировать
        </a>
      <?php endif; ?>

      <h1 class="post-title"><?= htmlspecialchars($rowPost['title_post']) ?></h1>
      
      <div class="post-meta">
        <div class="post-meta-left">
          <div class="post-meta-item">
            <i class="fas fa-user"></i>
            <span><?= htmlspecialchars($rowPost['author_post'] ?? 'Автор') ?></span>
          </div>
          <div class="post-meta-item">
            <i class="fas fa-calendar"></i>
            <span><?= date('d.m.Y', strtotime($rowPost['date_post'])) ?></span>
          </div>
          <div class="post-meta-item">
            <i class="fas fa-eye"></i>
            <span><?= number_format($rowPost['view_post']) ?></span>
          </div>
        </div>
        <div>
          <a href="<?= $categoryUrl ?>" class="post-category-badge text-decoration-none"><?= $categoryName ?></a>
        </div>
  </div>
</div>
</div>

<div class="container">
    <?php if (!empty($rowPost['description_post'])): ?>
      <div class="post-description">
        <?= html_entity_decode($rowPost['description_post'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>
    
    <div class="post-images">
      <?php
      // Display images if they exist
      for ($i = 1; $i <= 3; $i++) {
        $imageField = 'image_post_' . $i;
        if (!empty($rowPost[$imageField])) {
          $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/" . $rowPost[$imageField];
          if (file_exists($imagePath)) {
            echo '<img src="/images/posts-images/' . $rowPost[$imageField] . '" alt="Post Image ' . $i . '" class="post-image">';
          }
        } else {
          // Try default naming convention
          $defaultImage = $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/{$rowPost['id_post']}_{$i}.jpg";
          if (file_exists($defaultImage)) {
            echo '<img src="/images/posts-images/' . $rowPost['id_post'] . '_' . $i . '.jpg" alt="Post Image ' . $i . '" class="post-image">';
          }
        }
      }
      ?>
    </div>
    
    <article class="post-content">
      <?php 
      // The text already contains HTML, so we use html_entity_decode to properly display it
      echo html_entity_decode($rowPost['text_post'], ENT_QUOTES, 'UTF-8');
      ?>
    </article>
</div>

<?php } else { ?>
  <div class="container">
    <div class="alert alert-danger">
      <h3>Ошибка</h3>
      <p>Данные поста не найдены. Пожалуйста, вернитесь на главную страницу.</p>
      <a href="/" class="btn btn-primary">На главную</a>
    </div>
  </div>
<?php } ?>