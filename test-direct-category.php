<?php
// Direct test bypassing all routing
$_GET['category_en'] = 'a-naposledok-ya-skazhu';

echo "<!-- Direct include test at " . date('Y-m-d H:i:s') . " -->\n";
echo "<!-- File size: " . filesize($_SERVER['DOCUMENT_ROOT'] . '/pages/category/category.php') . " bytes -->\n";
echo "<!-- Cards grid size: " . filesize($_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php') . " bytes -->\n";

// Include category.php directly
include $_SERVER['DOCUMENT_ROOT'] . '/pages/category/category.php';
?>