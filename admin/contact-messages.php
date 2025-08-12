<?php
// Admin contact messages viewer
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

$page_title = 'Сообщения с формы обратной связи - Админ панель';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /admin/login.php');
    exit;
}

$db = Database::getInstance();

// Handle actions
$action = $_GET['action'] ?? '';
$messageId = $_GET['id'] ?? 0;

if ($action && $messageId) {
    switch ($action) {
        case 'mark_read':
            $db->update('contact_messages', 
                ['status' => 'read', 'read_at' => date('Y-m-d H:i:s')], 
                'id = ?', [$messageId]);
            break;
        case 'mark_replied':
            $db->update('contact_messages', 
                ['status' => 'replied', 'replied_at' => date('Y-m-d H:i:s')], 
                'id = ?', [$messageId]);
            break;
        case 'delete':
            $db->delete('contact_messages', 'id = ?', [$messageId]);
            break;
    }
    
    // Redirect to avoid resubmission
    header('Location: /admin/contact-messages.php');
    exit;
}

// Get filter
$status_filter = $_GET['status'] ?? 'all';
$where_clause = '';
$params = [];

if ($status_filter !== 'all') {
    $where_clause = 'WHERE status = ?';
    $params[] = $status_filter;
}

// Pagination
$page = intval($_GET['page'] ?? 1);
$limit = 20;
$offset = ($page - 1) * $limit;

// Get total count
$total_count = $db->fetchColumn("SELECT COUNT(*) FROM contact_messages $where_clause", $params);
$total_pages = ceil($total_count / $limit);

// Get messages
$messages = $db->fetchAll("
    SELECT * FROM contact_messages 
    $where_clause 
    ORDER BY created_at DESC 
    LIMIT $limit OFFSET $offset
", $params);

// Get statistics
$stats = [
    'total' => $db->fetchColumn("SELECT COUNT(*) FROM contact_messages"),
    'new' => $db->fetchColumn("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'"),
    'read' => $db->fetchColumn("SELECT COUNT(*) FROM contact_messages WHERE status = 'read'"),
    'replied' => $db->fetchColumn("SELECT COUNT(*) FROM contact_messages WHERE status = 'replied'")
];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .header {
            background: white;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .breadcrumb {
            color: #666;
            font-size: 14px;
        }
        
        .breadcrumb a {
            color: #667eea;
            text-decoration: none;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-number.new { color: #e74c3c; }
        .stat-number.read { color: #f39c12; }
        .stat-number.replied { color: #27ae60; }
        .stat-number.total { color: #3498db; }
        
        .filters {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filters select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-success {
            background: #27ae60;
            color: white;
        }
        
        .btn-warning {
            background: #f39c12;
            color: white;
        }
        
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        
        .messages-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th,
        .table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .table th {
            background: #f8f9fa;
            font-weight: 600;
            font-size: 14px;
            color: #555;
        }
        
        .table tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-new {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .status-read {
            background: #fef3c7;
            color: #d97706;
        }
        
        .status-replied {
            background: #dcfce7;
            color: #16a34a;
        }
        
        .message-preview {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .actions {
            display: flex;
            gap: 5px;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 20px;
        }
        
        .pagination a,
        .pagination span {
            padding: 8px 12px;
            background: white;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #333;
        }
        
        .pagination .current {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .pagination a:hover {
            background: #f8f9fa;
        }
        
        .no-messages {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .no-messages i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 10px;
        }
        
        @media (max-width: 768px) {
            .table {
                font-size: 14px;
            }
            
            .table th,
            .table td {
                padding: 10px 5px;
            }
            
            .message-preview {
                max-width: 150px;
            }
            
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1><i class="fas fa-envelope"></i> Сообщения обратной связи</h1>
            <div class="breadcrumb">
                <a href="/">Главная</a> / <a href="/admin/index.php">Админ панель</a> / Сообщения
                <span style="float: right; font-size: 14px;">
                    <i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['user_name'] ?? 'Администратор') ?>
                    | <a href="/admin/logout.php" style="color: #e74c3c;">Выйти</a>
                </span>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Statistics -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number total"><?= $stats['total'] ?></div>
                <div>Всего сообщений</div>
            </div>
            <div class="stat-card">
                <div class="stat-number new"><?= $stats['new'] ?></div>
                <div>Новые</div>
            </div>
            <div class="stat-card">
                <div class="stat-number read"><?= $stats['read'] ?></div>
                <div>Прочитанные</div>
            </div>
            <div class="stat-card">
                <div class="stat-number replied"><?= $stats['replied'] ?></div>
                <div>Отвеченные</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters">
            <strong>Фильтр:</strong>
            <select onchange="window.location.href='?status=' + this.value">
                <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>Все сообщения</option>
                <option value="new" <?= $status_filter === 'new' ? 'selected' : '' ?>>Новые</option>
                <option value="read" <?= $status_filter === 'read' ? 'selected' : '' ?>>Прочитанные</option>
                <option value="replied" <?= $status_filter === 'replied' ? 'selected' : '' ?>>Отвеченные</option>
            </select>
            
            <a href="/contact.php" class="btn btn-primary" target="_blank">
                <i class="fas fa-external-link-alt"></i> Форма обратной связи
            </a>
        </div>

        <!-- Messages Table -->
        <div class="messages-table">
            <?php if (empty($messages)): ?>
                <div class="no-messages">
                    <i class="fas fa-inbox"></i>
                    <h3>Нет сообщений</h3>
                    <p>Сообщения будут появляться здесь после отправки через форму обратной связи.</p>
                </div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Имя</th>
                            <th>Email</th>
                            <th>Тема</th>
                            <th>Сообщение</th>
                            <th>Статус</th>
                            <th>Дата</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $msg): ?>
                            <tr>
                                <td><?= $msg['id'] ?></td>
                                <td><?= htmlspecialchars($msg['name']) ?></td>
                                <td><?= htmlspecialchars($msg['email']) ?></td>
                                <td><?= htmlspecialchars($msg['subject']) ?></td>
                                <td class="message-preview" title="<?= htmlspecialchars($msg['message']) ?>">
                                    <?= htmlspecialchars(mb_substr($msg['message'], 0, 50)) ?>...
                                </td>
                                <td>
                                    <span class="status-badge status-<?= $msg['status'] ?>">
                                        <?= $msg['status'] === 'new' ? 'Новое' : 
                                           ($msg['status'] === 'read' ? 'Прочитано' : 'Отвечено') ?>
                                    </span>
                                </td>
                                <td><?= date('d.m.Y H:i', strtotime($msg['created_at'])) ?></td>
                                <td>
                                    <div class="actions">
                                        <?php if ($msg['status'] === 'new'): ?>
                                            <a href="?action=mark_read&id=<?= $msg['id'] ?>" 
                                               class="btn btn-warning" title="Отметить как прочитанное">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($msg['status'] !== 'replied'): ?>
                                            <a href="?action=mark_replied&id=<?= $msg['id'] ?>" 
                                               class="btn btn-success" title="Отметить как отвеченное">
                                                <i class="fas fa-reply"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <a href="?action=delete&id=<?= $msg['id'] ?>" 
                                           class="btn btn-danger" title="Удалить"
                                           onclick="return confirm('Удалить это сообщение?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <?php if ($i === $page): ?>
                        <span class="current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $i ?>&status=<?= $status_filter ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Auto refresh every 30 seconds if on 'new' messages
        <?php if ($status_filter === 'new' || $status_filter === 'all'): ?>
        setTimeout(function() {
            if (document.hidden === false) { // Only refresh if tab is active
                window.location.reload();
            }
        }, 30000);
        <?php endif; ?>
    </script>
</body>
</html>