<?php
// VPO (Universities) management dashboard - migrated to use real_template.php

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

// Get filter and pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

// Search
$search = $_GET['search'] ?? '';
$searchParams = [];
$whereClause = '';

// Build WHERE clause for search
if (!empty($search)) {
    $whereClause = "WHERE (name_university LIKE ? OR abbr_university LIKE ?)";
    $searchParam = '%' . $search . '%';
    $searchParams = [$searchParam, $searchParam];
}

// Get total count with prepared statement
$countQuery = "SELECT COUNT(*) as total FROM universities $whereClause";
if (!empty($searchParams)) {
    $stmt = $connection->prepare($countQuery);
    $stmt->bind_param("ss", ...$searchParams);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalVPO = $result->fetch_assoc()['total'];
    $stmt->close();
} else {
    $result = $connection->query($countQuery);
    $totalVPO = $result ? $result->fetch_assoc()['total'] : 0;
}
$totalPages = ceil($totalVPO / $limit);

// Get universities with prepared statement
$query = "SELECT u.*, t.name_town, r.name_region 
          FROM universities u
          LEFT JOIN towns t ON u.id_town = t.id_town
          LEFT JOIN regions r ON u.id_region = r.id_region
          $whereClause
          ORDER BY u.id_university DESC 
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
while ($row = $result->fetch_assoc()) {
    $universities[] = $row;
}
$stmt->close();

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Управление ВУЗами', [
    'fontSize' => '28px',
    'margin' => '30px 0',
    'subtitle' => 'Всего ВУЗов: ' . $totalVPO
]);
$greyContent1 = ob_get_clean();

// Section 2: Search and actions
ob_start();
?>
<div style="padding: 20px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display: flex; gap: 20px; align-items: center; justify-content: space-between; flex-wrap: wrap;">
            <form method="GET" style="flex: 1; max-width: 400px;">
                <div style="display: flex; gap: 10px;">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Поиск ВУЗов..." 
                           style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                    <button type="submit" style="padding: 10px 20px; background: #007bff; color: white; 
                                                 border: none; border-radius: 8px; cursor: pointer;">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            
            <div style="display: flex; gap: 10px;">
                <a href="/admin/institutions/universities.php?action=new" class="action-btn primary">
                    <i class="fas fa-plus"></i> Добавить ВУЗ
                </a>
                <a href="/admin/dashboard.php" class="action-btn secondary">
                    <i class="fas fa-arrow-left"></i> К панели
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s;
}

.action-btn.primary {
    background: #28a745;
    color: white;
}

.action-btn.primary:hover {
    background: #218838;
}

.action-btn.secondary {
    background: #6c757d;
    color: white;
}

.action-btn.secondary:hover {
    background: #5a6268;
}
</style>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Empty
$greyContent3 = '';

// Section 4: Empty
$greyContent4 = '';

// Section 5: Universities table
ob_start();
?>
<div style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    <div style="background: var(--surface, #ffffff); border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 15px; text-align: left; color: #666;">ID</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Название</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Аббревиатура</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Город</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Регион</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Рейтинг</th>
                    <th style="padding: 15px; text-align: center; color: #666;">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($universities as $uni): ?>
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 15px;"><?= $uni['id_university'] ?></td>
                    <td style="padding: 15px;">
                        <a href="/vpo/<?= htmlspecialchars($uni['url_university']) ?>" target="_blank"
                           style="color: #007bff; text-decoration: none;">
                            <?= htmlspecialchars($uni['name_university']) ?>
                        </a>
                    </td>
                    <td style="padding: 15px; color: #666;">
                        <?= htmlspecialchars($uni['abbr_university'] ?? '') ?>
                    </td>
                    <td style="padding: 15px; color: #666;">
                        <?= htmlspecialchars($uni['name_town'] ?? 'Не указан') ?>
                    </td>
                    <td style="padding: 15px; color: #666;">
                        <?= htmlspecialchars($uni['name_region'] ?? 'Не указан') ?>
                    </td>
                    <td style="padding: 15px; color: #666;">
                        <?= $uni['rating'] ?? '-' ?>
                    </td>
                    <td style="padding: 15px;">
                        <div style="display: flex; gap: 10px; justify-content: center;">
                            <a href="/admin/institutions/universities.php?action=edit&id=<?= $uni['id_university'] ?>" 
                               style="color: #007bff; text-decoration: none;" title="Редактировать">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" onclick="deleteVPO(<?= $uni['id_university'] ?>); return false;" 
                               style="color: #dc3545; text-decoration: none;" title="Удалить">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if (empty($universities)): ?>
        <div style="padding: 60px; text-align: center;">
            <i class="fas fa-university" style="font-size: 48px; color: #ddd; margin-bottom: 20px;"></i>
            <p style="color: #666;">ВУЗы не найдены</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function deleteVPO(uniId) {
    if (confirm('Вы уверены, что хотите удалить этот ВУЗ?')) {
        // TODO: Implement delete functionality
        window.location.href = '/admin/institutions/universities.php?action=delete&id=' + uniId;
    }
}
</script>

<style>
[data-theme="dark"] table {
    background: var(--surface-dark, #2d3748);
}

[data-theme="dark"] tr[style*="background: #f8f9fa"] {
    background: var(--surface-darker, #1a202c) !important;
}

[data-theme="dark"] td,
[data-theme="dark"] th {
    color: var(--text-primary, #e4e6eb);
    border-color: #4a5568 !important;
}
</style>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Pagination
ob_start();
if ($totalPages > 1) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/pagination-modern.php';
    renderPaginationModern($page, $totalPages, '/dashboard/vpo?search=' . urlencode($search) . '&page=');
}
$greyContent6 = ob_get_clean();

// Blue section: Empty
$blueContent = '';

// Page title
$pageTitle = 'Управление ВУЗами - 11-классники';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>