<h3 class="text-center text-white mb-3"><?php echo $pageTitle; ?></h3>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

// Select news data using prepared statements
$query = "SELECT * FROM news WHERE approved IS NULL OR approved != 1";
$stmtSelectNews = mysqli_prepare($connection, $query);

// Check if the preparation was successful
if ($stmtSelectNews === false) {
    header("Location: /error");
    exit();
}

// Execute the prepared statement
if (!mysqli_stmt_execute($stmtSelectNews)) {
    header("Location: /error");
    exit();
}

// Get the result set
$result = mysqli_stmt_get_result($stmtSelectNews);

if ($result === false) {
    header("Location: /error");
    exit();
}

if ($result->num_rows > 0) {
    echo '<div class="table-responsive">';
    echo '<table class="table table-hover table-sm table-bordered align-middle text-center" style="font-size: 13px;">';
    echo '<thead class="table-dark">';
    echo "<tr>";
    echo '<th class="text-center">id</th>';
    echo '<th class="text-center">category_news</th>';
    echo '<th class="text-center">title_news</th>';
    echo '<th class="text-center">description_news</th>';
    echo '<th class="text-center">text_news</th>';
    echo '<th class="text-center">user_id</th>';
    echo '<th class="text-center">review</th>';
    echo '<th class="text-center">delete</th>';
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo '<td class="text-center">' . $row["id_news"] . "</td>";
        echo '<td class="text-center">' . $row["category_news"] . "</td>";
        echo '<td class="text-center">' . $row["title_news"] . "</td>";
        echo '<td class="text-center">' . $row["description_news"] . "</td>";
        echo '<td class="text-center">' . $row["text_news"] . "</td>";
        echo '<td class="text-center"><a href="/pages/dashboard/users-dashboard/user.php?id=' . $row["user_id"] . '">' . $row["user_id"] . '</a></td>';
        echo '<td>
        <a href="/pages/dashboard/news-dashboard/news-approve/news-approve-edit-form.php?id_news=' . $row['id_news'] . '" class="btn btn-primary btn-sm">Review</a>
        </td>';
        echo '<td>
        <button type="button" class="btn btn-danger btn-sm" onclick="deleteNews(' . $row['id_news'] . ', \'' . addslashes($row['title_news']) . '\')">
            <i class="fas fa-trash-alt"></i> Удалить
        </button>
        </td>';
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
} else {
    echo '<h4 class="text-center text-white">Nothing to approve for the NEWS.</h4>';
}
?>

<script>
    // Delete News function with ID and Title
    function deleteNews(messageId, messageTitle) {
        if (confirm(`Are you sure you want to delete this post?\nID: ${messageId}\nTitle: "${messageTitle}"`)) {
            window.location.href = `/pages/dashboard/news-dashboard/news-approve/news-approve-delete.php?id_news=${messageId}`;
        }
    }
</script>
