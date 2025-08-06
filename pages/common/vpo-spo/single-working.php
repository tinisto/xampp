<?php
// SPO/VPO Single Page - Working version without template engine
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get institution data from URL slug
$url_slug = $_GET['url_slug'] ?? basename($_SERVER['REQUEST_URI']);
$url_slug = preg_replace('/\?.*/', '', $url_slug); // Remove query string

// Determine the type from URL parameter or URL path
$type = $_GET['type'] ?? null;
if (!$type) {
    // Fallback: determine from URL path
    $requestUri = $_SERVER['REQUEST_URI'];
    $type = strpos($requestUri, '/vpo/') !== false ? 'vpo' : 'spo';
}

// Validate type
if (!in_array($type, ['vpo', 'spo'])) {
    header("Location: /404");
    exit();
}

// Get the institution data with region and town info
$query = "SELECT i.*, r.region_name, r.region_name_en, t.town_name, t.town_name_en 
          FROM $type i
          LEFT JOIN regions r ON i.region_id = r.region_id
          LEFT JOIN towns t ON i.town_id = t.town_id
          WHERE i.url_slug = ?";
$stmt = $connection->prepare($query);
if (!$stmt) {
    header("Location: /error");
    exit();
}

$stmt->bind_param("s", $url_slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: /404");
    exit();
}

$row = $result->fetch_assoc();
$pageTitle = $row['name'] ?? 'Учебное заведение';

// Build location info for badge
$locationParts = array_filter([
    $row['town_name'] ?? '',
    $row['region_name'] ?? ''
]);
$locationText = implode(', ', $locationParts);

// Update view count
$updateQuery = "UPDATE $type SET view = view + 1 WHERE url_slug = ?";
$updateStmt = $connection->prepare($updateQuery);
$updateStmt->bind_param("s", $url_slug);
$updateStmt->execute();
$updateStmt->close();
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
        'badge' => $locationText
    ]);
    ?>
    
    <main style="padding: 40px 0;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="display: flex; gap: 30px;">
                <!-- Main content -->
                <div style="flex: 1;">
                    <!-- Contact Information -->
                    <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
                        <h2 style="color: #28a745; margin-bottom: 20px;">Контактная информация</h2>
                        
                        <?php if (!empty($row['street'])): ?>
                            <p style="margin: 10px 0;">
                                <i class="fas fa-map-marker-alt" style="width: 20px; color: #28a745;"></i>
                                <strong>Адрес:</strong> <?= htmlspecialchars($row['zip_code'] ? $row['zip_code'] . ', ' : '') . htmlspecialchars($row['street']) ?>
                            </p>
                        <?php endif; ?>
                        
                        <?php if (!empty($row['tel'])): ?>
                            <p style="margin: 10px 0;">
                                <i class="fas fa-phone" style="width: 20px; color: #28a745;"></i>
                                <strong>Телефон:</strong> <?= htmlspecialchars($row['tel']) ?>
                            </p>
                        <?php endif; ?>
                        
                        <?php if (!empty($row['fax'])): ?>
                            <p style="margin: 10px 0;">
                                <i class="fas fa-fax" style="width: 20px; color: #28a745;"></i>
                                <strong>Факс:</strong> <?= htmlspecialchars($row['fax']) ?>
                            </p>
                        <?php endif; ?>
                        
                        <?php if (!empty($row['email'])): ?>
                            <p style="margin: 10px 0;">
                                <i class="fas fa-envelope" style="width: 20px; color: #28a745;"></i>
                                <strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($row['email']) ?>" style="color: #28a745;"><?= htmlspecialchars($row['email']) ?></a>
                            </p>
                        <?php endif; ?>
                        
                        <?php if (!empty($row['site'])): ?>
                            <p style="margin: 10px 0;">
                                <i class="fas fa-globe" style="width: 20px; color: #28a745;"></i>
                                <strong>Сайт:</strong> <a href="<?= htmlspecialchars($row['site']) ?>" target="_blank" style="color: #28a745;"><?= htmlspecialchars($row['site']) ?></a>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Administration -->
                    <?php if (!empty($row['director_name'])): ?>
                    <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
                        <h2 style="color: #28a745; margin-bottom: 20px;">Администрация</h2>
                        
                        <p style="margin: 10px 0;">
                            <i class="fas fa-user-tie" style="width: 20px; color: #28a745;"></i>
                            <strong><?= htmlspecialchars($row['director_role'] ?? 'Директор') ?>:</strong> <?= htmlspecialchars($row['director_name']) ?>
                        </p>
                        
                        <?php if (!empty($row['director_phone'])): ?>
                            <p style="margin: 10px 0;">
                                <i class="fas fa-phone" style="width: 20px; color: #28a745;"></i>
                                <strong>Телефон директора:</strong> <?= htmlspecialchars($row['director_phone']) ?>
                            </p>
                        <?php endif; ?>
                        
                        <?php if (!empty($row['director_email'])): ?>
                            <p style="margin: 10px 0;">
                                <i class="fas fa-envelope" style="width: 20px; color: #28a745;"></i>
                                <strong>Email директора:</strong> <a href="mailto:<?= htmlspecialchars($row['director_email']) ?>" style="color: #28a745;"><?= htmlspecialchars($row['director_email']) ?></a>
                            </p>
                        <?php endif; ?>
                        
                        <?php if (!empty($row['director_info'])): ?>
                            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                                <h3 style="color: #333; font-size: 18px;">Информация о директоре</h3>
                                <p><?= nl2br(htmlspecialchars($row['director_info'])) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($row['history'])): ?>
                        <!-- History -->
                        <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
                            <h2 style="color: #28a745; margin-bottom: 20px;">История</h2>
                            <div><?= nl2br(htmlspecialchars($row['history'])) ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($row['full_name']) && $row['full_name'] !== $row['name']): ?>
                        <!-- Full name -->
                        <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <h2 style="color: #28a745; margin-bottom: 20px;">Полное наименование</h2>
                            <p><?= htmlspecialchars($row['full_name']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <div style="width: 300px;">
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                        <h3 style="margin: 0 0 15px 0; font-size: 18px;">Информация</h3>
                        <p style="margin: 10px 0;">
                            <i class="fas fa-graduation-cap" style="width: 20px; color: #28a745;"></i>
                            Тип: <?= $type === 'vpo' ? 'Высшее учебное заведение' : 'Среднее профессиональное образование' ?>
                        </p>
                        <?php if (!empty($row['year'])): ?>
                            <p style="margin: 10px 0;">
                                <i class="fas fa-calendar" style="width: 20px; color: #28a745;"></i>
                                Год основания: <?= htmlspecialchars($row['year']) ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($row['accreditation'])): ?>
                            <p style="margin: 10px 0;">
                                <i class="fas fa-certificate" style="width: 20px; color: #28a745;"></i>
                                Аккредитация: <?= htmlspecialchars($row['accreditation']) ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($row['licence'])): ?>
                            <p style="margin: 10px 0;">
                                <i class="fas fa-file-alt" style="width: 20px; color: #28a745;"></i>
                                Лицензия: <?= htmlspecialchars($row['licence']) ?>
                            </p>
                        <?php endif; ?>
                        <p style="margin: 10px 0;">
                            <i class="fas fa-eye" style="width: 20px; color: #28a745;"></i>
                            Просмотров: <?= number_format($row['view'] ?? 0) ?>
                        </p>
                    </div>

                    <?php if (!empty($row['region_name_en'])): ?>
                        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                            <h3 style="margin: 0 0 15px 0; font-size: 18px;">Другие учебные заведения</h3>
                            <a href="/<?= $type ?>-in-region/<?= htmlspecialchars($row['region_name_en']) ?>" style="color: #28a745; text-decoration: none;">
                                <i class="fas fa-arrow-right"></i> Все <?= $type === 'vpo' ? 'ВУЗы' : 'СПО' ?> в регионе <?= htmlspecialchars($row['region_name']) ?>
                            </a>
                        </div>
                    <?php endif; ?>
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