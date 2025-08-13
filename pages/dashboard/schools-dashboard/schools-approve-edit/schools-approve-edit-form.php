<?php
ensureAdminAuthenticated();

$mainContent = "schools-approve-edit-form-table.php";
$pageTitle = "Schools Verification";

// include 'template-engine.php';
include $_SERVER["DOCUMENT_ROOT"] .
    "/common-components/template-engine.php";

// Render the template with dynamic content
renderTemplate($pageTitle, $mainContent);
