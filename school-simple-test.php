<?php
// Ultra-simple school template to test
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';

$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$connection->set_charset("utf8mb4");

// Get parameters
$url_slug = $_GET['url_slug'] ?? null;
$school_id = $_GET['id_school'] ?? null;

if ($url_slug) {
    $query = "SELECT * FROM schools WHERE url_slug = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $url_slug);
} elseif ($school_id) {
    $school_id_int = intval($school_id);
    $query = "SELECT * FROM schools WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $school_id_int);
} else {
    die("No parameters");
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("School not found");
}

$row = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($row['name']) ?></title>
    <meta charset="UTF-8">
</head>
<body>
    <h1><?= htmlspecialchars($row['name']) ?></h1>
    <p><strong>Address:</strong> <?= htmlspecialchars($row['street'] ?? 'No address') ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($row['tel'] ?? 'No phone') ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($row['email'] ?? 'No email') ?></p>
    <p><strong>Director:</strong> <?= htmlspecialchars($row['director_name'] ?? 'No director') ?></p>
    
    <?php if (!empty($row['history'])): ?>
        <h2>History</h2>
        <p><?= nl2br(htmlspecialchars($row['history'])) ?></p>
    <?php endif; ?>
    
    <p><em>School ID: <?= $row['id'] ?></em></p>
</body>
</html>
<?php
$stmt->close();
$connection->close();
?>