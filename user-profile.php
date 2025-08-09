<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get user ID from URL or session
$profileUserId = isset($_GET['id']) ? (int)$_GET['id'] : ($_SESSION['user_id'] ?? 0);

if ($profileUserId <= 0) {
    header('Location: /');
    exit();
}

// Get user information
$userQuery = "SELECT u.*, 
              COUNT(DISTINCT c.id) as total_comments,
              SUM(c.likes) as total_likes,
              COUNT(DISTINCT c.entity_id) as discussed_items
              FROM users u
              LEFT JOIN comments c ON u.id = c.user_id
              WHERE u.id = ?
              GROUP BY u.id";

$stmt = $connection->prepare($userQuery);
$stmt->bind_param("i", $profileUserId);
$stmt->execute();
$userResult = $stmt->get_result();

if ($userResult->num_rows === 0) {
    header('Location: /404');
    exit();
}

$user = $userResult->fetch_assoc();

// Check if viewing own profile
$isOwnProfile = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $profileUserId;

// Get recent comments
$recentCommentsQuery = "SELECT c.*, 
                       CASE 
                           WHEN c.entity_type = 'posts' THEN p.name
                           WHEN c.entity_type = 'school' THEN sch.name
                           WHEN c.entity_type = 'vpo' THEN vpo.short_name
                           WHEN c.entity_type = 'spo' THEN spo.short_name
                       END as entity_name,
                       CASE 
                           WHEN c.entity_type = 'posts' THEN CONCAT('/posts/', p.url)
                           WHEN c.entity_type = 'school' THEN CONCAT('/schools/', sch.url)
                           WHEN c.entity_type = 'vpo' THEN CONCAT('/vpo/', vpo.url)
                           WHEN c.entity_type = 'spo' THEN CONCAT('/spo/', spo.url)
                       END as entity_url
                       FROM comments c
                       LEFT JOIN posts p ON c.entity_type = 'posts' AND c.entity_id = p.id
                       LEFT JOIN all_schools sch ON c.entity_type = 'school' AND c.entity_id = sch.id
                       LEFT JOIN all_vpo vpo ON c.entity_type = 'vpo' AND c.entity_id = vpo.id
                       LEFT JOIN all_spo spo ON c.entity_type = 'spo' AND c.entity_id = spo.id
                       WHERE c.user_id = ? AND c.is_approved = 1
                       ORDER BY c.date DESC
                       LIMIT 10";

$stmt = $connection->prepare($recentCommentsQuery);
$stmt->bind_param("i", $profileUserId);
$stmt->execute();
$recentComments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Include template
$currentFile = 'user-profile';
include 'real_template.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($user['first_name']) ?> - Профиль пользователя</title>
    <style>
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            border-radius: 12px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3e%3ccircle cx='50' cy='50' r='40' fill='none' stroke='%23ffffff' stroke-width='0.5' opacity='0.1'/%3e%3c/svg%3e");
            background-size: 100px 100px;
            animation: float 20s infinite linear;
        }
        
        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(-100px, -100px) rotate(360deg); }
        }
        
        .profile-info {
            display: flex;
            align-items: center;
            gap: 30px;
            position: relative;
            z-index: 1;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: white;
            color: #667eea;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: 700;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        
        .profile-details h1 {
            margin: 0 0 10px;
            font-size: 32px;
        }
        
        .profile-stats {
            display: flex;
            gap: 30px;
            margin-top: 15px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-value {
            display: block;
            font-size: 24px;
            font-weight: 700;
        }
        
        .stat-label {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .profile-content {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
        }
        
        .profile-sidebar {
            background: var(--surface);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }
        
        .sidebar-section {
            margin-bottom: 25px;
        }
        
        .sidebar-section:last-child {
            margin-bottom: 0;
        }
        
        .sidebar-section h3 {
            font-size: 16px;
            margin-bottom: 15px;
            color: var(--text-primary);
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            color: var(--text-secondary);
            font-size: 14px;
        }
        
        .info-item i {
            width: 20px;
            text-align: center;
            color: var(--primary-color);
        }
        
        .edit-profile-btn {
            width: 100%;
            padding: 12px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .edit-profile-btn:hover {
            background: #0056b3;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }
        
        .profile-main {
            background: var(--surface);
            padding: 30px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .comment-item {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            transition: background 0.3s;
        }
        
        .comment-item:last-child {
            border-bottom: none;
        }
        
        .comment-item:hover {
            background: var(--bg-light);
        }
        
        .comment-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            font-size: 14px;
            color: var(--text-secondary);
        }
        
        .comment-meta a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .comment-meta a:hover {
            text-decoration: underline;
        }
        
        .comment-text {
            color: var(--text-primary);
            line-height: 1.6;
            margin-bottom: 10px;
        }
        
        .comment-stats {
            display: flex;
            gap: 20px;
            font-size: 14px;
            color: var(--text-secondary);
        }
        
        .comment-stat {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        @media (max-width: 768px) {
            .profile-content {
                grid-template-columns: 1fr;
            }
            
            .profile-info {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-stats {
                justify-content: center;
            }
            
            .profile-header {
                padding: 30px 20px;
            }
            
            .profile-avatar {
                width: 100px;
                height: 100px;
                font-size: 40px;
            }
            
            .profile-details h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-info">
                <div class="profile-avatar">
                    <?= mb_substr($user['first_name'], 0, 1) ?>
                </div>
                <div class="profile-details">
                    <h1><?= htmlspecialchars($user['first_name']) ?></h1>
                    <div class="profile-stats">
                        <div class="stat-item">
                            <span class="stat-value"><?= number_format($user['total_comments'] ?? 0) ?></span>
                            <span class="stat-label">Комментариев</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?= number_format($user['total_likes'] ?? 0) ?></span>
                            <span class="stat-label">Лайков</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?= number_format($user['discussed_items'] ?? 0) ?></span>
                            <span class="stat-label">Обсуждений</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="profile-content">
            <aside class="profile-sidebar">
                <div class="sidebar-section">
                    <h3>Информация</h3>
                    <?php if (!empty($user['email'])): ?>
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <span><?= $isOwnProfile ? htmlspecialchars($user['email']) : 'Скрыто' ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($user['created_at'])): ?>
                    <div class="info-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>На сайте с <?= date('d.m.Y', strtotime($user['created_at'])) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($user['occupation'])): ?>
                    <div class="info-item">
                        <i class="fas fa-user-tag"></i>
                        <span><?= htmlspecialchars($user['occupation']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($isOwnProfile): ?>
                <div class="sidebar-section">
                    <button class="edit-profile-btn" onclick="editProfile()">
                        <i class="fas fa-edit"></i> Редактировать профиль
                    </button>
                </div>
                <?php endif; ?>
                
                <div class="sidebar-section">
                    <h3>Активность</h3>
                    <div class="info-item">
                        <i class="fas fa-comment"></i>
                        <span><?= $user['total_comments'] ?? 0 ?> комментариев</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-heart"></i>
                        <span><?= $user['total_likes'] ?? 0 ?> лайков получено</span>
                    </div>
                </div>
            </aside>
            
            <main class="profile-main">
                <div class="section-header">
                    <h2 class="section-title">Последние комментарии</h2>
                </div>
                
                <?php if (count($recentComments) > 0): ?>
                    <?php foreach ($recentComments as $comment): ?>
                    <div class="comment-item">
                        <div class="comment-meta">
                            <span>Комментарий к</span>
                            <a href="<?= htmlspecialchars($comment['entity_url']) ?>">
                                <?= htmlspecialchars($comment['entity_name'] ?? 'Неизвестно') ?>
                            </a>
                            <span>•</span>
                            <span><?= date('d.m.Y H:i', strtotime($comment['date'])) ?></span>
                        </div>
                        <div class="comment-text">
                            <?= nl2br(htmlspecialchars($comment['comment_text'])) ?>
                        </div>
                        <div class="comment-stats">
                            <div class="comment-stat">
                                <i class="fas fa-thumbs-up"></i>
                                <span><?= $comment['likes'] ?></span>
                            </div>
                            <div class="comment-stat">
                                <i class="fas fa-thumbs-down"></i>
                                <span><?= $comment['dislikes'] ?></span>
                            </div>
                            <?php if ($comment['parent_id']): ?>
                            <div class="comment-stat">
                                <i class="fas fa-reply"></i>
                                <span>Ответ</span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-comments"></i>
                        <p>Пока нет комментариев</p>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
    
    <?php if ($isOwnProfile): ?>
    <!-- Edit Profile Modal -->
    <div id="editProfileModal" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3>Редактировать профиль</h3>
                <button class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <form id="editProfileForm" onsubmit="saveProfile(event)">
                <div class="form-group">
                    <label>Имя</label>
                    <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Род деятельности</label>
                    <select name="occupation">
                        <option value="">Не указано</option>
                        <option value="student" <?= $user['occupation'] == 'student' ? 'selected' : '' ?>>Студент</option>
                        <option value="teacher" <?= $user['occupation'] == 'teacher' ? 'selected' : '' ?>>Преподаватель</option>
                        <option value="parent" <?= $user['occupation'] == 'parent' ? 'selected' : '' ?>>Родитель</option>
                        <option value="other" <?= $user['occupation'] == 'other' ? 'selected' : '' ?>>Другое</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeEditModal()">Отмена</button>
                    <button type="submit" class="btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
    
    <style>
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        }
        
        .modal-content {
            background: var(--surface);
            border-radius: 12px;
            width: 90%;
            max-height: 90vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .modal-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h3 {
            margin: 0;
            color: var(--text-primary);
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            color: var(--text-secondary);
            cursor: pointer;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.3s;
        }
        
        .modal-close:hover {
            background: var(--bg-light);
        }
        
        .form-group {
            margin-bottom: 20px;
            padding: 0 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-primary);
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 16px;
            background: var(--bg-light);
            color: var(--text-primary);
        }
        
        .form-actions {
            padding: 20px;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .btn-primary,
        .btn-secondary {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
        
        .btn-secondary {
            background: transparent;
            color: var(--text-secondary);
        }
        
        .btn-secondary:hover {
            background: var(--bg-light);
        }
    </style>
    
    <script>
        function editProfile() {
            document.getElementById('editProfileModal').style.display = 'flex';
        }
        
        function closeEditModal() {
            document.getElementById('editProfileModal').style.display = 'none';
        }
        
        async function saveProfile(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            
            try {
                const response = await fetch('/api/profile/update.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Профиль успешно обновлен!');
                    location.reload();
                } else {
                    alert('Ошибка: ' + (result.error || 'Неизвестная ошибка'));
                }
            } catch (error) {
                alert('Ошибка при сохранении профиля');
                console.error(error);
            }
        }
    </script>
    <?php endif; ?>
</body>
</html>