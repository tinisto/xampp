<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/buttons/form-buttons.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/requiredAsterisk.php";


// Check if 'id_news' parameter is present in the URL
if (isset($_GET['id_news'])) {
  // Retrieve and sanitize the newsId from the URL
  $newsId = filter_var($_GET['id_news'], FILTER_SANITIZE_NUMBER_INT);

  // Fetch data for the specified newsId from the database
  // Replace this with your actual database query
  $resultNews = $connection->query("SELECT * FROM news WHERE id_news = $newsId");

  // Check if data is fetched successfully
  if ($resultNews->num_rows > 0) {
    $row = $resultNews->fetch_assoc();
  } else {
    echo "Post not found.";
  }
} else {
  echo "No newsId specified in the URL.";
}
?>

<h3 class="text-center text-white mb-3"><?php echo $pageTitle; ?> #
  <?php echo $row['id_news']; ?>
</h3>

<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="card" style="font-size: 14px;">
      <div class="card-body">
        <form action="/pages/dashboard/news-dashboard/admin-view-news/admin-view-news-edit-form-process.php" method="post">
          <input type="hidden" id="newsId" name="newsId" value="">

          <!-- category_news -->
          <div class="form-group mb-3">
            <label for="category_news">Category News</label>
            <select class="form-select" id="category_news" name="category_news" aria-label="Select a category_news"
              required>
              <option disabled>Select a category news</option>

              <?php
              // Fetch category_news titles from the 'categories' table
              $selectCategoriesQuery = "SELECT id_category_news, title_category_news FROM news_categories";
              $selectCategoriesResult = $connection->query($selectCategoriesQuery);

              // Check if there are categories
              if ($selectCategoriesResult->num_rows > 0) {
                while ($categoryRow = $selectCategoriesResult->fetch_assoc()) {
                  $idCategoryNews = $categoryRow['id_category_news'];
                  $titleCategoryNews = $categoryRow['title_category_news'];

                  // Check if the current category_news ID matches the category_news ID in the loop
                  $selected = ($row['category_news'] == $idCategoryNews) ? 'selected' : '';

                  echo "<option value=\"$idCategoryNews\" $selected>$titleCategoryNews</option>";
                }
              } else {
                echo "<option disabled>No categories available</option>";
              }

              // Close the result set
              $selectCategoriesResult->close();
              ?>

            </select>
          </div>

          <!-- title_news -->
          <div class="form-group mb-3">
            <label for="title_news">Title News <?php echo requiredAsterisk(); ?></label>
            <textarea class="form-control" id="title_news" name="title_news" rows="2"
              required><?php echo $row['title_news']; ?></textarea>
          </div>

          <!-- meta_d_news -->
          <div class="form-group mb-3">
            <label for="meta_d_news">Meta Description News</label>
            <textarea class="form-control" id="meta_d_news" name="meta_d_news"
              rows="3"><?php echo $row['meta_d_news']; ?></textarea>
          </div>

          <!-- meta_k_news -->
          <div class="form-group mb-3">
            <label for="meta_k_news">Meta Keywords News</label>
            <textarea class="form-control" id="meta_k_news" name="meta_k_news"
              rows="3"><?php echo $row['meta_k_news']; ?></textarea>
          </div>

          <!-- description_news -->
          <div class="form-group mb-3">
            <label for="description_news">Description News</label>
            <textarea class="form-control" id="description_news" name="description_news"
              rows="4"><?php echo $row['description_news']; ?></textarea>
          </div>

          <!-- text_news -->
          <div class="form-group mb-3">
            <label for="text_news">Text News <?php echo requiredAsterisk(); ?></label>
            <textarea class="form-control" id="text_news" name="text_news" rows="6" required><?php echo nl2br($row['text_news']); ?>
</textarea>
          </div>

          <!-- url_post -->
          <div class="form-group mb-3">
            <label for="url_news">URL News <?php echo requiredAsterisk(); ?></label>
            <input type="text" class="form-control" id="url_news" name="url_news" required
              value="<?php echo $row['url_news']; ?>">
          </div>

          <input type="hidden" name="id_news" value="<?php echo $row['id_news']; ?>">
          <input type="hidden" name="view_news" value="<?php echo $row['view_news']; ?>">

          <?= renderButtonBlock("Сохранить изменения", "Отмена", "/dashboard"); ?>

        </form>
      </div>
    </div>
  </div>
</div>