<?php
// First check if id_school is passed as GET parameter (from .htaccess rewrite)
if (isset($_GET['id_school']) && is_numeric($_GET['id_school'])) {
  $id_school = $_GET['id_school'];
  $entityType = 'school';
} else {
  // Fallback to URL path parsing
  $requestPath = $_SERVER['REQUEST_URI'];
  
  // Split the path into segments
  $pathSegments = explode('/', trim($requestPath, '/'));
  
  // Check if the URL matches the expected structure
  if (count($pathSegments) >= 2 && $pathSegments[0] === 'school') {
    // The school ID is the second segment
    $id_school = $pathSegments[1];
    $entityType = 'school'; // Set the entity type
  } else {
    // Handle the case where the URL structure doesn't match
    $id_school = null;
    $entityType = null; // Set entity type to null
  }
}
?>