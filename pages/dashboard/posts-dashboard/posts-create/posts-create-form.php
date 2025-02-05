<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/buttons/form-buttons.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/requiredAsterisk.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/config/constants.php";

$currentDate = date("Y-m-d");

if (isset($_SESSION['email']) && $_SESSION['role'] === 'admin') {
  $authorName = ADMIN_NAME;
} else {
  $authorName = '';
}
?>

<h3 class="text-center text-white"><?php echo $pageTitle; ?></h3>
<p class='text-center text-danger'>Поля, отмеченные звездочкой (*), обязательны для заполнения.</p>

<div class="container">
  <div class="row justify-content-center">
    <form action="posts-create-process.php" method="post" enctype="multipart/form-data">
      <!-- author -->
      <div class="form-floating mb-3">
        <input type="text" class="form-control" id="author_post" name="author_post" value="<?php echo htmlspecialchars($authorName); ?>" required>
        <label for="author_post">Author <?php echo requiredAsterisk(); ?></label>
      </div>
      <div class="row">
        <div class="col-md-6"> <!-- category dropdown -->
          <div class="form-floating mb-3">
            <select class="form-select" id="category" name="category" aria-label="Select a category" required>
              <option value="" selected disabled>Select a category</option>
              <?php
              $selectCategoriesQuery = "SELECT id_category, title_category FROM categories";
              $selectCategoriesResult = $connection->query($selectCategoriesQuery);

              if ($selectCategoriesResult->num_rows > 0) {
                while ($categoryRow = $selectCategoriesResult->fetch_assoc()) {
                  $idCategory = $categoryRow["id_category"];
                  $titleCategory = $categoryRow["title_category"];
                  echo "<option value=\"$idCategory\">$titleCategory</option>";
                }
              } else {
                echo "<option disabled>No categories available</option>";
              }

              $selectCategoriesResult->close();
              ?>
            </select>
            <label for="category">Category <?php echo requiredAsterisk(); ?></label>
          </div>
        </div>
        <div class="col-md-6"> <!-- date_post -->
          <div class="form-floating mb-3">
            <input type="date" class="form-control" id="date_post" name="date_post" value="<?php echo $currentDate; ?>" required>
            <label for="date_post">Date</label>
          </div>
        </div>
      </div>


      <!-- title_post -->
      <div class="form-floating mb-3">
        <input type="text" class="form-control" id="title_post" name="title_post" required>
        <label for="title_post">Title Post <?php echo requiredAsterisk(); ?></label>
      </div>

      <!-- meta_d_post -->
      <div class="form-floating mb-3">
        <textarea class="form-control" id="meta_d_post" name="meta_d_post" style="height: 100px"></textarea>
        <label for="meta_d_post">Meta Description</label>
      </div>

      <!-- meta_k_post -->
      <div class="form-floating mb-3">
        <input type="text" class="form-control" id="meta_k_post" name="meta_k_post" required>
        <label for="meta_k_post">Meta Keywords <?php echo requiredAsterisk(); ?></label>
      </div>

      <!-- description_post -->
      <div class="form-floating mb-3">
        <textarea class="form-control" id="description_post" name="description_post" style="height: 100px"></textarea>
        <label for="description_post">Description</label>
      </div>

      <!-- bio_post -->
      <div class="form-floating mb-3">
        <textarea class="form-control" id="bio_post" name="bio_post"></textarea>
        <label for="bio_post">Bio</label>
      </div>

      <!-- text_post -->
      <div class="form-floating mb-3">
        <textarea class="form-control" id="text_post" name="text_post" style="height: 200px"></textarea>
        <label for="text_post">Text</label>
      </div>

      <!-- image_post -->
      <div class="row my-3">
        <?php for ($i = 1; $i <= 3; $i++): ?>
          <div class="col-md-4">
            <div class="form-floating mb-3">
              <input type="file" class="form-control" id="image_post_<?= $i ?>" name="image_post_<?= $i ?>" accept="image/*">
              <label for="image_post_<?= $i ?>">Загрузить изображение <?= $i ?> (необязательно)</label>
              <!-- Image Preview Container -->
              <div class="mt-3 position-relative" id="image-container-<?= $i ?>" style="display: <?= isset($_SESSION['temporary_images']['image_post_' . $i]) ? 'block' : 'none'; ?>;">
                <img id="image-preview-<?= $i ?>" src="<?= isset($_SESSION['temporary_images']['image_post_' . $i]) ? '/images/posts-images/' . htmlspecialchars($_SESSION['temporary_images']['image_post_' . $i]) : '' ?>" alt="Предпросмотр изображения <?= $i ?>" style="max-width: 200px; border: 1px solid #ddd; padding: 5px;">
                <!-- Close Button -->
                <button type="button" id="clear-image-<?= $i ?>" class="ms-2 btn btn-light btn-sm">Удалить</button>
              </div>
            </div>
          </div>
        <?php endfor; ?>
      </div>


      <?= renderButtonBlock("Сохранить изменения", "Отмена", "/dashboard"); ?>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const fileInputs = [
      document.getElementById('image_post_1'),
      document.getElementById('image_post_2'),
      document.getElementById('image_post_3')
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
            image: 'image_post_' + (index + 1)
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