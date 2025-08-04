<?php
// Fixed version - bypass template engine and add database connection
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

// Get URL parameters
$region_name_en = $_GET['region_name_en'] ?? '';
$type = $_GET['type'] ?? 'spo';

// Get region_id from region_name_en
$region_id = null;
$region_name = '';
if (!empty($region_name_en)) {
    $query_region = "SELECT region_id, region_name FROM regions WHERE region_name_en = ?";
    $stmt = $connection->prepare($query_region);
    $stmt->bind_param("s", $region_name_en);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $region_id = $row['region_id'];
        $region_name = $row['region_name'];
    }
    $stmt->close();
}

// If no region found, redirect to error
if (!$region_id) {
    header("Location: /404");
    exit();
}

// Define titles and table mappings based on type
switch ($type) {
    case 'spo':
        $pageTitle = 'СПО в регионе ' . $region_name;
        $institutionType = 'Средние профессиональные образовательные учреждения';
        $tableName = 'spo';
        $regionColumn = 'region_id';
        $nameColumn = 'name';
        $urlColumn = 'url_slug';
        $idColumn = 'id';
        break;
    case 'vpo':
        $pageTitle = 'ВПО в регионе ' . $region_name;
        $institutionType = 'Высшие учебные заведения';
        $tableName = 'vpo';
        $regionColumn = 'region_id';
        $nameColumn = 'name';
        $urlColumn = 'url_slug';
        $idColumn = 'id';
        break;
    default:
        $pageTitle = 'Школы в регионе ' . $region_name;
        $institutionType = 'Общеобразовательные учреждения';
        $tableName = 'schools';
        $regionColumn = 'region_id';
        $nameColumn = 'name';
        $urlColumn = 'id';
        $idColumn = 'id';
        $type = 'schools';
        break;
}

// Pagination
$institutionsPerPage = 20;
$currentPage = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$pageOffset = max(0, ($currentPage - 1) * $institutionsPerPage);

// Get institutions
$institutions_query = "SELECT * FROM $tableName WHERE $regionColumn = ? LIMIT $pageOffset, $institutionsPerPage";
$stmt_institutions = $connection->prepare($institutions_query);
$stmt_institutions->bind_param("i", $region_id);
$stmt_institutions->execute();
$institutions_result = $stmt_institutions->get_result();

// Get total count for pagination
$total_query = "SELECT COUNT(*) AS total FROM $tableName WHERE $regionColumn = ?";
$stmt_total = $connection->prepare($total_query);
$stmt_total->bind_param("i", $region_id);
$stmt_total->execute();
$total_result = $stmt_total->get_result();
$totalInstitutions = $total_result->fetch_assoc()['total'];
$stmt_total->close();
?>
<!DOCTYPE html>
<html lang="ru" data-bs-theme="light" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - 11-классники</title>
    
    <meta name="description" content="<?php echo htmlspecialchars($institutionType . ' в регионе ' . $region_name); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($type . ', ' . $region_name . ', образование'); ?>">
    
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
        
        .content-layout {
            display: flex;
            gap: 24px;
            margin-top: 24px;
        }
        
        .main-content {
            flex: 1;
            background: var(--surface);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 20px;
        }
        
        .sidebar {
            width: 300px;
            background: var(--surface);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 20px;
        }
        
        .institution-card {
            background: var(--surface);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .institution-card h3 {
            margin: 0 0 10px 0;
            font-size: 18px;
            color: var(--primary-color);
        }
        
        .institution-card p {
            margin: 5px 0;
            color: var(--text-primary);
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
        }
        
        .pagination a, .pagination span {
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            text-decoration: none;
            color: var(--text-primary);
        }
        
        .pagination .current {
            background: var(--primary-color);
            color: white;
        }
        
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: var(--text-primary);
        }
        
        main {
            flex: 1;
            padding: 40px 0;
        }
        
        @media (max-width: 768px) {
            .content-layout {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
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
            <h1><?= htmlspecialchars($pageTitle) ?></h1>
            
            <div class="content-layout">
                <!-- Main Content: Institutions List -->
                <div class="main-content">
                    <h2><?= htmlspecialchars($institutionType) ?></h2>
                    <p>Найдено учреждений: <?= $totalInstitutions ?></p>
                    
                    <?php if ($institutions_result && $institutions_result->num_rows > 0): ?>
                        <?php while ($institution = $institutions_result->fetch_assoc()): ?>
                            <div class="institution-card">
                                <h3>
                                    <?php if ($type === 'schools'): ?>
                                        <a href="/school/<?= htmlspecialchars($institution[$urlColumn]) ?>">
                                            <?= htmlspecialchars($institution[$nameColumn] ?? 'Название не указано') ?>
                                        </a>
                                    <?php else: ?>
                                        <a href="/<?= $type ?>/<?= htmlspecialchars($institution[$urlColumn] ?? '') ?>">
                                            <?= htmlspecialchars($institution[$nameColumn] ?? 'Название не указано') ?>
                                        </a>
                                    <?php endif; ?>
                                </h3>
                                
                                <?php 
                                // Get the correct field names based on type
                                $addressField = $type === 'vpo' ? 'vpo_address' : ($type === 'spo' ? 'spo_address' : 'school_address');
                                $phoneField = $type === 'vpo' ? 'vpo_phone' : ($type === 'spo' ? 'spo_phone' : 'school_phone');
                                $websiteField = $type === 'vpo' ? 'vpo_site' : ($type === 'spo' ? 'spo_site' : 'school_site');
                                ?>
                                
                                <?php if (isset($institution[$addressField]) && !empty($institution[$addressField])): ?>
                                    <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($institution[$addressField]) ?></p>
                                <?php endif; ?>
                                
                                <?php if (isset($institution[$phoneField]) && !empty($institution[$phoneField])): ?>
                                    <p><i class="fas fa-phone"></i> <?= htmlspecialchars($institution[$phoneField]) ?></p>
                                <?php endif; ?>
                                
                                <?php if (isset($institution[$websiteField]) && !empty($institution[$websiteField])): ?>
                                    <p><i class="fas fa-globe"></i> <a href="<?= htmlspecialchars($institution[$websiteField]) ?>" target="_blank"><?= htmlspecialchars($institution[$websiteField]) ?></a></p>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                        
                        <!-- Pagination -->
                        <?php if ($totalInstitutions > $institutionsPerPage): ?>
                            <div class="pagination">
                                <?php
                                $totalPages = ceil($totalInstitutions / $institutionsPerPage);
                                $baseUrl = "/$type-in-region/$region_name_en";
                                
                                // Previous page
                                if ($currentPage > 1) {
                                    echo '<a href="' . $baseUrl . '?page=' . ($currentPage - 1) . '">&laquo; Назад</a>';
                                }
                                
                                // Page numbers
                                for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++) {
                                    if ($i == $currentPage) {
                                        echo '<span class="current">' . $i . '</span>';
                                    } else {
                                        echo '<a href="' . $baseUrl . '?page=' . $i . '">' . $i . '</a>';
                                    }
                                }
                                
                                // Next page
                                if ($currentPage < $totalPages) {
                                    echo '<a href="' . $baseUrl . '?page=' . ($currentPage + 1) . '">Вперед &raquo;</a>';
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <p>В данном регионе нет учебных заведений этого типа.</p>
                    <?php endif; ?>
                </div>
                
                <!-- Sidebar: Towns List -->
                <div class="sidebar">
                    <h3>Города региона</h3>
                    <?php
                    // Get towns with institutions
                    $towns_query = "
                        SELECT DISTINCT t.town_id, t.town_name, t.town_name_en, COUNT(i.town_id) as count
                        FROM towns t
                        JOIN $tableName i ON t.town_id = i.town_id
                        WHERE t.region_id = ?
                        GROUP BY t.town_id, t.town_name, t.town_name_en
                        ORDER BY t.town_name ASC
                    ";
                    $stmt_towns = $connection->prepare($towns_query);
                    $stmt_towns->bind_param("i", $region_id);
                    $stmt_towns->execute();
                    $towns_result = $stmt_towns->get_result();
                    ?>
                    
                    <?php if ($towns_result && $towns_result->num_rows > 0): ?>
                        <ul style="list-style: none; padding: 0;">
                            <?php while ($town = $towns_result->fetch_assoc()): ?>
                                <li style="margin-bottom: 8px;">
                                    <a href="/<?= $type ?>/<?= htmlspecialchars($region_name_en) ?>/<?= htmlspecialchars($town['town_name_en']) ?>" 
                                       style="text-decoration: none; color: var(--text-primary);">
                                        <?= htmlspecialchars($town['town_name']) ?> 
                                        <span style="color: #666;">(<?= $town['count'] ?>)</span>
                                    </a>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p>Города не найдены.</p>
                    <?php endif; ?>
                    
                    <?php $stmt_towns->close(); ?>
                </div>
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
