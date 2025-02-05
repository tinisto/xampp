<h3 class="text-center text-white mb-3"><?php echo htmlspecialchars($pageTitle); ?></h3>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/pagination.php";

// Pagination variables
$newsPerPage = 20; // Set the number of news to display per page
$currentPage = isset($_GET["page"]) ? max(1, (int) $_GET["page"]) : 1;
$result = fetchPaginatedResults(
    $newsPerPage,
    $currentPage,
    $connection,
    "news",
    "date_news"
);

if ($result->num_rows > 0) {
    echo '<div class="table-responsive">';
    echo '<table class="table table-hover table-sm table-bordered" style="font-size: 13px;">';
    echo '<thead class="table-dark">';
    echo "<tr>";
    // Table headers
    $headers = ['id', 'cat', 'metaD', 'metaK', 'description', 'title', 'text', 'url', 'view', 'image', 'approval', 'edit', 'delete', 'created by'];
    foreach ($headers as $header) {
        echo "<th class='text-center'>$header</th>";
    }
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    // Table body with the rows
    while ($row = $result->fetch_assoc()) {
        $isPending = $row["approved"] == 2;
        echo "<tr>";
        echo '<td class="text-center">' . $row["id_news"] . "</td>";
        echo '<td class="text-center">' . $row["category_news"] . "</td>";
        echo '<td class="' .
            (empty($row["meta_d_news"]) ? "table-danger" : "") .
            '">' . $row["meta_d_news"] . "</td>";
        echo '<td class="' .
            (empty($row["meta_k_news"]) ? "table-danger" : "") .
            '">' . $row["meta_k_news"] . "</td>";
        echo '<td class="' .
            (empty($row["description_news"]) ? "table-danger" : "") .
            '">' . $row["description_news"] . "</td>";
        echo '<td class="' .
            ($isPending ? "table-warning" : "") .
            '">' . $row["title_news"] . "</td>";
        echo "<td>" . substr(strip_tags($row["text_news"]), 0, 500) . "...</td>";
        echo '<td class="text-center"><a href="/news/' . $row["url_news"] . '" target="_blank">' . $row["url_news"] . "</a></td>";
        echo '<td class="text-center">' . $row["view_news"] . "</td>";
        echo '<td class="text-center">' . (isset($row["image_news"]) ? $row["image_news"] : "") . "</td>";
        echo '<td class="text-center ' .
            ($isPending ? "table-warning" : "") .
            '">' . ($row["approved"] == 1 ? 'Approved' : ($row["approved"] == 2 ? 'Pending' : 'Not Approved')) . "</td>";
        echo '<td class="text-center">';
        echo '<a href="/pages/common/news/news-form.php?id_news=' . $row["id_news"] . '" class="edit-icon" target="_blank"><i class="fas fa-edit" style="color: green;"></i></a>';
        echo '</td>';
        echo '<td class="text-center">';
        echo '<i class="fas fa-trash" onclick="deleteNews(' . $row["id_news"] . ', \'' . addslashes($row["title_news"]) . '\')" style="color: red; cursor: pointer;"></i>';
        echo '</td>';
        echo "<td class='text-center'><a href='/pages/dashboard/users-dashboard/user.php?id=" . $row["user_id"] . "'>" . $row["user_id"] . "</a></td>";
        echo "</tr>";
    }
    echo "</tbody>";

    // Table Footer (Repeat headers)
    echo "<tfoot class='table-dark'>";
    echo "<tr>";
    foreach ($headers as $header) {
        echo "<th class='text-center'>$header</th>";
    }
    echo "</tr>";
    echo "</tfoot>";

    echo "</table>";
    echo "</div>";

    // Pagination links
    $totalPages = calculateTotalPages($connection, "news", $newsPerPage);
    if ($totalPages > 1) {
        generatePagination($currentPage, $totalPages);
    }
}
?>

<script>
    function deleteNews(newsId, title) {
        if (confirm(`Are you sure you want to delete this news? ID: ${newsId}\nTitle: ${title}`)) {
            window.location.href = `/pages/common/news/news-user-delete-news.php?id_news=${newsId}`;
        }
    }
</script>