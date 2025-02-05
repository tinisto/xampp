<?php
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Get the current URL and extract the last part as url_news
    $currentUrl = $_SERVER['REQUEST_URI'];
    $urlNews = basename($currentUrl);

    // Prepare the query to get the news ID by url_news
    $stmt = $connection->prepare("SELECT id_news, user_id FROM news WHERE url_news = ?");
    $stmt->bind_param("s", $urlNews);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the news record exists
    if ($result->num_rows > 0) {
        $newsRecord = $result->fetch_assoc();
        $newsId = $newsRecord['id_news'];

        // If the logged-in user is the author of the news, show edit and delete options
        if ($userId === $newsRecord['user_id']) : ?>
            <a href="/pages/common/news/news-form.php?id_news=<?= htmlspecialchars($newsId, ENT_QUOTES, 'UTF-8') ?>" class="text-secondary me-2" title="Редактировать"><i class="fas fa-edit"></i></a>
            <a href="/pages/common/news/news-user-delete-news.php?id_news=<?= htmlspecialchars($newsId, ENT_QUOTES, 'UTF-8') ?>" class="text-danger" title="Удалить" onclick="return confirm('Вы уверены, что хотите удалить эту новость?');"><i class="fas fa-trash-alt"></i></a>
<?php endif;
    }
}
?>
