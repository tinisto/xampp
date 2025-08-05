<?php
// Get news data from additionalData (passed from template engine)
$newsData = isset($additionalData['newsData']) ? $additionalData['newsData'] : null;
$urlNews = isset($additionalData['urlNews']) ? $additionalData['urlNews'] : null;

if ($newsData && $urlNews) {
  // Check if the user has not visited the page during the current session
  if (!isset($_SESSION['visited'])) {
    // Increase view count
    $updatedViews = $newsData['view_news'] + 1;
    $queryUpdateViews = "UPDATE news SET view_news = $updatedViews WHERE url_slug = ?";
    $stmt = mysqli_prepare($connection, $queryUpdateViews);
    mysqli_stmt_bind_param($stmt, 's', $urlNews);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    // Set the session variable to indicate that the user has visited the page
    $_SESSION['visited'] = true;
  }

  // Get category name
  $categoryName = '';
  $categoryUrl = '';
  $queryCat = "SELECT title_category_news, url_category_news FROM news_categories WHERE id_category_news = ?";
  $stmtCat = mysqli_prepare($connection, $queryCat);
  mysqli_stmt_bind_param($stmtCat, 'i', $newsData['category_news']);
  mysqli_stmt_execute($stmtCat);
  $resultCat = mysqli_stmt_get_result($stmtCat);
  if ($rowCat = mysqli_fetch_assoc($resultCat)) {
    $categoryName = $rowCat['title_category_news'];
    $categoryUrl = '/category-news/' . $rowCat['url_category_news'];
  }
  mysqli_stmt_close($stmtCat);
?>

<style>
  .news-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 60px 0 40px;
    margin-bottom: 40px;
  }
  .news-category-badge {
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
    text-decoration: none;
  }
  .news-category-badge:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-1px);
    color: white !important;
  }
  .news-title {
    font-size: 42px;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 30px;
  }
  .news-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
    font-size: 16px;
    opacity: 0.9;
  }
  .news-meta-left {
    display: flex;
    align-items: center;
    gap: 20px;
  }
  .news-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .news-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 20px;
  }
  .news-content {
    background: white;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.08);
    margin-bottom: 40px;
    line-height: 1.8;
    font-size: 16px;
  }
  .news-content p {
    margin-bottom: 20px;
  }
  .news-description {
    font-size: 20px;
    color: #666;
    line-height: 1.6;
    margin-bottom: 30px;
    font-style: italic;
  }
  .news-images {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 30px 0;
  }
  .news-image {
    width: 100%;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
  }
  .news-image:hover {
    transform: scale(1.02);
  }
  .admin-edit {
    position: absolute;
    top: 20px;
    right: 20px;
    background: rgba(255,255,255,0.2);
    color: white;
    padding: 10px;
    border-radius: 50%;
    transition: all 0.3s ease;
    font-size: 18px;
  }
  .admin-edit:hover {
    background: rgba(255,255,255,0.3);
    transform: scale(1.1);
    color: white;
  }
  @media (max-width: 768px) {
    .news-header {
      padding: 40px 0 30px;
    }
    .news-title {
      font-size: 28px;
    }
    .news-content {
      padding: 25px;
      margin: 0 15px 30px;
    }
    .news-meta {
      flex-direction: column;
      align-items: flex-start;
      gap: 15px;
    }
    .news-images {
      grid-template-columns: 1fr;
      gap: 15px;
    }
  }
</style>

<div class="news-header">
  <div class="container">
    <div style="position: relative;">
      <?php if ($categoryName): ?>
        <a href="<?= $categoryUrl ?>" class="news-category-badge"><?= htmlspecialchars($categoryName) ?></a>
      <?php endif; ?>

      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="/pages/common/news/news-form.php?id_news=<?= $newsData['id_news'] ?>" class="admin-edit">
          <i class="fas fa-edit"></i>
        </a>
      <?php endif; ?>

      <h1 class="news-title"><?= htmlspecialchars($newsData['title_news']) ?></h1>
      
      <div class="news-meta">
        <div class="news-meta-left">
          <div class="news-meta-item">
            <i class="fas fa-eye"></i>
            <span><?= number_format($newsData['view_news']) ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="news-container">
  <div class="news-content">
    <?php if (!empty($newsData['description_news'])): ?>
      <p class="news-description"><?= htmlspecialchars($newsData['description_news']) ?></p>
    <?php endif; ?>

    <?php 
    // Check for images and construct proper image paths
    $images = [];
    
    // Check for image 1
    if (!empty($newsData['image_news_1'])) {
        $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/news-images/{$newsData['id_news']}_1.jpg";
        if (file_exists($imagePath)) {
            $images[] = "/images/news-images/{$newsData['id_news']}_1.jpg";
        }
    }
    
    // Check for image 2
    if (!empty($newsData['image_news_2'])) {
        $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/news-images/{$newsData['id_news']}_2.jpg";
        if (file_exists($imagePath)) {
            $images[] = "/images/news-images/{$newsData['id_news']}_2.jpg";
        }
    }
    
    // Check for image 3
    if (!empty($newsData['image_news_3'])) {
        $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/news-images/{$newsData['id_news']}_3.jpg";
        if (file_exists($imagePath)) {
            $images[] = "/images/news-images/{$newsData['id_news']}_3.jpg";
        }
    }
    
    if (!empty($images)): ?>
      <div class="news-images">
        <?php foreach($images as $imageUrl): ?>
          <img src="<?= htmlspecialchars($imageUrl) ?>" 
               class="news-image" 
               alt="<?= htmlspecialchars($newsData['title_news']) ?>">
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="news-text">
      <?= $newsData['text_news'] ?>
    </div>
  </div>
</div>

<?php
} else {
  header("Location: /404");
  exit();
}
?>