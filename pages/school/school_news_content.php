<?php
// Fetch and display news content here
$queryNews = "SELECT * FROM news WHERE id_school=?";
$stmtSelectNews = mysqli_prepare($connection, $queryNews);

// Assuming $row['id_school'] is an integer, use "i" as the type
mysqli_stmt_bind_param($stmtSelectNews, "i", $row['id_school']);
mysqli_stmt_execute($stmtSelectNews);
$resultNews = mysqli_stmt_get_result($stmtSelectNews);

while ($rowNews = mysqli_fetch_assoc($resultNews)) {
  // Display news content with a hyperlink
  echo '<p><a href="/news/' . $rowNews['url_news'] . '" target="_blank" class="link-custom text-dark">' . $rowNews['title_news'] . '</a></p>';
}

// Close the statement
mysqli_stmt_close($stmtSelectNews);
