<?php
// Comment Moderation Dashboard
session_start();

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('HTTP/1.1 401 Unauthorized');
    header('Location: /unauthorized.php');
    exit;
}

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

// Handle moderation actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $comment_id = (int)($_POST['comment_id'] ?? 0);
    
    switch ($action) {
        case 'approve':
            db_execute("UPDATE comments SET is_approved = 1 WHERE id = ?", [$comment_id]);
            break;
            
        case 'reject':
            db_execute("UPDATE comments SET is_approved = 0 WHERE id = ?", [$comment_id]);
            break;
            
        case 'delete':
            db_execute("DELETE FROM comments WHERE id = ?", [$comment_id]);
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

// Get statistics
$statsData = db_fetch_one("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN is_approved = 0 THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN is_approved = 1 THEN 1 ELSE 0 END) as approved
    FROM comments");
$stats = [
    'total' => $statsData['total'],
    'pending' => $statsData['pending'], 
    'approved' => $statsData['approved'],
    'reported' => $statsData['pending'] // Same as pending since no reports table
];

// Build query conditions
$where_conditions = [];
$params = [];

if ($filter === 'pending') {
    $where_conditions[] = "c.is_approved = 0";
} elseif ($filter === 'approved') {
    $where_conditions[] = "c.is_approved = 1";
}

if (!empty($search)) {
    $where_conditions[] = "(c.comment_text LIKE ? OR u.email LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
}

// Get comments
$whereClause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";
$query = "SELECT c.*, u.email as author_email FROM comments c 
          LEFT JOIN users u ON c.user_id = u.id
          $whereClause
          ORDER BY c.created_at DESC
          LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$comments = db_fetch_all($query, $params);

// Get total count for pagination
$countQuery = "SELECT COUNT(DISTINCT c.id) FROM comments c 
               LEFT JOIN users u ON c.user_id = u.id
               $whereClause";

if (!empty($params)) {
    $countParams = array_slice($params, 0, -2); // Remove limit and offset
    $totalRecords = db_fetch_column($countQuery, $countParams);
} else {
    $totalRecords = db_fetch_column($countQuery);
}
$totalPages = ceil($totalRecords / $limit);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Модерация комментариев - 11klassniki.ru</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f7fa;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            cursor: pointer;
        }
        
        .stat-card.active {
            border: 2px solid #007bff;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        
        .comments-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .comment-item {
            padding: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .comment-meta {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .comment-text {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        
        .comment-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-approve { background: #28a745; color: white; }
        .btn-reject { background: #dc3545; color: white; }
        .btn-delete { background: #6c757d; color: white; }
        
        .pagination {
            padding: 20px;
            text-align: center;
        }
        
        .pagination a {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 4px;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 4px;
            color: #007bff;
        }
        
        .pagination a.active {
            background: #007bff;
            color: white;
        }
        
        .search-bar {
            margin: 20px 0;
        }
        
        .search-bar input {
            padding: 10px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        
        .search-bar button {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Модерация комментариев</h1>
            <p>Управление комментариями пользователей</p>
        </div>

        <!-- Statistics -->
        <div class="stats">
            <a href="?filter=all" class="stat-card <?= $filter === 'all' ? 'active' : '' ?>" style="text-decoration: none; color: inherit;">
                <div class="stat-value"><?= $stats['total'] ?></div>
                <div>Всего</div>
            </a>
            <a href="?filter=pending" class="stat-card <?= $filter === 'pending' ? 'active' : '' ?>" style="text-decoration: none; color: inherit;">
                <div class="stat-value"><?= $stats['pending'] ?></div>
                <div>На модерации</div>
            </a>
            <a href="?filter=approved" class="stat-card <?= $filter === 'approved' ? 'active' : '' ?>" style="text-decoration: none; color: inherit;">
                <div class="stat-value"><?= $stats['approved'] ?></div>
                <div>Одобрено</div>
            </a>
        </div>

        <!-- Search -->
        <div class="search-bar">
            <form method="get">
                <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                <input type="text" name="search" placeholder="Поиск по комментариям..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit"><i class="fas fa-search"></i> Найти</button>
                <?php if (!empty($search)): ?>
                <a href="?filter=<?= $filter ?>" style="margin-left: 10px;">Очистить</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Comments List -->
        <div class="comments-container">
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                <div class="comment-item">
                    <div class="comment-header">
                        <div class="comment-meta">
                            <div><strong><?= htmlspecialchars($comment['author_email'] ?: 'Аноним') ?></strong></div>
                            <div><?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?></div>
                        </div>
                        <div>
                            <span style="background: <?= $comment['is_approved'] ? '#28a745' : '#dc3545' ?>; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                <?= $comment['is_approved'] ? 'Одобрено' : 'На модерации' ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="comment-text">
                        <?= htmlspecialchars($comment['comment_text']) ?>
                    </div>
                    
                    <form class="comment-actions" method="post" style="display: inline;">
                        <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                        
                        <?php if (!$comment['is_approved']): ?>
                        <button type="submit" name="action" value="approve" class="btn btn-approve">
                            <i class="fas fa-check"></i> Одобрить
                        </button>
                        <?php endif; ?>
                        
                        <?php if ($comment['is_approved']): ?>
                        <button type="submit" name="action" value="reject" class="btn btn-reject">
                            <i class="fas fa-times"></i> Отклонить
                        </button>
                        <?php endif; ?>
                        
                        <button type="submit" name="action" value="delete" class="btn btn-delete" 
                                onclick="return confirm('Удалить комментарий?')">
                            <i class="fas fa-trash"></i> Удалить
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="padding: 40px; text-align: center; color: #6c757d;">
                    <i class="fas fa-comments" style="font-size: 48px; margin-bottom: 20px;"></i>
                    <p>Комментарии не найдены</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?filter=<?= $filter ?>&search=<?= urlencode($search) ?>&page=<?= $i ?>" 
                   class="<?= $i === $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>