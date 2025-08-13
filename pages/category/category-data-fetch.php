<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/security/security_config.php';

// Initialize default values
$pageTitle = 'Категория не найдена';
$metaD = '';
$metaK = '';

// Check if the URL parameter is set
if (isset($_GET['url_category'])) {
  // Sanitize the input
  $urlCategory = sanitizeInput($_GET['url_category'], 'string');
  
  // Validate input
  if (validateInput($urlCategory, 'length', ['min' => 1, 'max' => 100])) {
    
    // Check if database connection exists
    if ($connection && !$connection->connect_error) {
      // Use prepared statement for security
      $stmt = $connection->prepare("SELECT id_category, title_category, url_category FROM categories WHERE url_category = ? LIMIT 1");
      $stmt->bind_param("s", $urlCategory);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result && $result->num_rows > 0) {
        $categoryData = $result->fetch_assoc();
        $pageTitle = htmlspecialchars($categoryData['title_category'], ENT_QUOTES, 'UTF-8');
        $metaD = "Статьи в категории: " . $categoryData['title_category'];
        $metaK = $categoryData['title_category'] . ", статьи, образование, 11 класс";
        
        // Store category ID for content fetching
        $GLOBALS['categoryId'] = $categoryData['id_category'];
        $categoryId = $categoryData['id_category'];
        
        $stmt->close();
      } else {
        // Category not found, redirect to 404
        header("Location: /pages/404/404.php");
        exit();
      }
    } else {
      // Database connection failed
      error_log("Database connection failed in category-data-fetch.php");
      header("Location: /pages/error/error.php");
      exit();
    }
  } else {
    // Invalid URL parameter
    header("Location: /pages/404/404.php");
    exit();
  }
} else {
  // No URL parameter provided
  header("Location: /pages/404/404.php");
  exit();
}
?>
