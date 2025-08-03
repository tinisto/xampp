<?php
// Use the parameters set by the .htaccess rewrite rule instead of manual URL parsing
$region_name_en = isset($_GET['region_name_en']) ? mysqli_real_escape_string($connection, $_GET['region_name_en']) : null;
$type = isset($_GET['type']) ? $_GET['type'] : null;

// Check if both required parameters are present
if ($region_name_en && $type) {

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
      // Keep the region_name_en for later use (it's already set from GET parameter)
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
  // Handle the case where required parameters are missing
  // Redirect to 404 page
  header("Location: /404");
  exit();
}
?>
