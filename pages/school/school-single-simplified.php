<?php
// Simplified school single page - same design as SPO/VPO
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Database connection
try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
    
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

// Get school ID from URL
$school_id = basename($_SERVER['REQUEST_URI']);
$school_id = preg_replace('/\?.*/', '', $school_id); // Remove query string
$school_id = intval($school_id);

if ($school_id <= 0) {
    header("Location: /404");
    exit();
}

// Get school data
$query = "SELECT * FROM schools WHERE id_school = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $school_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: /404");
    exit();
}

$row = $result->fetch_assoc();
$pageTitle = $row['school_name'] ?? 'Школа';

// Build address from components
$addressParts = array_filter([
    $row['zip_code'] ?? '',
    $row['city'] ?? '',
    $row['street'] ?? ''
]);
$address = implode(', ', $addressParts);
?>
<!DOCTYPE html>
<html lang="ru" data-bs-theme="light" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - 11-классники</title>
    
    <meta name="description" content="<?php echo htmlspecialchars($pageTitle . ' – общеобразовательное учреждение'); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars('школа, образование, ' . $pageTitle); ?>">
    
    <!-- Unified Styles -->
    <link href="/css/unified-styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #28a745;
            --text-primary: #333;
            --background: #ffffff;
            --surface: #ffffff;
            --border-color: #e2e8f0;
        }
        
        [data-theme="dark"] {
            --primary-color: #68d391;
            --text-primary: #f7fafc;
            --background: #1a202c;
            --surface: #1e293b;
            --border-color: #4a5568;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--background);
            color: var(--text-primary);
            line-height: 1.6;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        main {
            flex: 1;
            padding: 40px 0;
        }
        
        .institution-card {
            background: var(--surface);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 20px;
        }
        
        .institution-header {
            margin-bottom: 30px;
        }
        
        .institution-title {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: var(--text-primary);
        }
        
        .institution-type {
            color: #6c757d;
            font-size: 1.1rem;
        }
        
        .info-section {
            margin-bottom: 25px;
        }
        
        .info-section h3 {
            font-size: 1.3rem;
            margin-bottom: 15px;
            color: var(--primary-color);
        }
        
        .info-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            width: 200px;
            flex-shrink: 0;
            color: var(--text-primary);
        }
        
        .info-value {
            color: var(--text-primary);
            word-break: break-word;
        }
        
        .info-value a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .info-value a:hover {
            text-decoration: underline;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .institution-title {
                font-size: 1.5rem;
            }
            
            .info-row {
                flex-direction: column;
            }
            
            .info-label {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
    
    <script>
        (function() {
            const savedTheme = localStorage.getItem('preferred-theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
</head>
<body>
    <!-- Header -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
    
    <!-- Main Content -->
    <main>
        <div class="container">
            <?php
            // Get region info
            $region_query = "SELECT r.region_name, r.region_name_en FROM regions r 
                             JOIN schools s ON r.id_region = s.id_region 
                             WHERE s.id_school = ?";
            $stmt_region = $connection->prepare($region_query);
            $stmt_region->bind_param("i", $school_id);
            $stmt_region->execute();
            $region_result = $stmt_region->get_result();
            if ($region_row = $region_result->fetch_assoc()) {
                echo '<a href="/schools-in-region/' . htmlspecialchars($region_row['region_name_en']) . '" class="back-link">';
                echo '← Вернуться к списку школ региона ' . htmlspecialchars($region_row['region_name']);
                echo '</a>';
            }
            ?>
            
            <div class="institution-card">
                <div class="institution-header">
                    <h1 class="institution-title"><?= htmlspecialchars($pageTitle) ?></h1>
                    <p class="institution-type">Общеобразовательное учреждение</p>
                </div>
                
                <!-- Contact Information -->
                <div class="info-section">
                    <h3>Контактная информация</h3>
                    
                    <?php if (!empty($address)): ?>
                    <div class="info-row">
                        <div class="info-label">Адрес:</div>
                        <div class="info-value"><?= htmlspecialchars($address) ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($row['tel'])): ?>
                    <div class="info-row">
                        <div class="info-label">Телефон:</div>
                        <div class="info-value"><?= htmlspecialchars($row['tel']) ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($row['email'])): ?>
                    <div class="info-row">
                        <div class="info-label">Email:</div>
                        <div class="info-value">
                            <a href="mailto:<?= htmlspecialchars($row['email']) ?>"><?= htmlspecialchars($row['email']) ?></a>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($row['site'])): ?>
                    <div class="info-row">
                        <div class="info-label">Сайт:</div>
                        <div class="info-value">
                            <a href="<?= htmlspecialchars($row['site']) ?>" target="_blank"><?= htmlspecialchars($row['site']) ?></a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Administration -->
                <?php if (!empty($row['director_name'])): ?>
                <div class="info-section">
                    <h3>Руководство</h3>
                    <div class="info-row">
                        <div class="info-label">Директор:</div>
                        <div class="info-value"><?= htmlspecialchars($row['director_name']) ?></div>
                    </div>
                    
                    <?php if (!empty($row['director_email'])): ?>
                    <div class="info-row">
                        <div class="info-label">Email директора:</div>
                        <div class="info-value">
                            <a href="mailto:<?= htmlspecialchars($row['director_email']) ?>"><?= htmlspecialchars($row['director_email']) ?></a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <!-- Additional Information -->
                <?php if (!empty($row['description']) || !empty($row['text'])): ?>
                <div class="info-section">
                    <h3>Дополнительная информация</h3>
                    <div class="info-value">
                        <?= nl2br(htmlspecialchars($row['description'] ?? $row['text'] ?? '')) ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- School Type Info -->
                <?php if (!empty($row['school_type'])): ?>
                <div class="info-section">
                    <h3>Тип учреждения</h3>
                    <div class="info-value">
                        <?= htmlspecialchars($row['school_type']) ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php'; ?>
</body>
</html>
<?php
$connection->close();
?>