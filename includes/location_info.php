<?php

// Assuming you have FontAwesome included, you can replace these icons with the appropriate class names.
$phoneIcon = 'fa fa-phone';
$faxIcon = 'fa fa-fax';
$emailIcon = 'fa fa-envelope';
$additionalEmailIcon = 'fa fa-envelope-o';
$websiteIcon = 'fa fa-globe';

// Fetching IDs from the fetched data
$zip_code = isset($row['zip_code']) ? mysqli_real_escape_string($connection, $row['zip_code']) : '';
$id_town = isset($row['id_town']) ? mysqli_real_escape_string($connection, $row['id_town']) : '';
$id_area = isset($row['id_area']) ? mysqli_real_escape_string($connection, $row['id_area']) : '';
$id_region = isset($row['id_region']) ? mysqli_real_escape_string($connection, $row['id_region']) : '';
$id_country = isset($row['id_country']) ? mysqli_real_escape_string($connection, $row['id_country']) : '';

// Define an associative array to map table names to IDs
$tables = [
  'towns' => $id_town,
  'areas' => $id_area,
  'regions' => $id_region,
  // Add more tables as needed
];

// Fetch data from each table
foreach ($tables as $table => $id) {
  if ($id !== '') { // Check if the ID is not empty
    $idColumn = 'id_' . rtrim($table, 's'); // Remove 's' from the end if present
    $query = "SELECT * FROM $table WHERE $idColumn='$id'";
    $result = mysqli_query($connection, $query);

    if (!$result) {
      printf("Error: %s\n", mysqli_error($connection));
    } else {
      ${"myrow_$table"} = mysqli_fetch_array($result);
    }
  }
}

// Fetch data from the 'regions' table
if ($id_region !== '') {
  $query_regions = "SELECT * FROM regions WHERE id_region='$id_region'";
  $result_regions = mysqli_query($connection, $query_regions);
  $myrow_region = mysqli_fetch_array($result_regions);
}

// Fetch data from the 'areas' table
if ($id_area !== '') {
  $query_areas = "SELECT * FROM areas WHERE id_area='$id_area'";
  $result_areas = mysqli_query($connection, $query_areas);
  $myrow_area = mysqli_fetch_array($result_areas);
}

// Fetch data from the 'towns' table
if ($id_town !== '') {
  $query_towns = "SELECT * FROM towns WHERE id_town='$id_town'";
  $result_towns = mysqli_query($connection, $query_towns);
  $myrow_town = mysqli_fetch_array($result_towns);
}
