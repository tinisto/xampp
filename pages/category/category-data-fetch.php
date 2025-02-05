<?php
// Check if the URL parameter is set
if (isset($_GET['url_category'])) {
  // Sanitize the input
  $urlCategory = mysqli_real_escape_string($connection, $_GET['url_category']);

  // Fetch category data
  $queryCategory = "SELECT * FROM categories WHERE url_category = '$urlCategory'";
  $resultCategory = mysqli_query($connection, $queryCategory);

  if ($resultCategory && mysqli_num_rows($resultCategory) > 0) {
    $categoryData = mysqli_fetch_assoc($resultCategory);
    $pageTitle = $categoryData['title_category'];
    $metaD = $categoryData['meta_d_category'];
    $metaK = $categoryData['meta_k_category'];

    // Free the result set for categories
    mysqli_free_result($resultCategory);
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
