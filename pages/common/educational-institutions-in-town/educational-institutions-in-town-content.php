<?php
// Include necessary files
include_once __DIR__ . '/outputInstitutionsInTown.php';

// Get the requested URL path
$requestPath = $_SERVER['REQUEST_URI'];

// Remove query parameters
$requestPath = strtok($requestPath, '?');

// Split the path into segments
$pathSegments = explode('/', trim($requestPath, '/'));

// Check if the URL matches the expected structure
if (count($pathSegments) >= 3) {
  // The type of institution is the first segment
  $type = $pathSegments[0];

  // The region name is the second segment
  $region_name_en = isset($pathSegments[1]) ? mysqli_real_escape_string($connection, $pathSegments[1]) : null;

  // The town url_slug_town is the third segment
  $town_url_slug = isset($pathSegments[2]) ? mysqli_real_escape_string($connection, $pathSegments[2]) : null;

  // Fetch data from the 'towns' table using town_url_slug and region_name_en
  $query_towns = "SELECT t.* FROM towns t JOIN regions r ON t.id_region = r.id_region WHERE t.url_slug_town = ? AND r.region_name_en = ?";
  $stmt = mysqli_prepare($connection, $query_towns);

  if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ss", $town_url_slug, $region_name_en);
    mysqli_stmt_execute($stmt);
    $result_towns = mysqli_stmt_get_result($stmt);
    $myrow_town = mysqli_fetch_array($result_towns);

    // Check if the town was found
    if ($myrow_town) {
      $town_name = $myrow_town['name'];
      $town_id = $myrow_town['id_town']; // Initialize $town_id

      // Set the pageTitle to include the name of the town
      switch ($type) {
        case 'schools':
          $pageTitle = 'Школы — ' . $town_name;
          $metaD = "Школы, гимназии и лицеи в " . $town_name;
          $metaK = "школы, гимназии, лицеи " . $town_name;
          break;
        case 'spo':
          $pageTitle = 'Колледжи / Техникумы — ' . $town_name;
          $metaD = "Колледжи, техникумы и училища в " . $town_name;
          $metaK = "колледжи, техникумы, училища " . $town_name;
          break;
        case 'vpo':
          $pageTitle = 'Университеты / Институты / Академии — ' . $town_name;
          $metaD = "Университеты, институты и академии в " . $town_name;
          $metaK = "университеты, институты, академии " . $town_name;
          break;
        default:
          header("Location: /error");
          exit();
      }

      // Fetch all institutions in the town
      $query_institutions = "SELECT * FROM $type WHERE id_town = ?";
      $stmt_institutions = mysqli_prepare($connection, $query_institutions);
      mysqli_stmt_bind_param($stmt_institutions, "i", $town_id);
      mysqli_stmt_execute($stmt_institutions);
      $result_institutions = mysqli_stmt_get_result($stmt_institutions);

      // Close the statement
      mysqli_stmt_close($stmt);
      mysqli_stmt_close($stmt_institutions);
    } else {
      // Handle the case where the town is not found
      // Redirect to 404 page
      header("Location: /404");
      exit();
    }
  } else {
    // Handle the case where the prepared statement could not be created
    // For example, display an error message
    header("Location: /error");
  }
} else {
  // Handle the case where the URL structure doesn't match
  // Handle the case where the town is not found
  // Redirect to 404 page
  header("Location: /404");
  exit();
}

$additionalData = [
  'type' => $type,
];

// Output the content
ob_start();
include_once __DIR__ . '/institutions-in-town-links.php';
echo '<h1>' . $pageTitle . '</h1>';
outputInstitutionsInTown($town_id, $type);
$content = ob_get_clean();

// Set the additional data
$additionalData = [
  'content' => $content
];
