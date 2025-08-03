<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Determine the type of institution based on the URL
$type = isset($_GET['type']) ? $_GET['type'] : 'schools';
$pageTitle = '';
$table = '';
$linkPrefix = '';

switch ($type) {
    case 'schools':
        $pageTitle = 'Школы в регионах России';
        $table = 'schools';
        $linkPrefix = 'schools-in-region';
        break;
    case 'spo':
        $pageTitle = 'Среднее профессиональное образование в регионах России';
        $table = 'spo';
        $linkPrefix = 'spo-in-region';
        break;
    case 'vpo':
        $pageTitle = 'Высшее образование в регионах России';
        $table = 'vpo';
        $linkPrefix = 'vpo-in-region';
        break;
    default:
        header("Location: /error");
        exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - 11-классники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .main-content {
            flex: 1;
        }
        .page-title {
            text-align: center;
            margin: 30px 0;
            color: #333;
        }
        .page-title h1 {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .search-box input {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            display: block;
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
        }
        .region-item {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .region-item:hover {
            border-color: #28a745;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .region-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-decoration: none;
            color: #333;
        }
        .region-count {
            background: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .stats-box {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 40px;
        }
        .stats-box .number {
            font-size: 36px;
            font-weight: 700;
            color: #28a745;
        }
    </style>
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
    
    <main class="main-content">
        <div class="container">
            <div class="page-title">
                <h1><?= htmlspecialchars($pageTitle) ?></h1>
                <p>Выберите регион для просмотра образовательных учреждений</p>
            </div>
            
            <div class="mb-4">
                <input type="text" id="regionSearch" placeholder="Поиск региона..." class="form-control">
            </div>
            
            <?php
            $sql = "SELECT id_region, region_name, region_name_en FROM regions WHERE id_country = 1 ORDER BY region_name ASC";
            $result = $connection->query($sql);
            
            if (!$result) {
                echo '<div class="alert alert-danger">Ошибка загрузки данных</div>';
            } else {
                $totalRegions = 0;
                $totalInstitutions = 0;
                $regionsData = [];
                
                while ($row = $result->fetch_assoc()) {
                    $count_sql = "SELECT COUNT(*) AS count FROM $table WHERE id_region = {$row['id_region']}";
                    $count_result = $connection->query($count_sql);
                    
                    if ($count_result) {
                        $institution_count = $count_result->fetch_assoc()['count'];
                        if ($institution_count > 0) {
                            $totalRegions++;
                            $totalInstitutions += $institution_count;
                            $regionsData[] = [
                                'name' => $row['region_name'],
                                'name_en' => $row['region_name_en'],
                                'count' => $institution_count
                            ];
                        }
                    }
                }
                ?>
                
                <div class="stats-box">
                    <h3>Статистика</h3>
                    <div class="row">
                        <div class="col-6">
                            <div class="number"><?= number_format($totalRegions) ?></div>
                            <div>Регионов</div>
                        </div>
                        <div class="col-6">
                            <div class="number"><?= number_format($totalInstitutions) ?></div>
                            <div><?= $type === 'schools' ? 'Школ' : ($type === 'vpo' ? 'ВУЗов' : 'ССУЗов') ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <?php foreach ($regionsData as $region): ?>
                        <div class="col-md-6 col-lg-4 mb-3" data-region="<?= mb_strtolower($region['name']) ?>">
                            <div class="region-item">
                                <a href="/<?= $linkPrefix ?>/<?= $region['name_en'] ?>" class="region-link">
                                    <span><?= htmlspecialchars($region['name']) ?></span>
                                    <span class="region-count"><?= number_format($region['count']) ?></span>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php
            }
            ?>
        </div>
    </main>
    
    <?php include \$_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('regionSearch').addEventListener('input', function(e) {
        const search = e.target.value.toLowerCase();
        document.querySelectorAll('[data-region]').forEach(item => {
            const region = item.getAttribute('data-region');
            item.style.display = region.includes(search) ? '' : 'none';
        });
    });
    </script>
</body>
</html>