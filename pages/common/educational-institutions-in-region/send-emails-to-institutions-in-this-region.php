<?php
$mainContent = 'send-emails-to-institutions-in-this-region-content.php';

// Set the page title
$pageTitle = 'Send Emails';

// include 'template-engine.php';
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine.php';

// Render the template with dynamic content
renderTemplate($pageTitle, $mainContent);
?>
