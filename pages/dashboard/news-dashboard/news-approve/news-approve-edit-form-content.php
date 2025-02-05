<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/buttons/form-buttons.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/requiredAsterisk.php";

// Check if 'id_news' parameter is present in the URL
if (isset($_GET["id_news"])) {
    $newsId = filter_var($_GET["id_news"], FILTER_SANITIZE_NUMBER_INT);

    // Use prepared statement to fetch the data securely
    $stmt = $connection->prepare("SELECT * FROM news WHERE id_news = ?");
    $stmt->bind_param("i", $newsId);
    $stmt->execute();
    $resultNews = $stmt->get_result();

    if ($resultNews->num_rows > 0) {
        $row = $resultNews->fetch_assoc();
    } else {
        echo "NEWS not found.";
        exit();
    }
} else {
    echo "No newsId specified in the URL.";
    exit();
}

// Get the page title from the URL parameter
$newsTitle = htmlspecialchars($row['title_news']);
?>

<h3 class="text-center text-white mb-3">
    <?php echo $pageTitle; ?><br>
    <span class='text-primary'><?php echo htmlspecialchars($row["title_news"]); ?></span>
</h3>
<p class='text-center text-danger'>Поля, отмеченные звездочкой (*), обязательны для заполнения.</p>

<div class="container mt-4">
    <div class="row justify-content-center">
        <form action="news-approve-edit-form-process.php" method="post" enctype="multipart/form-data">

            <div class="form-floating mb-3">
                <input type="text" value="<?= 'User: ' . htmlspecialchars($row['user_id'] ?? ''); ?>" disabled>
            </div>

            <input type="hidden" id="newsId" name="newsId" value="<?= htmlspecialchars($row['id_news']); ?>">
            <input type="hidden" id="user_id" name="user_id" value="<?= htmlspecialchars($row['user_id']); ?>">

            <!-- Category Selection -->
            <div class="form-floating mb-3">
                <select class="form-select" id="category_news" name="category_news" aria-label="Выберите категорию новости" required>
                    <option value="" disabled>Выберите категорию новости</option>
                    <?php
                    $selectCategoriesQuery = "SELECT id_category_news, title_category_news FROM news_categories";
                    $selectCategoriesResult = $connection->query($selectCategoriesQuery);
                    if ($selectCategoriesResult->num_rows > 0) {
                        while ($categoryRow = $selectCategoriesResult->fetch_assoc()) {
                            $idCategory = $categoryRow["id_category_news"];
                            $titleCategory = $categoryRow["title_category_news"];
                            echo "<option value=\"$idCategory\" " . ($row['category_news'] == $idCategory ? 'selected' : '') . ">$titleCategory</option>";
                        }
                    } else {
                        echo "<option disabled>No categories available</option>";
                    }
                    $selectCategoriesResult->close();
                    ?>
                </select>
                <label for="category_news">Category <?php echo requiredAsterisk(); ?></label>
            </div>

            <!-- title_news -->
            <div class="form-floating mb-3">
                <textarea class="form-control" id="title_news" name="title_news" style="height: 65px; font-size: 14px;" required placeholder="title"><?= htmlspecialchars($row["title_news"]); ?></textarea>
                <label for="title_news">Заголовок новости <?php echo requiredAsterisk(); ?></label>
            </div>

            <!-- description_news -->
            <div class="form-floating mb-3">
                <textarea class="form-control" id="description_news" name="description_news" style="height: 85px; font-size: 14px;" required placeholder="description_news"><?= htmlspecialchars($row["description_news"]); ?></textarea>
                <label for="description_news">Описание новости <?php echo requiredAsterisk(); ?></label>
            </div>

            <div class="col">
                <!-- text_news -->
                <div class="form-floating mb-3">
                    <textarea class="form-control" id="text_news" name="text_news" style="height: 185px; font-size: 14px;" required placeholder="text_news"><?= htmlspecialchars($row["text_news"]); ?></textarea>
                    <label for="text_news">Текст новости <?php echo requiredAsterisk(); ?></label>
                </div>
            </div>

            <!-- URL -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="url_news" name="url_news" value="<?= htmlspecialchars($row["url_news"]); ?>" placeholder="URL" style="font-size: 14px;" required>
                <label for="url_news">URL <?php echo requiredAsterisk(); ?></label>
            </div>

            <div class="row my-3">
                <?php for ($i = 1; $i <= 3; $i++): ?>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input type="file" class="form-control" id="image_news_<?= $i ?>" name="image_news_<?= $i ?>" accept="image/*">
                            <label for="image_news_<?= $i ?>">Загрузить изображение <?= $i ?> (необязательно)</label>
                            <!-- Image Preview Container -->
                            <div class="mt-3 position-relative" id="image-container-<?= $i ?>" style="display: <?= !empty($row['image_news_' . $i]) ? 'block' : 'none'; ?>;">
                                <img id="image-preview-<?= $i ?>" src="<?= !empty($row['image_news_' . $i]) ? '/images/news-images/' . htmlspecialchars($row['image_news_' . $i]) : '' ?>" alt="Предпросмотр изображения <?= $i ?>" style="max-width: 200px; border: 1px solid #ddd; padding: 5px;">
                                <!-- Close Button -->
                                <button type="button" id="clear-image-<?= $i ?>" class="ms-2 btn btn-light btn-sm">Удалить</button>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>

            <div class="d-flex justify-content-center align-items-center mt-3 gap-3">
                <!-- Approved Radio Button -->
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="approveNEWS" id="approveNEWS" value="1" <?= isset($row['approved']) && $row['approved'] == 1 ? 'checked' : ''; ?>>
                    <label class="form-check-label text-white" for="approveNEWS">Approve</label>
                </div>

                <!-- Pending Radio Button (set as default) -->
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="approveNEWS" id="pendingNEWS" value="2" <?= isset($row['approved']) && $row['approved'] == 2 ? 'checked' : (!isset($row['approved']) ? 'checked' : ''); ?>>
                    <label class="form-check-label text-white" for="pendingNEWS">Pending</label>
                </div>

                <!-- Not Approved Radio Button -->
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="approveNEWS" id="notApproveNEWS" value="0" <?= isset($row['approved']) && $row['approved'] == 0 ? 'checked' : ''; ?>>
                    <label class="form-check-label text-white" for="notApproveNEWS">Not Approved</label>
                </div>

                <button type="submit" class="btn btn-success btn-sm">Сохранить</button>
                <a href="/dashboard/news-approve.php" class="btn btn-secondary btn-sm">Отмена</a>
                <button type="button" class="btn btn-danger btn-sm" onclick="deleteNews(<?= $row['id_news']; ?>, '<?= $newsTitle; ?>')">
                    <i class="fas fa-trash-alt"></i> Удалить
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    // Handle checkbox state change for approve
    document.getElementById("approveNEWS").addEventListener("change", function() {
        document.getElementById("approved").value = this.checked ? 1 : 0;
    });

    // Delete News function with ID and Title
    function deleteNews(messageId, messageTitle) {
        if (confirm(`Are you sure you want to delete this post?\nID: ${messageId}\nTitle: "${messageTitle}"`)) {
            window.location.href = `/pages/dashboard/news-dashboard/news-approve/news-approve-delete.php?id_news=${messageId}`;
        }
    }

    // Image preview and clear functionality
    document.addEventListener('DOMContentLoaded', function() {
        const fileInputs = [
            document.getElementById('image_news_1'),
            document.getElementById('image_news_2'),
            document.getElementById('image_news_3')
        ];

        const imagePreviews = [
            document.getElementById('image-preview-1'),
            document.getElementById('image-preview-2'),
            document.getElementById('image-preview-3')
        ];

        const clearButtons = [
            document.getElementById('clear-image-1'),
            document.getElementById('clear-image-2'),
            document.getElementById('clear-image-3')
        ];

        const imageContainers = [
            document.getElementById('image-container-1'),
            document.getElementById('image-container-2'),
            document.getElementById('image-container-3')
        ];

        fileInputs.forEach((fileInput, index) => {
            fileInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreviews[index].src = e.target.result;
                        imageContainers[index].style.display = 'block'; // Show preview with close button
                    };
                    reader.readAsDataURL(file);
                }
            });
        });

        clearButtons.forEach((clearButton, index) => {
            clearButton.addEventListener('click', function() {
                fileInputs[index].value = ""; // Clear file input
                imagePreviews[index].src = ""; // Clear preview image
                imageContainers[index].style.display = 'none'; // Hide preview and button

                // Send AJAX request to delete the temporary image
                fetch('/delete-temporary-image.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        image: 'image_news_' + (index + 1),
                        type: 'news'
                    })
                }).then(response => response.json()).then(data => {
                    if (data.success) {
                        console.log('Temporary image deleted successfully.');
                    } else {
                        console.error('Error deleting temporary image:', data.error);
                    }
                });
            });
        });
    });
</script>
