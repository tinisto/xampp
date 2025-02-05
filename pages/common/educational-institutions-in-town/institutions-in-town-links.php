<?php
$commonText = '';
$allRegionsLink = '';
$regionLink = '';

// Ensure $table is defined
if (!isset($table)) {
    $table = isset($additionalData['type']) ? $additionalData['type'] : 'spo';
}

switch ($table) {
    case 'schools':
        $commonText = "Школы / Гимназии / Лицеи";
        $allRegionsLink = '<a href="/schools-all-regions" class="link-custom text-dark"> в регионах России</a>';
        break;
    case 'spo':
        $commonText = "Колледжи / Техникумы";
        $allRegionsLink = '<a href="/spo-all-regions" class="link-custom text-dark"> в регионах России</a>';
        break;
    case 'vpo':
        $commonText = "Университеты / Институты / Академии";
        $allRegionsLink = '<a href="/vpo-all-regions" class="link-custom text-dark"> в регионах России</a>';
        break;
}

// Fetch the region information based on the town's region ID
$id_region = mysqli_real_escape_string($connection, $myrow_town['id_region']);
$query_regions = "SELECT * FROM regions WHERE id_region='$id_region'";
$result_regions = mysqli_query($connection, $query_regions);
$myrow_region = mysqli_fetch_array($result_regions);

// Check if the key 'region_name_en' exists in $myrow_region and is not null
if (isset($myrow_region["region_name_en"])) {
  $regionLink =
    '<a href="/' . $table . '-in-region/' .
    $myrow_region["region_name_en"] .
    '" class="link-custom text-dark"> в ' .
    $myrow_region["region_name_rod"] .
    "</a>";
}
?>

<div class="d-flex justify-content-between">
  <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><small>
          <?php echo $commonText; ?>
        </small>
      </li>
      <li class="breadcrumb-item"><small>
          <?php echo $allRegionsLink; ?>
        </small>
      </li>
      <li class="breadcrumb-item"><small>
          <?php echo $regionLink; ?>
        </small>
      </li>
      <li class="breadcrumb-item active" aria-current="page"><small>
          <?php echo $town_name; ?>
        </small>
      </li>
    </ol>
  </nav>
</div>
