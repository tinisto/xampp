<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Retrieve data from the form
  $newsId = $_POST['id_news'];
  $categoryNews = $_POST['category_news'];
  $titleNews = $_POST['title_news'];
  $metaDNews = $_POST['meta_d_news'];
  $metaKNews = $_POST['meta_k_news'];
  $descriptionNews = $_POST['description_news'];
  $textNews = $_POST['text_news'];
  $urlNews = $_POST['url_news'];
  $viewNews = $_POST['view_news'];

  // Validate and sanitize your data as needed

  // If view_news is empty, set it to NULL or 0 (based on your DB schema)
  $viewNews = ($viewNews === '') ? 0 : (int)$viewNews;

  // Prepare the update statement
  $query = $connection->prepare("UPDATE news SET 
                                      category_news = ?,
                                      title_news = ?,
                                      meta_d_news = ?,
                                      meta_k_news = ?,
                                      description_news = ?,
                                      text_news = ?,
                                      url_slug = ?,
                                      view_news = ? 
                                    WHERE id_news = ?");

  // Bind parameters
  $query->bind_param("ssssssssi", $categoryNews, $titleNews, $metaDNews, $metaKNews, $descriptionNews, $textNews, $urlNews, $viewNews, $newsId);

  // Construct the SQL query for debugging
  $sqlQuery = "UPDATE news SET 
                  category_news = '$categoryNews',
                  title_news = '$titleNews',
                  meta_d_news = '$metaDNews',
                  meta_k_news = '$metaKNews',
                  description_news = '$descriptionNews',
                  text_news = '$textNews',
                  url_slug = '$urlNews',
                  view_news = '$viewNews'
                WHERE id_news = $newsId";

  // Output the generated SQL query for debugging (you may also want to log this for debugging purposes)
  // echo "SQL Query: $sqlQuery<br>"; // Commented out for production

  // Execute the update
  $result = $query->execute();

  // Check if the update was successful
  if ($result) {
    $_SESSION["success-message"] = "Новость успешно обновлена!";
    header("Location: /news/$urlNews");
    exit();
  } else {
    $_SESSION["error-message"] = "Ошибка при обновлении новости: " . $query->error;
  }

  // Close the statement
  $query->close();
} else {
  // If the form is not submitted, handle accordingly (you can add a message or log)
  $_SESSION["error-message"] = "Форма не отправлена.";
}
