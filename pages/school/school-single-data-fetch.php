<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Establish database connection only if not already established
if (!isset($connection)) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
}

include $_SERVER['DOCUMENT_ROOT'] . '/pages/school/extract-school-id.php';

if (!is_numeric($id_school)) {
  // Redirect to 404 page
  header("Location: /404");
  exit();
}

$id_school = preg_replace("/[^0-9]/", "", $id_school);

// Check if the user has not visited the page during the current session
if (!isset($_SESSION['visited'])) {
  // Update the view count
  $updateViewQuery = "UPDATE schools SET view = view + 1 WHERE id_school=?";
  $stmtUpdateView = mysqli_prepare($connection, $updateViewQuery);
  if ($stmtUpdateView) {
    mysqli_stmt_bind_param($stmtUpdateView, "i", $id_school);
    if (!mysqli_stmt_execute($stmtUpdateView)) {
      // Log the error and redirect to the error page
      error_log("Error updating view count: " . mysqli_stmt_error($stmtUpdateView));
      header("Location: /error");
      exit();
    }
    mysqli_stmt_close($stmtUpdateView);
  } else {
    // Log the error and redirect to the error page
    error_log("Error preparing update view statement: " . mysqli_error($connection));
    header("Location: /error");
    exit();
  }

  // Set the session variable to indicate that the user has visited the page
  $_SESSION['visited'] = true;
}

// Select school data using prepared statements
$query = "SELECT * FROM schools WHERE id_school=? AND approved='1'";
$stmtSelectSchool = mysqli_prepare($connection, $query);
if ($stmtSelectSchool) {
  mysqli_stmt_bind_param($stmtSelectSchool, "i", $id_school);
  if (!mysqli_stmt_execute($stmtSelectSchool)) {
    // Log the error and redirect to the error page
    error_log("Error executing select school statement: " . mysqli_stmt_error($stmtSelectSchool));
    header("Location: /error");
    exit();
  }
  $result = mysqli_stmt_get_result($stmtSelectSchool);
  if ($result) {
    $row = mysqli_fetch_assoc($result);
    if (isset($row['school_name'])) {
      $pageTitle = $row['school_name'];
      $metaD = isset($row['meta_d_school']) ? $row['meta_d_school'] : '';
      $metaK = isset($row['meta_k_school']) ? $row['meta_k_school'] : '';
    } else {
      // Redirect to 404 page
      header("Location: /404");
      exit();
    }
    mysqli_stmt_close($stmtSelectSchool);
  } else {
    // Log the error and redirect to the error page
    error_log("Error getting result from select school statement: " . mysqli_stmt_error($stmtSelectSchool));
    header("Location: /error");
    exit();
  }
} else {
  // Log the error and redirect to the error page
  error_log("Error preparing select school statement: " . mysqli_error($connection));
  header("Location: /error");
  exit();
}
