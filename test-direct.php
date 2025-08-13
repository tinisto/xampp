<?php
// Direct test access - bypassing routing to test the test-simple.php functionality

// Get test parameter from URL or default to math-test
$_GET['test'] = $_GET['test'] ?? 'math-test';
$_GET['q'] = $_GET['q'] ?? 0;

// Include the test file directly
include __DIR__ . '/pages/tests/test-simple.php';
?>