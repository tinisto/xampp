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

<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px; background: var(--background, #ffffff); color: var(--text-primary, #333);">
  <!-- Post Type Navigation -->
  <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 30px;">
      <?php
      $inactiveStyle = "padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: 400; transition: all 0.3s ease; background: var(--surface, #ffffff); color: var(--text-primary, #333); border: 1px solid var(--border-color, #e2e8f0);";
      ?>
      
      <a href="/posts" style="<?= $inactiveStyle ?>">Все посты</a>
      <a href="/posts/category/1" style="<?= $inactiveStyle ?>">Образование</a>
      <a href="/posts/category/2" style="<?= $inactiveStyle ?>">Карьера</a>
      <a href="/posts/category/3" style="<?= $inactiveStyle ?>">Студенческая жизнь</a>
  </div>

  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h1 style="color: var(--text-primary, #333); margin: 0;">
      <?php echo htmlspecialchars($rowPost['title_post']); ?>
    </h1>
    <div>
      <?php
      // Check if the user is an admin
      if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        echo '<div style="display: flex; gap: 10px;">';
        echo '<a href="/edit/post/' . (int)$rowPost['id_post'] . '" style="padding: 6px 12px; background: #3b82f6; color: white; text-decoration: none; border-radius: 6px; font-size: 14px; display: inline-flex; align-items: center; gap: 5px; transition: background 0.2s;"><i class="fas fa-edit"></i> Редактировать</a>';
        echo '<a href="/delete-post.php?id=' . (int)$rowPost['id_post'] . '" onclick="return confirm(\'Вы уверены, что хотите удалить этот пост?\')" style="padding: 6px 12px; background: #ef4444; color: white; text-decoration: none; border-radius: 6px; font-size: 14px; display: inline-flex; align-items: center; gap: 5px; transition: background 0.2s;"><i class="fas fa-trash"></i> Удалить</a>';
        echo '</div>';
      }
      ?>
    </div>
  </div>

  <!-- Date, Author and View Count -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--border-color, #e2e8f0);">
    <div style="color: var(--text-secondary, #666); font-size: 14px;">
      <span style="margin-right: 20px;">
        <i class="fas fa-user" style="margin-right: 5px;"></i>
        <?php echo htmlspecialchars($rowPost['author_post']); ?>
      </span>
      <span>
        <i class="fas fa-calendar-alt" style="margin-right: 5px;"></i>
        <?php 
        // Format the date
        $date = new DateTime($rowPost['date_post']);
        echo $date->format('d.m.Y');
        ?>
      </span>
    </div>
    <div style="color: var(--text-secondary, #666); font-size: 14px;">
      <i class="fas fa-eye" style="margin-right: 5px;"></i>
      <?php echo number_format((int)$rowPost['view_post']); ?> просмотров
    </div>
  </div>

  <?php if (!empty($rowPost['description_post'])): ?>
  <p class="lead fw-medium" style="color: var(--text-secondary, #666); font-size: 1.1em; margin-bottom: 20px;">
    <?php echo htmlspecialchars($rowPost['description_post']); ?>
  </p>
  <?php endif; ?>

  <?php 
  // Display main image if exists
  $imageToShow = null;
  
  // Check for new image_post field
  if (!empty($rowPost['image_post'])) {
      $imageToShow = $rowPost['image_post'];
  } 
  // If no image_post, check for old image fields
  elseif (!empty($rowPost['img1_post'])) {
      // Check if old format image exists on server
      $oldImagePath = "/images/post-images/{$rowPost['id_post']}_1.jpg";
      if (file_exists($_SERVER['DOCUMENT_ROOT'] . $oldImagePath)) {
          $imageToShow = $oldImagePath;
      }
  }
  
  // Display the image if we found one
  if ($imageToShow): ?>
    <div style="margin-bottom: 30px;">
      <img src="<?= htmlspecialchars($imageToShow) ?>" 
           alt="<?= htmlspecialchars($rowPost['title_post']) ?>" 
           style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    </div>
  <?php endif; ?>

  <?php if (!empty($rowPost['bio_post'])): ?>
  <div style="background: var(--light, #f8f9fa); padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid var(--primary, #3b82f6);">
    <div style="font-style: italic; color: var(--text-primary, #333);">
      <?php 
      // Safely display HTML content by allowing only specific tags
      $allowed_tags = '<p><br><strong><b><em><i><u><a><ul><ol><li><blockquote><h1><h2><h3><h4><h5><h6>';
      echo strip_tags($rowPost['bio_post'], $allowed_tags);
      ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if (!empty($rowPost['text_post'])): ?>
  <div style="color: var(--text-primary, #333); line-height: 1.6; font-size: 16px;">
    <?php 
    // Safely display HTML content by allowing only specific tags
    $allowed_tags = '<p><br><strong><b><em><i><u><a><ul><ol><li><blockquote><h1><h2><h3><h4><h5><h6><span><div>';
    echo strip_tags($rowPost['text_post'], $allowed_tags);
    ?>
  </div>
  <?php endif; ?>

  <!-- Additional Images -->
  <div class="row" style="margin-top: 30px;">
    <?php 
    // Check for image 2
    if (!empty($rowPost['img2_post'])) {
        $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/post-images/{$rowPost['id_post']}_2.jpg";
        if (file_exists($imagePath)) : ?>
          <div class="col-md-4 mb-3">
            <img src="/images/post-images/<?= htmlspecialchars($rowPost['id_post']) ?>_2.jpg"
              class="img-fluid img-thumbnail" alt="Image 2" style="border-radius: 8px;">
          </div>
        <?php endif;
    } ?>

    <?php 
    // Check for image 3
    if (!empty($rowPost['img3_post'])) {
        $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/post-images/{$rowPost['id_post']}_3.jpg";
        if (file_exists($imagePath)) : ?>
          <div class="col-md-4 mb-3">
            <img src="/images/post-images/<?= htmlspecialchars($rowPost['id_post']) ?>_3.jpg"
              class="img-fluid img-thumbnail" alt="Image 3" style="border-radius: 8px;">
          </div>
        <?php endif;
    } ?>
  </div>

  <!-- Comments Section -->
  <div style="margin-top: 50px;">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/comments/modern-comments-component.php'; ?>
  </div>

</div> <!-- Close container -->

<?php
} else {
  echo '<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px;">';
  echo '<div class="alert alert-danger">Post data not found</div>';
  echo '</div>';
}
?>