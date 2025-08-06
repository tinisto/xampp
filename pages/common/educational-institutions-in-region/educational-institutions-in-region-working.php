<?php
// Working version based on debug information
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get URL parameters
$region_name_en = $_GET['region_name_en'] ?? '';
$type = $_GET['type'] ?? 'spo';

// Validate type
if (!in_array($type, ['schools', 'spo', 'vpo'])) {
    header("Location: /404");
    exit();
}

// Get region data - the regions table has id_region (not region_id)
$region_id = null;
$region_name = '';
if (!empty($region_name_en)) {
    $query = "SELECT id_region, region_name FROM regions WHERE region_name_en = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $region_name_en);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $region_id = $row['id_region'];  // This is the correct column name
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

// Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

// Get total count - VPO table uses region_id column
$count_query = "SELECT COUNT(*) as total FROM $type WHERE region_id = ?";
$stmt = $connection->prepare($count_query);
$stmt->bind_param("i", $region_id);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Get institutions
$query = "SELECT * FROM $type WHERE region_id = ? ORDER BY name ASC LIMIT ? OFFSET ?";
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
        'showSearch' => false,
        'badge' => $total . ' ' . ($total == 1 ? 'учреждение' : ($total < 5 ? 'учреждения' : 'учреждений'))
    ]);
    ?>
    
    <main style="padding: 40px 0;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="display: flex; gap: 30px;">
                <!-- Main content -->
                <div style="flex: 1; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <?php if ($institutions->num_rows > 0): ?>
                        <?php while ($inst = $institutions->fetch_assoc()): ?>
                            <div style="padding: 20px 0; border-bottom: 1px solid #eee;">
                                <h3 style="margin: 0 0 10px 0; font-size: 20px; color: #333;">
                                    <?php if (!empty($inst['url_slug'])): ?>
                                        <a href="/<?= $type ?>/<?= htmlspecialchars($inst['url_slug']) ?>" style="color: #28a745; text-decoration: none;">
                                            <?= htmlspecialchars($inst['name']) ?>
                                        </a>
                                    <?php else: ?>
                                        <?= htmlspecialchars($inst['name']) ?>
                                    <?php endif; ?>
                                </h3>
                                
                                <?php if (!empty($inst['full_name'])): ?>
                                    <p style="margin: 5px 0; color: #666; font-size: 14px;"><?= htmlspecialchars($inst['full_name']) ?></p>
                                <?php endif; ?>
                                
                                <?php if (!empty($inst['street'])): ?>
                                    <p style="margin: 5px 0; color: #666;">
                                        <i class="fas fa-map-marker-alt" style="width: 20px;"></i>
                                        <?= htmlspecialchars($inst['street']) ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if (!empty($inst['tel'])): ?>
                                    <p style="margin: 5px 0; color: #666;">
                                        <i class="fas fa-phone" style="width: 20px;"></i>
                                        <?= htmlspecialchars($inst['tel']) ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if (!empty($inst['site'])): ?>
                                    <p style="margin: 5px 0; color: #666;">
                                        <i class="fas fa-globe" style="width: 20px;"></i>
                                        <a href="<?= htmlspecialchars($inst['site']) ?>" target="_blank" style="color: #28a745;">
                                            <?= htmlspecialchars($inst['site']) ?>
                                        </a>
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                        
                        <?php if ($total > $limit): ?>
                            <div style="margin-top: 30px; text-align: center;">
                                <?php
                                $totalPages = ceil($total / $limit);
                                $baseUrl = "/$type-in-region/$region_name_en";
                                
                                // Previous button
                                if ($page > 1): ?>
                                    <a href="<?= $baseUrl ?>?page=<?= $page - 1 ?>" style="padding: 8px 15px; background: #f8f9fa; margin: 0 5px; text-decoration: none; border-radius: 4px;">&laquo; Назад</a>
                                <?php endif;
                                
                                // Page numbers
                                for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++):
                                    if ($i == $page): ?>
                                        <span style="padding: 8px 15px; background: #28a745; color: white; margin: 0 5px; border-radius: 4px;"><?= $i ?></span>
                                    <?php else: ?>
                                        <a href="<?= $baseUrl ?>?page=<?= $i ?>" style="padding: 8px 15px; background: #f8f9fa; margin: 0 5px; text-decoration: none; border-radius: 4px;"><?= $i ?></a>
                                    <?php endif;
                                endfor;
                                
                                // Next button
                                if ($page < $totalPages): ?>
                                    <a href="<?= $baseUrl ?>?page=<?= $page + 1 ?>" style="padding: 8px 15px; background: #f8f9fa; margin: 0 5px; text-decoration: none; border-radius: 4px;">Вперед &raquo;</a>
                                <?php endif;
                                ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #666; padding: 40px 0;">В данном регионе нет учебных заведений этого типа.</p>
                    <?php endif; ?>
                </div>
                
                <!-- Sidebar with towns -->
                <div style="width: 300px; background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h3 style="margin: 0 0 20px 0; font-size: 18px;">Города региона</h3>
                    <?php
                    // Get towns with VPOs
                    $towns_query = "SELECT DISTINCT t.id_town, t.town_name, t.town_name_en, COUNT(v.id) as count 
                                   FROM towns t 
                                   JOIN $type v ON t.id_town = v.town_id 
                                   WHERE t.id_region = ? 
                                   GROUP BY t.id_town, t.town_name, t.town_name_en 
                                   ORDER BY t.town_name";
                    $stmt_towns = $connection->prepare($towns_query);
                    $stmt_towns->bind_param("i", $region_id);
                    $stmt_towns->execute();
                    $towns = $stmt_towns->get_result();
                    ?>
                    
                    <?php if ($towns->num_rows > 0): ?>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <?php while ($town = $towns->fetch_assoc()): ?>
                                <li style="margin-bottom: 10px;">
                                    <a href="/<?= $type ?>/<?= htmlspecialchars($region_name_en) ?>/<?= htmlspecialchars($town['town_name_en']) ?>" 
                                       style="text-decoration: none; color: #333;">
                                        <?= htmlspecialchars($town['town_name']) ?> 
                                        <span style="color: #999;">(<?= $town['count'] ?>)</span>
                                    </a>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p style="color: #666;">Нет городов с учреждениями</p>
                    <?php endif; ?>
                    <?php $stmt_towns->close(); ?>
                </div>
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