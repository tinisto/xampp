<?php
// Get the requested URL path
$requestPath = $_SERVER['REQUEST_URI'];

// Remove query parameters
$requestPath = strtok($requestPath, '?');

// Split the path into segments
$pathSegments = explode('/', trim($requestPath, '/'));

// Check if the URL matches the expected structure
if (count($pathSegments) >= 2) {
  // The region name is the second segment
  $region_name_en = isset($pathSegments[1]) ? mysqli_real_escape_string($connection, $pathSegments[1]) : null;
  $type = $pathSegments[0];

  // Fetch data from the 'regions' table using region_name_en
  $query_regions = "SELECT * FROM regions WHERE region_name_en=?";
  $stmt = mysqli_prepare($connection, $query_regions);

  if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $region_name_en);
    mysqli_stmt_execute($stmt);
    $result_regions = mysqli_stmt_get_result($stmt);
    $myrow_region = mysqli_fetch_array($result_regions);

    // Check if the region was found
    if ($myrow_region) {
      $region_id = $myrow_region['id_region'];
    } else {
      // Handle the case where the region is not found
      // Redirect to 404 page
      header("Location: /404");
      exit();
    }

    // Close the statement
    mysqli_stmt_close($stmt);
  } else {
    // Handle the case where the prepared statement could not be created
    // For example, display an error message
    header("Location: /error");
  }
} else {
  // Handle the case where the URL structure doesn't match
  // Handle the case where the region is not found
  // Redirect to 404 page
  header("Location: /404");
  exit();
}
?>
