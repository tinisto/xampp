<?php
// Get news data from extracted variables (passed from template engine via extract())
// Variables are available directly after extract() in template engine

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
  include $_SERVER['DOCUMENT_ROOT'] . '/pages/common/news/news-header-links/news-header-links.php';
?>

      <div class="d-flex justify-content-between align-items-center">
        <h1>
          <?php echo $newsData['title_news']; ?>
        </h1>
        <div>
          <?php
          // Check if the user is an admin
          if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            echo '<a href="/pages/common/news/news-form.php?id_news=' . $newsData['id_news'] . '" class="edit-icon"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;';
          }
          ?>
        </div>
      </div>

      <p class="lead fw-medium">
        <?php echo $newsData['description_news']; ?>
      </p>

      <div class="row">
        <?php 
        // Check for image 1
        if (!empty($newsData['image_news_1'])) {
            $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/news-images/{$newsData['id_news']}_1.jpg";
            if (file_exists($imagePath)) : ?>
              <div class="col-md-4 mb-3">
                <img src="/images/news-images/<?= htmlspecialchars($newsData['id_news']) ?>_1.jpg"
                  class="img-fluid img-thumbnail" alt="Image 1">
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
                  class="img-fluid img-thumbnail" alt="Image 2">
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
                  class="img-fluid img-thumbnail" alt="Image 3">
              </div>
            <?php endif;
        } ?>
      </div>
      <p>
        <?= nl2br($newsData['text_news']) ?>
      </p>

<?php
} else {
  echo '<p class="text-danger">News data not found</p>';
}
?>