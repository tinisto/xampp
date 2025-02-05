<?php
function displayPagination(
    $connection,
    $searchEntityType,
    $searchEntityID,
    $currentPage,
    $commentsPerPage
) {
    // Calculate total pages
    $totalComments = getTotalCommentsCount(
        $connection,
        $searchEntityType,
        $searchEntityID
    );
    $totalPages = max(1, ceil($totalComments / $commentsPerPage));

    // Adjust the current page if it's beyond the total pages
    $currentPage = min($currentPage, $totalPages);

    // Do not render pagination if there's only one page
    if ($totalPages <= 1) {
        return;
    }

    echo '<nav aria-label="Pagination">';
    echo '<ul class="pagination justify-content-center p-2 rounded">';

    $visiblePages = 10;
    $startPage = max(1, $currentPage - floor($visiblePages / 2));
    $endPage = min($totalPages, $startPage + $visiblePages - 1);

    // Build the base URL with search parameters excluding the page parameter
    $baseUrl =
        "?entity_type=" .
        urlencode($searchEntityType) .
        "&entity_id=" .
        urlencode($searchEntityID);

    if ($currentPage > 1) {
        echo '<li class="page-item">';
        echo '<a class="page-link bg-secondary text-white border-light" href="' .
            $baseUrl .
            "&page=1" .
            (isset($_GET["search"]) ? "&search=true" : "") .
            '" aria-label="First">&laquo;&laquo;</a>';
        echo '</li>';
    }

    for ($i = $startPage; $i <= $endPage; $i++) {
        echo '<li class="page-item ' . ($i == $currentPage ? 'active' : '') . '">';
        echo '<a class="page-link ' . ($i == $currentPage ? 'bg-white text-dark' : 'bg-secondary text-white border-light') . '" href="' .
            $baseUrl .
            "&page=" .
            $i .
            (isset($_GET["search"]) ? "&search=true" : "") .
            '">' .
            $i .
            '</a>';
        echo '</li>';
    }

    if ($currentPage < $totalPages) {
        echo '<li class="page-item">';
        echo '<a class="page-link bg-secondary text-white border-light" href="' .
            $baseUrl .
            "&page=" .
            $totalPages .
            (isset($_GET["search"]) ? "&search=true" : "") .
            '" aria-label="Last">&raquo;&raquo;</a>';
        echo '</li>';
    }

    echo '</ul>';
    echo '</nav>';
}

function getTotalCommentsCount(
    $connection,
    $entityType = null,
    $entityID = null
) {
    $sql = "SELECT COUNT(*) AS total_comments FROM comments WHERE 1";

    if (!empty($entityType)) {
        $sql .= " AND entity_type = ?";
    }

    if (!empty($entityID)) {
        $sql .= " AND id_entity = ?";
    }

    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    // Bind parameters
    if (!empty($entityType) && !empty($entityID)) {
        mysqli_stmt_bind_param($stmt, "ss", $entityType, $entityID);
    } elseif (!empty($entityType)) {
        mysqli_stmt_bind_param($stmt, "s", $entityType);
    } elseif (!empty($entityID)) {
        mysqli_stmt_bind_param($stmt, "s", $entityID);
    }

    mysqli_stmt_execute($stmt);

    // Check for errors
    if (mysqli_stmt_error($stmt)) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    $result = $stmt->get_result();

    if ($result && ($row = $result->fetch_assoc())) {
        return $row["total_comments"];
    }

    return 0;
}
