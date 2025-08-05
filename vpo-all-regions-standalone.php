<?php
// Standalone VPO all regions page - no includes that might interfere

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

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - 11-классники</title>
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        h1 {
            text-align: center;
            color: #28a745;
            margin-bottom: 30px;
        }
        
        .regions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .region-card {
            background: #f8f9fa;
            padding: 15px 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .region-card:hover {
            border-color: #28a745;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.2);
        }
        
        .region-card a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .region-card a:hover {
            color: #28a745;
        }
        
        .badge {
            background: #6c757d;
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: normal;
        }
        
        .debug {
            background: #e9ecef;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 14px;
        }
        
        .error {
            color: #dc3545;
            text-align: center;
            padding: 20px;
        }
        
        .back-link {
            text-align: center;
            margin-top: 30px;
        }
        
        .back-link a {
            color: #28a745;
            text-decoration: none;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?= htmlspecialchars($pageTitle) ?></h1>
        
        <div class="debug">
            <strong>Debug Info:</strong><br>
            Database: <?= $connection->query("SELECT DATABASE()")->fetch_row()[0] ?><br>
            <?php
            // Check regions table structure
            $check = $connection->query("SHOW COLUMNS FROM regions");
            if ($check) {
                $cols = [];
                while ($col = $check->fetch_assoc()) {
                    $cols[] = $col['Field'];
                }
                echo "Regions columns: " . implode(', ', $cols) . "<br>";
            }
            ?>
        </div>
        
        <div class="regions-grid">
            <?php 
            $sql = "SELECT id_region, region_name, region_name_en FROM regions WHERE id_country = 1 ORDER BY region_name ASC";
            $result = $connection->query($sql);
            
            if (!$result) {
                echo '<div class="error">Query error: ' . $connection->error . '<br>SQL: ' . htmlspecialchars($sql) . '</div>';
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
                <div class="region-card">
                    <a href="<?= $linkPrefix ?>/<?= $row['region_name_en'] ?>">
                        <span><?= htmlspecialchars($row['region_name']) ?></span>
                        <span class="badge"><?= $institution_count ?></span>
                    </a>
                </div>
            <?php 
                        }
                    }
                }
                
                if ($displayed_count == 0) {
                    echo '<div class="error">В данный момент нет доступных учебных заведений.</div>';
                }
            } else {
                echo '<div class="error">Регионы не найдены.</div>';
            }
            ?>
        </div>
        
        <div class="back-link">
            <a href="/">← На главную</a> | 
            <a href="/spo-all-regions">СПО по регионам</a>
        </div>
    </div>
</body>
</html>
<?php
$connection->close();
?>