<?php
// Bypass template engine issue - direct rendering
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Get the type from URL parameter
$type = $_GET['type'] ?? 'schools';

// Define table and field names based on type
switch ($type) {
    case 'spo':
        $table = 'colleges';  // Use old table name since new tables are empty
        $countField = 'spo_count';
        $linkPrefix = '/spo-in-region';
        $pageTitle = 'СПО по регионам';
        $metaD = 'Средние профессиональные образовательные учреждения (СПО) по регионам России';
        $metaK = 'СПО, колледжи, техникумы, регионы, среднее профессиональное образование';
        $regionColumn = 'region_id';
        break;
    case 'vpo':
        $table = 'universities';  // Use old table name since new tables are empty
        $countField = 'vpo_count';
        $linkPrefix = '/vpo-in-region';
        $pageTitle = 'ВПО по регионам';
        $metaD = 'Высшие учебные заведения (ВПО) по регионам России';
        $metaK = 'ВПО, университеты, институты, регионы, высшее образование';
        $regionColumn = 'region_id';
        break;
    default: // schools
        $table = 'schools';
        $countField = 'school_count';
        $linkPrefix = '/schools-in-region';
        $pageTitle = 'Школы по регионам';
        $metaD = 'Школы по регионам России';
        $metaK = 'школы, регионы, среднее образование';
        $regionColumn = 'region_id';
        break;
}

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
?>
<!DOCTYPE html>
<html lang="ru" data-bs-theme="light" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - 11-классники</title>
    
    <meta name="description" content="<?php echo htmlspecialchars($metaD); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($metaK); ?>">
    
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
        
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -15px;
        }
        
        .col-md-4 {
            width: 33.333%;
            padding: 0 15px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .col-md-4 {
                width: 100%;
            }
        }
        
        .region {
            background: var(--surface);
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .region h6 {
            margin: 0;
            font-size: 16px;
        }
        
        .region a {
            color: var(--text-primary);
            text-decoration: none;
        }
        
        .region a:hover {
            color: var(--primary-color);
        }
        
        .badge {
            background: #6c757d;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: 8px;
        }
        
        h5 {
            text-align: center;
            font-weight: bold;
            margin-bottom: 30px;
            color: var(--text-primary);
        }
        
        main {
            flex: 1;
            padding: 40px 0;
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
            <h5><?= htmlspecialchars($pageTitle) ?></h5>
            
            <div class="row">
                <?php 
                $sql = "SELECT id_region, region_name, region_name_en FROM regions WHERE id_country = 1 ORDER BY region_name ASC";
                $result = $connection->query($sql);
                
                if ($result && $result->num_rows > 0):
                    $displayed_count = 0;
                    while ($row = $result->fetch_assoc()): 
                        // Count institutions in this region
                        $count_sql = "SELECT COUNT(*) AS count FROM $table WHERE $regionColumn = {$row['id_region']}";
                        $count_result = $connection->query($count_sql);
                        
                        if ($count_result) {
                            $count_row = $count_result->fetch_assoc();
                            $institution_count = $count_row['count'];
                            
                            if ($institution_count > 0):
                                $displayed_count++;
                ?>
                    <div class="col-md-4">
                        <div class="region" data-region-id="<?= $row['id_region'] ?>">
                            <h6>
                                <a href="<?= $linkPrefix ?>/<?= $row['region_name_en'] ?>">
                                    <?= htmlspecialchars($row['region_name']) ?>
                                </a>
                                <span class="badge"><?= $institution_count ?></span>
                            </h6>
                        </div>
                    </div>
                <?php 
                            endif;
                        }
                    endwhile;
                    
                    if ($displayed_count == 0):
                ?>
                    <div class="col-12">
                        <p style="text-align: center; margin-top: 40px;">
                            В данный момент нет доступных учебных заведений.
                        </p>
                    </div>
                <?php endif; ?>
                <?php else: ?>
                    <div class="col-12">
                        <p style="text-align: center; margin-top: 40px; color: red;">
                            Ошибка загрузки данных.
                        </p>
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
