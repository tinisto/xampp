<?php
// Unified template - nofollow layout (full layout with SEO restrictions)
include $_SERVER["DOCUMENT_ROOT"] . "/common-components/template-engine.php";

// Use unified template with 'nofollow' layout
function renderTemplate($pageTitle, $mainContent, $additionalData = [], $metaD = "", $metaK = "") {
    renderUnifiedTemplate($pageTitle, $mainContent, $additionalData, $metaD, $metaK, "", "", "", 'nofollow');
}
?>