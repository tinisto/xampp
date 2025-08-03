<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Determine the type (vpo or spo) based on the URL
$requestUri = $_SERVER['REQUEST_URI'];
$type = strpos($requestUri, '/vpo/') !== false ? 'vpo' : 'spo';
$commonText = $type === 'vpo' ? "Университеты / Институты / Академии" : "Колледжи / Техникумы";
$allRegionsLink = '<a href="/' . $type . '-all-regions" class="link-custom"> в регионах России</a>';
$regionLink = "";
$townLink = "";

// Validate and fetch region information
$region_id = isset($row["region_id"]) ? mysqli_real_escape_string($connection, $row["region_id"]) : null;
$regionSlug = ""; // Variable to hold the region slug
if ($region_id) {
    $query_regions = "SELECT * FROM regions WHERE id='$region_id'";
    $result_regions = mysqli_query($connection, $query_regions);
    if ($result_regions && $myrow_region = mysqli_fetch_assoc($result_regions)) {
        if (!empty($myrow_region["region_name_en"])) {
            $regionSlug = htmlspecialchars($myrow_region["region_name_en"]);
            $regionLink = '<a href="/' . $type . '-in-region/' . $regionSlug . '" class="link-custom"> в ' . htmlspecialchars($myrow_region["region_where"]) . '</a>';
        }
    }
}

// Validate and fetch town information
$town_id = isset($row["town_id"]) ? mysqli_real_escape_string($connection, $row["town_id"]) : null;
if ($town_id) {
    $query_town = "SELECT * FROM towns WHERE id='$town_id'";
    $result_town = mysqli_query($connection, $query_town);
    if ($result_town && $myrow_town = mysqli_fetch_assoc($result_town)) {
        if (!empty($myrow_town["url_slug_town"]) && $regionSlug) {
            $townLink = '<a href="/' . $type . '/' . $regionSlug . '/' . htmlspecialchars($myrow_town["url_slug_town"]) . '" class="link-custom">' . htmlspecialchars($myrow_town["name"]) . '</a>';
        }
    }
}

?>

<style>
  .link-custom {
    color: var(--text-primary, #333);
    text-decoration: none;
    transition: color 0.2s ease;
  }
  
  .link-custom:hover {
    color: var(--primary-color, #28a745);
    text-decoration: underline;
  }
  
  .breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 1rem;
  }
  
  .breadcrumb-item {
    color: var(--text-secondary, #6c757d);
  }
  
  .breadcrumb-item.active {
    color: var(--text-primary, #333);
  }
  
  /* Dark mode support */
  [data-theme="dark"] .link-custom {
    color: var(--text-primary, #f9fafb);
  }
  
  [data-theme="dark"] .breadcrumb-item {
    color: var(--text-secondary, #9ca3af);
  }
  
  [data-theme="dark"] .breadcrumb-item.active {
    color: var(--text-primary, #f9fafb);
  }
</style>

<div class="d-flex justify-content-between">
  <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><small>
          <?= htmlspecialchars($commonText); ?>
        </small>
      </li>
      <li class="breadcrumb-item"><small>
          <?= $allRegionsLink; ?>
        </small>
      </li>
      <li class="breadcrumb-item"><small>
          <?= $regionLink ?: "Регион не найден"; ?>
        </small>
      </li>
      <li class="breadcrumb-item active" aria-current="page"><small>
          <?= $townLink ?: "Город не найден"; ?>
        </small></li>
    </ol>
  </nav>
</div>
