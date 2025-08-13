<?php
// Unified template - VPO/SPO layout (full layout for educational institutions)
include $_SERVER["DOCUMENT_ROOT"] . "/common-components/template-engine.php";

// Use unified template with default layout (full features)
function renderTemplate($pageTitle, $mainContent, $additionalData = [], $metaD = "", $metaK = "") {
    renderUnifiedTemplate($pageTitle, $mainContent, $additionalData, $metaD, $metaK, "", "", "", 'default');
}
?>