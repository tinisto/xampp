<?php
// Simple version that handles column differences
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get URL parameters
$region_name_en = $_GET['region_name_en'] ?? '';
$type = $_GET['type'] ?? 'spo';

// Validate type
if (!in_array($type, ['schools', 'spo', 'vpo'])) {
    header("Location: /404");
    exit();
}

// Get region data
$region_id = null;
$region_name = '';
if (!empty($region_name_en)) {
    $query = "SELECT id_region, region_name FROM regions WHERE region_name_en = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $region_name_en);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $region_id = $row['id_region'];
        $region_name = $row['region_name'];
    }
    $stmt->close();
}

if (!$region_id) {
    header("Location: /404");
    exit();
}

// Set page title
$pageTitle = '';
switch ($type) {
    case 'schools':
        $pageTitle = 'Школы в регионе ' . $region_name;
        break;
    case 'spo':
        $pageTitle = 'СПО в регионе ' . $region_name;
        break;
    case 'vpo':
        $pageTitle = 'ВПО в регионе ' . $region_name;
        break;
}

// Test query to check column names
$test_query = "SHOW COLUMNS FROM $type";
$test_result = $connection->query($test_query);
$columns = [];
while ($col = $test_result->fetch_assoc()) {
    $columns[] = $col['Field'];
}

// Determine region column name
$regionColumn = 'id_region';
if (in_array('region_id', $columns)) {
    $regionColumn = 'region_id';
}

// Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

// Get total count
$count_query = "SELECT COUNT(*) as total FROM $type WHERE $regionColumn = ?";
$stmt = $connection->prepare($count_query);
$stmt->bind_param("i", $region_id);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Get institutions
$query = "SELECT * FROM $type WHERE $regionColumn = ? LIMIT ? OFFSET ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("iii", $region_id, $limit, $offset);
$stmt->execute();
$institutions = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - 11-классники</title>
    <link href="/css/unified-styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
    
    <?php 
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/page-section-header.php';
    renderPageSectionHeader([
        'title' => $pageTitle,
        'showSearch' => false
    ]);
    ?>
    
    <main style="padding: 40px 0;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <p style="margin-bottom: 20px;">Найдено учреждений: <?= $total ?></p>
                
                <?php if ($institutions->num_rows > 0): ?>
                    <?php while ($inst = $institutions->fetch_assoc()): ?>
                        <div style="padding: 15px 0; border-bottom: 1px solid #eee;">
                            <?php
                            // Try different name fields
                            $name = $inst['name'] ?? $inst['vpo_name'] ?? $inst['spo_name'] ?? $inst['school_name'] ?? 'Название не указано';
                            ?>
                            <h3 style="margin: 0 0 10px 0; font-size: 18px;"><?= htmlspecialchars($name) ?></h3>
                            
                            <?php if (!empty($inst['address'])): ?>
                                <p style="margin: 5px 0;"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($inst['address']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                    
                    <?php if ($total > $limit): ?>
                        <div style="margin-top: 20px; text-align: center;">
                            <?php
                            $totalPages = ceil($total / $limit);
                            for ($i = 1; $i <= $totalPages; $i++):
                                if ($i == $page): ?>
                                    <span style="padding: 5px 10px; background: #28a745; color: white; margin: 0 5px;"><?= $i ?></span>
                                <?php else: ?>
                                    <a href="?page=<?= $i ?>" style="padding: 5px 10px; background: #f8f9fa; margin: 0 5px; text-decoration: none;"><?= $i ?></a>
                                <?php endif;
                            endfor;
                            ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p>В данном регионе нет учебных заведений этого типа.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php'; ?>
</body>
</html>
<?php
$stmt->close();
$connection->close();
?>