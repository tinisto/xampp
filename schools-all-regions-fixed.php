<?php
// Fixed schools all regions page
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$table = 'schools';
$linkPrefix = '/schools-in-region';
$pageTitle = 'Школы по регионам';

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
        
        .nav-links {
            text-align: center;
            margin-top: 30px;
        }
        
        .nav-links a {
            color: #28a745;
            text-decoration: none;
            margin: 0 10px;
        }
        
        .nav-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?= htmlspecialchars($pageTitle) ?></h1>
        
        <div class="debug">
            <strong>Debug Info:</strong><br>
            <?php
            // Check total schools
            $total_schools = $connection->query("SELECT COUNT(*) as total FROM schools");
            if ($total_schools) {
                $total = $total_schools->fetch_assoc();
                echo "Total schools in database: " . $total['total'] . "<br>";
            }
            
            // Check schools table columns
            $cols_query = $connection->query("SHOW COLUMNS FROM schools");
            $cols = [];
            while ($col = $cols_query->fetch_assoc()) {
                $cols[] = $col['Field'];
            }
            echo "Schools table columns: " . implode(', ', $cols) . "<br>";
            
            // Check if schools have id_region
            $check_region = $connection->query("SELECT COUNT(*) as count FROM schools WHERE id_region IS NOT NULL AND id_region > 0");
            if ($check_region) {
                $region_count = $check_region->fetch_assoc();
                echo "Schools with id_region: " . $region_count['count'] . "<br>";
            }
            ?>
        </div>
        
        <div class="regions-grid">
            <?php 
            // Get regions with school counts - using correct column names
            $sql = "SELECT r.id, r.region_name, r.region_name_en, COUNT(s.id_school) as school_count 
                    FROM regions r 
                    LEFT JOIN schools s ON s.id_region = r.id 
                    WHERE r.country_id = 1 
                    GROUP BY r.id 
                    HAVING school_count > 0 
                    ORDER BY r.region_name ASC";
            
            $result = $connection->query($sql);
            
            if (!$result) {
                echo '<div class="error">Query error: ' . $connection->error . '<br>SQL: ' . htmlspecialchars($sql) . '</div>';
            } elseif ($result->num_rows > 0) {
                $displayed_count = 0;
                while ($row = $result->fetch_assoc()) { 
                    $displayed_count++;
            ?>
                <div class="region-card">
                    <a href="<?= $linkPrefix ?>/<?= $row['region_name_en'] ?>">
                        <span><?= htmlspecialchars($row['region_name']) ?></span>
                        <span class="badge"><?= $row['school_count'] ?></span>
                    </a>
                </div>
            <?php 
                }
                
                if ($displayed_count == 0) {
                    echo '<div class="error">Не найдено регионов со школами.</div>';
                }
            } else {
                echo '<div class="error">Регионы не найдены или нет школ в базе данных.</div>';
            }
            ?>
        </div>
        
        <div class="nav-links">
            <a href="/">← На главную</a> | 
            <a href="/vpo-all-regions">ВПО по регионам</a> | 
            <a href="/spo-all-regions">СПО по регионам</a>
        </div>
    </div>
</body>
</html>
<?php
$connection->close();
?>