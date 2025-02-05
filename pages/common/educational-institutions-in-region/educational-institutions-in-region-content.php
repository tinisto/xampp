<?php
// Initialize region_id and type from additionalData or set to null
$region_id = isset($additionalData['region_id']) ? (int) $additionalData['region_id'] : null;
$type = isset($additionalData['type']) ? $additionalData['type'] : 'spo';

// Include necessary files
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/pagination.php";
include 'outputEducationalInstitutions.php';
include 'function-query.php';
include 'outputTowns.php';

// Constants
$institutionsPerPage = 20; // Institutions per page
$currentPage = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1; // Current page
$pageOffset = max(0, ($currentPage - 1) * $institutionsPerPage); // Pagination offset

// Fetch institutions data
$institutions_result = getInstitutions($connection, $region_id, $type, $pageOffset, $institutionsPerPage);

// Fetch region data
$query_regions = "SELECT * FROM regions WHERE id_region = ?";
$stmt_regions = $connection->prepare($query_regions);
$stmt_regions->bind_param("i", $region_id);
$stmt_regions->execute();
$result_regions = $stmt_regions->get_result();
$myrow_region = $result_regions->fetch_assoc();
$stmt_regions->close();

if ($myrow_region) {
  // Set the region_name_en variable
  $region_name_en = $myrow_region['region_name_en'];

  // Fetch total institutions count for pagination
  $totalInstitutions_sql = "SELECT COUNT(*) AS total FROM $type WHERE id_region = ?";
  $stmt_total = $connection->prepare($totalInstitutions_sql);
  $stmt_total->bind_param("i", $region_id);
  $stmt_total->execute();
  $totalInstitutions_result = $stmt_total->get_result();
  $totalInstitutions = $totalInstitutions_result->fetch_assoc()['total'];
  $stmt_total->close();

  // Include header links
  $id_region = $region_id;
  include 'header-links.php';
}
?>

<!-- Two-Column Layout -->
<div class="row">
  <!-- First Column: Institutions List -->
  <div class="col-md-9">
    <div class="p-3 bg-light border">
      <?php if ($institutions_result): ?>
        <?php outputInstitutions($institutions_result, $type); ?>
      <?php else: ?>
        <p>Данные не найдены.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Second Column: Towns List -->
  <div class="col-md-3">
    <div class="p-3 bg-light border">
      <?php
      // Fetch towns with institutions in the region
      $query_towns = "
      SELECT DISTINCT t.*
      FROM towns t
      JOIN $type s ON t.id_town = s.id_town
      WHERE t.id_region = ?
      ";
      $stmt_towns = $connection->prepare($query_towns);
      $stmt_towns->bind_param("i", $region_id);
      $stmt_towns->execute();
      $result_towns = $stmt_towns->get_result();
      $stmt_towns->close();

      // Output towns list
      outputTowns($result_towns, $type, $region_name_en);
      ?>
    </div>
  </div>

  <?php
  // Add pagination if needed
  if ($totalInstitutions > $institutionsPerPage) {
    $totalPages = ceil($totalInstitutions / $institutionsPerPage);
    $visiblePages = 5; // Limit visible page numbers
    generatePagination($currentPage, $totalPages, $region_id);
  }
  ?>
