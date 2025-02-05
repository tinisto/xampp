<h3 class="text-center text-white mb-3"><?php echo htmlspecialchars($pageTitle); ?></h3>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/pagination.php";

// Pagination variables
$postsPerPage = 10; // Set the number of posts to display per page
$currentPage = isset($_GET["page"]) ? max(1, (int) $_GET["page"]) : 1;
$result = fetchPaginatedResults(
    $postsPerPage,
    $currentPage,
    $connection,
    "posts",
    "date_post"
);

if ($result->num_rows > 0) {
    echo '<div class="table-responsive">';
    echo '<table class="table table-hover table-sm table-bordered" style="font-size: 13px;">';
    echo '<thead class="table-dark">';
    echo "<tr>";
    // Table headers
    $headers = ['idPost', 'cat', 'title', 'metaD', 'metaK', 'description_post', 'bio_post', 'text_post', 'url', 'view', 'image1', 'image2', 'image3', 'Edit', 'Delete'];
    foreach ($headers as $header) {
        echo "<th class='text-center'>$header</th>";
    }
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    while ($row = $result->fetch_assoc()) {
        $postId = (int) $row["id_post"];
        $title = htmlspecialchars($row["title_post"] ?? "");
        $metaD = htmlspecialchars($row["meta_d_post"] ?? "");
        $metaK = htmlspecialchars($row["meta_k_post"] ?? "");
        $description = htmlspecialchars($row["description_post"] ?? "");
        $bio = htmlspecialchars($row["bio_post"] ?? "");
        $textPost = htmlspecialchars(substr($row["text_post"] ?? "", 0, 500)) . '...';
        $urlPost = htmlspecialchars($row["url_post"] ?? "");

        // Safe initialization of $viewPost
        $viewPost = isset($row["view_post"]) ? (int) $row["view_post"] : 0;

        $image1 = htmlspecialchars($row["image_file_1"] ?? "");
        $image2 = htmlspecialchars($row["image_file_2"] ?? "");
        $image3 = htmlspecialchars($row["image_file_3"] ?? "");

        echo "<tr>";
        echo "<td class='text-center'>$postId</td>";
        echo "<td class='text-center'>" . htmlspecialchars($row["category"] ?? "") . "</td>";
        echo "<td>$title</td>";
        echo "<td class='" . (empty($metaD) ? "table-danger" : "") . "'>$metaD</td>";
        echo "<td class='" . (empty($metaK) ? "table-danger" : "") . "'>$metaK</td>";
        echo "<td>$description</td>";
        echo "<td>$bio</td>";
        echo "<td>$textPost</td>";
        echo "<td class='text-center'><a href='/post/$urlPost' target='_blank'>$urlPost</a></td>";
        echo "<td class='text-center'>$viewPost</td>";
        echo "<td class='text-center'>$image1</td>";
        echo "<td class='text-center'>$image2</td>";
        echo "<td class='text-center'>$image3</td>";
        echo "<td class='text-center'><a href='/pages/dashboard/posts-dashboard/posts-view/posts-view-edit-form.php?id_post=$postId' class='edit-icon' target='_blank'><i class='fas fa-edit' style='color: green;'></i></a></td>";
        echo "<td class='text-center'><i class='fas fa-trash' onclick='deletePost($postId, \"$title\")' style='color: red; cursor: pointer;'></i></td>";
        echo "</tr>";
    }
    echo "</tbody>";

    // Table Footer
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
    $totalPages = calculateTotalPages($connection, "posts", $postsPerPage);
    if ($totalPages > 1) {
        generatePagination($currentPage, $totalPages);
    }
} else {
    echo "<p>No posts found.</p>";
}
?>

<script>
    function deletePost(postId, title) {
        if (confirm(`Are you sure you want to delete this post? ID: ${postId}\nTitle: ${title}`)) {
            window.location.href = `/pages/dashboard/posts-dashboard/posts-view/posts-view-delete-post.php?id=${postId}`;
        }
    }
</script>