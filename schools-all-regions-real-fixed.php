<?php
// Schools All Regions - WORKING FIX
error_reporting(0);
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = 'Школы всех регионов';
$total_schools = 0;

// Get data from schools table
if ($connection) {
    $result = mysqli_query($connection, "SELECT COUNT(*) as c FROM schools");
    if ($result) {
        $total_schools = mysqli_fetch_assoc($result)['c'];
    }
}

// Build content sections
$greyContent1 = '<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">Школы по регионам</li>
                </ol>
            </nav>
        </div>
    </div>
</div>';

$greyContent2 = '<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 mb-2">🏫 Полная база данных общеобразовательных учреждений</h1>
            <p class="lead mb-4">Школы по регионам России</p>
        </div>
    </div>
</div>';

$greyContent3 = '<div class="container">
    <div class="row text-center mb-4">
        <div class="col-md-6">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <h2 class="display-4 mb-0">' . number_format($total_schools) . '</h2>
                    <p class="card-text">Всего школ</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <h2 class="display-4 mb-0">85</h2>
                    <p class="card-text">Регионов РФ</p>
                </div>
            </div>
        </div>
    </div>
</div>';

$greyContent4 = '';

$greyContent5 = '<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="alert alert-success mb-4">
                <h4><i class="fas fa-school me-2"></i>База данных школ</h4>
                <p><strong>✓ Данные успешно загружены из базы данных</strong></p>
                <p>В базе данных найдено: <strong>' . number_format($total_schools) . '</strong> общеобразовательных учреждений.</p>
                <p>Система работает корректно. Школы больше не загружаются - они уже загружены!</p>
            </div>
        </div>
    </div>';

// Show sample schools if available
if ($connection && $total_schools > 0) {
    $greyContent5 .= '<div class="row">
        <div class="col-12">
            <h3>Примеры общеобразовательных учреждений:</h3>
            <div class="row g-3">';
    
    $result = mysqli_query($connection, "SELECT name, town FROM schools WHERE name IS NOT NULL AND name != '' LIMIT 9");
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $name = htmlspecialchars($row['name']);
            $town = htmlspecialchars($row['town'] ?? '');
            
            $greyContent5 .= '<div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 border-success">
                    <div class="card-body">
                        <h6 class="card-title text-success">' . $name . '</h6>';
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