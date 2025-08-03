<?php
// Include reusable components for consistent styling
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/content-wrapper.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/typography.php';

// Initialize region_id and type from additionalData or set to null
$region_id = isset($additionalData['region_id']) ? (int) $additionalData['region_id'] : null;
$type = isset($additionalData['type']) ? $additionalData['type'] : 'spo';

// Include necessary files
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/pagination.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/redirectToErrorPage.php";
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

  // Map type to actual table names
  switch ($type) {
      case 'vpo':
          $tableName = 'vpo';
          $regionColumn = 'id_region';
          break;
      case 'spo':
          $tableName = 'spo';
          $regionColumn = 'id_region';
          break;
      default:
          $tableName = 'schools';
          $regionColumn = 'id_region';
          break;
  }

  // Fetch total institutions count for pagination
  $totalInstitutions_sql = "SELECT COUNT(*) AS total FROM $tableName WHERE $regionColumn = ?";
  $stmt_total = $connection->prepare($totalInstitutions_sql);
  $stmt_total->bind_param("i", $region_id);
  $stmt_total->execute();
  $totalInstitutions_result = $stmt_total->get_result();
  $totalInstitutions = $totalInstitutions_result->fetch_assoc()['total'];
  $stmt_total->close();

  // Include header links
  $id_region = $region_id;
  
  // Start content wrapper
  renderContentWrapper('start');
  
  include 'header-links.php';
}
?>

<style>
  .content-layout {
    display: flex;
    gap: 24px;
    margin-top: 24px;
  }
  
  .main-content {
    flex: 1;
    background: var(--surface, #f8f9fa);
    border: 1px solid var(--border-color, #e9ecef);
    border-radius: 8px;
    padding: 20px;
    word-wrap: break-word;
    overflow-wrap: break-word;
  }
  
  .sidebar {
    width: 300px;
    background: var(--surface, #f8f9fa);
    border: 1px solid var(--border-color, #e9ecef);
    border-radius: 8px;
    padding: 20px;
  }
  
  /* Dark mode support */
  [data-theme="dark"] .main-content,
  [data-theme="dark"] .sidebar {
    background: var(--surface, #2d3748);
    border-color: var(--border-color, #4a5568);
  }
  
  /* Institution list styling */
  .main-content ul.list-unstyled li {
    margin-bottom: 12px;
    line-height: 1.5;
    word-wrap: break-word;
    overflow-wrap: break-word;
    hyphens: auto;
  }
  
  .main-content ul.list-unstyled li a {
    display: inline;
    word-wrap: break-word;
    overflow-wrap: break-word;
  }
  
  /* Mobile responsive */
  @media (max-width: 768px) {
    .content-layout {
      flex-direction: column;
    }
    
    .sidebar {
      width: 100%;
    }
    
    .main-content ul.list-unstyled li {
      margin-bottom: 16px;
      font-size: 15px;
    }
  }
</style>

<!-- Two-Column Layout -->
<div class="content-layout">
  <!-- First Column: Institutions List -->
  <div class="main-content">
    <?php if ($institutions_result): ?>
      <?php outputInstitutions($institutions_result, $type); ?>
    <?php else: ?>
      <p>Данные не найдены.</p>
    <?php endif; ?>
  </div>

  <!-- Second Column: Towns List -->
  <div class="sidebar">
      <?php
      // Fetch towns with institutions in the region
      $query_towns = "
      SELECT DISTINCT t.*
      FROM towns t
      JOIN $tableName s ON t.id_town = s.id_town
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
  echo '<div style="margin-top: 40px;">';
  generatePagination($currentPage, $totalPages, $region_id);
  echo '</div>';
}

// End content wrapper
renderContentWrapper('end');
?>
