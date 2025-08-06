<?php
// Check if the URL parameter is set (support both parameter names)
if (isset($_GET['category_en']) || isset($_GET['url_category'])) {
  // Sanitize the input
  if (isset($_GET['category_en'])) {
    $urlCategory = mysqli_real_escape_string($connection, $_GET['category_en']);
  } else {
    $urlCategory = mysqli_real_escape_string($connection, $_GET['url_category']);
  }

  // Fetch category data
  $queryCategory = "SELECT * FROM categories WHERE url_category = '$urlCategory'";
  $resultCategory = mysqli_query($connection, $queryCategory);

  if ($resultCategory && mysqli_num_rows($resultCategory) > 0) {
    $categoryData = mysqli_fetch_assoc($resultCategory);
    $pageTitle = $categoryData['title_category'];
    $metaD = $categoryData['meta_description'] ?? '';

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
