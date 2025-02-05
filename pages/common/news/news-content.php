<?php
// Check if the URL parameter is set
if (isset($_GET['url_news'])) {
  // Sanitize the input
  $urlNews = mysqli_real_escape_string($connection, $_GET['url_news']);

  // Fetch news
  $queryNews = "SELECT * FROM news WHERE url_news = '$urlNews'";
  $resultNews = mysqli_query($connection, $queryNews);

  if ($resultNews) {
    // Display news
    while ($rowNews = mysqli_fetch_assoc($resultNews)) {

      // Check if the user has not visited the page during the current session
      if (!isset($_SESSION['visited'])) {
        // Increase view count
        $updatedViews = $rowNews['view_news'] + 1;
        $queryUpdateViews = "UPDATE news SET view_news = $updatedViews WHERE url_news = '$urlNews'";
        mysqli_query($connection, $queryUpdateViews);
        // Set the session variable to indicate that the user has visited the page
        $_SESSION['visited'] = true;
      }
      include $_SERVER['DOCUMENT_ROOT'] . '/pages/common/news/news-header-links/news-header-links.php';
?>

      <div class="d-flex justify-content-between align-items-center">
        <h1>
          <?php echo $rowNews['title_news'];
          ?>
        </h1>
        <div>
          <?php
          // Check if the user is an admin
          if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            echo '<a href="/pages/common/news/news-form.php?id_news=' . $rowNews['id_news'] . '" class="edit-icon"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;';
          }
          ?>
        </div>
      </div>

      <p class="lead fw-medium">
        <?php echo $rowNews['description_news']; ?>
      </p>

      <div class="row">
        <?php if (!empty($rowNews['image_news_1'])) : ?>
          <div class="col-md-4 mb-3">
            <img src="../images/news-images/<?= htmlspecialchars($rowNews['image_news_1']); ?>"
              class="img-fluid img-thumbnail" alt="Image 1">
          </div>
        <?php endif; ?>

        <?php if (!empty($rowNews['image_news_2'])) : ?>
          <div class="col-md-4 mb-3">
            <img src="../images/news-images/<?= htmlspecialchars($rowNews['image_news_2']); ?>"
              class="img-fluid img-thumbnail" alt="Image 2">
          </div>
        <?php endif; ?>

        <?php if (!empty($rowNews['image_news_3'])) : ?>
          <div class="col-md-4 mb-3">
            <img src="../images/news-images/<?= htmlspecialchars($rowNews['image_news_3']); ?>"
              class="img-fluid img-thumbnail" alt="Image 3">
          </div>
        <?php endif; ?>
      </div>
      <p>
        <?= nl2br($rowNews['text_news']) ?>
      </p>

<?php
    }

    // Free the result set for news
    mysqli_free_result($resultNews);
  } else {
    echo '<p class="text-danger">Error fetching news from the database</p>';
  }
} else {
  echo '<p class="text-danger">Invalid URL</p>';
}
?>