<?php
$commonText = "Школы / Гимназии / Лицеи";

$allRegionsLink =
    '<a href="/schools-all-regions" class="link-custom text-dark"> в регионах России</a>';

// Validate and fetch region information
$id_region = isset($row["id_region"]) ? mysqli_real_escape_string($connection, $row["id_region"]) : null;
$query_regions = "SELECT * FROM regions WHERE id_region='$id_region'";
$result_regions = mysqli_query($connection, $query_regions);
$myrow_region = mysqli_fetch_array($result_regions);

$regionLink = "";
$regionSlug = "";

// Check if the key 'region_name_en' exists in $myrow_region and is not null
if (!empty($myrow_region["region_name_en"])) {
    $regionSlug = htmlspecialchars($myrow_region["region_name_en"]);
    $regionLink =
        '<a href="/schools-in-region/' .
        $regionSlug .
        '" class="link-custom text-dark"> в ' .
        htmlspecialchars($myrow_region["region_where"]) .
        "</a>";
}

// Validate and fetch town information
$id_town = isset($row["id_town"]) ? mysqli_real_escape_string($connection, $row["id_town"]) : null;
$townLink = "";
if ($id_town) {
    $query_town = "SELECT * FROM towns WHERE id_town='$id_town'";
    $result_town = mysqli_query($connection, $query_town);
    $myrow_town = mysqli_fetch_array($result_town);

    // Check if the key 'url_slug_town' exists in $myrow_town and is not null
    if (!empty($myrow_town["url_slug_town"])) {
        $townSlug = htmlspecialchars($myrow_town["url_slug_town"]);
        $townLink =
            '<a href="/schools/' .
            $regionSlug .
            '/' .
            $townSlug .
            '" class="link-custom text-dark">' .
            htmlspecialchars($myrow_town["name"]) .
            "</a>";
    }
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
          <?= $allRegionsLink ?>
        </small>
      </li>
      <li class="breadcrumb-item"><small>
          <?= $regionLink ?: "Регион не найден"; ?>
        </small>
      </li>
      <li class="breadcrumb-item active" aria-current="page"><small>
          <?= $townLink ?: "Город не найден"; ?>
        </small>
      </li>
    </ol>
  </nav>
</div>
