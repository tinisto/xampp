<?php
// Simple version that bypasses template engine
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Include data fetch
include 'school-single-data-fetch.php';

// Check if we got school data
if (!isset($row) || !isset($pageTitle)) {
    header("Location: /404");
    exit();
}

// Output simple HTML
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - 11-классники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1><?= htmlspecialchars($pageTitle) ?></h1>
        <p>School ID: <?= htmlspecialchars($row['id_school']) ?></p>
        <p>Address: <?= htmlspecialchars($row['address'] ?? 'Not specified') ?></p>
        <p>Director: <?= htmlspecialchars($row['fio_director'] ?? 'Not specified') ?></p>
        <hr>
        <p>This is a simplified school page for testing.</p>
        <a href="/" class="btn btn-primary">Back to Home</a>
    </div>
</body>
</html>