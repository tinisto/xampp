<?php
// Standalone VPO test
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Direct database connection
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

// VPO settings
$table = 'universities';
$linkPrefix = '/vpo-in-region';
$pageTitle = 'ВПО по регионам';
$regionColumn = 'region_id';
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
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .regions {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .region {
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #fafafa;
        }
        .region a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
        }
        .region a:hover {
            color: #28a745;
        }
        .badge {
            background: #6c757d;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
            margin-left: 8px;
            float: right;
        }
        .back-link {
            margin-top: 20px;
            text-align: center;
        }
        .back-link a {
            color: #007bff;
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
        
        <div class="regions">
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
                <div class="region">
                    <a href="<?= $linkPrefix ?>/<?= $row['region_name_en'] ?>">
                        <?= htmlspecialchars($row['region_name']) ?>
                        <span class="badge"><?= $institution_count ?></span>
                    </a>
                </div>
            <?php 
                        endif;
                    }
                endwhile;
                
                if ($displayed_count == 0):
            ?>
                <p style="text-align: center; grid-column: 1 / -1;">В данный момент нет доступных учебных заведений.</p>
            <?php endif; ?>
            <?php else: ?>
                <p style="color: red; text-align: center; grid-column: 1 / -1;">
                    Ошибка загрузки данных: <?= $connection->error ?>
                </p>
            <?php endif; ?>
        </div>
        
        <div class="back-link">
            <a href="/">← На главную</a> | 
            <a href="/vpo-all-regions">Обычная страница ВПО</a>
        </div>
    </div>
</body>
</html>
<?php
$connection->close();
?>