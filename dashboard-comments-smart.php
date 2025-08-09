<?php
// Comments management dashboard - Smart loading version

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

// AJAX endpoint for loading comments
if (isset($_GET['ajax']) && $_GET['ajax'] === 'load_comments') {
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = 10; // Smaller chunks for better performance
    $offset = ($page - 1) * $limit;
    
    $search = $_GET['search'] ?? '';
    $searchCondition = '';
    if (!empty($search)) {
        $searchLike = '%' . $connection->real_escape_string($search) . '%';
        $searchCondition = "WHERE comment_text LIKE '$searchLike' OR author_of_comment LIKE '$searchLike'";
    }
    
    $query = "SELECT id, user_id, author_of_comment, comment_text, date, entity_type, entity_id
              FROM comments 
              $searchCondition
              ORDER BY date DESC 
              LIMIT $limit OFFSET $offset";
    
    $result = $connection->query($query);
    $comments = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'comments' => $comments,
        'has_more' => count($comments) === $limit,
        'next_page' => $page + 1
    ]);
    exit;
}

// Get total count for statistics only
$countQuery = "SELECT COUNT(*) as total FROM comments";
$countResult = $connection->query($countQuery);
$totalComments = $countResult->fetch_assoc()['total'];

// Get user info
$username = $_SESSION['first_name'] ?? $_SESSION['email'] ?? 'Admin';
$userInitial = strtoupper(mb_substr($username, 0, 1));

// Set dashboard title
$dashboardTitle = 'Управление комментариями';

// Build dashboard content
ob_start();
?>
<style>
.comments-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.search-section {
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
    width: 100%;
    max-width: 400px;
}

.stats-cards {
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

.comments-container {
    background: var(--surface);
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid var(--border-color);
    min-height: 400px;
}

.comments-list {
    padding: 0;
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

.comment-info {
    font-size: 12px;
    color: var(--text-secondary);
}

.comment-text {
    color: var(--text-primary);
    line-height: 1.5;
    margin-bottom: 12px;
    word-wrap: break-word;
}

.comment-actions {
    display: flex;
    gap: 8px;
}

.btn-delete {
    padding: 4px 8px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-delete:hover {
    background: #c82333;
}

.loading-indicator {
    text-align: center;
    padding: 40px 20px;
    color: var(--text-secondary);
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid var(--border-color);
    border-top: 3px solid var(--primary-color);
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
    padding: 20px;
    border-top: 1px solid var(--border-color);
}

.load-more-btn {
    padding: 12px 24px;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.load-more-btn:hover {
    background: #0056b3;
}

.load-more-btn:disabled {
    background: #6c757d;
    cursor: not-allowed;
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

.search-results-info {
    padding: 15px 20px;
    background: var(--bg-light);
    border-bottom: 1px solid var(--border-color);
    font-size: 14px;
    color: var(--text-secondary);
}

@media (max-width: 768px) {
    .comment-header {
        flex-direction: column;
        gap: 8px;
    }
    
    .comment-actions {
        align-self: flex-end;
    }
    
    .comments-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
}
</style>

<div class="comments-header">
    <h2>Управление комментариями</h2>
</div>

<!-- Search Section -->
<div class="search-section">
    <input type="text" id="searchInput" placeholder="Поиск по тексту комментария или автору..." class="search-input">
</div>

<!-- Statistics Cards -->
<div class="stats-cards">
    <div class="stat-card">
        <div class="stat-value"><?= number_format($totalComments) ?></div>
        <div class="stat-label">Всего комментариев</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" id="loadedCount">0</div>
        <div class="stat-label">Загружено</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" id="filteredCount">0</div>
        <div class="stat-label">Найдено по поиску</div>
    </div>
</div>

<!-- Comments Container -->
<div class="comments-container">
    <div id="searchResultsInfo" class="search-results-info" style="display: none;">
        Поиск: "<span id="searchTerm"></span>" - найдено <span id="searchCount">0</span> результатов
    </div>
    
    <div id="commentsList" class="comments-list">
        <!-- Comments will be loaded here dynamically -->
    </div>
    
    <div id="loadingIndicator" class="loading-indicator" style="display: none;">
        <div class="loading-spinner"></div>
        <div>Загружаем комментарии...</div>
    </div>
    
    <div id="loadMoreSection" class="load-more-section" style="display: none;">
        <button id="loadMoreBtn" class="load-more-btn">
            <i class="fas fa-chevron-down"></i>
            Загрузить еще
        </button>
    </div>
    
    <div id="emptyState" class="empty-state" style="display: none;">
        <i class="fas fa-comments"></i>
        <div>Комментарии не найдены</div>
        <p>Попробуйте изменить условия поиска</p>
    </div>
</div>

<script>
class CommentsManager {
    constructor() {
        this.currentPage = 1;
        this.hasMore = true;
        this.isLoading = false;
        this.searchTerm = '';
        this.loadedCount = 0;
        this.debounceTimeout = null;
        
        this.init();
    }
    
    init() {
        // Load initial comments
        this.loadComments();
        
        // Setup search
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', (e) => {
            clearTimeout(this.debounceTimeout);
            this.debounceTimeout = setTimeout(() => {
                this.search(e.target.value);
            }, 500); // 500ms debounce
        });
        
        // Setup load more button
        document.getElementById('loadMoreBtn').addEventListener('click', () => {
            this.loadMore();
        });
    }
    
    async loadComments(page = 1, search = '') {
        if (this.isLoading) return;
        
        this.isLoading = true;
        this.showLoading();
        
        try {
            const url = `?ajax=load_comments&page=${page}&search=${encodeURIComponent(search)}`;
            const response = await fetch(url);
            const data = await response.json();
            
            if (data.success) {
                if (page === 1) {
                    // New search or initial load
                    document.getElementById('commentsList').innerHTML = '';
                    this.loadedCount = 0;
                }
                
                this.renderComments(data.comments);
                this.hasMore = data.has_more;
                this.currentPage = data.next_page;
                this.loadedCount += data.comments.length;
                
                this.updateUI();
            }
        } catch (error) {
            console.error('Error loading comments:', error);
            this.showError('Ошибка загрузки комментариев');
        } finally {
            this.isLoading = false;
            this.hideLoading();
        }
    }
    
    async search(term) {
        this.searchTerm = term;
        this.currentPage = 1;
        this.hasMore = true;
        
        await this.loadComments(1, term);
        
        // Update search info
        if (term) {
            document.getElementById('searchTerm').textContent = term;
            document.getElementById('searchCount').textContent = this.loadedCount;
            document.getElementById('searchResultsInfo').style.display = 'block';
            document.getElementById('filteredCount').textContent = this.loadedCount;
        } else {
            document.getElementById('searchResultsInfo').style.display = 'none';
            document.getElementById('filteredCount').textContent = '0';
        }
    }
    
    async loadMore() {
        await this.loadComments(this.currentPage, this.searchTerm);
    }
    
    renderComments(comments) {
        const container = document.getElementById('commentsList');
        
        comments.forEach(comment => {
            const commentEl = this.createCommentElement(comment);
            container.appendChild(commentEl);
        });
    }
    
    createCommentElement(comment) {
        const div = document.createElement('div');
        div.className = 'comment-item';
        div.innerHTML = `
            <div class="comment-header">
                <div class="comment-meta">
                    <div class="comment-author">${this.escapeHtml(comment.author_of_comment || 'Аноним')}</div>
                    <div class="comment-date">${this.formatDate(comment.date)}</div>
                    <div class="comment-info">ID: ${comment.id} • ${comment.entity_type || 'N/A'}</div>
                </div>
                <div class="comment-actions">
                    <button onclick="commentsManager.deleteComment(${comment.id})" class="btn-delete" title="Удалить">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="comment-text">${this.escapeHtml(comment.comment_text)}</div>
        `;
        return div;
    }
    
    updateUI() {
        document.getElementById('loadedCount').textContent = this.loadedCount;
        
        // Show/hide load more button
        const loadMoreSection = document.getElementById('loadMoreSection');
        if (this.hasMore && this.loadedCount > 0) {
            loadMoreSection.style.display = 'block';
        } else {
            loadMoreSection.style.display = 'none';
        }
        
        // Show empty state if no comments
        const emptyState = document.getElementById('emptyState');
        if (this.loadedCount === 0 && !this.isLoading) {
            emptyState.style.display = 'block';
        } else {
            emptyState.style.display = 'none';
        }
    }
    
    showLoading() {
        document.getElementById('loadingIndicator').style.display = 'block';
    }
    
    hideLoading() {
        document.getElementById('loadingIndicator').style.display = 'none';
    }
    
    showError(message) {
        // Could implement a toast notification or error banner
        console.error(message);
    }
    
    deleteComment(commentId) {
        ModalManager.confirm('Удаление комментария', 'Вы уверены, что хотите удалить этот комментарий? Это действие нельзя отменить.', () => {
            window.location.href = `/api/comments/delete/${commentId}?redirect=/dashboard/comments`;
        }, 'danger');
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('ru-RU') + ' ' + date.toLocaleTimeString('ru-RU', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', () => {
    window.commentsManager = new CommentsManager();
});
</script>

<?php
$dashboardContent = ob_get_clean();

// Include the dashboard template
include $_SERVER['DOCUMENT_ROOT'] . '/dashboard-template.php';
?>