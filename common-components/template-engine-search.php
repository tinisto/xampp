<?php
// Unified template - search layout (full layout optimized for search)
include $_SERVER["DOCUMENT_ROOT"] . "/common-components/template-engine.php";

// Use unified template with 'search' layout
function renderTemplate($pageTitle, $mainContent, $additionalData = [], $metaD = "", $metaK = "") {
    renderUnifiedTemplate($pageTitle, $mainContent, $additionalData, $metaD, $metaK, "", "", "", 'search');
}
?>