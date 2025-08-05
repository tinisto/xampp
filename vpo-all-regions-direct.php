<?php
// Direct VPO all regions page with explicit database connection

// Force use of the new database
$connection = new mysqli(
    '11klassnikiru67871.ipagemysql.com',
    'admin_claude',
    'W4eZ!#9uwLmrMay',
    '11klassniki_claude'
);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$connection->set_charset("utf8mb4");

// Force selection of the correct database
$connection->select_db('11klassniki_claude');

// Get the type from URL parameter
$type = $_GET['type'] ?? 'vpo';

// Define table and field names based on type
$table = 'universities';
$linkPrefix = '/vpo-in-region';
$pageTitle = 'ВПО по регионам';
$metaD = 'Высшие учебные заведения (ВПО) по регионам России';
$metaK = 'ВПО, университеты, институты, регионы, высшее образование';

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
        
        .debug {
            background: #f8f9fa;
            padding: 10px;
            margin: 20px 0;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-family: monospace;
            font-size: 12px;
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
            
            <div class="debug">
                Database: <?= $connection->query("SELECT DATABASE()")->fetch_row()[0] ?><br>
                Query test: 
                <?php
                $test = $connection->query("SELECT COUNT(*) FROM regions");
                echo $test ? "✅ Success (" . $test->fetch_row()[0] . " regions)" : "❌ Failed: " . $connection->error;
                ?>
            </div>
            
            <div class="row">
                <?php 
                $sql = "SELECT id_region, region_name, region_name_en FROM regions WHERE id_country = 1 ORDER BY region_name ASC";
                $result = $connection->query($sql);
                
                if (!$result) {
                    echo '<div class="col-12"><p style="color: red;">Query error: ' . $connection->error . '</p></div>';
                } elseif ($result->num_rows > 0) {
                    $displayed_count = 0;
                    while ($row = $result->fetch_assoc()) { 
                        // Count institutions in this region
                        $count_sql = "SELECT COUNT(*) AS count FROM $table WHERE region_id = {$row['id_region']}";
                        $count_result = $connection->query($count_sql);
                        
                        if ($count_result) {
                            $count_row = $count_result->fetch_assoc();
                            $institution_count = $count_row['count'];
                            
                            if ($institution_count > 0) {
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
                            }
                        }
                    }
                    
                    if ($displayed_count == 0) {
                        echo '<div class="col-12"><p style="text-align: center; margin-top: 40px;">В данный момент нет доступных учебных заведений.</p></div>';
                    }
                } else {
                    echo '<div class="col-12"><p style="text-align: center; margin-top: 40px;">Регионы не найдены.</p></div>';
                }
                ?>
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