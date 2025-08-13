<?php
// Unified template - minimal layout (no header, with footer)
include $_SERVER["DOCUMENT_ROOT"] . "/common-components/template-engine.php";

// Use unified template with 'minimal' layout
function renderTemplate($pageTitle, $mainContent, $additionalData = [], $metaD = "", $metaK = "") {
    renderUnifiedTemplate($pageTitle, $mainContent, $additionalData, $metaD, $metaK, "", "", "", 'minimal');
}
?>