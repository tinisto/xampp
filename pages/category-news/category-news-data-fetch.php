<?php
// Check if the URL parameter is set
if (isset($_GET['url_category_news'])) {
  // Sanitize the input
  $urlCategoryNews = mysqli_real_escape_string($connection, $_GET['url_category_news']);

  // Fetch category data
  $queryCategoryNews = "SELECT * FROM news_categories WHERE url_category_news = '$urlCategoryNews'";
  $resultCategoryNews = mysqli_query($connection, $queryCategoryNews);


  if ($resultCategoryNews && mysqli_num_rows($resultCategoryNews) > 0) {
    $categoryData = mysqli_fetch_assoc($resultCategoryNews);
    $pageTitle = $categoryData['title_category_news'];
    $metaD = $categoryData['meta_d_category_news'];
    $metaK = $categoryData['meta_k_category_news'];

    // Free the result set for news_categories
    mysqli_free_result($resultCategoryNews);
  } else {
    // Redirect to 404 page
    header("Location: /404");
    exit();
  }
} else {
  // Redirect to 404 page
  header("Location: /404");
  exit();
}
