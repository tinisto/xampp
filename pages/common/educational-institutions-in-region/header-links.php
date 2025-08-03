<?php
// Admin button removed per request
// if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
//   echo '<a href="/pages/educational-institutions-in-region/send-emails-to-institutions-in-this-region.php?id_region=' . urlencode($id_region) . '&type=' . urlencode($type) . '" class="btn btn-sm btn-secondary" target="_blank">Send Emails in This Region</a>';
// }

$commonText = '';
$Link = '';

switch ($type) {
    case 'schools':
        $commonText = 'Школы / Гимназии / Лицеи';
        $linkUrl = '/schools-all-regions';
        $linkText = 'Школы / Гимназии / Лицеи в регионах России';
        break;
    case 'spo':
        $commonText = 'Колледжи / Техникумы';
        $linkUrl = '/spo-all-regions';
        $linkText = 'Среднее профессиональное образование в регионах России';
        break;
    case 'vpo':
        $commonText = 'Университеты / Институты / Академии';
        $linkUrl = '/vpo-all-regions';
        $linkText = 'Высшее профессиональное образование в регионах России';
        break;
}
?>

<style>
  .breadcrumb-nav {
    margin-bottom: 24px;
    padding: 12px 0;
    border-bottom: 1px solid var(--border-color, #e2e8f0);
  }
  
  .breadcrumb-list {
    display: flex;
    align-items: center;
    gap: 8px;
    list-style: none;
    margin: 0;
    padding: 0;
    font-size: 14px;
  }
  
  .breadcrumb-list li {
    display: flex;
    align-items: center;
    color: var(--text-secondary, #64748b);
  }
  
  .breadcrumb-list li:not(:last-child)::after {
    content: ">";
    margin-left: 8px;
    color: var(--text-muted, #cbd5e1);
  }
  
  .breadcrumb-list a {
    color: var(--primary-color, #28a745);
    text-decoration: none;
    transition: color 0.2s ease;
  }
  
  .breadcrumb-list a:hover {
    color: var(--primary-hover, #218838);
    text-decoration: underline;
  }
  
  .institution-count {
    background: var(--surface-variant, #e2e8f0);
    color: var(--text-primary, #1a202c);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
    margin-left: 8px;
    display: inline-block;
  }
  
  /* Dark mode */
  [data-theme="dark"] .breadcrumb-nav {
    border-bottom-color: var(--border-color, #374151);
  }
  
  [data-theme="dark"] .institution-count {
    background: var(--surface-variant, #374151);
    color: var(--text-primary, #f9fafb);
  }
</style>

<nav class="breadcrumb-nav">
  <ul class="breadcrumb-list">
    <li>
      <a href="<?= $linkUrl ?>"><?= $linkText ?></a>
    </li>
    <li>
      <?= "$commonText {$myrow_region['region_name_rod']}" ?>
      <span class="institution-count"><?= $totalInstitutions ?></span>
    </li>
  </ul>
</nav>
