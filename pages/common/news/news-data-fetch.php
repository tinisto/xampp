<?php
// Check if the URL parameter is set
if (isset($_GET['url_news'])) {
  // Sanitize the input
  $urlNews = mysqli_real_escape_string($connection, $_GET['url_news']);

  // Fetch post data
  $queryNews = "SELECT * FROM news WHERE url_news = '$urlNews'";
  $resultNews = mysqli_query($connection, $queryNews);

  if ($resultNews && mysqli_num_rows($resultNews) > 0) {
    $newsData = mysqli_fetch_assoc($resultNews);
    $pageTitle = $newsData['title_news'];
    $metaD = $newsData['meta_d_news'];
    $metaK = $newsData['meta_k_news'];

    // Free the result set for news
    mysqli_free_result($resultNews);
  } else {
    // echo '<p class="text-danger">Post not found</p>';
    header("Location: /404");
    exit();
  }
} else {
  // echo '<p class="text-danger">Invalid URL</p>';
  header("Location: /404");
  exit();
}
