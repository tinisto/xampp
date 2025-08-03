<?php
// Include reusable components
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/content-wrapper.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/typography.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/image-lazy-load.php';

// Get news data from extracted variables (passed from template engine via extract())
// Variables are available directly after extract() in template engine

renderContentWrapper('start');

if ($newsData && $urlNews) {
  // Check if the user has not visited the page during the current session
  if (!isset($_SESSION['visited'])) {
    // Increase view count
    $updatedViews = $newsData['view_news'] + 1;
    $queryUpdateViews = "UPDATE news SET view_news = $updatedViews WHERE url_news = '$urlNews'";
    mysqli_query($connection, $queryUpdateViews);
    // Set the session variable to indicate that the user has visited the page
    $_SESSION['visited'] = true;
  }
  
  // Use news data for header links
  $rowNews = $newsData; // Alias for compatibility with header links
  include $_SERVER['DOCUMENT_ROOT'] . '/pages/common/news/news-header-links/news-header-links-unified.php';
?>

  <style>
    .news-images-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 16px;
      margin-bottom: 32px;
    }
    
    .news-image-item {
      border-radius: 8px;
      overflow: hidden;
      background: var(--surface-variant, #f8f9fa);
    }
    
    .news-content {
      line-height: 1.8;
      color: var(--text-primary, #374151);
      white-space: pre-wrap;
    }
    
    .edit-icon {
      color: var(--text-secondary, #6b7280);
      font-size: 18px;
      transition: color 0.2s ease;
    }
    
    .edit-icon:hover {
      color: var(--primary-color, #28a745);
    }
    
    /* Dark mode */
    [data-theme="dark"] .news-content {
      color: var(--text-primary, #e5e7eb);
    }
    
    [data-theme="dark"] .news-image-item {
      background: var(--surface-variant, #374151);
    }
  </style>

  <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
    <div style="flex: 1;">
      <?php renderSectionTitle($newsData['title_news'], '', ['level' => 1]); ?>
      <?php if (!empty($newsData['description_news'])): ?>
        <?php renderText('<p class="lead">' . htmlspecialchars($newsData['description_news']) . '</p>', ['size' => 'large', 'color' => 'muted']); ?>
      <?php endif; ?>
    </div>
    <div>
      <?php
      // Check if the user is an admin
      if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        echo '<a href="/pages/common/news/news-form.php?id_news=' . $newsData['id_news'] . '" class="edit-icon"><i class="fas fa-edit"></i></a>';
      }
      ?>
    </div>
  </div>

  <?php 
  // Check for images and display them in a grid
  $hasImages = false;
  for ($i = 1; $i <= 3; $i++) {
    if (!empty($newsData["image_news_$i"])) {
      $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/news-images/{$newsData['id_news']}_$i.jpg";
      if (file_exists($imagePath)) {
        $hasImages = true;
        break;
      }
    }
  }
  
  if ($hasImages): ?>
    <div class="news-images-grid">
      <?php for ($i = 1; $i <= 3; $i++): 
        if (!empty($newsData["image_news_$i"])) {
          $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/news-images/{$newsData['id_news']}_$i.jpg";
          if (file_exists($imagePath)): ?>
            <div class="news-image-item">
              <?php renderLazyImage([
                'src' => "/images/news-images/" . htmlspecialchars($newsData['id_news']) . "_$i.jpg",
                'alt' => "Image $i",
                'aspectRatio' => '4:3'
              ]); ?>
            </div>
          <?php endif;
        }
      endfor; ?>
    </div>
  <?php endif; ?>
  
  <div class="news-content">
    <?= nl2br($newsData['text_news']) ?>
  </div>

<?php
} else {
  renderCallout('News data not found', 'error', 'Error');
}

renderContentWrapper('end');
?>