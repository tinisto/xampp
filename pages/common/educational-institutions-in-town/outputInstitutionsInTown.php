<?php
function outputInstitutionsInTown($town_id, $type)

{
  global $connection; // Use the global connection variable

  // Fetch all institutions in the town
  $query_institutions = "SELECT * FROM $type WHERE id_town = ?";
  $stmt_institutions = mysqli_prepare($connection, $query_institutions);
  mysqli_stmt_bind_param($stmt_institutions, "i", $town_id);
  mysqli_stmt_execute($stmt_institutions);
  $result_institutions = mysqli_stmt_get_result($stmt_institutions);

  // Add Bootstrap's list-unstyled class to remove bullet points
  echo '<ul class="list-unstyled">';

  while ($institution_row = $result_institutions->fetch_assoc()) {
    echo '<li>';

    // Determine the column names based on the type of institution
    $nameColumn = '';
    $urlColumn = '';
    switch ($type) {
      case 'schools':
        $link = 'school';
        $nameColumn = 'school_name';
        $urlColumn = 'id_school';
        break;
      case 'spo':
        $link = 'spo';
        $nameColumn = 'spo_name';
        $urlColumn = 'spo_url';
        break;
      case 'vpo':
        $link = 'vpo';
        $nameColumn = 'vpo_name';
        $urlColumn = 'vpo_url';
        break;
    }

    // Display the institution name with the link
    echo '<a href="/' . $link . '/' . $institution_row[$urlColumn] . '" class="text-dark link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover">' . $institution_row[$nameColumn] . '</a>';

    // Check if user has 'admin' role in session and display the institution URL in bold
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
      echo ' <strong class="font-weight-bold text-dark">' . $institution_row[$urlColumn] . '</strong>';
    }

    echo '</li>';
  }

  echo '</ul>';

  // Close the statement
  mysqli_stmt_close($stmt_institutions);
}
