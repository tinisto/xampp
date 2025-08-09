<?php
// Comment Moderation Dashboard
session_start();

// Check admin access
if ((!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') && 
    (!isset($_SESSION['occupation']) || $_SESSION['occupation'] !== 'admin')) {
    header('Location: /unauthorized');
    exit();
}

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Handle moderation actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $comment_id = (int)($_POST['comment_id'] ?? 0);
    
    switch ($action) {
        case 'approve':
            $stmt = $connection->prepare("UPDATE comments SET is_approved = 1 WHERE id = ?");
            $stmt->bind_param("i", $comment_id);
            $stmt->execute();
            break;
            
        case 'reject':
            $stmt = $connection->prepare("UPDATE comments SET is_approved = 0 WHERE id = ?");
            $stmt->bind_param("i", $comment_id);
            $stmt->execute();
            break;
            
        case 'delete':
            $stmt = $connection->prepare("DELETE FROM comments WHERE id = ?");
            $stmt->bind_param("i", $comment_id);
            $stmt->execute();
            break;
            
        case 'resolve_report':
            $report_id = (int)($_POST['report_id'] ?? 0);
            $stmt = $connection->prepare("UPDATE comment_reports SET status = 'resolved', reviewed_at = NOW(), reviewed_by = ? WHERE id = ?");
            $stmt->bind_param("ii", $_SESSION['user_id'], $report_id);
            $stmt->execute();
            break;
    }
    
    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']);
    exit;
}

// Get filter parameters
$filter = $_GET['filter'] ?? 'pending';
$search = $_GET['search'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

// Build query based on filter
$where_conditions = [];
$params = [];
$types = "";

if ($filter === 'pending') {
    $where_conditions[] = "c.is_approved = 0";
} elseif ($filter === 'approved') {
    $where_conditions[] = "c.is_approved = 1";
} elseif ($filter === 'reported') {
    // Will handle separately with reports query
}

if (!empty($search)) {
    $where_conditions[] = "(c.comment_text LIKE ? OR c.author_of_comment LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "ss";
}

// Get statistics
$stats = [
    'total' => 0,
    'pending' => 0,
    'approved' => 0,
    'reported' => 0
];

$statsQuery = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN is_approved = 0 THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN is_approved = 1 THEN 1 ELSE 0 END) as approved
    FROM comments";
$result = $connection->query($statsQuery);
$statsData = $result->fetch_assoc();
$stats['total'] = $statsData['total'];
$stats['pending'] = $statsData['pending'];
$stats['approved'] = $statsData['approved'];

// Get reported comments count
$reportQuery = "SELECT COUNT(DISTINCT comment_id) as reported FROM comment_reports WHERE status = 'pending'";
$result = $connection->query($reportQuery);
$stats['reported'] = $result->fetch_assoc()['reported'];

// Get user info
$username = $_SESSION['first_name'] ?? $_SESSION['email'] ?? 'Admin';
$userInitial = strtoupper(mb_substr($username, 0, 1));

// Set dashboard title
$dashboardTitle = 'Модерация комментариев';

// Build dashboard content
ob_start();
?>
<style>
.moderation-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
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
    cursor: pointer;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.stat-card.active {
    border-color: var(--primary-color);
    background: rgba(0, 123, 255, 0.05);
}

.stat-value {
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 8px;
}

.stat-value.pending { color: #ffc107; }
.stat-value.approved { color: #28a745; }
.stat-value.reported { color: #dc3545; }

.stat-label {
    color: var(--text-secondary);
    font-size: 14px;
}

.filters-section {
    display: flex;
    gap: 20px;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.search-input {
    flex: 1;
    min-width: 250px;
    padding: 12px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 16px;
    background: var(--surface);
    color: var(--text-primary);
}

.comments-container {
    background: var(--surface);
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid var(--border-color);
}

.comment-item {
    padding: 20px;
    border-bottom: 1px solid var(--border-color);
    transition: all 0.3s ease;
}

.comment-item:last-child {
    border-bottom: none;
}

.comment-item:hover {
    background: var(--bg-light);
}

.comment-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}

.comment-meta {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.comment-author {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 14px;
}

.comment-date {
    font-size: 12px;
    color: var(--text-secondary);
}

.comment-status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-approved {
    background: #d4edda;
    color: #155724;
}

.status-reported {
    background: #f8d7da;
    color: #721c24;
}

.comment-text {
    color: var(--text-primary);
    line-height: 1.5;
    margin-bottom: 12px;
    word-wrap: break-word;
}

.comment-info {
    display: flex;
    gap: 20px;
    font-size: 12px;
    color: var(--text-secondary);
    margin-bottom: 12px;
}

.report-info {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 12px;
}

.report-reason {
    font-weight: 600;
    color: #721c24;
    margin-bottom: 4px;
}

.report-description {
    color: #721c24;
    font-size: 13px;
}

.comment-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-action {
    padding: 6px 12px;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-approve {
    background: #28a745;
    color: white;
}

.btn-approve:hover {
    background: #218838;
}

.btn-reject {
    background: #ffc107;
    color: #212529;
}

.btn-reject:hover {
    background: #e0a800;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn-delete:hover {
    background: #c82333;
}

.btn-resolve {
    background: #17a2b8;
    color: white;
}

.btn-resolve:hover {
    background: #138496;
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
    color: var(--text-secondary);
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    opacity: 0.5;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    padding: 20px;
    border-top: 1px solid var(--border-color);
}

.page-link {
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    color: var(--text-primary);
    text-decoration: none;
    transition: all 0.3s;
}

.page-link:hover {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.page-link.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

@media (max-width: 768px) {
    .moderation-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .comment-header {
        flex-direction: column;
        gap: 8px;
    }
    
    .filters-section {
        flex-direction: column;
        width: 100%;
    }
    
    .search-input {
        width: 100%;
    }
}
</style>

<div class="moderation-header">
    <h2>Модерация комментариев</h2>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <a href="?filter=all" class="stat-card <?= $filter === 'all' ? 'active' : '' ?>">
        <div class="stat-value"><?= number_format($stats['total']) ?></div>
        <div class="stat-label">Всего комментариев</div>
    </a>
    <a href="?filter=pending" class="stat-card <?= $filter === 'pending' ? 'active' : '' ?>">
        <div class="stat-value pending"><?= number_format($stats['pending']) ?></div>
        <div class="stat-label">На модерации</div>
    </a>
    <a href="?filter=approved" class="stat-card <?= $filter === 'approved' ? 'active' : '' ?>">
        <div class="stat-value approved"><?= number_format($stats['approved']) ?></div>
        <div class="stat-label">Одобрено</div>
    </a>
    <a href="?filter=reported" class="stat-card <?= $filter === 'reported' ? 'active' : '' ?>">
        <div class="stat-value reported"><?= number_format($stats['reported']) ?></div>
        <div class="stat-label">Жалобы</div>
    </a>
</div>

<!-- Filters -->
<div class="filters-section">
    <input type="text" id="searchInput" placeholder="Поиск по тексту или автору..." 
           value="<?= htmlspecialchars($search) ?>" class="search-input">
</div>

<!-- Comments List -->
<div class="comments-container">
    <?php
    if ($filter === 'reported') {
        // Show reported comments
        $query = "SELECT c.*, r.id as report_id, r.reason, r.description, r.created_at as report_date,
                  COUNT(r.id) as report_count
                  FROM comments c
                  INNER JOIN comment_reports r ON c.id = r.comment_id
                  WHERE r.status = 'pending'
                  " . (!empty($search) ? "AND (c.comment_text LIKE ? OR c.author_of_comment LIKE ?)" : "") . "
                  GROUP BY c.id
                  ORDER BY report_count DESC, r.created_at DESC
                  LIMIT ? OFFSET ?";
        
        $stmt = $connection->prepare($query);
        if (!empty($search)) {
            $stmt->bind_param("ssii", $searchParam, $searchParam, $limit, $offset);
        } else {
            $stmt->bind_param("ii", $limit, $offset);
        }
    } else {
        // Show regular comments
        $whereClause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";
        $query = "SELECT c.* FROM comments c 
                  $whereClause
                  ORDER BY c.date DESC
                  LIMIT ? OFFSET ?";
        
        $stmt = $connection->prepare($query);
        if (!empty($params)) {
            $types .= "ii";
            $params[] = $limit;
            $params[] = $offset;
            $stmt->bind_param($types, ...$params);
        } else {
            $stmt->bind_param("ii", $limit, $offset);
        }
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0):
        while ($comment = $result->fetch_assoc()):
    ?>
        <div class="comment-item">
            <div class="comment-header">
                <div class="comment-meta">
                    <div class="comment-author"><?= htmlspecialchars($comment['author_of_comment'] ?: 'Аноним') ?></div>
                    <div class="comment-date"><?= date('d.m.Y H:i', strtotime($comment['date'])) ?></div>
                </div>
                <div class="comment-status status-<?= $comment['is_approved'] ? 'approved' : 'pending' ?>">
                    <?= $comment['is_approved'] ? 'Одобрено' : 'На модерации' ?>
                </div>
            </div>
            
            <div class="comment-text"><?= nl2br(htmlspecialchars($comment['comment_text'])) ?></div>
            
            <div class="comment-info">
                <span>ID: <?= $comment['id'] ?></span>
                <span>Тип: <?= htmlspecialchars($comment['entity_type']) ?></span>
                <span>Объект: <?= $comment['entity_id'] ?></span>
                <?php if ($comment['email']): ?>
                    <span>Email: <?= htmlspecialchars($comment['email']) ?></span>
                <?php endif; ?>
                <span>IP: <?= htmlspecialchars($comment['author_ip'] ?? 'N/A') ?></span>
            </div>
            
            <?php if (isset($comment['report_id'])): ?>
            <div class="report-info">
                <div class="report-reason">Причина жалобы: <?= htmlspecialchars($comment['reason']) ?></div>
                <?php if ($comment['description']): ?>
                    <div class="report-description"><?= htmlspecialchars($comment['description']) ?></div>
                <?php endif; ?>
                <div class="report-description">Жалоб: <?= $comment['report_count'] ?></div>
            </div>
            <?php endif; ?>
            
            <form method="POST" class="comment-actions">
                <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                
                <?php if (!$comment['is_approved']): ?>
                    <button type="submit" name="action" value="approve" class="btn-action btn-approve">
                        <i class="fas fa-check"></i> Одобрить
                    </button>
                <?php else: ?>
                    <button type="submit" name="action" value="reject" class="btn-action btn-reject">
                        <i class="fas fa-ban"></i> Отклонить
                    </button>
                <?php endif; ?>
                
                <button type="submit" name="action" value="delete" class="btn-action btn-delete" 
                        onclick="return confirm('Удалить комментарий?')">
                    <i class="fas fa-trash"></i> Удалить
                </button>
                
                <?php if (isset($comment['report_id'])): ?>
                    <input type="hidden" name="report_id" value="<?= $comment['report_id'] ?>">
                    <button type="submit" name="action" value="resolve_report" class="btn-action btn-resolve">
                        <i class="fas fa-check-circle"></i> Закрыть жалобу
                    </button>
                <?php endif; ?>
            </form>
        </div>
    <?php 
        endwhile;
    else:
    ?>
        <div class="empty-state">
            <i class="fas fa-comments"></i>
            <div>Комментарии не найдены</div>
        </div>
    <?php endif; ?>
    
    <?php
    // Pagination
    $totalQuery = str_replace("SELECT c.*", "SELECT COUNT(DISTINCT c.id) as total", $query);
    $totalQuery = preg_replace('/LIMIT \? OFFSET \?/', '', $totalQuery);
    $totalQuery = preg_replace('/ORDER BY[^)]*/', '', $totalQuery);
    
    $stmt = $connection->prepare($totalQuery);
    if (!empty($search) && $filter === 'reported') {
        $stmt->bind_param("ss", $searchParam, $searchParam);
    } elseif (!empty($params) && $filter !== 'reported') {
        array_pop($params); // Remove limit
        array_pop($params); // Remove offset
        $types = substr($types, 0, -2);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
    }
    $stmt->execute();
    $totalResult = $stmt->get_result()->fetch_assoc();
    $totalRecords = $totalResult['total'];
    $totalPages = ceil($totalRecords / $limit);
    
    if ($totalPages > 1):
    ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?filter=<?= $filter ?>&search=<?= urlencode($search) ?>&page=<?= $page - 1 ?>" class="page-link">
                <i class="fas fa-chevron-left"></i>
            </a>
        <?php endif; ?>
        
        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
            <a href="?filter=<?= $filter ?>&search=<?= urlencode($search) ?>&page=<?= $i ?>" 
               class="page-link <?= $i === $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
        
        <?php if ($page < $totalPages): ?>
            <a href="?filter=<?= $filter ?>&search=<?= urlencode($search) ?>&page=<?= $page + 1 ?>" class="page-link">
                <i class="fas fa-chevron-right"></i>
            </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
// Handle search
document.getElementById('searchInput').addEventListener('keyup', function(e) {
    if (e.key === 'Enter') {
        const search = this.value;
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('search', search);
        urlParams.set('page', '1');
        window.location.search = urlParams.toString();
    }
});

// Add debounced search
let searchTimeout;
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const search = this.value;
    
    searchTimeout = setTimeout(() => {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('search', search);
        urlParams.set('page', '1');
        window.location.search = urlParams.toString();
    }, 1000);
});
</script>

<?php
$dashboardContent = ob_get_clean();

// Set active menu
$activeMenu = 'moderation';

// Include the dashboard template
include $_SERVER['DOCUMENT_ROOT'] . '/dashboard-template.php';
?>