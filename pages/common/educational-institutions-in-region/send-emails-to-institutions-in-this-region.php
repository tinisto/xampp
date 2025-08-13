<?php
$mainContent = 'send-emails-to-institutions-in-this-region-content.php';

// Set the page title
$pageTitle = 'Send Emails';

// include 'template.php';
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template.php';

// Render the template with dynamic content
renderTemplate($pageTitle, $mainContent);
?>
