<?php
include $_SERVER['DOCUMENT_ROOT'] . '/includes/russian_date_helper.php';
// Fetch category data using $rowPost['category']
$queryCategory = "SELECT * FROM categories WHERE id_category = {$rowPost['category']}";
$resultCategory = mysqli_query($connection, $queryCategory);

if ($resultCategory && mysqli_num_rows($resultCategory) > 0) {
  $categoryData = mysqli_fetch_assoc($resultCategory);
  $linkCategory = "/category/{$categoryData['url_category']}";
} 
?>
<nav style="--bs-breadcrumb-divider: '|';" aria-label="breadcrumb">
  <ol class="breadcrumb d-md-inline-flex justify-content-center">
    <li class="breadcrumb-item"><small>
        <a href="<?= $linkCategory ?>">
          <?= $categoryData['title_category'] ?>
        </a>
      </small>
    </li>
    <li class="breadcrumb-item"><small>
        <?= $rowPost['author_post'] ?>
      </small>
    </li>
    <?php if (!empty($rowPost['date_post'])) { ?>
      <li class="breadcrumb-item">
        <small>
          <?= getRussianDate($rowPost['date_post']) ?>
        </small>
      </li>
    <?php } ?>
    <li class="breadcrumb-item"><small>
        <i class="fas fa-eye"></i>
        <?= $rowPost['view_post'] ?>
      </small>
    </li>
  </ol>
</nav>