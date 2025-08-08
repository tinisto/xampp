<?php
// Users management dashboard - migrated to use real_template.php

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check admin access
if (!isset($_SESSION['occupation']) || $_SESSION['occupation'] !== 'admin') {
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
    $searchCondition = "WHERE email LIKE '$searchLike' OR first_name LIKE '$searchLike' OR last_name LIKE '$searchLike'";
}

// Get total users count
$countQuery = "SELECT COUNT(*) as total FROM users $searchCondition";
$countResult = $connection->query($countQuery);
$totalUsers = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalUsers / $limit);

// Get users
$query = "SELECT id, email, first_name, last_name, city, occupation, created_at 
          FROM users 
          $searchCondition
          ORDER BY created_at DESC 
          LIMIT $limit OFFSET $offset";
$result = $connection->query($query);
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

// Get statistics
$stats = [];
$query = "SELECT occupation, COUNT(*) as count FROM users GROUP BY occupation";
$result = $connection->query($query);
while ($row = $result->fetch_assoc()) {
    $stats[$row['occupation']] = $row['count'];
}

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Управление пользователями', [
    'fontSize' => '28px',
    'margin' => '30px 0',
    'subtitle' => 'Всего пользователей: ' . $totalUsers
]);
$greyContent1 = ob_get_clean();

// Section 2: Search and actions
ob_start();
?>
<div style="padding: 20px;">
    <div style="display: flex; gap: 20px; align-items: center; justify-content: space-between; flex-wrap: wrap; max-width: 1200px; margin: 0 auto;">
        <form method="GET" style="flex: 1; max-width: 400px;">
            <div style="display: flex; gap: 10px;">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                       placeholder="Поиск пользователей..." 
                       style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                <button type="submit" style="padding: 10px 20px; background: #007bff; color: white; 
                                             border: none; border-radius: 8px; cursor: pointer;">
                    <i class="fas fa-search"></i> Найти
                </button>
            </div>
        </form>
        
        <a href="/dashboard" class="back-btn">
            <i class="fas fa-arrow-left"></i> Назад к панели
        </a>
    </div>
</div>

<style>
.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #6c757d;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s;
}

.back-btn:hover {
    background: #5a6268;
    transform: translateX(-5px);
}
</style>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Statistics
ob_start();
?>
<div style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="stat-card">
            <i class="fas fa-users" style="color: #28a745;"></i>
            <h4><?= $totalUsers ?></h4>
            <p>Всего пользователей</p>
        </div>
        
        <div class="stat-card">
            <i class="fas fa-user-shield" style="color: #dc3545;"></i>
            <h4><?= $stats['admin'] ?? 0 ?></h4>
            <p>Администраторов</p>
        </div>
        
        <div class="stat-card">
            <i class="fas fa-user" style="color: #007bff;"></i>
            <h4><?= $stats['user'] ?? 0 ?></h4>
            <p>Обычных пользователей</p>
        </div>
        
        <div class="stat-card">
            <i class="fas fa-user-clock" style="color: #ffc107;"></i>
            <h4><?= count(array_filter($users, fn($u) => strtotime($u['created_at']) > strtotime('-7 days'))) ?></h4>
            <p>Новых за неделю</p>
        </div>
    </div>
</div>

<style>
.stat-card {
    background: var(--surface, #ffffff);
    padding: 25px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.stat-card i {
    font-size: 32px;
    margin-bottom: 10px;
}

.stat-card h4 {
    color: var(--text-primary, #333);
    font-size: 28px;
    margin: 10px 0;
}

.stat-card p {
    color: var(--text-secondary, #666);
    margin: 0;
}

[data-theme="dark"] .stat-card {
    background: var(--surface-dark, #2d3748);
}
</style>
<?php
$greyContent3 = ob_get_clean();

// Section 4: Empty
$greyContent4 = '';

// Section 5: Users table
ob_start();
?>
<div style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    <div style="background: var(--surface, #ffffff); border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 15px; text-align: left; color: #666;">ID</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Email</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Имя</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Город</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Роль</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Дата регистрации</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 15px;"><?= $user['id'] ?></td>
                    <td style="padding: 15px;">
                        <a href="mailto:<?= htmlspecialchars($user['email']) ?>" style="color: #007bff; text-decoration: none;">
                            <?= htmlspecialchars($user['email']) ?>
                        </a>
                    </td>
                    <td style="padding: 15px;">
                        <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                    </td>
                    <td style="padding: 15px; color: #666;">
                        <?= htmlspecialchars($user['city'] ?: '-') ?>
                    </td>
                    <td style="padding: 15px;">
                        <?php if ($user['occupation'] === 'admin'): ?>
                            <span style="color: #dc3545; font-weight: 500;">Админ</span>
                        <?php else: ?>
                            <span style="color: #28a745;">Пользователь</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 15px; color: #666;">
                        <?= date('d.m.Y', strtotime($user['created_at'])) ?>
                    </td>
                    <td style="padding: 15px;">
                        <div style="display: flex; gap: 10px;">
                            <a href="#" onclick="editUser(<?= $user['id'] ?>); return false;" 
                               style="color: #007bff; text-decoration: none;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <a href="#" onclick="deleteUser(<?= $user['id'] ?>); return false;" 
                               style="color: #dc3545; text-decoration: none;">
                                <i class="fas fa-trash"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if (empty($users)): ?>
        <div style="padding: 60px; text-align: center;">
            <i class="fas fa-users" style="font-size: 48px; color: #ddd; margin-bottom: 20px;"></i>
            <p style="color: #666;">Пользователи не найдены</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function editUser(userId) {
    // TODO: Implement user edit functionality
    alert('Редактирование пользователя #' + userId + ' (функция в разработке)');
}

function deleteUser(userId) {
    if (confirm('Вы уверены, что хотите удалить этого пользователя?')) {
        // TODO: Implement user delete functionality
        alert('Удаление пользователя #' + userId + ' (функция в разработке)');
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
    renderPaginationModern($page, $totalPages, '/dashboard/users?search=' . urlencode($search) . '&page=');
}
$greyContent6 = ob_get_clean();

// Blue section: Empty
$blueContent = '';

// Page title
$pageTitle = 'Управление пользователями - 11-классники';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>