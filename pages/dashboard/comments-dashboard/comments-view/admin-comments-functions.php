<?php
require_once "admin-comments-search-form.php";

function displayComments($connection, $currentPage, $commentsPerPage = 10)
{
    require_once "admin-comments-pagination-functions.php";
    require_once "admin-comments-search-functions.php";
    require_once "admin-display-comments-table.php";

    $searchEntityType = isset($_GET["entity_type"]) ? $_GET["entity_type"] : "";
    $searchEntityID = isset($_GET["entity_id"]) ? $_GET["entity_id"] : "";

    $offset = calculateOffset($currentPage, $commentsPerPage);
    $comments = getComments(
        $connection,
        $offset,
        $commentsPerPage,
        $searchEntityType,
        $searchEntityID
    );

    displaySearchForm($searchEntityType, $searchEntityID);
    displayCommentsTable($connection, $comments);

    // Pagination links with search parameters
    displayPagination(
        $connection,
        $searchEntityType,
        $searchEntityID,
        $currentPage,
        $commentsPerPage
    );
}

function calculateOffset($currentPage, $commentsPerPage)
{
    return ($currentPage - 1) * $commentsPerPage;
}

function getComments(
    $connection,
    $offset,
    $commentsPerPage,
    $searchEntityType,
    $searchEntityID
) {
    if (isset($_GET["search"])) {
        $currentPage = ceil(($offset + 1) / $commentsPerPage);
        return searchComments(
            $connection,
            $searchEntityType,
            $searchEntityID,
            $currentPage,
            $commentsPerPage
        );
    } else {
        return getPaginatedComments($connection, $offset, $commentsPerPage);
    }
}

function getPaginatedComments($connection, $offset, $commentsPerPage)
{
    // Assuming you have a 'comments' table with columns: id, comment_text, date, user_id, entity_type, id_entity, parent_id
    $sql = "SELECT * FROM comments ORDER BY date DESC LIMIT ?, ?";
    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    // Bind parameters
    mysqli_stmt_bind_param($stmt, "ii", $offset, $commentsPerPage);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    $comments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $comments[] = $row;
    }

    mysqli_stmt_close($stmt);

    return $comments;
}
