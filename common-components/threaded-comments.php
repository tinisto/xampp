<?php
/**
 * Beautiful Threaded Comments Component
 * 
 * Modern comment system with parent-child reply functionality
 * Supports nested replies, avatars, and smart loading
 * 
 * Usage:
 * include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/threaded-comments.php';
 * renderThreadedComments('posts', $post_id, [
 *     'title' => 'Комментарии',
 *     'loadLimit' => 10,
 *     'allowNewComments' => true,
 *     'allowReplies' => true
 * ]);
 */

function renderThreadedComments($entityType, $entityId, $options = []) {
    global $connection;
    
    // Default options
    $defaults = [
        'title' => 'Комментарии',
        'loadLimit' => 10,
        'allowNewComments' => true,
        'allowReplies' => true,
        'showStats' => true,
        'maxDepth' => 5
    ];
    
    $options = array_merge($defaults, $options);
    
    // Get comments count (only parent comments)
    $countQuery = "SELECT COUNT(*) as total FROM comments WHERE entity_type = ? AND entity_id = ? AND (parent_id IS NULL OR parent_id = 0)";
    $stmt = $connection->prepare($countQuery);
    $stmt->bind_param("si", $entityType, $entityId);
    $stmt->execute();
    $totalComments = $stmt->get_result()->fetch_assoc()['total'];
    
    // Get total replies count
    $repliesQuery = "SELECT COUNT(*) as total FROM comments WHERE entity_type = ? AND entity_id = ? AND parent_id IS NOT NULL AND parent_id > 0";
    $stmt = $connection->prepare($repliesQuery);
    $stmt->bind_param("si", $entityType, $entityId);
    $stmt->execute();
    $totalReplies = $stmt->get_result()->fetch_assoc()['total'];
    
    // Generate unique ID for this comments section
    $commentsId = 'threaded-comments-' . $entityType . '-' . $entityId;
    ?>
    
    <!-- Include CSS only once -->
    <?php if (!defined('THREADED_COMMENTS_CSS_INCLUDED')): ?>
    <?php define('THREADED_COMMENTS_CSS_INCLUDED', true); ?>
    <style>
        /* Beautiful Threaded Comments Styles */
        .threaded-comments-section {
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--surface, #ffffff);
            border-radius: 16px;
            border: 1px solid var(--border-color, #e1e5e9);
            overflow: hidden;
        }
        
        .comments-header {
            background: linear-gradient(135deg, var(--primary-color, #007bff) 0%, var(--secondary-color, #6c63ff) 100%);
            color: white;
            padding: 24px 30px;
            position: relative;
            overflow: hidden;
        }
        
        .comments-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 20'%3e%3cpath d='M0 0L100 20L0 20Z' fill='%23ffffff' fill-opacity='0.1'/%3e%3c/svg%3e");
            background-size: 100% 100%;
        }
        
        .comments-title {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 1;
        }
        
        .comments-title i {
            font-size: 24px;
            opacity: 0.9;
        }
        
        .comments-stats {
            display: flex;
            gap: 20px;
            margin-top: 12px;
            position: relative;
            z-index: 1;
        }
        
        .stat-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            opacity: 0.9;
        }
        
        .stat-value {
            font-weight: 600;
            background: rgba(255,255,255,0.2);
            padding: 4px 8px;
            border-radius: 12px;
        }
        
        .comments-body {
            padding: 0;
        }
        
        .comment-form-wrapper {
            padding: 30px;
            background: var(--bg-light, #f8f9fa);
            border-bottom: 1px solid var(--border-color, #e1e5e9);
        }
        
        .comment-form {
            display: grid;
            gap: 20px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--text-primary, #2c3e50);
            font-size: 14px;
        }
        
        .form-control {
            padding: 14px 16px;
            border: 2px solid var(--border-color, #e1e5e9);
            border-radius: 12px;
            font-size: 16px;
            font-family: inherit;
            transition: all 0.3s ease;
            background: white;
            color: var(--text-primary, #2c3e50);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color, #007bff);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
            transform: translateY(-1px);
        }
        
        .form-control::placeholder {
            color: var(--text-secondary, #6c757d);
        }
        
        .comment-textarea {
            min-height: 120px;
            resize: vertical;
            font-family: inherit;
        }
        
        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 44px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color, #007bff) 0%, #0056b3 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
        }
        
        .btn-secondary {
            background: var(--surface, #ffffff);
            color: var(--text-secondary, #6c757d);
            border: 2px solid var(--border-color, #e1e5e9);
        }
        
        .btn-secondary:hover {
            background: var(--bg-light, #f8f9fa);
            border-color: var(--primary-color, #007bff);
            color: var(--primary-color, #007bff);
        }
        
        .comments-list {
            padding: 0;
        }
        
        .comment-item {
            padding: 24px 30px;
            border-bottom: 1px solid var(--border-color, #e1e5e9);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .comment-item:hover {
            background: var(--bg-light, #f8f9fa);
        }
        
        .comment-item.reply {
            margin-left: 40px;
            border-left: 3px solid var(--primary-color, #007bff);
            background: rgba(0, 123, 255, 0.02);
        }
        
        .comment-item.reply.depth-2 { margin-left: 60px; }
        .comment-item.reply.depth-3 { margin-left: 80px; }
        .comment-item.reply.depth-4 { margin-left: 100px; }
        .comment-item.reply.depth-5 { margin-left: 120px; }
        
        .comment-header {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 16px;
        }
        
        .comment-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color, #007bff), var(--secondary-color, #6c63ff));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 16px;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .comment-meta {
            flex: 1;
        }
        
        .comment-author {
            font-weight: 700;
            font-size: 16px;
            color: var(--text-primary, #2c3e50);
            margin-bottom: 4px;
        }
        
        .comment-date {
            font-size: 13px;
            color: var(--text-secondary, #6c757d);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .comment-actions {
            display: flex;
            gap: 8px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .comment-item:hover .comment-actions {
            opacity: 1;
        }
        
        .comment-text {
            color: var(--text-primary, #2c3e50);
            line-height: 1.6;
            font-size: 15px;
            margin-bottom: 16px;
            word-wrap: break-word;
        }
        
        .comment-footer {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }
        
        .btn-reply, .btn-like, .btn-edit, .btn-report {
            background: none;
            border: none;
            color: var(--text-secondary, #6c757d);
            font-size: 13px;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 20px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .btn-reply:hover, .btn-edit:hover {
            background: rgba(0, 123, 255, 0.1);
            color: var(--primary-color, #007bff);
        }
        
        .btn-report:hover {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        
        .comment-edited {
            font-size: 12px;
            color: var(--text-secondary, #6c757d);
            margin-top: 8px;
            font-style: italic;
        }
        
        .mention {
            color: #007bff;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }
        
        .mention:hover {
            text-decoration: underline;
        }
        
        .edit-form {
            margin-top: 15px;
            padding: 15px;
            background: rgba(0, 123, 255, 0.05);
            border-radius: 8px;
            border: 1px solid rgba(0, 123, 255, 0.1);
            display: none;
        }
        
        .edit-form.show {
            display: block;
            animation: slideDown 0.3s ease;
        }
        
        .btn-like:hover {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        
        .btn-like.liked {
            color: #28a745;
            background: rgba(40, 167, 69, 0.1);
        }
        
        .btn-dislike {
            margin-left: 10px;
        }
        
        .btn-dislike.liked {
            color: #dc3545;
            background: rgba(220, 53, 69, 0.1);
        }
        
        .reply-form {
            margin-top: 20px;
            padding: 20px;
            background: rgba(0, 123, 255, 0.05);
            border-radius: 12px;
            border: 1px solid rgba(0, 123, 255, 0.1);
            display: none;
        }
        
        .reply-form.show {
            display: block;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .loading-indicator {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-secondary, #6c757d);
        }
        
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--border-color, #e1e5e9);
            border-top: 3px solid var(--primary-color, #007bff);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 16px auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .load-more-section {
            text-align: center;
            padding: 30px;
            background: var(--bg-light, #f8f9fa);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 30px;
            color: var(--text-secondary, #6c757d);
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }
        
        .empty-state h3 {
            margin: 0 0 8px 0;
            font-size: 18px;
            color: var(--text-primary, #2c3e50);
        }
        
        .reply-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-secondary, #6c757d);
            margin-bottom: 12px;
        }
        
        .reply-indicator::before {
            content: '↳';
            font-size: 14px;
            color: var(--primary-color, #007bff);
        }
        
        /* Dark mode support */
        [data-theme="dark"] .threaded-comments-section {
            background: var(--surface, #1a1a1a);
            border-color: var(--border-color, #333);
        }
        
        [data-theme="dark"] .comment-form-wrapper {
            background: var(--bg-light, #2a2a2a);
        }
        
        [data-theme="dark"] .form-control {
            background: var(--surface, #1a1a1a);
            border-color: var(--border-color, #333);
            color: var(--text-primary, #ffffff);
        }
        
        [data-theme="dark"] .comment-item:hover {
            background: var(--bg-light, #2a2a2a);
        }
        
        /* Mobile responsive */
        @media (max-width: 768px) {
            .threaded-comments-section {
                border-radius: 0;
                border-left: none;
                border-right: none;
            }
            
            .comments-header {
                padding: 20px;
            }
            
            .comments-title {
                font-size: 22px;
            }
            
            .comment-form-wrapper, .comment-item {
                padding: 20px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .comment-item.reply {
                margin-left: 20px;
            }
            
            .comment-item.reply.depth-2 { margin-left: 30px; }
            .comment-item.reply.depth-3 { margin-left: 40px; }
            .comment-item.reply.depth-4,
            .comment-item.reply.depth-5 { margin-left: 50px; }
            
            .comment-header {
                gap: 12px;
            }
            
            .comment-avatar {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }
            
            .form-actions {
                flex-direction: column-reverse;
                align-items: stretch;
            }
            
            .btn {
                justify-content: center;
            }
        }
    </style>
    <?php endif; ?>
    
    <div class="threaded-comments-section" id="<?= htmlspecialchars($commentsId) ?>">
        <div class="comments-header">
            <h3 class="comments-title">
                <i class="fas fa-comments"></i>
                <?= htmlspecialchars($options['title']) ?>
            </h3>
            <div class="comments-stats">
                <div class="stat-item">
                    <i class="fas fa-comment"></i>
                    <span class="stat-value comments-count"><?= $totalComments ?></span>
                    <span>комментариев</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-reply"></i>
                    <span class="stat-value replies-count"><?= $totalReplies ?></span>
                    <span>ответов</span>
                </div>
            </div>
        </div>
        
        <div class="comments-body">
            <?php if ($options['allowNewComments']): ?>
            <div class="comment-form-wrapper">
                <form class="comment-form" id="comment-form-<?= htmlspecialchars($commentsId) ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="comment-author-<?= htmlspecialchars($commentsId) ?>">Ваше имя *</label>
                            <input type="text" 
                                   id="comment-author-<?= htmlspecialchars($commentsId) ?>" 
                                   name="author" 
                                   required 
                                   placeholder="Как к вам обращаться?"
                                   class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="comment-email-<?= htmlspecialchars($commentsId) ?>">Email (не публикуется)</label>
                            <input type="email" 
                                   id="comment-email-<?= htmlspecialchars($commentsId) ?>" 
                                   name="email" 
                                   placeholder="your@email.com"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label" for="comment-text-<?= htmlspecialchars($commentsId) ?>">Ваш комментарий *</label>
                        <textarea id="comment-text-<?= htmlspecialchars($commentsId) ?>" 
                                  name="comment" 
                                  required 
                                  placeholder="Поделитесь своими мыслями..."
                                  class="form-control comment-textarea"></textarea>
                    </div>
                    <div class="form-actions">
                        <div class="comment-tips">
                            <small style="color: var(--text-secondary, #6c757d);">
                                <i class="fas fa-info-circle"></i>
                                Будьте вежливы и конструктивны в своих комментариях
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i>
                            Опубликовать
                        </button>
                    </div>
                </form>
            </div>
            <?php endif; ?>
            
            <div class="comments-loading" id="loading-<?= htmlspecialchars($commentsId) ?>" style="display: none;">
                <div class="loading-indicator">
                    <div class="loading-spinner"></div>
                    <div>Загружаем комментарии...</div>
                </div>
            </div>
            
            <div class="comments-list" id="comments-list-<?= htmlspecialchars($commentsId) ?>">
                <!-- Comments will be loaded here via AJAX -->
            </div>
            
            <div class="load-more-section" id="load-more-<?= htmlspecialchars($commentsId) ?>" style="display: none;">
                <button class="btn btn-secondary" id="load-more-btn-<?= htmlspecialchars($commentsId) ?>">
                    <i class="fas fa-chevron-down"></i>
                    Загрузить еще комментарии
                </button>
            </div>
            
            <div class="empty-state" id="no-comments-<?= htmlspecialchars($commentsId) ?>" style="display: none;">
                <i class="fas fa-comments"></i>
                <h3>Пока нет комментариев</h3>
                <p>Будьте первым, кто оставит комментарий!</p>
            </div>
        </div>
    </div>

    <script>
    // Beautiful Threaded Comments System
    (function() {
        const commentsId = '<?= htmlspecialchars($commentsId) ?>';
        const entityType = '<?= htmlspecialchars($entityType) ?>';
        const entityId = <?= (int)$entityId ?>;
        const loadLimit = <?= (int)$options['loadLimit'] ?>;
        const maxDepth = <?= (int)$options['maxDepth'] ?>;
        const currentUserId = <?= isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 'null' ?>;
        const isAdmin = <?= (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') || (isset($_SESSION['occupation']) && $_SESSION['occupation'] === 'admin') ? 'true' : 'false' ?>;
        let currentPage = 1;
        let totalPages = 1;
        let isLoading = false;
        
        // DOM elements
        const commentsList = document.getElementById('comments-list-' + commentsId);
        const loadingEl = document.getElementById('loading-' + commentsId);
        const loadMoreSection = document.getElementById('load-more-' + commentsId);
        const loadMoreBtn = document.getElementById('load-more-btn-' + commentsId);
        const noCommentsEl = document.getElementById('no-comments-' + commentsId);
        const commentsCountEl = document.querySelector('#' + commentsId + ' .comments-count');
        const repliesCountEl = document.querySelector('#' + commentsId + ' .replies-count');
        
        // Initialize
        init();
        
        function init() {
            loadComments(1);
            setupEventListeners();
        }
        
        function setupEventListeners() {
            // Main comment form
            <?php if ($options['allowNewComments']): ?>
            const commentForm = document.getElementById('comment-form-' + commentsId);
            if (commentForm) {
                commentForm.addEventListener('submit', handleCommentSubmit);
            }
            <?php endif; ?>
            
            // Load more button
            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', () => loadComments(currentPage + 1, false));
            }
        }
        
        // Load comments for a specific page
        async function loadComments(page = 1, replace = true) {
            if (isLoading) return;
            isLoading = true;
            
            showLoading();
            
            try {
                const response = await fetch(`/api/comments/threaded?entity_type=${entityType}&entity_id=${entityId}&page=${page}&limit=${loadLimit}`);
                const data = await response.json();
                
                if (data.success) {
                    if (replace) {
                        commentsList.innerHTML = '';
                        currentPage = 1;
                    }
                    
                    displayComments(data.comments);
                    currentPage = data.currentPage;
                    totalPages = data.totalPages;
                    
                    updateLoadMoreButton();
                    
                    if (data.comments.length === 0 && page === 1) {
                        showNoComments();
                    } else {
                        hideNoComments();
                    }
                    
                    // Update counts
                    if (commentsCountEl) commentsCountEl.textContent = data.totalComments;
                    if (repliesCountEl) repliesCountEl.textContent = data.totalReplies;
                } else {
                    console.error('Error loading comments:', data.error);
                }
            } catch (error) {
                console.error('Error loading comments:', error);
            } finally {
                hideLoading();
                isLoading = false;
            }
        }
        
        // Display comments with threading
        function displayComments(comments) {
            const commentsMap = new Map();
            const rootComments = [];
            
            // First pass: create all comment elements
            comments.forEach(comment => {
                commentsMap.set(comment.id, {
                    ...comment,
                    element: createCommentElement(comment),
                    children: []
                });
            });
            
            // Second pass: organize into tree structure
            comments.forEach(comment => {
                if (comment.parent_id && commentsMap.has(parseInt(comment.parent_id))) {
                    commentsMap.get(parseInt(comment.parent_id)).children.push(commentsMap.get(comment.id));
                } else {
                    rootComments.push(commentsMap.get(comment.id));
                }
            });
            
            // Third pass: render tree
            rootComments.forEach(comment => {
                renderCommentTree(comment, 0);
            });
        }
        
        // Render comment tree recursively
        function renderCommentTree(comment, depth) {
            const element = comment.element;
            
            if (depth > 0) {
                element.classList.add('reply', `depth-${Math.min(depth, maxDepth)}`);
            }
            
            commentsList.appendChild(element);
            
            // Render children
            comment.children
                .sort((a, b) => new Date(a.date) - new Date(b.date))
                .forEach(child => renderCommentTree(child, depth + 1));
        }
        
        // Create a comment HTML element
        function createCommentElement(comment) {
            const div = document.createElement('div');
            div.className = 'comment-item';
            div.dataset.commentId = comment.id;
            
            const isReply = comment.parent_id && comment.parent_id > 0;
            const authorInitial = (comment.author_of_comment || 'А').charAt(0).toUpperCase();
            
            div.innerHTML = `
                ${isReply ? `<div class="reply-indicator">Ответ на комментарий</div>` : ''}
                <div class="comment-header">
                    <div class="comment-avatar">${authorInitial}</div>
                    <div class="comment-meta">
                        <div class="comment-author">${escapeHtml(comment.author_of_comment || 'Аноним')}</div>
                        <div class="comment-date">
                            <i class="fas fa-clock"></i>
                            ${formatDate(comment.date)}
                        </div>
                    </div>
                    <div class="comment-actions">
                        <button class="btn-like ${comment.user_liked ? 'liked' : ''}" onclick="likeComment(${comment.id}, 'like')" title="Нравится">
                            <i class="fas fa-thumbs-up"></i>
                            <span class="like-count">${comment.likes || 0}</span>
                        </button>
                        <button class="btn-like btn-dislike ${comment.user_disliked ? 'liked' : ''}" onclick="likeComment(${comment.id}, 'dislike')" title="Не нравится">
                            <i class="fas fa-thumbs-down"></i>
                            <span class="dislike-count">${comment.dislikes || 0}</span>
                        </button>
                    </div>
                </div>
                <div class="comment-text" id="comment-text-${comment.id}">${formatCommentText(comment.comment_text)}</div>
                ${comment.edited_at ? `<div class="comment-edited"><small><i class="fas fa-edit"></i> Отредактировано ${formatDate(comment.edited_at)}</small></div>` : ''}
                <div class="comment-footer">
                    ${<?= $options['allowReplies'] ? 'true' : 'false' ?> ? `
                        <button class="btn-reply" onclick="showReplyForm(${comment.id})" title="Ответить">
                            <i class="fas fa-reply"></i>
                            Ответить
                        </button>
                    ` : ''}
                    ${canEditComment(comment) ? `
                        <button class="btn-edit" onclick="showEditForm(${comment.id})" title="Редактировать">
                            <i class="fas fa-edit"></i>
                            Редактировать
                        </button>
                    ` : ''}
                    <button class="btn-report" onclick="reportComment(${comment.id})" title="Пожаловаться">
                        <i class="fas fa-flag"></i>
                        Жалоба
                    </button>
                    <div class="comment-info">
                        <small>ID: ${comment.id}</small>
                    </div>
                </div>
                ${<?= $options['allowReplies'] ? 'true' : 'false' ?> ? `
                    <div class="reply-form" id="reply-form-${comment.id}">
                        <form onsubmit="handleReplySubmit(event, ${comment.id})">
                            <div class="form-group">
                                <label class="form-label">Ваше имя *</label>
                                <input type="text" name="author" required placeholder="Как к вам обращаться?" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Ваш ответ *</label>
                                <textarea name="comment" required placeholder="Напишите ответ..." class="form-control" rows="3"></textarea>
                            </div>
                            <div class="form-actions">
                                <button type="button" onclick="hideReplyForm(${comment.id})" class="btn btn-secondary">
                                    Отмена
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-reply"></i>
                                    Ответить
                                </button>
                            </div>
                        </form>
                    </div>
                ` : ''}
                <div class="edit-form" id="edit-form-${comment.id}">
                    <form onsubmit="handleEditSubmit(event, ${comment.id})">
                        <div class="form-group">
                            <label class="form-label">Редактировать комментарий</label>
                            <textarea name="comment_text" required class="form-control" rows="4">${escapeHtml(comment.comment_text)}</textarea>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" onclick="hideEditForm(${comment.id})">
                                <i class="fas fa-times"></i>
                                Отмена
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Сохранить
                            </button>
                        </div>
                    </form>
                </div>
            `;
            
            return div;
        }
        
        // Handle main comment form submission
        async function handleCommentSubmit(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            formData.append('entity_type', entityType);
            formData.append('entity_id', entityId);
            
            try {
                const response = await fetch('/api/comments/add', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    e.target.reset();
                    loadComments(1); // Reload comments
                    showMessage('Комментарий успешно добавлен!', 'success');
                } else {
                    showMessage('Ошибка: ' + (data.error || 'Неизвестная ошибка'), 'error');
                }
            } catch (error) {
                showMessage('Ошибка при добавлении комментария', 'error');
                console.error(error);
            }
        }
        
        // Global functions for reply functionality
        window.showReplyForm = function(commentId) {
            // Hide all other reply forms
            document.querySelectorAll('.reply-form.show').forEach(form => {
                form.classList.remove('show');
            });
            
            const replyForm = document.getElementById(`reply-form-${commentId}`);
            if (replyForm) {
                replyForm.classList.add('show');
                replyForm.querySelector('input[name="author"]').focus();
            }
        };
        
        window.hideReplyForm = function(commentId) {
            const replyForm = document.getElementById(`reply-form-${commentId}`);
            if (replyForm) {
                replyForm.classList.remove('show');
                replyForm.querySelector('form').reset();
            }
        };
        
        window.showEditForm = function(commentId) {
            const editForm = document.getElementById(`edit-form-${commentId}`);
            const commentText = document.getElementById(`comment-text-${commentId}`);
            
            if (editForm) {
                // Hide comment text and show edit form
                if (commentText) commentText.style.display = 'none';
                editForm.classList.add('show');
                
                // Focus on textarea
                const textarea = editForm.querySelector('textarea');
                if (textarea) {
                    textarea.focus();
                    textarea.setSelectionRange(textarea.value.length, textarea.value.length);
                }
            }
        };
        
        window.hideEditForm = function(commentId) {
            const editForm = document.getElementById(`edit-form-${commentId}`);
            const commentText = document.getElementById(`comment-text-${commentId}`);
            
            if (editForm) {
                editForm.classList.remove('show');
                if (commentText) commentText.style.display = 'block';
            }
        };
        
        window.handleEditSubmit = async function(e, commentId) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const newText = formData.get('comment_text');
            
            try {
                const response = await fetch('/api/comments/edit.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        comment_id: commentId,
                        new_text: newText,
                        edit_reason: ''
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Update comment text in UI
                    const commentTextEl = document.getElementById(`comment-text-${commentId}`);
                    if (commentTextEl) {
                        commentTextEl.innerHTML = formatCommentText(data.comment.comment_text);
                    }
                    
                    // Add/update edited indicator
                    const commentEl = document.querySelector(`[data-comment-id="${commentId}"]`);
                    if (commentEl) {
                        let editedEl = commentEl.querySelector('.comment-edited');
                        if (!editedEl) {
                            editedEl = document.createElement('div');
                            editedEl.className = 'comment-edited';
                            commentTextEl.after(editedEl);
                        }
                        editedEl.innerHTML = `<small><i class="fas fa-edit"></i> Отредактировано ${formatDate(data.comment.edited_at)}</small>`;
                    }
                    
                    hideEditForm(commentId);
                    showToast('success', 'Комментарий успешно отредактирован');
                } else {
                    showToast('error', data.error || 'Ошибка при редактировании');
                }
            } catch (error) {
                console.error('Error editing comment:', error);
                showToast('error', 'Ошибка при редактировании комментария');
            }
        };
        
        window.handleReplySubmit = async function(e, parentId) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            formData.append('entity_type', entityType);
            formData.append('entity_id', entityId);
            formData.append('parent_id', parentId);
            
            try {
                const response = await fetch('/api/comments/add', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    hideReplyForm(parentId);
                    loadComments(1); // Reload comments
                    showMessage('Ответ успешно добавлен!', 'success');
                } else {
                    showMessage('Ошибка: ' + (data.error || 'Неизвестная ошибка'), 'error');
                }
            } catch (error) {
                showMessage('Ошибка при добавлении ответа', 'error');
                console.error(error);
            }
        };
        
        window.likeComment = async function(commentId, action) {
            try {
                const response = await fetch('/api/comments/like.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        comment_id: commentId,
                        action: action
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Update UI
                    const commentEl = document.querySelector(`[data-comment-id="${commentId}"]`);
                    if (commentEl) {
                        const likeBtn = commentEl.querySelector('.btn-like:not(.btn-dislike)');
                        const dislikeBtn = commentEl.querySelector('.btn-dislike');
                        const likeCount = likeBtn.querySelector('.like-count');
                        const dislikeCount = dislikeBtn.querySelector('.dislike-count');
                        
                        // Update counts
                        likeCount.textContent = data.likes;
                        dislikeCount.textContent = data.dislikes;
                        
                        // Update button states
                        if (action === 'like') {
                            if (data.action_type === 'removed') {
                                likeBtn.classList.remove('liked');
                            } else {
                                likeBtn.classList.add('liked');
                                dislikeBtn.classList.remove('liked');
                            }
                        } else {
                            if (data.action_type === 'removed') {
                                dislikeBtn.classList.remove('liked');
                            } else {
                                dislikeBtn.classList.add('liked');
                                likeBtn.classList.remove('liked');
                            }
                        }
                    }
                } else {
                    showToast('error', data.error || 'Ошибка при голосовании');
                }
            } catch (error) {
                console.error('Error liking comment:', error);
                showToast('error', 'Ошибка при голосовании');
            }
        };
        
        window.reportComment = async function(commentId) {
            const reason = prompt('Выберите причину жалобы:\n1. Спам\n2. Оскорбление\n3. Другое\n\nВведите номер (1-3):');
            
            let reasonCode;
            switch(reason) {
                case '1': reasonCode = 'spam'; break;
                case '2': reasonCode = 'offensive'; break;
                case '3': reasonCode = 'other'; break;
                default:
                    showToast('error', 'Неверная причина жалобы');
                    return;
            }
            
            let description = '';
            if (reasonCode === 'other') {
                description = prompt('Опишите причину жалобы:') || '';
            }
            
            try {
                const response = await fetch('/api/comments/report.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        comment_id: commentId,
                        reason: reasonCode,
                        description: description
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast('success', data.message || 'Жалоба отправлена');
                } else {
                    showToast('error', data.error || 'Ошибка при отправке жалобы');
                }
            } catch (error) {
                console.error('Error reporting comment:', error);
                showToast('error', 'Ошибка при отправке жалобы');
            }
        };
        
        // UI helper functions
        function updateLoadMoreButton() {
            if (currentPage < totalPages) {
                loadMoreSection.style.display = 'block';
            } else {
                loadMoreSection.style.display = 'none';
            }
        }
        
        function showLoading() {
            if (loadingEl) loadingEl.style.display = 'block';
        }
        
        function hideLoading() {
            if (loadingEl) loadingEl.style.display = 'none';
        }
        
        function showNoComments() {
            if (noCommentsEl) noCommentsEl.style.display = 'block';
        }
        
        function hideNoComments() {
            if (noCommentsEl) noCommentsEl.style.display = 'none';
        }
        
        function showMessage(message, type = 'info') {
            // Create toast notification
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 16px 24px;
                background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#007bff'};
                color: white;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 10000;
                animation: slideInRight 0.3s ease;
            `;
            toast.textContent = message;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
        
        // Utility functions
        function canEditComment(comment) {
            if (!currentUserId) return false;
            if (isAdmin) return true;
            if (comment.user_id !== currentUserId) return false;
            
            // Check if within 15 minutes
            const commentDate = new Date(comment.date);
            const now = new Date();
            const diffMinutes = (now - commentDate) / (1000 * 60);
            
            return diffMinutes <= 15 && (!comment.edit_count || comment.edit_count < 3);
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        }
        
        function formatCommentText(text) {
            // First escape HTML
            let formatted = escapeHtml(text);
            
            // Then convert @mentions to links
            formatted = formatted.replace(/@(\w+)/g, '<span class="mention">@$1</span>');
            
            return formatted;
        }
        
        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffTime = Math.abs(now - date);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays === 1) {
                return 'Сегодня в ' + date.toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
            } else if (diffDays === 2) {
                return 'Вчера в ' + date.toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
            } else if (diffDays <= 7) {
                return diffDays + ' дня назад';
            } else {
                return date.toLocaleDateString('ru-RU', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
        }
        
    })();
    </script>
    
    <!-- Add animations -->
    <style>
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    </style>
    
    <?php
}
?>