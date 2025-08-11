<?php
// Universal version that handles all institution types
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get URL parameters
$region_url_slug = $_GET['region_name_en'] ?? '';  // This comes from URL rewrite
$institution_type = $_GET['type'] ?? 'spo';

// Validate institution type
if (!in_array($institution_type, ['schools', 'spo', 'vpo'])) {
    header("Location: /404");
    exit();
}

// Get region data using the URL slug
$region_database_id = null;
$region_display_name = '';
$region_url_slug_stored = '';
if (!empty($region_url_slug)) {
    $query = "SELECT region_id, region_name, region_name_en FROM regions WHERE region_name_en = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $region_url_slug);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $region_database_id = $row['region_id'];
        $region_display_name = $row['region_name'];
        $region_url_slug_stored = $row['region_name_en'];  // Store for later use in URLs
    }
    $stmt->close();
}

if (!$region_database_id) {
    header("Location: /404");
    exit();
}

// Configure based on type
$config = [
    'vpo' => [
        'title' => 'ВПО в регионе ' . $region_display_name,
        'region_column' => 'region_id',
        'name_column' => 'name',
        'id_column' => 'id',
        'url_prefix' => 'vpo',
        'address_column' => 'street',
        'phone_column' => 'tel',
        'website_column' => 'site'
    ],
    'spo' => [
        'title' => 'СПО в регионе ' . $region_display_name,
        'region_column' => 'region_id',
        'name_column' => 'spo_name',
        'id_column' => 'id_spo',
        'url_prefix' => 'spo',
        'address_column' => 'spo_address',
        'phone_column' => 'spo_phone',
        'website_column' => 'spo_site'
    ],
    'schools' => [
        'title' => 'Школы в регионе ' . $region_display_name,
        'region_column' => 'region_id',
        'name_column' => 'school_name',
        'id_column' => 'id_school',
        'url_prefix' => 'school',
        'address_column' => 'school_address',
        'phone_column' => 'school_phone',
        'website_column' => 'school_site'
    ]
];

$typeConfig = $config[$institution_type];
$pageTitle = $typeConfig['title'];

// Pagination
$current_page = max(1, (int)($_GET['page'] ?? 1));
$items_per_page = 20;
$offset = ($current_page - 1) * $items_per_page;

// Get total count
$count_query = "SELECT COUNT(*) as total FROM $institution_type WHERE {$typeConfig['region_column']} = ?";
$stmt = $connection->prepare($count_query);
$stmt->bind_param("i", $region_database_id);
$stmt->execute();
$total_institutions = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Get institutions
$query = "SELECT * FROM $institution_type WHERE {$typeConfig['region_column']} = ? LIMIT ? OFFSET ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("iii", $region_database_id, $items_per_page, $offset);
$stmt->execute();
$institutions_result = $stmt->get_result();
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
        'badge' => $total_institutions . ' ' . ($total_institutions == 1 ? 'учреждение' : ($total_institutions < 5 ? 'учреждения' : 'учреждений'))
    ]);
    ?>
    
    <main style="padding: 40px 0;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="display: flex; gap: 30px;">
                <!-- Main content -->
                <div style="flex: 1; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <?php if ($institutions_result->num_rows > 0): ?>
                    <?php while ($inst = $institutions_result->fetch_assoc()): ?>
                        <div style="padding: 20px 0; border-bottom: 1px solid #eee;">
                            <?php
                            $name = $inst[$typeConfig['name_column']] ?? $inst['name'] ?? 'Название не указано';
                            $id = $inst[$typeConfig['id_column']] ?? $inst['id'] ?? '';
                            $url_slug = $inst['url_slug'] ?? '';
                            ?>
                            <h3 style="margin: 0 0 10px 0; font-size: 20px; color: #333;">
                                <?php if ($url_slug): ?>
                                    <a href="/<?= $typeConfig['url_prefix'] ?>/<?= htmlspecialchars($url_slug) ?>" style="color: #28a745; text-decoration: none;">
                                        <?= htmlspecialchars($name) ?>
                                    </a>
                                <?php elseif ($id): ?>
                                    <a href="/<?= $typeConfig['url_prefix'] ?>/<?= htmlspecialchars($id) ?>" style="color: #28a745; text-decoration: none;">
                                        <?= htmlspecialchars($name) ?>
                                    </a>
                                <?php else: ?>
                                    <?= htmlspecialchars($name) ?>
                                <?php endif; ?>
                            </h3>
                            
                            <?php
                            // Address
                            $address = $inst[$typeConfig['address_column']] ?? $inst['address'] ?? '';
                            if ($address): ?>
                                <p style="margin: 5px 0; color: #666;">
                                    <i class="fas fa-map-marker-alt" style="width: 20px;"></i>
                                    <?= htmlspecialchars($address) ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php
                            // Phone
                            $phone = $inst[$typeConfig['phone_column']] ?? $inst['phone'] ?? $inst['tel'] ?? '';
                            if ($phone): ?>
                                <p style="margin: 5px 0; color: #666;">
                                    <i class="fas fa-phone" style="width: 20px;"></i>
                                    <?= htmlspecialchars($phone) ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php
                            // Website
                            $website = $inst[$typeConfig['website_column']] ?? $inst['website'] ?? $inst['site'] ?? '';
                            if ($website): ?>
                                <p style="margin: 5px 0; color: #666;">
                                    <i class="fas fa-globe" style="width: 20px;"></i>
                                    <a href="<?= htmlspecialchars($website) ?>" target="_blank" style="color: #28a745;">
                                        <?= htmlspecialchars($website) ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                    
                    <?php if ($total_institutions > $items_per_page): ?>
                        <div style="margin-top: 30px; text-align: center;">
                            <?php
                            $total_pages = ceil($total_institutions / $items_per_page);
                            $baseUrl = "/$institution_type-in-region/$region_url_slug_stored";
                            
                            if ($current_page > 1): ?>
                                <a href="<?= $baseUrl ?>?page=<?= $current_page - 1 ?>" style="padding: 8px 15px; background: #f8f9fa; margin: 0 5px; text-decoration: none; border-radius: 4px;">&laquo; Назад</a>
                            <?php endif;
                            
                            for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++):
                                if ($i == $current_page): ?>
                                    <span style="padding: 8px 15px; background: #28a745; color: white; margin: 0 5px; border-radius: 4px;"><?= $i ?></span>
                                <?php else: ?>
                                    <a href="<?= $baseUrl ?>?page=<?= $i ?>" style="padding: 8px 15px; background: #f8f9fa; margin: 0 5px; text-decoration: none; border-radius: 4px;"><?= $i ?></a>
                                <?php endif;
                            endfor;
                            
                            if ($current_page < $total_pages): ?>
                                <a href="<?= $baseUrl ?>?page=<?= $current_page + 1 ?>" style="padding: 8px 15px; background: #f8f9fa; margin: 0 5px; text-decoration: none; border-radius: 4px;">Вперед &raquo;</a>
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
                    // Get towns with institutions
                    // Note: Need to handle different column names for different institution types
                    $town_id_column = ($institution_type === 'vpo') ? 'town_id' : 'id_town';
                    $region_id_column = ($institution_type === 'vpo') ? 'region_id' : 'id_region';
                    $towns_query = "SELECT DISTINCT t.town_id as town_database_id, t.town_name as town_display_name, t.town_name_en as town_url_slug, COUNT(*) as institution_count 
                                   FROM towns t 
                                   JOIN $institution_type inst ON t.town_id = inst.$town_id_column 
                                   WHERE t.region_id = ? 
                                   GROUP BY t.town_id, t.town_name, t.town_name_en 
                                   ORDER BY t.town_name";
                    $stmt_towns = $connection->prepare($towns_query);
                    $stmt_towns->bind_param("i", $region_database_id);
                    $stmt_towns->execute();
                    $towns_result = $stmt_towns->get_result();
                    ?>
                    
                    <?php if ($towns_result->num_rows > 0): ?>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <?php while ($town = $towns_result->fetch_assoc()): ?>
                                <li style="margin-bottom: 10px;">
                                    <a href="/<?= $institution_type ?>/<?= htmlspecialchars($region_url_slug_stored) ?>/<?= htmlspecialchars($town['town_url_slug']) ?>" 
                                       style="text-decoration: none; color: #333;">
                                        <?= htmlspecialchars($town['town_display_name']) ?> 
                                        <span style="color: #999;">(<?= $town['institution_count'] ?>)</span>
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