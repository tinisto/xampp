<?php
// Redirect to test-simple.php with default test
$test = $_GET['test'] ?? 'math-test';
$question = $_GET['q'] ?? '0';

header("Location: /pages/tests/test-simple.php?test=$test&q=$question");
exit();
?>