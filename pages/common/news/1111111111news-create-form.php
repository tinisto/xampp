<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";

include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/buttons/form-buttons.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/functions/requiredAsterisk.php";

// Default values for variables (will be overridden in the user/admin-specific files)
$category_news = '';  // Will be set dynamically based on the user role
$currentDate = date("Y-m-d");  // Current date for admin date field
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';  // User ID for hidden input
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin'; // Check if the user is admin
$pageTitle = ''; // Default title
$formType = 'news'; // Define the form type

// Constants for occupation categories (these should ideally be defined outside of this code, but included here for clarity)
define('OCCUPATION_UNIVERSITY', 'Представитель ВУЗа');
define('OCCUPATION_COLLEGE', 'Представитель ССУЗа');
define('OCCUPATION_SCHOOL', 'Представитель школы');

// If the user is an admin, the category_news is not required, so we leave it empty or set default
if ($isAdmin) {
  // Admin does not need an occupation and doesn't need category_news.
  $category_news = '';  // Leave it empty or handle accordingly
  $pageTitle = "Создать новость (Админ)";
} else {
  // For non-admin users, we assign category based on the user's occupation
  if (isset($_SESSION['occupation'])) {
    switch ($_SESSION['occupation']) {
      case OCCUPATION_UNIVERSITY:
        $category_news = '1';
        $pageTitle = "Создать новость вашего ВУЗа";
        break;
      case OCCUPATION_COLLEGE:
        $category_news = '2';
        $pageTitle = "Создать новость вашего ССУЗа";
        break;
      case OCCUPATION_SCHOOL:
        $category_news = '3';
        $pageTitle = "Создать новость вашей школы";
        break;
      default:
        // Handle unexpected occupation values if necessary
        $category_news = '';
        $pageTitle = "Создать новость (Неизвестная категория)";
        break;
    }
  } else {
    // If the occupation is not set, handle the situation
    $category_news = '';
    $pageTitle = "Создать новость (Нет должности)";
  }
}
?>

<h3 class="text-center text-white"><?php echo htmlspecialchars($pageTitle); ?></h3>
<p class="text-center text-danger">Поля, отмеченные звездочкой (*), обязательны для заполнения.</p>

<form action="/pages/common/news/news-create-process.php" method="post" enctype="multipart/form-data">
  <!-- Hidden inputs for PHP variables -->
  <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">

  <!-- Admin-specific category selection -->
  <?php if ($isAdmin): ?>
    <!-- Category dropdown for admin -->
    <div class="form-floating mb-3">
      <select class="form-select" id="category_news" name="category_news" aria-label="Выберите категорию новости" required>
        <option value="" selected disabled>Выберите категорию новости</option>
        <?php
        $selectCategoriesQuery = "SELECT id_category_news, title_category_news FROM news_categories";
        $selectCategoriesResult = $connection->query($selectCategoriesQuery);
        if ($selectCategoriesResult->num_rows > 0) {
          while ($categoryRow = $selectCategoriesResult->fetch_assoc()) {
            $idCategory = $categoryRow["id_category_news"];
            $titleCategory = $categoryRow["title_category_news"];
            echo "<option value=\"$idCategory\">$titleCategory</option>";
          }
        } else {
          echo "<option disabled>No categories available</option>";
        }
        $selectCategoriesResult->close();
        ?>
      </select>
      <label for="category_news">Category <?php echo requiredAsterisk(); ?></label>
    </div>
  <?php else: ?>
    <!-- Hidden category input for users -->
    <input type="hidden" name="category_news" value="<?php echo htmlspecialchars($category_news); ?>">
  <?php endif; ?>

  <!-- Inside common-components/news-create-form.php -->
  <?php if ($isAdmin): ?>
    <!-- Admin-specific fields -->
    <div class="form-floating mb-3">
      <textarea class="form-control" id="meta_d_news" name="meta_d_news" placeholder="Meta Description"></textarea>
      <label for="meta_d_news">Meta Description</label>
    </div>
    <div class="form-floating mb-3">
      <textarea class="form-control" id="meta_k_news" name="meta_k_news" placeholder="Meta Keywords"></textarea>
      <label for="meta_k_news">Meta Keywords</label>
    </div>
    <div class="form-group mb-3">
      <input type="date" class="form-control" id="date_news" name="date_news" required value="<?php echo $currentDate; ?>">
    </div>
  <?php endif; ?>

  <!-- Title Input -->
  <div class="form-floating mb-3 small">
    <input type="text" class="form-control" id="title_news" name="title_news" required placeholder="Заголовок новости">
    <label for="title_news">Заголовок новости <?php echo requiredAsterisk(); ?></label>
  </div>

  <!-- Description Input (Optional) -->
  <div class="form-floating mb-3 small">
    <textarea class="form-control" id="description_news" name="description_news" placeholder="Краткий обзор новости" style="height: 100px;"></textarea>
    <label for="description_news">Краткий обзор новости (необязательно)</label>
  </div>

  <!-- Full Text Input -->
  <div class="form-floating mb-3 small">
    <textarea class="form-control" id="text_news" name="text_news" required placeholder="Полный текст новости" style="height: 150px;"></textarea>
    <label for="text_news">Полный текст новости <?php echo requiredAsterisk(); ?></label>
  </div>

  <div class="row my-3">
    <?php for ($i = 1; $i <= 3; $i++): ?>
      <div class="col-md-4">
        <div class="form-floating mb-3">
          <input type="file" class="form-control" id="image_<?= $formType ?>_<?= $i ?>" name="image_<?= $formType ?>_<?= $i ?>" accept="image/*">
          <label for="image_<?= $formType ?>_<?= $i ?>">Загрузить изображение <?= $i ?> (необязательно)</label>
          <!-- Image Preview Container -->
          <div class="mt-3 position-relative" id="image-container-<?= $i ?>" style="display: <?= isset($_SESSION['temporary_images']['image_' . $formType . '_' . $i]) ? 'block' : 'none'; ?>;">
            <img id="image-preview-<?= $i ?>" src="<?= isset($_SESSION['temporary_images']['image_' . $formType . '_' . $i]) ? '/images/' . $formType . '-images/' . htmlspecialchars($_SESSION['temporary_images']['image_' . $formType . '_' . $i]) : '' ?>" alt="Предпросмотр изображения <?= $i ?>" style="max-width: 200px; border: 1px solid #ddd; padding: 5px;">
            <!-- Close Button -->
            <button type="button" id="clear-image-<?= $i ?>" class="ms-2 btn btn-light btn-sm">Удалить</button>
          </div>
        </div>
      </div>
    <?php endfor; ?>
  </div>

  <!-- Submit Button -->
  <?= renderButtonBlock("Сохранить", "Отмена"); ?>
</form>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const fileInputs = [
      document.getElementById('image_<?= $formType ?>_1'),
      document.getElementById('image_<?= $formType ?>_2'),
      document.getElementById('image_<?= $formType ?>_3')
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
            image: 'image_<?= $formType ?>_' + (index + 1),
            type: '<?= $formType ?>'
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