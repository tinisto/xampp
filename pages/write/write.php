<?php
// Direct include approach since template-engine.php is missing
$pageTitle = 'Напишите нам';

// Include header
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php';

// Include the write form
include $_SERVER['DOCUMENT_ROOT'] . '/pages/write/write-form-modern.php';

// Include footer
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php';
