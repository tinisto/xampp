<?php
// Simple router that just includes the working version
$_GET['category_en'] = isset($_GET['category_en']) ? $_GET['category_en'] : '';

// Include the working category page
include $_SERVER['DOCUMENT_ROOT'] . '/category-working.php';
?>