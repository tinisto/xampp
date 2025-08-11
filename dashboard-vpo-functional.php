<?php
// Universities (VPO) management dashboard - Functional version

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check admin access
if ((!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') && 
    (!isset($_SESSION['occupation']) || $_SESSION['occupation'] !== 'admin')) {
    header('Location: /unauthorized');
    exit();
}

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

// Search
$search = $_GET['search'] ?? '';
$searchCondition = '';
$searchParams = [];

// Build WHERE clause for search
if (!empty($search)) {
    $searchCondition = "WHERE name LIKE ? OR city LIKE ? OR region LIKE ?";
    $searchParam = '%' . $search . '%';
    $searchParams = [$searchParam, $searchParam, $searchParam];
}

// Get total universities count with prepared statement
$countQuery = "SELECT COUNT(*) as total FROM universities $searchCondition";
if (!empty($searchParams)) {
    $stmt = $connection->prepare($countQuery);
    $stmt->bind_param("sss", ...$searchParams);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalUniversities = $result->fetch_assoc()['total'];
    $stmt->close();
} else {
    $result = $connection->query($countQuery);
    $totalUniversities = $result ? $result->fetch_assoc()['total'] : 0;
}
$totalPages = ceil($totalUniversities / $limit);

// Get universities with prepared statement
$query = "SELECT id, name, city, region, created_at 
          FROM universities 
          $searchCondition
          ORDER BY name ASC 
          LIMIT ? OFFSET ?";

if (!empty($searchParams)) {
    $stmt = $connection->prepare($query);
    $allParams = array_merge($searchParams, [$limit, $offset]);
    $types = str_repeat('s', count($searchParams)) . 'ii';
    $stmt->bind_param($types, ...$allParams);
} else {
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
$universities = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $universities[] = $row;
    }
}

// Get statistics
$stats = [];
$stats['total'] = $totalUniversities;

// Universities by region
$regionQuery = "SELECT region, COUNT(*) as count FROM universities GROUP BY region ORDER BY count DESC LIMIT 5";
$regionResult = $connection->query($regionQuery);
$topRegions = [];
if ($regionResult) {
    while ($row = $regionResult->fetch_assoc()) {
        if (!empty($row['region'])) {
            $topRegions[] = $row;
        }
    }
}

// Recent universities (last 30 days)
$recentQuery = "SELECT COUNT(*) as count FROM universities WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
$recentResult = $connection->query($recentQuery);
$stats['recent'] = $recentResult ? $recentResult->fetch_assoc()['count'] : 0;

// Get user info
$username = $_SESSION['first_name'] ?? $_SESSION['email'] ?? 'Admin';
$userInitial = strtoupper(mb_substr($username, 0, 1));

// Set dashboard title
$dashboardTitle = 'Управление ВУЗами';

// Build dashboard content
ob_start();
?>
<style>
.vpo-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.search-box {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.search-input {
    flex: 1;
    padding: 12px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 16px;
    background: var(--surface);
    color: var(--text-primary);
}

.btn-search {
    padding: 12px 24px;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-search:hover {
    background: #0056b3;
}

.btn-primary {
    padding: 12px 20px;
    background: #6f42c1;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}

.btn-primary:hover {
    background: #5a2d91;
    transform: translateY(-2px);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: var(--surface);
    padding: 20px;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    text-align: center;
}

.stat-value {
    font-size: 24px;
    font-weight: bold;
    color: #6f42c1;
    margin-bottom: 8px;
}

.stat-label {
    color: var(--text-secondary);
    font-size: 14px;
}

.vpo-table {
    background: var(--surface);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}

.vpo-table table {
    width: 100%;
    border-collapse: collapse;
}

.vpo-table th {
    background: var(--bg-light);
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: var(--text-secondary);
    border-bottom: 1px solid var(--border-color);
}

.vpo-table td {
    padding: 15px;
    border-bottom: 1px solid var(--border-color);
}

.vpo-table tr:last-child td {
    border-bottom: none;
}

.vpo-table tr:hover {
    background: var(--bg-light);
}

.university-name {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.university-location {
    font-size: 13px;
    color: var(--text-secondary);
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.btn-sm {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
    text-decoration: none;
    transition: all 0.3s;
}

.btn-edit {
    background: #17a2b8;
    color: white;
}

.btn-edit:hover {
    background: #138496;
}

.btn-view {
    background: #6f42c1;
    color: white;
}

.btn-view:hover {
    background: #5a2d91;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-secondary);
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    opacity: 0.5;
}

.top-regions {
    margin-top: 30px;
}

.region-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.region-item {
    background: var(--surface);
    padding: 15px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.region-name {
    font-weight: 500;
    color: var(--text-primary);
}

.region-count {
    color: var(--text-secondary);
    font-size: 14px;
}

@media (max-width: 768px) {
    .search-box {
        flex-direction: column;
    }
    
    .vpo-table {
        overflow-x: auto;
    }
    
    .vpo-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
}
</style>

<div class="vpo-header">
    <h2>Управление ВУЗами</h2>
    <a href="/create/vpo" class="btn-primary">
        <i class="fas fa-plus"></i> Добавить ВУЗ
    </a>
</div>

<!-- Search -->
<form method="GET" class="search-box">
    <input type="text" name="search" placeholder="Поиск по названию ВУЗа, городу или региону..." 
           value="<?= htmlspecialchars($search) ?>" class="search-input">
    <button type="submit" class="btn-search">
        <i class="fas fa-search"></i> Поиск
    </button>
    <?php if ($search): ?>
        <a href="/dashboard/vpo" class="btn-search" style="background: #6c757d;">
            <i class="fas fa-times"></i>
        </a>
    <?php endif; ?>
</form>

<!-- Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= number_format($stats['total']) ?></div>
        <div class="stat-label">Всего ВУЗов</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $stats['recent'] ?? 0 ?></div>
        <div class="stat-label">Новых за 30 дней</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= count($topRegions) ?></div>
        <div class="stat-label">Активных регионов</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $totalPages ?></div>
        <div class="stat-label">Страниц</div>
    </div>
</div>

<!-- Universities Table -->
<div class="vpo-table">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Название ВУЗа</th>
                <th>Город</th>
                <th>Регион</th>
                <th>Дата добавления</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($universities)): ?>
                <?php foreach ($universities as $university): ?>
                <tr>
                    <td><?= htmlspecialchars($university['id']) ?></td>
                    <td>
                        <div class="university-name"><?= htmlspecialchars($university['name']) ?></div>
                    </td>
                    <td><?= htmlspecialchars($university['city'] ?? 'Не указан') ?></td>
                    <td><?= htmlspecialchars($university['region'] ?? 'Не указан') ?></td>
                    <td><?= $university['created_at'] ? date('d.m.Y', strtotime($university['created_at'])) : 'Не указана' ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="/vpo/<?= $university['id'] ?>" class="btn-sm btn-view" target="_blank" title="Просмотр">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/edit/vpo/<?= $university['id'] ?>" class="btn-sm btn-edit" title="Редактировать">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="fas fa-graduation-cap"></i>
                            <div>ВУЗы не найдены</div>
                            <?php if ($search): ?>
                                <p>Попробуйте изменить условия поиска</p>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div>
    <?php 
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/pagination-modern.php';
    renderPaginationModern($page, $totalPages, '/dashboard/vpo');
    ?>
</div>
<?php endif; ?>

<!-- Top Regions Section -->
<?php if (!empty($topRegions)): ?>
<div class="top-regions">
    <h3>Популярные регионы</h3>
    <div class="region-list">
        <?php foreach ($topRegions as $region): ?>
            <div class="region-item">
                <span class="region-name"><?= htmlspecialchars($region['region']) ?></span>
                <span class="region-count"><?= $region['count'] ?> ВУЗов</span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php
$dashboardContent = ob_get_clean();

// Include the dashboard template
include $_SERVER['DOCUMENT_ROOT'] . '/dashboard-template.php';
?>