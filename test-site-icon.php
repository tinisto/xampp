<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/site-icon.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Site Icon Test</title>
</head>
<body>
    <h1>Site Icon Test</h1>
    
    <h2>Gradient Version (Header/Footer):</h2>
    <?php renderSiteIcon('small'); ?>
    
    <h2>SVG Version (Forms):</h2>
    <?php renderSiteIconSVG('medium', '/'); ?>
    
    <h2>SVG Without Link:</h2>
    <?php renderSiteIconSVG('medium'); ?>
    
</body>
</html>