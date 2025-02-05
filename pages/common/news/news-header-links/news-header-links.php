<?php
include $_SERVER['DOCUMENT_ROOT'] . '/includes/russian_date_helper.php';
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/get_avatar.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/getUserInfoById.php";

// Fetch category data using $rowNews['category']
$queryCategoryNews = "SELECT * FROM news_categories WHERE id_category_news = {$rowNews['category_news']}";
$resultCategoryNews = mysqli_query($connection, $queryCategoryNews);

if ($resultCategoryNews && mysqli_num_rows($resultCategoryNews) > 0) {
  $categoryNewsData = mysqli_fetch_assoc($resultCategoryNews);
  $linkCategoryNews = "/category-news/{$categoryNewsData['url_category_news']}";
} else {
  // Default link if category not found
  $linkCategoryNews = '/category-news/default';
}

// Fetch user information if user_id is present
$userInfo = null;
$avatarPath = '';
if (!empty($rowNews['user_id'])) {
  $userInfo = getUserInfoById($connection, $rowNews['user_id']);
  $avatarUrl = $userInfo['avatar'];
  $avatarPath = getAvatar($avatarUrl);
}
?>

<div class="d-flex justify-content-between align-items-center" style="--bs-breadcrumb-divider: '|';">
  <div class="d-flex align-items-center">
    <?php if ($userInfo): ?>
      <div class="me-3">
        <img src="<?= htmlspecialchars($avatarPath) ?>" alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%;">
      </div>
      <div>
        <div>
          <small>
            <?= htmlspecialchars($userInfo['firstname'] . ' ' . $userInfo['lastname'], ENT_QUOTES, 'UTF-8') ?>
          </small>
        </div>
        <div>
          <small>
            <?php if (!empty($rowNews['date_edited'])): ?>
              Изменено: <?= str_replace(',', '', getRussianDate($rowNews['date_edited'])) ?>
            <?php else: ?>
              Опубликовано: <?= str_replace(',', '', getRussianDate($rowNews['date_news'])) ?>
            <?php endif; ?>
          </small>
        </div>
      </div>
    <?php else: ?>
      <div>
        <small>
          <?php if (!empty($rowNews['date_edited'])): ?>
            Изменено: <?= str_replace(',', '', getRussianDate($rowNews['date_edited'])) ?>
          <?php else: ?>
            Опубликовано: <?= str_replace(',', '', getRussianDate($rowNews['date_news'])) ?>
          <?php endif; ?>
        </small>
      </div>
    <?php endif; ?>
  </div>
  <div>
    <?php include 'news-actions.php'; ?>
  </div>
  <div>
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><small>
          <a href="<?= $linkCategoryNews ?>">
            <?= $categoryNewsData['title_category_news'] ?>
          </a>
        </small></li>
      <?php if (!empty($rowNews['author_news'])) { ?>
        <li class="breadcrumb-item"><small>
            <?= $rowNews['author_news'] ?>
          </small></li>
      <?php } ?>
      <li class="breadcrumb-item">
        <small>
          <i class="fas fa-eye"></i>
          <?= $rowNews['view_news'] ?>
        </small>
      </li>
    </ol>
  </div>
</div>