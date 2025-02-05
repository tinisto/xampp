<?php
// Check if user has 'admin' role in session and display the institution URL in bold
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
  echo '<a href="/pages/educational-institutions-in-region/send-emails-to-institutions-in-this-region.php?id_region=' . urlencode($id_region) . '&type=' . urlencode($type) . '" class="btn btn-sm btn-secondary" target="_blank">Send Emails in This Region</a>';
}

$commonText = '';
$Link = '';

switch ($type) {
    case 'schools':
        $commonText = 'Школы / Гимназии / Лицеи';
        $Link = '<a href="/schools-all-regions" class="link-custom text-dark">Школы / Гимназии / Лицеи в регионах России</a>';
        break;
    case 'spo':
        $commonText = 'Колледжи / Техникумы';
        $Link = '<a href="/spo-all-regions" class="link-custom text-dark">Среднее профессиональное образование в регионах России</a>';
        break;
    case 'vpo':
        $commonText = 'Университеты / Институты / Академии';
        $Link = '<a href="/vpo-all-regions" class="link-custom text-dark">Высшее профессиональное образование в регионах России</a>';
        break;
}
?>

<nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><small>
        <?php echo $Link; ?>
      </small>
    </li>
    <li class="breadcrumb-item"><small>
        <?= "$commonText {$myrow_region['region_name_rod']} <span class='badge bg-secondary rounded-pill ms-2'>$totalInstitutions</span>" ?>
      </small></li>
  </ol>
</nav>
