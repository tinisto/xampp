<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';




require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';




$mainContent = 'index_content.php';

$pageTitle = '11-классники';


// include 'template-engine.php';
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine.php';




// Render the template with dynamic content
renderTemplate($pageTitle, $mainContent);
