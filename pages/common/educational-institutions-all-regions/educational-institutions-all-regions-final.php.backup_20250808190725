<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get the type from URL parameter
$type = $_GET['type'] ?? 'schools';

// Define table and field names based on type
switch ($type) {
    case 'spo':
        $table = 'spo';
        $linkPrefix = '/spo-in-region';
        $pageTitle = 'СПО по регионам';
        $metaD = 'Средние профессиональные образовательные учреждения (СПО) по регионам России';
        $metaK = 'СПО, колледжи, техникумы, регионы, среднее профессиональное образование';
        break;
    case 'vpo':
        $table = 'vpo';
        $linkPrefix = '/vpo-in-region';
        $pageTitle = 'ВПО по регионам';
        $metaD = 'Высшие учебные заведения (ВПО) по регионам России';
        $metaK = 'ВПО, университеты, институты, регионы, высшее образование';
        break;
    default: // schools
        $table = 'schools';
        $linkPrefix = '/schools-in-region';
        $pageTitle = 'Школы по регионам';
        $metaD = 'Школы по регионам России';
        $metaK = 'школы, регионы, среднее образование';
        break;
}

// Template configuration
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'bootstrap',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true
];

// Include template engine
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';

// Start template rendering
ob_start();
?>
<!DOCTYPE html>
<html lang="ru" data-theme="light" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - 11 классники</title>
    <meta name="description" content="<?= $metaD ?>">
    <meta name="keywords" content="<?= $metaK ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Unified Styles -->
    <link href="/css/unified-styles.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .main-content {
            flex: 1;
            padding-bottom: 20px;
        }
        
        .region-card {
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
        }
        
        .region-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }
        
        .region-card .stretched-link {
            color: var(--text-primary);
        }
        
        .region-card .badge {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
        }
    </style>
    
    <script>
        // Theme initialization
        (function() {
            const savedTheme = localStorage.getItem('preferred-theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
    
    <main class="main-content">
        <div class="container py-4">
            <h1 class="section-title text-center mb-4"><?= $pageTitle ?></h1>
            
            <div class="row g-3">
                <?php 
                $sql = "SELECT id_region, region_name, region_name_en FROM regions WHERE id_country = 1 ORDER BY region_name ASC";
                $result = $connection->query($sql);

                if (!$result) {
                    echo '<div class="col-12"><div class="alert alert-danger">Ошибка загрузки данных</div></div>';
                } else {
                    $displayed_count = 0;
                    while ($row = $result->fetch_assoc()): 
                        // Count institutions in this region
                        $count_sql = "SELECT COUNT(*) AS count FROM $table WHERE id_region = {$row['id_region']}";
                        $count_result = $connection->query($count_sql);
                        
                        if (!$count_result) {
                            continue;
                        }
                        
                        $count_row = $count_result->fetch_assoc();
                        $institution_count = $count_row['count'];
                        
                        if ($institution_count > 0):
                            $displayed_count++;
                ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 region-card">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <a href="<?= $linkPrefix ?>/<?= $row['region_name_en'] ?>" 
                                   class="text-decoration-none stretched-link">
                                    <h6 class="mb-0"><?= $row['region_name'] ?></h6>
                                </a>
                                <span class="badge bg-primary rounded-pill"><?= $institution_count ?></span>
                            </div>
                        </div>
                    </div>
                <?php 
                        endif;
                    endwhile;
                    
                    if ($displayed_count == 0): ?>
                        <div class="col-12">
                            <div class="text-center mt-5">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    В данный момент нет доступных учебных заведений.
                                </div>
                            </div>
                        </div>
                    <?php endif;
                }
                ?>
            </div>
        </div>
    </main>
    
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer.php'; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Make toggleTheme available globally if not already defined
        if (typeof window.toggleTheme === 'undefined') {
            window.toggleTheme = function() {
                const html = document.documentElement;
                const currentTheme = html.getAttribute('data-theme') || 'light';
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                html.setAttribute('data-theme', newTheme);
                html.setAttribute('data-bs-theme', newTheme);
                localStorage.setItem('preferred-theme', newTheme);
                
                // Update theme icons
                const themeIcon = document.getElementById('theme-icon');
                const themeIconUser = document.getElementById('theme-icon-user');
                const iconClass = newTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
                
                if (themeIcon) themeIcon.className = iconClass;
                if (themeIconUser) themeIconUser.className = iconClass;
            };
        }
    </script>
</body>
</html>
<?php
$connection->close();
?>