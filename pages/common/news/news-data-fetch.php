<?php
// Check if the URL parameter is set
if (isset($_GET['url_news'])) {
  // Sanitize the input
  $urlNews = mysqli_real_escape_string($connection, $_GET['url_news']);

  // Fetch news data using prepared statement for security  
  $queryNews = "SELECT * FROM news WHERE url_slug = ?";
  $stmt = mysqli_prepare($connection, $queryNews);
  mysqli_stmt_bind_param($stmt, "s", $urlNews);
  mysqli_stmt_execute($stmt);
  $resultNews = mysqli_stmt_get_result($stmt);

  if ($resultNews && mysqli_num_rows($resultNews) > 0) {
    $newsData = mysqli_fetch_assoc($resultNews);
    $pageTitle = $newsData['title_news'];
    $metaD = isset($newsData['meta_d_news']) ? $newsData['meta_d_news'] : $newsData['title_news'];
    $metaK = isset($newsData['meta_k_news']) ? $newsData['meta_k_news'] : '';

    mysqli_stmt_close($stmt);
  } else {
    mysqli_stmt_close($stmt);
    header("Location: /404");
    exit();
  }
} else {
  // echo '<p class="text-danger">Invalid URL</p>';
  header("Location: /404");
  exit();
}
