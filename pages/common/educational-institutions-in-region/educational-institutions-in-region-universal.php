<?php
// Universal version that handles all institution types
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

// Configure based on type
$config = [
    'vpo' => [
        'title' => 'ВПО в регионе ' . $region_name,
        'region_column' => 'region_id',
        'name_column' => 'name',
        'id_column' => 'id',
        'url_prefix' => 'vpo',
        'address_column' => 'street',
        'phone_column' => 'tel',
        'website_column' => 'site'
    ],
    'spo' => [
        'title' => 'СПО в регионе ' . $region_name,
        'region_column' => 'id_region',
        'name_column' => 'spo_name',
        'id_column' => 'id_spo',
        'url_prefix' => 'spo',
        'address_column' => 'spo_address',
        'phone_column' => 'spo_phone',
        'website_column' => 'spo_site'
    ],
    'schools' => [
        'title' => 'Школы в регионе ' . $region_name,
        'region_column' => 'id_region',
        'name_column' => 'school_name',
        'id_column' => 'id_school',
        'url_prefix' => 'school',
        'address_column' => 'school_address',
        'phone_column' => 'school_phone',
        'website_column' => 'school_site'
    ]
];

$typeConfig = $config[$type];
$pageTitle = $typeConfig['title'];

// Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

// Get total count
$count_query = "SELECT COUNT(*) as total FROM $type WHERE {$typeConfig['region_column']} = ?";
$stmt = $connection->prepare($count_query);
$stmt->bind_param("i", $region_id);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Get institutions
$query = "SELECT * FROM $type WHERE {$typeConfig['region_column']} = ? LIMIT ? OFFSET ?";
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
            <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <?php if ($institutions->num_rows > 0): ?>
                    <?php while ($inst = $institutions->fetch_assoc()): ?>
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
                    
                    <?php if ($total > $limit): ?>
                        <div style="margin-top: 30px; text-align: center;">
                            <?php
                            $totalPages = ceil($total / $limit);
                            $baseUrl = "/$type-in-region/$region_name_en";
                            
                            if ($page > 1): ?>
                                <a href="<?= $baseUrl ?>?page=<?= $page - 1 ?>" style="padding: 8px 15px; background: #f8f9fa; margin: 0 5px; text-decoration: none; border-radius: 4px;">&laquo; Назад</a>
                            <?php endif;
                            
                            for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++):
                                if ($i == $page): ?>
                                    <span style="padding: 8px 15px; background: #28a745; color: white; margin: 0 5px; border-radius: 4px;"><?= $i ?></span>
                                <?php else: ?>
                                    <a href="<?= $baseUrl ?>?page=<?= $i ?>" style="padding: 8px 15px; background: #f8f9fa; margin: 0 5px; text-decoration: none; border-radius: 4px;"><?= $i ?></a>
                                <?php endif;
                            endfor;
                            
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
        </div>
    </main>
    
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php'; ?>
</body>
</html>
<?php
$stmt->close();
$connection->close();
?>