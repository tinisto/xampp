<?php
// Assuming $connection is your MySQLi connection
$userId = $_SESSION['user_id'];  // Get user ID from session
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/getRussianDate.php';

// Fetch user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $connection->prepare($sql);
if (!$stmt) {
    header("Location: /error");
    exit();
}
$stmt->bind_param('i', $userId);
if (!$stmt->execute()) {
    header("Location: /error");
    exit();
}
$userData = $stmt->get_result()->fetch_assoc();

// Fetch user news
$sqlNews = "SELECT * FROM news WHERE user_id = ? ORDER BY date_news DESC";
$stmtNews = $connection->prepare($sqlNews);
if (!$stmtNews) {
    header("Location: /error");
    exit();
}
$stmtNews->bind_param('i', $userId);
if (!$stmtNews->execute()) {
    header("Location: /error");
    exit();
}
$news = $stmtNews->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<?php
if ($occupation === "Представитель ВУЗа") {
    echo '<a class="btn btn-secondary btn-sm w-auto" href="/pages/common/news/news-form.php">Создать новость вашего ВУЗа</a>';
} elseif ($occupation === "Представитель ССУЗа") {
    echo '<a class="btn btn-secondary btn-sm w-auto" href="/pages/common/news/news-form.php">Создать новость вашего ССУЗа</a>';
} elseif ($occupation === "Представитель школы") {
    echo '<a class="btn btn-secondary btn-sm w-auto" href="/pages/common/news/news-form.php">Создать новость вашей школы</a>';
}
?>


<?php if (!empty($news)): ?>
    <div class="container">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Дата</th>
                    <th scope="col">Название</th>
                    <th scope="col" class="text-end">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($news as $singleNews): ?>
                    <tr>
                        <td><?= getRussianDate($singleNews['date_news'] ?? '') ?></td>
                        <td>
                            <?php if ($singleNews['approved'] == 1): ?>
                                <a href="/news/<?= htmlspecialchars($singleNews['url_news'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($singleNews['title_news'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            <?php else: ?>
                                <?= htmlspecialchars($singleNews['title_news'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if ($singleNews['approved'] == 1): ?>
                                <span class="badge text-bg-success me-2">Одобрено</span>
                                <a href="/pages/common/news/news-form.php?id_news=<?= htmlspecialchars($singleNews['id_news'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="text-warning me-2" title="Edit"><i class="fas fa-edit"></i></a>

                                <a href="/pages/common/news/news-user-delete-news.php?id_news=<?= htmlspecialchars($singleNews['id_news'], ENT_QUOTES, 'UTF-8') ?>"
                                    class="text-danger"
                                    title="Удалить"
                                    onclick="return confirm('Вы уверены, что хотите удалить новость: \n<?= nl2br(htmlspecialchars($singleNews['title_news'], ENT_QUOTES, 'UTF-8')) ?>');">
                                    <i class="fas fa-trash-alt"></i>
                                </a>

                            <?php else: ?>
                                <?php
                                switch ($singleNews['approved']) {
                                    case 0:
                                        echo '<span class="badge text-bg-danger">Отклонено</span>';
                                        break;
                                    case 2:
                                        echo '<span class="badge text-bg-warning">На модерации</span>';
                                        break;
                                    default:
                                        echo '<span class="badge text-bg-secondary">Неизвестный статус</span>';
                                }
                                ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p>Новостей не найдено.</p>
<?php endif; ?>