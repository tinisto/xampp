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
    "messages",
    true
);

if ($resultWithSort->num_rows > 0) {
    echo '<div class="table-responsive">';
    echo '<table class="table table-hover table-sm table-bordered align-middle" style="font-size: 13px;">';
    echo '<thead class="table-dark">';
    echo "<tr>";
    echo '<th class="text-center">ID</th>';
    echo "<th>Email</th>";
    echo '<th class="text-center">User ID</th>';
    echo "<th>Message</th>";
    echo '<th class="text-center">Timestamp</th>';
    echo '<th class="text-center">Delete</th>';
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    while ($row = $resultWithSort->fetch_assoc()) {
        echo "<tr>";
        echo '<td class="text-center">' . $row["id"] . "</td>";
        echo "<td>" . $row["userEmail"] . "</td>";
        echo '<td class="text-center">' . $row["user_id"] . "</td>";
        echo "<td>" . $row["message"] . "</td>";
        echo '<td class="text-center">' . $row["timestamp"] . "</td>";
        echo '<td class="text-center"><i class="fas fa-trash-alt" onclick="deleteMessage(' .
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
    function deleteMessage(messageId) {
        if (confirm(`Are you sure you want to delete this post? ID: ${messageId}`)) {
            window.location.href = '/pages/dashboard/messages-dashboard/messages-view/messages-view-delete.php?id=' + messageId;
        }
    }
</script>