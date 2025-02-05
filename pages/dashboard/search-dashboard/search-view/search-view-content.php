<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";
?>

<h3 class="text-center text-white mb-3"><?php echo $pageTitle; ?></h3>

<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/pagination.php";
// Pagination variables
$postsPerPage = 10; // Set the number of posts to display per page
$currentPage = isset($_GET["page"]) ? max(1, (int) $_GET["page"]) : 1;
$resultWithSort = fetchPaginatedResults(
    $postsPerPage,
    $currentPage,
    $connection,
    "search_queries",
    true
);

if ($resultWithSort->num_rows > 0) {
    echo '<div class="table-responsive">';
    echo '<table class="table table-hover table-sm table-bordered align-middle" style="font-size: 13px;">';
    echo '<thead class="table-dark">';
    echo "<tr>";
    echo '<th class="text-center">ID</th>';
    echo '<th class="text-center">User Email</th>';
    echo "<th>Search</th>";
    echo '<th class="text-center">Timestamp</th>';
    echo '<th class="text-center">Delete</th>';
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    while ($row = $resultWithSort->fetch_assoc()) {
        echo "<tr>";
        echo '<td class="text-center">' . $row["id"] . "</td>";
        echo '<td class="text-center">' . $row["user_email"] . "</td>";
        echo "<td>" . $row["query"] . "</td>";
        echo '<td class="text-center">' . $row["created_at"] . "</td>";
        echo '<td class="text-center"><i class="fas fa-trash-alt" onclick="deleteSearch(' .
            $row["id"] .
            ')" style="color: red; cursor: pointer;"></i></td>';
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    echo "</div>";

    // Pagination links
    $totalPages = calculateTotalPages($connection, "messages", $postsPerPage);
    if ($totalPages > 1) {
        // Use the function from pagination_functions.php
        generatePagination($currentPage, $totalPages);
    }
}
?>
<script>
    function deleteSearch(searchId) {
        if (confirm('Are you sure you want to delete this message?')) {
            window.location.href = '/dashboard/admin-search-delete.php?id=' + searchId;
        }
    }
</script>