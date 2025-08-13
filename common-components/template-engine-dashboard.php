<?php
// Unified template - dashboard layout
include $_SERVER["DOCUMENT_ROOT"] . "/common-components/template-engine.php";

// Use unified template with 'dashboard' layout  
function renderTemplate($pageTitle, $mainContent, $additionalData = [], $metaD = "", $metaK = "") {
    renderUnifiedTemplate($pageTitle, $mainContent, $additionalData, $metaD, $metaK, "", "", "", 'dashboard');
}
?>