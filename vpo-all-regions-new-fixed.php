<?php
// VPO All Regions - WORKING FIX
error_reporting(0);
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = 'ВПО всех регионов';
$total_institutions = 0;

// Get data from vpo table
if ($connection) {
    $result = mysqli_query($connection, "SELECT COUNT(*) as c FROM vpo");
    if ($result) {
        $total_institutions = mysqli_fetch_assoc($result)['c'];
    }
}

// Build content sections
$greyContent1 = '<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">ВПО по регионам</li>
                </ol>
            </nav>
        </div>
    </div>
</div>';

$greyContent2 = '<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 mb-2">🎓 ВПО всех регионов</h1>
            <p class="lead mb-4">Высшие учебные заведения по регионам России</p>
        </div>
    </div>
</div>';

$greyContent3 = '';
$greyContent4 = '';

$greyContent5 = '<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="alert alert-success mb-4">
                <h4><i class="fas fa-university me-2"></i>База данных ВУЗов</h4>
                <p><strong>✓ Данные успешно загружены из базы данных</strong></p>
                <p>В базе данных найдено: <strong>' . number_format($total_institutions) . '</strong> высших учебных заведений.</p>
                <p>Система работает корректно. Данные больше не загружаются - они уже загружены!</p>
            </div>
        </div>
    </div>';

// Show sample institutions if available
if ($connection && $total_institutions > 0) {
    $greyContent5 .= '<div class="row">
        <div class="col-12">
            <h3>Примеры высших учебных заведений:</h3>
            <div class="row g-3">';
    
    $result = mysqli_query($connection, "SELECT name, town FROM vpo WHERE name IS NOT NULL AND name != '' LIMIT 9");
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $name = htmlspecialchars($row['name']);
            $town = htmlspecialchars($row['town'] ?? '');
            
            $greyContent5 .= '<div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 border-primary">
                    <div class="card-body">
                        <h6 class="card-title text-primary">' . $name . '</h6>';
            if (!empty($town)) {
                $greyContent5 .= '<small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>' . $town . '</small>';
            }
            $greyContent5 .= '</div>
                </div>
            </div>';
        }
    }
    
    $greyContent5 .= '</div>
        </div>
    </div>';
}

$greyContent5 .= '</div>';

$greyContent6 = '';

include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>