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

<style>
  .news-meta-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 0;
    border-bottom: 1px solid var(--border-color, #e2e8f0);
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
  }
  
  .news-author-info {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  
  .news-author-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
  }
  
  .news-author-details {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  
  .news-author-name {
    font-size: 14px;
    color: var(--text-primary, #1a202c);
    font-weight: 500;
  }
  
  .news-publish-date {
    font-size: 13px;
    color: var(--text-secondary, #64748b);
  }
  
  .news-breadcrumb {
    display: flex;
    align-items: center;
    gap: 16px;
    list-style: none;
    margin: 0;
    padding: 0;
    font-size: 13px;
  }
  
  .news-breadcrumb li {
    display: flex;
    align-items: center;
    color: var(--text-secondary, #64748b);
  }
  
  .news-breadcrumb li:not(:last-child)::after {
    content: "|";
    margin-left: 16px;
    color: var(--text-muted, #cbd5e1);
  }
  
  .news-breadcrumb a {
    color: var(--primary-color, #28a745);
    text-decoration: none;
    transition: color 0.2s ease;
  }
  
  .news-breadcrumb a:hover {
    color: var(--primary-hover, #218838);
    text-decoration: underline;
  }
  
  .news-view-count {
    display: flex;
    align-items: center;
    gap: 6px;
  }
  
  /* Dark mode */
  [data-theme="dark"] .news-meta-container {
    border-bottom-color: var(--border-color, #374151);
  }
  
  [data-theme="dark"] .news-author-name {
    color: var(--text-primary, #f9fafb);
  }
  
  [data-theme="dark"] .news-publish-date {
    color: var(--text-secondary, #9ca3af);
  }
  
  [data-theme="dark"] .news-breadcrumb li {
    color: var(--text-secondary, #9ca3af);
  }
  
  [data-theme="dark"] .news-breadcrumb li::after {
    color: var(--text-muted, #4b5563);
  }
  
  /* Mobile responsive */
  @media (max-width: 768px) {
    .news-meta-container {
      flex-direction: column;
      align-items: flex-start;
    }
    
    .news-breadcrumb {
      flex-wrap: wrap;
    }
  }
</style>

<div class="news-meta-container">
  <div class="news-author-info">
    <?php if ($userInfo): ?>
      <img src="<?= htmlspecialchars($avatarPath) ?>" alt="Avatar" class="news-author-avatar">
      <div class="news-author-details">
        <div class="news-author-name">
          <?= htmlspecialchars($userInfo['firstname'] . ' ' . $userInfo['lastname'], ENT_QUOTES, 'UTF-8') ?>
        </div>
        <div class="news-publish-date">
          <?php if (!empty($rowNews['date_edited'])): ?>
            Изменено: <?= str_replace(',', '', getRussianDate($rowNews['date_edited'])) ?>
          <?php else: ?>
            Опубликовано: <?= str_replace(',', '', getRussianDate($rowNews['date_news'])) ?>
          <?php endif; ?>
        </div>
      </div>
    <?php else: ?>
      <div class="news-author-details">
        <div class="news-publish-date">
          <?php if (!empty($rowNews['date_edited'])): ?>
            Изменено: <?= str_replace(',', '', getRussianDate($rowNews['date_edited'])) ?>
          <?php else: ?>
            Опубликовано: <?= str_replace(',', '', getRussianDate($rowNews['date_news'])) ?>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
  
  <div>
    <?php include 'news-actions.php'; ?>
  </div>
  
  <ul class="news-breadcrumb">
    <li>
      <a href="<?= $linkCategoryNews ?>">
        <?= $categoryNewsData['title_category_news'] ?>
      </a>
    </li>
    <?php if (!empty($rowNews['author_news'])) { ?>
      <li><?= $rowNews['author_news'] ?></li>
    <?php } ?>
    <li class="news-view-count">
      <i class="fas fa-eye"></i>
      <?= $rowNews['view_news'] ?>
    </li>
  </ul>
</div>