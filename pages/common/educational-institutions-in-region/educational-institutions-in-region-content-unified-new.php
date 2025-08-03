<?php
// Include reusable components
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

renderContentWrapper('start');

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

  // Prepare breadcrumb data
  $commonText = '';
  $Link = '';
  $linkUrl = '';

  switch ($type) {
      case 'schools':
          $commonText = 'Школы / Гимназии / Лицеи';
          $linkUrl = '/schools-all-regions';
          $linkText = 'Школы / Гимназии / Лицеи в регионах России';
          break;
      case 'spo':
          $commonText = 'Колледжи / Техникумы';
          $linkUrl = '/spo-all-regions';
          $linkText = 'Среднее профессиональное образование в регионах России';
          break;
      case 'vpo':
          $commonText = 'Университеты / Институты / Академии';
          $linkUrl = '/vpo-all-regions';
          $linkText = 'Высшее профессиональное образование в регионах России';
          break;
  }
?>

<style>
  .breadcrumb-nav {
    margin-bottom: 24px;
    padding: 12px 0;
    border-bottom: 1px solid var(--border-color, #e2e8f0);
  }
  
  .breadcrumb-list {
    display: flex;
    align-items: center;
    gap: 8px;
    list-style: none;
    margin: 0;
    padding: 0;
    font-size: 14px;
  }
  
  .breadcrumb-list li {
    display: flex;
    align-items: center;
    color: var(--text-secondary, #64748b);
  }
  
  .breadcrumb-list li:not(:last-child)::after {
    content: ">";
    margin-left: 8px;
    color: var(--text-muted, #cbd5e1);
  }
  
  .breadcrumb-list a {
    color: var(--primary-color, #28a745);
    text-decoration: none;
    transition: color 0.2s ease;
  }
  
  .breadcrumb-list a:hover {
    color: var(--primary-hover, #218838);
    text-decoration: underline;
  }
  
  .institution-count-badge {
    background: var(--surface-variant, #e2e8f0);
    color: var(--text-primary, #1a202c);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
    margin-left: 8px;
  }
  
  .admin-actions {
    margin-bottom: 16px;
  }
  
  .admin-button {
    display: inline-block;
    padding: 8px 16px;
    background: var(--surface-variant, #6c757d);
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-size: 13px;
    transition: background 0.2s ease;
  }
  
  .admin-button:hover {
    background: var(--surface-variant-hover, #5a6268);
    color: white;
    text-decoration: none;
  }
  
  .content-layout {
    display: flex;
    gap: 32px;
  }
  
  .main-content {
    flex: 1;
  }
  
  .sidebar {
    width: 300px;
    flex-shrink: 0;
  }
  
  /* Dark mode */
  [data-theme="dark"] .breadcrumb-nav {
    border-bottom-color: var(--border-color, #374151);
  }
  
  [data-theme="dark"] .institution-count-badge {
    background: var(--surface-variant, #374151);
    color: var(--text-primary, #f9fafb);
  }
  
  [data-theme="dark"] .admin-button {
    background: var(--surface-variant, #4b5563);
  }
  
  [data-theme="dark"] .admin-button:hover {
    background: var(--surface-variant-hover, #374151);
  }
  
  /* Mobile responsive */
  @media (max-width: 768px) {
    .content-layout {
      flex-direction: column;
    }
    
    .sidebar {
      width: 100%;
    }
    
    .breadcrumb-list {
      font-size: 13px;
      flex-wrap: wrap;
    }
  }
</style>

<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
  <div class="admin-actions">
    <a href="/pages/educational-institutions-in-region/send-emails-to-institutions-in-this-region.php?id_region=<?= urlencode($region_id) ?>&type=<?= urlencode($type) ?>" 
       class="admin-button" 
       target="_blank">
      Send Emails in This Region
    </a>
  </div>
<?php endif; ?>

<nav class="breadcrumb-nav">
  <ul class="breadcrumb-list">
    <li>
      <a href="<?= $linkUrl ?>"><?= $linkText ?></a>
    </li>
    <li>
      <?= "$commonText {$myrow_region['region_name_rod']}" ?>
      <span class="institution-count-badge"><?= $totalInstitutions ?></span>
    </li>
  </ul>
</nav>

<div class="content-layout">
  <div class="main-content">
    <?php
    // Output educational institutions
    outputEducationalInstitutions($institutions_result, $type, $connection);

    // Pagination
    if ($totalInstitutions > $institutionsPerPage) {
      $totalPages = ceil($totalInstitutions / $institutionsPerPage);
      $url = '/' . $type . '-in-region/' . $region_name_en;
      
      echo '<div style="display: flex; justify-content: center; margin-top: 40px;">';
      renderPagination($url, $currentPage, $totalPages);
      echo '</div>';
    }
    ?>
  </div>
  
  <aside class="sidebar">
    <?php
    // Output towns
    outputTowns($region_id, $type, $connection);
    ?>
  </aside>
</div>

<?php
} else {
  renderCallout('Регион не найден', 'error', 'Ошибка');
}

renderContentWrapper('end');
?>