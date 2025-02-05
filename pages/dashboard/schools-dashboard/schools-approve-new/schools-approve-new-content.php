<h3 class="text-center text-white mb-3"><?php echo htmlspecialchars($pageTitle); ?></h3>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

// Query to fetch school data
$query = "SELECT * FROM schools WHERE approved IS NULL OR approved != 1";
$stmtSelectCollege = mysqli_prepare($connection, $query);

if ($stmtSelectCollege === false) {
    header("Location: /error");
    exit();
}

// Execute the prepared statement
if (!mysqli_stmt_execute($stmtSelectCollege)) {
    header("Location: /error");
    exit();
}

$result = mysqli_stmt_get_result($stmtSelectCollege);

if ($result === false) {
    header("Location: /error");
    exit();
}

// Check if there are rows to display
if ($result->num_rows > 0) {
    echo '<div class="table-responsive">';
    echo '<table class="table table-hover table-sm table-bordered align-middle text-center" style="font-size: 13px;">';
    echo '<thead class="table-dark">';
    echo '<tr>';
    $headers = [
        'ID',
        'Approved',
        'Name',
        'Full Name',
        'Short Name',
        'Director Name',
        'Director Info',
        'Director Phone',
        'Director Email',
        'Site',
        'Email',
        'History',
        'Image-1',
        'Image-2',
        'Image-3',
        'Created By ID',
        'Edit',
        'Delete',
    ];
    foreach ($headers as $header) {
        echo '<th class="text-center">' . htmlspecialchars($header) . '</th>';
    }
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    // Fetch and display each row
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td class="text-center">' . htmlspecialchars($row["id_school"] ?? '') . '</td>';
        echo '<td class="text-center">' . htmlspecialchars($row["approved"] ?? '') . '</td>';
        echo '<td class="text-center">' . htmlspecialchars($row["school_name"] ?? '') . '</td>';
        echo '<td class="text-center">' . htmlspecialchars($row["full_name"] ?? '') . '</td>';
        echo '<td class="text-center">' . htmlspecialchars($row["short_name"] ?? '') . '</td>';
        echo '<td class="text-center">' . htmlspecialchars($row["director_name"] ?? '') . '</td>';
        echo '<td class="text-center">' . htmlspecialchars($row["director_info"] ?? '') . '</td>';
        echo '<td class="text-center">' . htmlspecialchars($row["director_phone"] ?? '') . '</td>';
        echo '<td class="text-center">' . htmlspecialchars($row["director_email"] ?? '') . '</td>';
        echo '<td class="text-center">' . htmlspecialchars($row["site"] ?? '') . '</td>';
        echo '<td class="text-center">' . htmlspecialchars($row["email"] ?? '') . '</td>';
        echo '<td class="text-center">' . htmlspecialchars($row["history"] ?? '') . '</td>';
        echo '<td class="text-center">' . htmlspecialchars($row["image_school_1"] ?? '') . '</td>';
        echo '<td class="text-center">' . htmlspecialchars($row["image_school_2"] ?? '') . '</td>';
        echo '<td class="text-center">' . htmlspecialchars($row["image_school_3"] ?? '') . '</td>';

        // Edit link
        echo '<td class="text-center">';
        echo '<a href="/dashboard/admin-approve-school-edit-form.php?id_school=' .
            urlencode($row["id_school"] ?? '') .
            '&user_id=' .
            urlencode($row["user_id"] ?? '') .
            '" class="edit-icon"><i class="fas fa-edit" style="color: green;"></i></a>';
        echo '</td>';

        // Delete icon
        echo '<td class="text-center">';
        echo '<i class="fas fa-trash" onclick="deleteSchool(' . htmlspecialchars($row["id_school"] ?? '') . ')" style="color: red; cursor: pointer;"></i>';
        echo '</td>';
        echo '<td class="text-center">' . htmlspecialchars($row["user_id"] ?? '') . '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
} else {
    echo '<h4 class="text-center text-white">Nothing to approve for the schools.</h4>';
}
?>

<script>
  /**
   * Deletes a school after user confirmation
   * @param {number} id_school - The ID of the school to delete
   */
  function deleteSchool(id_school) {
    if (confirm('Are you sure you want to delete this school?')) {
      // Redirect to delete page
      window.location.href = '/pages/dashboard/schools-dashboard/schools-delete/schools-delete.php?id_school=' + encodeURIComponent(id_school);
    }
  }
</script>
