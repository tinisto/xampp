<?php
// Working SPO/VPO template - simplified but complete
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';

try {
    if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($connection->connect_error) {
            header("Location: /error");
            exit();
        }
        
        $connection->set_charset("utf8mb4");
    } else {
        header("Location: /error");
        exit();
    }
} catch (Exception $e) {
    header("Location: /error");
    exit();
}

// Determine the type from URL parameter or URL path
$type = $_GET['type'] ?? null;
if (!$type) {
    // Fallback: determine from URL path
    $requestUri = $_SERVER['REQUEST_URI'];
    $type = strpos($requestUri, '/vpo/') !== false ? 'vpo' : 'spo';
}

// Get institution data from URL slug
$url = $_GET['url_slug'] ?? basename($_SERVER['REQUEST_URI']);
$url = preg_replace('/\?.*/', '', $url); // Remove query string

$query = "SELECT * FROM $type WHERE url_slug = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $url);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: /404");
    exit();
}

$row = $result->fetch_assoc();
$pageTitle = $row['name'] ?? 'Учебное заведение';
$institutionType = $type === 'vpo' ? 'Высшее учебное заведение' : 'Среднее профессиональное образовательное учреждение';

// Build address
$addressParts = array_filter([
    $row['zip_code'] ?? '',
    $row['street'] ?? ''
]);
$address = implode(', ', $addressParts);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - 11-классники</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        .institution-header {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .institution-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .info-section {
            background: white;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .info-section h3 {
            margin-top: 0;
            color: #28a745;
        }
        @media (max-width: 768px) {
            .institution-info {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="institution-header">
        <h1><?= htmlspecialchars($pageTitle) ?></h1>
        <p><strong>Тип:</strong> <?= htmlspecialchars($institutionType) ?></p>
    </div>

    <div class="institution-info">
        <div class="info-section">
            <h3>Контактная информация</h3>
            
            <?php if (!empty($address)): ?>
                <p><strong>Адрес:</strong> <?= htmlspecialchars($address) ?></p>
            <?php endif; ?>
            
            <?php if (!empty($row['tel'])): ?>
                <p><strong>Телефон:</strong> <?= htmlspecialchars($row['tel']) ?></p>
            <?php endif; ?>
            
            <?php if (!empty($row['fax'])): ?>
                <p><strong>Факс:</strong> <?= htmlspecialchars($row['fax']) ?></p>
            <?php endif; ?>
            
            <?php if (!empty($row['email'])): ?>
                <p><strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($row['email']) ?>"><?= htmlspecialchars($row['email']) ?></a></p>
            <?php endif; ?>
            
            <?php if (!empty($row['site'])): ?>
                <p><strong>Сайт:</strong> <a href="<?= htmlspecialchars($row['site']) ?>" target="_blank"><?= htmlspecialchars($row['site']) ?></a></p>
            <?php endif; ?>
            
            <?php if (!empty($row['licence'])): ?>
                <p><strong>Лицензия:</strong> <?= htmlspecialchars($row['licence']) ?></p>
            <?php endif; ?>
            
            <?php if (!empty($row['accreditation'])): ?>
                <p><strong>Аккредитация:</strong> <?= htmlspecialchars($row['accreditation']) ?></p>
            <?php endif; ?>
        </div>

        <div class="info-section">
            <h3>Администрация</h3>
            
            <?php if (!empty($row['director_name'])): ?>
                <p><strong><?= htmlspecialchars($row['director_role'] ?? 'Директор') ?>:</strong> <?= htmlspecialchars($row['director_name']) ?></p>
            <?php endif; ?>
            
            <?php if (!empty($row['director_phone'])): ?>
                <p><strong>Телефон директора:</strong> <?= htmlspecialchars($row['director_phone']) ?></p>
            <?php endif; ?>
            
            <?php if (!empty($row['director_email'])): ?>
                <p><strong>Email директора:</strong> <a href="mailto:<?= htmlspecialchars($row['director_email']) ?>"><?= htmlspecialchars($row['director_email']) ?></a></p>
            <?php endif; ?>
            
            <?php if (!empty($row['year'])): ?>
                <p><strong>Год основания:</strong> <?= htmlspecialchars($row['year']) ?></p>
            <?php endif; ?>
            
            <?php if (!empty($row['vkontakte'])): ?>
                <p><strong>ВКонтакте:</strong> <a href="<?= htmlspecialchars($row['vkontakte']) ?>" target="_blank">Страница ВК</a></p>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($row['director_info'])): ?>
        <div class="info-section">
            <h3>Информация о руководителе</h3>
            <p><?= nl2br(htmlspecialchars($row['director_info'])) ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($row['history'])): ?>
        <div class="info-section">
            <h3>История учреждения</h3>
            <div><?= nl2br(htmlspecialchars($row['history'])) ?></div>
        </div>
    <?php endif; ?>

    <?php if (!empty($row['full_name']) && $row['full_name'] !== $row['name']): ?>
        <div class="info-section">
            <h3>Полное наименование</h3>
            <p><?= htmlspecialchars($row['full_name']) ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($row['short_name']) && $row['short_name'] !== $row['name']): ?>
        <div class="info-section">
            <h3>Краткое наименование</h3>
            <p><?= htmlspecialchars($row['short_name']) ?></p>
        </div>
    <?php endif; ?>

    <footer style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666;">
        <p>11-классники &copy; <?= date('Y') ?></p>
    </footer>
</body>
</html>
<?php
$stmt->close();
$connection->close();
?>