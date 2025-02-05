<?php
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/auth.php";
ensureAdminAuthenticated();
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/buttons/form-buttons.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/requiredAsterisk.php";

// Check if 'id_post' parameter is present in the URL
if (isset($_GET['id_post'])) {
  // Retrieve and sanitize the postId from the URL
  $postId = filter_var($_GET['id_post'], FILTER_SANITIZE_NUMBER_INT);

  // Fetch data for the specified postId from the database
  // Replace this with your actual database query
  $result = $connection->query("SELECT * FROM posts WHERE id_post = $postId");

  // Check if data is fetched successfully
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
  } else {
    echo "Post not found.";
  }
} else {
  echo "No postId specified in the URL.";
}
?>

<h3 class="text-center text-white mb-3"><?php echo $pageTitle; ?> #
  <?php echo $row['id_post']; ?>
</h3>

<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="card" style="font-size: 14px;">
      <form action="/pages/dashboard/posts-dashboard/posts-view/posts-view-edit-form-process.php" method="post" enctype="multipart/form-data">
        <input type="hidden" id="postId" name="postId" value="">

        <div class="form-group mb-3">
          <label for="category">Category</label>
          <select class="form-select" id="category" name="category" aria-label="Select a category" required>
            <option disabled>Select a category</option>

            <?php
            // Fetch category titles from the 'categories' table
            $selectCategoriesQuery = "SELECT id_category, title_category FROM categories";
            $selectCategoriesResult = $connection->query($selectCategoriesQuery);

            // Check if there are categories
            if ($selectCategoriesResult->num_rows > 0) {
              while ($categoryRow = $selectCategoriesResult->fetch_assoc()) {
                $idCategory = $categoryRow['id_category'];
                $titleCategory = $categoryRow['title_category'];

                // Check if the current category ID matches the category ID in the loop
                $selected = ($row['category'] == $idCategory) ? 'selected' : '';

                echo "<option value=\"$idCategory\" $selected>$titleCategory</option>";
              }
            } else {
              echo "<option disabled>No categories available</option>";
            }

            // Close the result set
            $selectCategoriesResult->close();
            ?>

          </select>
        </div>



        <!-- title_post -->
        <div class="form-group mb-3">
          <label for="title_post">Title <?php echo requiredAsterisk(); ?></label>
          <textarea class="form-control" id="title_post" name="title_post" rows="2"
            required><?php echo $row['title_post']; ?></textarea>
        </div>



        <!-- meta_d_post -->
        <div class="form-group mb-3">
          <label for="meta_d_post">Meta Description <?php echo requiredAsterisk(); ?></label>
          <textarea class="form-control" id="meta_d_post" name="meta_d_post" rows="3"
            required><?php echo $row['meta_d_post']; ?></textarea>
        </div>

        <!-- meta_k_post -->
        <div class="form-group mb-3">
          <label for="meta_k_post">Meta Keywords</label>
          <textarea class="form-control" id="meta_k_post" name="meta_k_post" rows="3"><?php echo $row['meta_k_post']; ?></textarea>
        </div>

        <!-- description_post -->
        <div class="form-group mb-3">
          <label for="description_post">Description <?php echo requiredAsterisk(); ?></label>
          <textarea class="form-control" id="description_post" name="description_post" rows="4"
            required><?php echo $row['description_post']; ?></textarea>
        </div>

        <!-- bio_post -->
        <div class="form-group mb-3">
          <label for="bio_post">Bio</label>
          <textarea class="form-control" id="bio_post" name="bio_post"
            rows="3"><?php echo $row['bio_post']; ?></textarea>
        </div>

        <!-- text_post -->
        <div class="form-group mb-3">
          <label for="text_post">Text</label>
          <textarea class="form-control" id="text_post" name="text_post"
            rows="6"><?php echo $row['text_post']; ?></textarea>
        </div>

        <!-- url_post -->
        <div class="form-group mb-3">
          <label for="url_post">URL <?php echo requiredAsterisk(); ?></label>
          <input type="text" class="form-control" id="url_post" name="url_post" required
            value="<?php echo $row['url_post']; ?>">
        </div>

        <!-- Display current image and allow deletion -->




        <?php if (!empty($row['image_file_1'])): ?>
          <div class="form-group mb-3">
            <label for="image_post">Current Image</label><br>
            <?php
            $imageFileName = isset($row['image_file_1']) ? $row['image_file_1'] : null;
            $imageUrl = $imageFileName ? "../images/posts-images/" . $imageFileName : null;
            $imagePath = $imageFileName ? $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/" . $imageFileName : null;

            if (!empty($row['image_file_1']) && file_exists($imagePath)) {
              echo '<div class="mb-2">';
              echo '<img src="' . $imageUrl . '" alt="Post Image 1" style="width: 100%; height: 100%;">';
              echo '</div>';
            }
            ?>

            <br>
            <input type="checkbox" id="delete_image" name="delete_image"> Delete current image
          </div>
        <?php endif; ?>

        <!-- Option to upload a new image -->
        <div class="form-group mb-3">
          <label for="image_post">Upload New Image (optional)</label>
          <input type="file" class="form-control" id="image_post" name="image_post" accept="image/*">
        </div>

        <input type="hidden" name="id_post" value="<?php echo $row['id_post']; ?>">
        <input type="hidden" name="view_post" value="<?php echo $row['view_post']; ?>">

        <?= renderButtonBlock("Сохранить изменения", "Отмена", "/dashboard"); ?>
      </form>
    </div>
  </div>
</div>