<?php
// Schools management dashboard - Functional version

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
if (!empty($search)) {
    $searchLike = '%' . $connection->real_escape_string($search) . '%';
    $searchCondition = "WHERE school_name LIKE '$searchLike' OR city LIKE '$searchLike' OR region LIKE '$searchLike'";
}

// Get total schools count
$countQuery = "SELECT COUNT(*) as total FROM schools $searchCondition";
$countResult = $connection->query($countQuery);
$totalSchools = $countResult ? $countResult->fetch_assoc()['total'] : 0;
$totalPages = ceil($totalSchools / $limit);

// Get schools
$query = "SELECT id, school_name, city, region, created_at 
          FROM schools 
          $searchCondition
          ORDER BY school_name ASC 
          LIMIT $limit OFFSET $offset";
$result = $connection->query($query);
$schools = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $schools[] = $row;
    }
}

// Get statistics
$stats = [];
$stats['total'] = $totalSchools;

// Schools by region
$regionQuery = "SELECT region, COUNT(*) as count FROM schools GROUP BY region ORDER BY count DESC LIMIT 5";
$regionResult = $connection->query($regionQuery);
$topRegions = [];
if ($regionResult) {
    while ($row = $regionResult->fetch_assoc()) {
        if (!empty($row['region'])) {
            $topRegions[] = $row;
        }
    }
}

// Recent schools (last 30 days)
$recentQuery = "SELECT COUNT(*) as count FROM schools WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
$recentResult = $connection->query($recentQuery);
$stats['recent'] = $recentResult ? $recentResult->fetch_assoc()['count'] : 0;

// Get user info
$username = $_SESSION['first_name'] ?? $_SESSION['email'] ?? 'Admin';
$userInitial = strtoupper(mb_substr($username, 0, 1));

// Set dashboard title
$dashboardTitle = 'Управление школами';

// Build dashboard content
ob_start();
?>
<style>
.schools-header {
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
    background: #28a745;
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
    background: #218838;
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
    color: var(--primary-color);
    margin-bottom: 8px;
}

.stat-label {
    color: var(--text-secondary);
    font-size: 14px;
}

.schools-table {
    background: var(--surface);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}

.schools-table table {
    width: 100%;
    border-collapse: collapse;
}

.schools-table th {
    background: var(--bg-light);
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: var(--text-secondary);
    border-bottom: 1px solid var(--border-color);
}

.schools-table td {
    padding: 15px;
    border-bottom: 1px solid var(--border-color);
}

.schools-table tr:last-child td {
    border-bottom: none;
}

.schools-table tr:hover {
    background: var(--bg-light);
}

.school-name {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.school-location {
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
    background: #28a745;
    color: white;
}

.btn-view:hover {
    background: #218838;
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
    
    .schools-table {
        overflow-x: auto;
    }
    
    .schools-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
}
</style>

<div class="schools-header">
    <h2>Управление школами</h2>
    <a href="/create/school" class="btn-primary">
        <i class="fas fa-plus"></i> Добавить школу
    </a>
</div>

<!-- Search -->
<form method="GET" class="search-box">
    <input type="text" name="search" placeholder="Поиск по названию школы, городу или региону..." 
           value="<?= htmlspecialchars($search) ?>" class="search-input">
    <button type="submit" class="btn-search">
        <i class="fas fa-search"></i> Поиск
    </button>
    <?php if ($search): ?>
        <a href="/dashboard/schools" class="btn-search" style="background: #6c757d;">
            <i class="fas fa-times"></i>
        </a>
    <?php endif; ?>
</form>

<!-- Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= number_format($stats['total']) ?></div>
        <div class="stat-label">Всего школ</div>
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

<!-- Schools Table -->
<div class="schools-table">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Название школы</th>
                <th>Город</th>
                <th>Регион</th>
                <th>Дата добавления</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($schools)): ?>
                <?php foreach ($schools as $school): ?>
                <tr>
                    <td><?= htmlspecialchars($school['id']) ?></td>
                    <td>
                        <div class="school-name"><?= htmlspecialchars($school['school_name']) ?></div>
                    </td>
                    <td><?= htmlspecialchars($school['city'] ?? 'Не указан') ?></td>
                    <td><?= htmlspecialchars($school['region'] ?? 'Не указан') ?></td>
                    <td><?= $school['created_at'] ? date('d.m.Y', strtotime($school['created_at'])) : 'Не указана' ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="/school/<?= $school['id'] ?>" class="btn-sm btn-view" target="_blank" title="Просмотр">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/edit/school/<?= $school['id'] ?>" class="btn-sm btn-edit" title="Редактировать">
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
                            <i class="fas fa-school"></i>
                            <div>Школы не найдены</div>
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
    renderPaginationModern($page, $totalPages, '/dashboard/schools');
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
                <span class="region-count"><?= $region['count'] ?> школ</span>
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