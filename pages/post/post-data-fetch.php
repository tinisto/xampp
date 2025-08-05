<?php
// Include database connection if not already included
if (!isset($connection) || !$connection) {
  require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
}

// Verify database connection exists
if (!isset($connection) || !$connection) {
  error_log("Database connection not available in post-data-fetch.php");
  header("Location: /error");
  exit();
}

if (isset($_GET['url_post']) && !empty($_GET['url_post'])) {
  $urlPost = mysqli_real_escape_string($connection, $_GET['url_post']);

  // Use prepared statement for better security
  $queryPost = "SELECT * FROM posts WHERE url_slug = ?";
  $stmt = mysqli_prepare($connection, $queryPost);
  
  if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $urlPost);
    if (!mysqli_stmt_execute($stmt)) {
      error_log("Error executing statement in post-data-fetch.php: " . mysqli_stmt_error($stmt));
      header("Location: /error");
      exit();
    }
    $resultPost = mysqli_stmt_get_result($stmt);

    if ($resultPost && mysqli_num_rows($resultPost) > 0) {
      $postData = mysqli_fetch_assoc($resultPost);
      $pageTitle = $postData['title_post'] ?? 'Post';
      $metaD = $postData['meta_d_post'] ?? '';
      $metaK = $postData['meta_k_post'] ?? '';
    } else {
      header("Location: /404");
      exit();
    }
    
    mysqli_stmt_close($stmt);
  } else {
    error_log("Error preparing statement in post-data-fetch.php: " . mysqli_error($connection));
    header("Location: /error");
    exit();
  }
} else {
  header("Location: /404");
  exit();
}
