<?php
// Check if the URL parameter is set (support both parameter names)
if (isset($_GET['category_en']) || isset($_GET['url_category'])) {
  // Sanitize the input
  if (isset($_GET['category_en'])) {
    $urlCategory = mysqli_real_escape_string($connection, $_GET['category_en']);
  } else {
    $urlCategory = mysqli_real_escape_string($connection, $_GET['url_category']);
  }

  // Fetch category data using prepared statement
  $queryCategory = "SELECT * FROM categories WHERE url_category = ?";
  $stmt = mysqli_prepare($connection, $queryCategory);
  mysqli_stmt_bind_param($stmt, "s", $urlCategory);
  mysqli_stmt_execute($stmt);
  $resultCategory = mysqli_stmt_get_result($stmt);

  if ($resultCategory && mysqli_num_rows($resultCategory) > 0) {
    $categoryData = mysqli_fetch_assoc($resultCategory);
    $pageTitle = $categoryData['title_category'];
    $metaD = $categoryData['meta_description'] ?? '';

    // Free the result set for categories
    mysqli_free_result($resultCategory);
    mysqli_stmt_close($stmt);
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
