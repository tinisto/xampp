<h3 class="text-center text-white mb-3"><?php echo $pageTitle; ?></h3>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/pagination.php";

$tableName = 'schools'; // Or make it dynamic based on your use case
$columns = getTableColumns($connection, $tableName);

$postsPerPage = 10; // Set the number of posts to display per page
$currentPage = isset($_GET["page"]) ? max(1, (int) $_GET["page"]) : 1;
$result = fetchPaginatedResults(
    $postsPerPage,
    $currentPage,
    $connection,
    $tableName,
    "id_school"  // or your desired sorting column
);

// Check if results are available
if ($result->num_rows > 0) {
    echo '<div class="table-responsive">';
    echo '<table class="table table-hover table-sm table-bordered" style="font-size: 13px;">';
    echo '<thead class="table-dark">';
    echo "<tr>";

    // Loop through column names to generate table headers dynamically
    foreach ($columns as $column) {
        echo '<th class="text-center">' . ucfirst(str_replace('_', ' ', $column)) . '</th>';
    }
    echo '<th class="text-center">Edit</th>';
    echo '<th class="text-center">Delete</th>';
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    // Loop through rows and display data
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";

        // Loop through the columns and display data dynamically
        foreach ($columns as $column) {
            // You can apply custom formatting based on column type, e.g., substr for text
            $value = $row[$column];

            if ($column === "text_post") {
                // Truncate long text columns
                $value = substr($value, 0, 500) . '...';
            } elseif ($column === "url_post") {
                // Add link formatting for URLs
                $value = '<a href="/post/' . $value . '" target="_blank">' . $value . '</a>';
            } elseif (in_array($column, ['image_file_1', 'image_file_2', 'image_file_3'])) {
                // Display image columns
                $value = '<img src="/uploads/' . $value . '" alt="' . $value . '" style="width: 50px; height: auto;">';
            }

            // Output the value dynamically
            echo "<td class='text-center'>$value</td>";
        }

        echo '<td class="text-center">';
        echo '<i class="fas fa-edit" style="color: green;"></i></td>';

        echo '<td class="text-center">
            <a href="/pages/dashboard/schools-dashboard/schools-delete/schools-delete.php?action=delete&id_school=' . $row["id_school"] . '" onclick="return confirmDeleteSchool(' . $row["id_school"] . ');">
                <i class="fas fa-trash icon" data-action="delete" style="color: red;"></i>
            </a>
        </td>';

        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";

    // Pagination links
    $totalPages = calculateTotalPages($connection, $tableName, $postsPerPage);
    if ($totalPages > 1) {
        generatePagination($currentPage, $totalPages);
    }
} else {
    echo "No results found.";
}
?>

<script>
    function confirmDeleteSchool(id_school) {
        return confirm("Are you sure you want to delete this school? ID: " + id_school);
    }
</script>
