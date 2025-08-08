<?php
echo "<!-- VARIABLE TEST -->";
$testVar = "TEST123";
echo "<!-- Before include: testVar = $testVar -->";
$greyContent1 = '<h1>THIS IS A TEST</h1>';
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>