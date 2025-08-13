<?php
ensureAdminAuthenticated();

$mainContent = "schools-approve-edit-form-table.php";
$pageTitle = "Schools Verification";

// include 'template.php';
include $_SERVER["DOCUMENT_ROOT"] .
    "/common-components/template.php";

// Render the template with dynamic content
renderTemplate($pageTitle, $mainContent);
