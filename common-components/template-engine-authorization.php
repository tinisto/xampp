<?php
// Unified template - authorization layout
include $_SERVER["DOCUMENT_ROOT"] . "/common-components/template-engine.php";

// Use unified template with 'auth' layout
renderUnifiedTemplate($pageTitle, $mainContent, [], "", "", "", "", "", 'auth');
?>