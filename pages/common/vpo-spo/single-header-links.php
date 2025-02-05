<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Determine the type (vpo or spo) based on the URL
$requestUri = $_SERVER['REQUEST_URI'];
$type = strpos($requestUri, '/vpo/') !== false ? 'vpo' : 'spo';
$commonText = $type === 'vpo' ? "Университеты / Институты / Академии" : "Колледжи / Техникумы";
$allRegionsLink = '<a href="/' . $type . '-all-regions" class="link-custom text-dark"> в регионах России</a>';
$regionLink = "";
$townLink = "";

// Validate and fetch region information
$id_region = isset($row["id_region"]) ? mysqli_real_escape_string($connection, $row["id_region"]) : null;
$regionSlug = ""; // Variable to hold the region slug
if ($id_region) {
    $query_regions = "SELECT * FROM regions WHERE id_region='$id_region'";
    $result_regions = mysqli_query($connection, $query_regions);
    if ($result_regions && $myrow_region = mysqli_fetch_assoc($result_regions)) {
        if (!empty($myrow_region["region_name_en"])) {
            $regionSlug = htmlspecialchars($myrow_region["region_name_en"]);
            $regionLink = '<a href="/' . $type . '-in-region/' . $regionSlug . '" class="link-custom text-dark"> в ' . htmlspecialchars($myrow_region["region_where"]) . '</a>';
        }
    }
}

// Validate and fetch town information
$id_town = isset($row["id_town"]) ? mysqli_real_escape_string($connection, $row["id_town"]) : null;
if ($id_town) {
    $query_town = "SELECT * FROM towns WHERE id_town='$id_town'";
    $result_town = mysqli_query($connection, $query_town);
    if ($result_town && $myrow_town = mysqli_fetch_assoc($result_town)) {
        if (!empty($myrow_town["url_slug_town"]) && $regionSlug) {
            $townLink = '<a href="/' . $type . '/' . $regionSlug . '/' . htmlspecialchars($myrow_town["url_slug_town"]) . '" class="link-custom text-dark">' . htmlspecialchars($myrow_town["name"]) . '</a>';
        }
    }
}

?>

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
