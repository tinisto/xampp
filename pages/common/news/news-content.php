<?php
// Get news data from extracted variables (passed from template engine via extract())
// Variables are available directly after extract() in template engine

// Debug if requested
if (isset($_GET['content_debug'])) {
    echo "<h2>News Content Debug</h2>";
    echo "newsData: " . (isset($newsData) ? "SET" : "NOT SET") . "<br>";
    echo "urlNews: " . (isset($urlNews) ? htmlspecialchars($urlNews) : "NOT SET") . "<br>";
    echo "connection: " . (isset($connection) ? "SET" : "NOT SET") . "<br>";
    if (isset($newsData)) {
        echo "<h3>News Data:</h3><pre>";
        print_r($newsData);
        echo "</pre>";
    }
    exit();
}

if (!empty($newsData) && !empty($urlNews)) {
  // Check if the user has not visited the page during the current session
  if (!isset($_SESSION['visited'])) {
    // Increase view count
    $updatedViews = (int)$newsData['view_news'] + 1;
    $queryUpdateViews = "UPDATE news SET view_news = ? WHERE url_news = ?";
    $stmt = mysqli_prepare($connection, $queryUpdateViews);
    mysqli_stmt_bind_param($stmt, 'is', $updatedViews, $urlNews);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    // Set the session variable to indicate that the user has visited the page
    $_SESSION['visited'] = true;
  }
  
  // Include news header links if file exists - commented out to remove "Опубликовано:" text
  // $headerLinksPath = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/news/news-header-links/news-header-links.php';
  // if (file_exists($headerLinksPath)) {
  //   include $headerLinksPath;
  // }
?>

<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px; background: var(--background, #ffffff); color: var(--text-primary, #333);">
  <!-- News Type Navigation -->
  <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 30px;">
      <?php
      $inactiveStyle = "padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: 400; transition: all 0.3s ease; background: var(--surface, #ffffff); color: var(--text-primary, #333); border: 1px solid var(--border-color, #e2e8f0);";
      ?>
      
      <a href="/news" style="<?= $inactiveStyle ?>">Все новости</a>
      <a href="/news/novosti-vuzov" style="<?= $inactiveStyle ?>">Новости ВПО</a>
      <a href="/news/novosti-spo" style="<?= $inactiveStyle ?>">Новости СПО</a>
      <a href="/news/novosti-shkol" style="<?= $inactiveStyle ?>">Новости школ</a>
      <a href="/news/novosti-obrazovaniya" style="<?= $inactiveStyle ?>">Новости образования</a>
  </div>

  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div style="display: flex; align-items: center; gap: 15px;">
      <h1 style="color: var(--text-primary, #333); margin: 0;">
        <?php echo htmlspecialchars($newsData['title_news']); ?>
      </h1>
      <?php if ($newsData['approved'] == 0): ?>
      <span style="background: #fbbf24; color: #000; padding: 4px 12px; border-radius: 20px; font-size: 14px; font-weight: 500;">
        📝 Черновик
      </span>
      <?php endif; ?>
    </div>
    <div>
      <?php
      // Check if the user is an admin
      if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        echo '<div style="display: flex; gap: 10px;">';
        echo '<a href="/edit/news/' . (int)$newsData['id_news'] . '" style="padding: 6px 12px; background: #3b82f6; color: white; text-decoration: none; border-radius: 6px; font-size: 14px; display: inline-flex; align-items: center; gap: 5px; transition: background 0.2s;"><i class="fas fa-edit"></i> Редактировать</a>';
        echo '<a href="/delete-news.php?id=' . (int)$newsData['id_news'] . '" onclick="return confirm(\'Вы уверены, что хотите удалить эту новость?\')" style="padding: 6px 12px; background: #ef4444; color: white; text-decoration: none; border-radius: 6px; font-size: 14px; display: inline-flex; align-items: center; gap: 5px; transition: background 0.2s;"><i class="fas fa-trash"></i> Удалить</a>';
        echo '</div>';
      }
      ?>
    </div>
  </div>

  <!-- Date and View Count -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--border-color, #e2e8f0);">
    <div style="color: var(--text-secondary, #666); font-size: 14px;">
      <i class="fas fa-calendar-alt" style="margin-right: 5px;"></i>
      <?php 
      // Format the date
      $date = new DateTime($newsData['date_news']);
      echo $date->format('d.m.Y в H:i');
      ?>
    </div>
    <div style="color: var(--text-secondary, #666); font-size: 14px;">
      <i class="fas fa-eye" style="margin-right: 5px;"></i>
      <?php echo number_format((int)$newsData['view_news']); ?> просмотров
    </div>
  </div>

  <?php if (!empty($newsData['description_news'])): ?>
  <p class="lead fw-medium" style="color: var(--text-secondary, #666); font-size: 1.1em; margin-bottom: 20px;">
    <?php echo htmlspecialchars($newsData['description_news']); ?>
  </p>
  <?php endif; ?>

  <?php 
  // Display main image if exists - check both new and old image fields
  $imageToShow = null;
  
  // First check for new image_news field
  if (!empty($newsData['image_news'])) {
      $imageToShow = $newsData['image_news'];
  } 
  // If no image_news, check for old image fields
  elseif (!empty($newsData['image_news_1'])) {
      // Check if old format image exists on server
      $oldImagePath = "/images/news-images/{$newsData['id_news']}_1.jpg";
      if (file_exists($_SERVER['DOCUMENT_ROOT'] . $oldImagePath)) {
          $imageToShow = $oldImagePath;
      }
  }
  
  // Display the image if we found one
  if ($imageToShow): ?>
    <div style="margin-bottom: 30px;">
      <img src="<?= htmlspecialchars($imageToShow) ?>" 
           alt="<?= htmlspecialchars($newsData['title_news']) ?>" 
           style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    </div>
  <?php endif; ?>

  <div class="row">
    <?php 
    // Check for image 1
    if (!empty($newsData['image_news_1'])) {
        $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/news-images/{$newsData['id_news']}_1.jpg";
        if (file_exists($imagePath)) : ?>
          <div class="col-md-4 mb-3">
            <img src="/images/news-images/<?= htmlspecialchars($newsData['id_news']) ?>_1.jpg"
              class="img-fluid img-thumbnail" alt="Image 1" style="border-radius: 8px;">
          </div>
        <?php endif;
    } ?>

    <?php 
    // Check for image 2
    if (!empty($newsData['image_news_2'])) {
        $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/news-images/{$newsData['id_news']}_2.jpg";
        if (file_exists($imagePath)) : ?>
          <div class="col-md-4 mb-3">
            <img src="/images/news-images/<?= htmlspecialchars($newsData['id_news']) ?>_2.jpg"
              class="img-fluid img-thumbnail" alt="Image 2" style="border-radius: 8px;">
          </div>
        <?php endif;
    } ?>

    <?php 
    // Check for image 3
    if (!empty($newsData['image_news_3'])) {
        $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/news-images/{$newsData['id_news']}_3.jpg";
        if (file_exists($imagePath)) : ?>
          <div class="col-md-4 mb-3">
            <img src="/images/news-images/<?= htmlspecialchars($newsData['id_news']) ?>_3.jpg"
              class="img-fluid img-thumbnail" alt="Image 3" style="border-radius: 8px;">
          </div>
        <?php endif;
    } ?>
  </div>
  
  <?php if (!empty($newsData['text_news'])): ?>
  <div style="color: var(--text-primary, #333); line-height: 1.6; font-size: 16px;">
    <?php 
    // Safely display HTML content by allowing only specific tags
    $allowed_tags = '<p><br><strong><b><em><i><u><a><ul><ol><li><blockquote><h1><h2><h3><h4><h5><h6><span><div>';
    echo strip_tags($newsData['text_news'], $allowed_tags);
    ?>
  </div>
  <?php endif; ?>

</div> <!-- Close container -->

<?php
} else {
  echo '<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px;">';
  echo '<div class="alert alert-danger">News data not found</div>';
  echo '<p>Debug info:</p>';
  echo '<ul>';
  echo '<li>newsData: ' . (isset($newsData) ? 'SET but empty' : 'NOT SET') . '</li>';
  echo '<li>urlNews: ' . (isset($urlNews) ? htmlspecialchars($urlNews) : 'NOT SET') . '</li>';
  echo '</ul>';
  echo '</div>';
}
?>