<?php
// This is the unified content template for educational institutions in region pages

// Get institutions data (passed from main file)
$institutions = [];
while ($row = mysqli_fetch_assoc($institutions_result)) {
    $institutions[] = $row;
}
mysqli_data_seek($institutions_result, 0); // Reset pointer

// Determine ID field based on type
$id_field = ($type === 'schools') ? 'id_school' : "id_$type";
?>

<style>
.page-header {
    background: #f8f9fa;
    padding: 20px 0;
    margin-bottom: 30px;
    border-bottom: 1px solid #dee2e6;
}
.breadcrumb {
    margin-bottom: 10px;
    background: none;
    padding: 0;
}
.breadcrumb a {
    color: #28a745;
    text-decoration: none;
}
.breadcrumb a:hover {
    text-decoration: underline;
}
.page-title {
    font-size: 28px;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}
.page-stats {
    font-size: 16px;
    color: #666;
}
.content-area {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.institution-item {
    border-bottom: 1px solid #eee;
    padding: 15px 0;
}
.institution-item:last-child {
    border-bottom: none;
}
.institution-name {
    font-size: 16px;
    font-weight: 500;
    margin-bottom: 5px;
}
.institution-name a {
    color: #333;
    text-decoration: none;
}
.institution-name a:hover {
    color: #28a745;
}
.institution-info {
    font-size: 14px;
    color: #666;
}
.sidebar {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
}
.sidebar h4 {
    font-size: 18px;
    margin-bottom: 15px;
    color: #333;
}
.town-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.town-list li {
    padding: 8px 0;
    border-bottom: 1px solid #e0e0e0;
}
.town-list li:last-child {
    border-bottom: none;
}
.town-list a {
    color: #666;
    text-decoration: none;
    font-size: 14px;
}
.town-list a:hover {
    color: #28a745;
}
</style>

<div class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="/">Главная</a> / 
            <a href="/<?= $type ?>-all-regions"><?= $currentType['name'] ?> России</a> / 
            <span><?= htmlspecialchars($myrow_region['region_name']) ?></span>
        </nav>
        <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>
        <div class="page-stats">Найдено учреждений: <?= number_format($totalInstitutions) ?></div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-lg-8">
            <div class="content-area">
                <?php if (!empty($institutions)): ?>
                    <?php foreach ($institutions as $institution): ?>
                        <?php
                        $name_field = $currentType['field'];
                        $id_value = $institution[$id_field] ?? null;
                        ?>
                        <div class="institution-item">
                            <div class="institution-name">
                                <?php if ($id_value): ?>
                                    <a href="/<?= $currentType['url'] ?>/<?= $id_value ?>">
                                        <?= htmlspecialchars($institution[$name_field]) ?>
                                    </a>
                                <?php else: ?>
                                    <?= htmlspecialchars($institution[$name_field]) ?>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($institution['address'])): ?>
                                <div class="institution-info">
                                    <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($institution['address']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php
                    // Pagination
                    if ($totalInstitutions > $institutionsPerPage) {
                        $totalPages = ceil($totalInstitutions / $institutionsPerPage);
                        include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/pagination-modern.php';
                        renderPaginationModern($currentPage, $totalPages);
                    }
                    ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-info-circle fa-3x mb-3" style="color: #ddd;"></i>
                        <p>В данном регионе учреждения не найдены</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="sidebar">
                <h4>Города региона</h4>
                <?php
                $query_towns = "
                    SELECT DISTINCT t.town_name, t.town_name_en, t.id_town
                    FROM towns t
                    JOIN $type s ON t.id_town = s.id_town
                    WHERE t.id_region = ?
                    ORDER BY t.town_name
                ";
                $stmt_towns = $connection->prepare($query_towns);
                $stmt_towns->bind_param("i", $region_id);
                $stmt_towns->execute();
                $result_towns = $stmt_towns->get_result();
                ?>
                
                <?php if ($result_towns->num_rows > 0): ?>
                    <ul class="town-list">
                        <?php while ($town = $result_towns->fetch_assoc()): ?>
                            <li>
                                <a href="/<?= $type ?>/<?= htmlspecialchars($myrow_region['region_name_en']) ?>/<?= htmlspecialchars($town['town_name_en']) ?>">
                                    <?= htmlspecialchars($town['town_name']) ?>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">Нет городов с учреждениями</p>
                <?php endif; ?>
                
                <?php $stmt_towns->close(); ?>
            </div>
        </div>
    </div>
</div>

