<?php
function fetchNewsContent($connection, $entityType, $entityId) {
    // Determine the column name based on the entity type
    $columnName = $entityType === 'vpo' ? 'university_id' : 'college_id';

    // Prepare the query
    $queryNews = "SELECT * FROM news WHERE $columnName = ?";
    $stmtSelectNews = mysqli_prepare($connection, $queryNews);

    // Assuming $entityId is an integer, use "i" as the type
    mysqli_stmt_bind_param($stmtSelectNews, "i", $entityId);
    mysqli_stmt_execute($stmtSelectNews);
    $resultNews = mysqli_stmt_get_result($stmtSelectNews);

    // Display news content with a hyperlink
    while ($rowNews = mysqli_fetch_assoc($resultNews)) {
        echo '<p><a href="/news/' . htmlspecialchars($rowNews['url_slug']) . '" target="_blank" class="link-custom">' . htmlspecialchars($rowNews['title']) . '</a></p>';
    }

    // Close the statement
    mysqli_stmt_close($stmtSelectNews);
}
?>
