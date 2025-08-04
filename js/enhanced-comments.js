/**
 * Enhanced Comments JavaScript
 * Handles interactive comment features
 */

class EnhancedComments {
    constructor(options = {}) {
        this.options = {
            apiUrl: '/api/comments.php',
            csrfToken: this.getCSRFToken(),
            autoRefresh: false,
            refreshInterval: 30000,
            ...options
        };
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        
        if (this.options.autoRefresh) {
            this.startAutoRefresh();
        }
    }
    
    bindEvents() {
        // Reaction buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('.comment-like-btn, .comment-dislike-btn')) {
                e.preventDefault();
                this.handleReaction(e.target);
            }
        });
        
        // Report buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('.comment-report-btn')) {
                e.preventDefault();
                this.showReportModal(e.target.dataset.commentId);
            }
        });
        
        // Pin buttons (admin only)
        document.addEventListener('click', (e) => {
            if (e.target.matches('.comment-pin-btn')) {
                e.preventDefault();
                this.handlePin(e.target);
            }
        });
        
        // Comment form submission
        const commentForm = document.getElementById('comment-form');
        if (commentForm) {
            commentForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitComment(commentForm);
            });
        }
        
        // Reply buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('.comment-reply-btn')) {
                e.preventDefault();
                this.showReplyForm(e.target.dataset.commentId);
            }
        });
        
        // Load more comments
        const loadMoreBtn = document.getElementById('load-more-comments');
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.loadMoreComments();
            });
        }
    }
    
    async handleReaction(button) {
        const commentId = button.dataset.commentId;
        const action = button.classList.contains('comment-like-btn') ? 'like' : 'dislike';
        
        if (!this.checkAuthentication()) {
            return;
        }
        
        // Disable button during request
        button.disabled = true;
        
        try {
            const response = await this.apiRequest('POST', {
                action: action,
                comment_id: commentId
            });
            
            if (response.success) {
                this.updateReactionUI(commentId, response);
            } else {
                this.showMessage(response.message, 'error');
            }
        } catch (error) {
            this.showMessage('Ошибка при обработке реакции', 'error');
        } finally {
            button.disabled = false;
        }
    }
    
    updateReactionUI(commentId, data) {
        const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
        if (!commentElement) return;
        
        // Update like count
        const likeCount = commentElement.querySelector('.like-count');
        if (likeCount) {
            likeCount.textContent = data.likes || 0;
        }
        
        // Update dislike count
        const dislikeCount = commentElement.querySelector('.dislike-count');
        if (dislikeCount) {
            dislikeCount.textContent = data.dislikes || 0;
        }
        
        // Update button states
        const likeBtn = commentElement.querySelector('.comment-like-btn');
        const dislikeBtn = commentElement.querySelector('.comment-dislike-btn');
        
        likeBtn.classList.toggle('active', data.user_reaction === 'like');
        dislikeBtn.classList.toggle('active', data.user_reaction === 'dislike');
    }
    
    showReportModal(commentId) {
        if (!this.checkAuthentication()) {
            return;
        }
        
        const modal = this.createReportModal(commentId);
        document.body.appendChild(modal);
        modal.style.display = 'block';
        
        // Focus on textarea
        modal.querySelector('textarea').focus();
    }
    
    createReportModal(commentId) {
        const modal = document.createElement('div');
        modal.className = 'comment-modal';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Пожаловаться на комментарий</h3>
                    <button type="button" class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="report-form">
                        <div class="form-group">
                            <label for="report-reason">Причина жалобы:</label>
                            <textarea id="report-reason" name="reason" rows="4" 
                                      placeholder="Опишите причину жалобы..." 
                                      maxlength="500" required></textarea>
                            <div class="char-count">0/500</div>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary modal-close">Отмена</button>
                            <button type="submit" class="btn btn-danger">Отправить жалобу</button>
                        </div>
                    </form>
                </div>
            </div>
        `;
        
        // Handle modal close
        modal.addEventListener('click', (e) => {
            if (e.target.matches('.modal-close') || e.target === modal) {
                modal.remove();
            }
        });
        
        // Handle character count
        const textarea = modal.querySelector('textarea');
        const charCount = modal.querySelector('.char-count');
        textarea.addEventListener('input', () => {
            charCount.textContent = `${textarea.value.length}/500`;
        });
        
        // Handle form submission
        const form = modal.querySelector('#report-form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const reason = textarea.value.trim();
            if (!reason) {
                this.showMessage('Введите причину жалобы', 'error');
                return;
            }
            
            try {
                const response = await this.apiRequest('POST', {
                    action: 'report',
                    comment_id: commentId,
                    reason: reason
                });
                
                if (response.success) {
                    this.showMessage(response.message, 'success');
                    modal.remove();
                } else {
                    this.showMessage(response.message, 'error');
                }
            } catch (error) {
                this.showMessage('Ошибка при отправке жалобы', 'error');
            }
        });
        
        return modal;
    }
    
    async handlePin(button) {
        const commentId = button.dataset.commentId;
        const isPinned = button.classList.contains('active');
        
        button.disabled = true;
        
        try {
            const response = await this.apiRequest('POST', {
                action: 'pin',
                comment_id: commentId,
                pinned: !isPinned
            });
            
            if (response.success) {
                button.classList.toggle('active', response.pinned);
                button.title = response.pinned ? 'Открепить комментарий' : 'Закрепить комментарий';
                this.showMessage(response.message, 'success');
                
                // Move pinned comment to top
                if (response.pinned) {
                    this.moveCommentToTop(commentId);
                }
            } else {
                this.showMessage(response.message, 'error');
            }
        } catch (error) {
            this.showMessage('Ошибка при изменении статуса закрепления', 'error');
        } finally {
            button.disabled = false;
        }
    }
    
    async submitComment(form) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const textarea = form.querySelector('textarea');
        
        if (!this.checkAuthentication()) {
            return;
        }
        
        // Validate content
        const content = textarea.value.trim();
        if (!content) {
            this.showMessage('Введите текст комментария', 'error');
            textarea.focus();
            return;
        }
        
        if (content.length > 2000) {
            this.showMessage('Комментарий слишком длинный (максимум 2000 символов)', 'error');
            return;
        }
        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Отправка...';
        
        try {
            const response = await this.apiRequest('POST', {
                action: 'create',
                post_id: formData.get('post_id'),
                parent_id: formData.get('parent_id') || null,
                content: content
            });
            
            if (response.success) {
                this.addCommentToList(response.comment);
                form.reset();
                this.showMessage(response.message, 'success');
                
                // Hide reply form if this was a reply
                const replyForm = form.closest('.reply-form');
                if (replyForm) {
                    replyForm.style.display = 'none';
                }
            } else {
                this.showMessage(response.message, 'error');
            }
        } catch (error) {
            this.showMessage('Ошибка при добавлении комментария', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Отправить';
        }
    }
    
    addCommentToList(comment) {
        const commentsList = document.getElementById('comments-list');
        if (!commentsList) return;
        
        const commentElement = this.createCommentElement(comment);
        
        if (comment.parent_id) {
            // Insert as reply
            const parentComment = commentsList.querySelector(`[data-comment-id="${comment.parent_id}"]`);
            if (parentComment) {
                let repliesContainer = parentComment.querySelector('.comment-replies');
                if (!repliesContainer) {
                    repliesContainer = document.createElement('div');
                    repliesContainer.className = 'comment-replies';
                    parentComment.appendChild(repliesContainer);
                }
                repliesContainer.appendChild(commentElement);
            }
        } else {
            // Insert as top-level comment
            commentsList.insertBefore(commentElement, commentsList.firstChild);
        }
        
        // Highlight new comment
        commentElement.classList.add('comment-new');
        setTimeout(() => {
            commentElement.classList.remove('comment-new');
        }, 3000);
    }
    
    createCommentElement(comment) {
        const div = document.createElement('div');
        div.className = 'comment-item';
        div.dataset.commentId = comment.id;
        
        div.innerHTML = `
            <div class="comment-header">
                <div class="comment-author">
                    ${comment.avatar ? `<img src="${comment.avatar}" alt="${comment.username}" class="comment-avatar">` : ''}
                    <span class="comment-username">${this.escapeHtml(comment.username)}</span>
                    ${comment.pinned ? '<span class="comment-pinned-badge">📌</span>' : ''}
                </div>
                <div class="comment-meta">
                    <span class="comment-time">${comment.created_at_formatted}</span>
                </div>
            </div>
            <div class="comment-content">
                ${comment.content_html}
            </div>
            <div class="comment-actions">
                <button type="button" class="comment-like-btn ${comment.user_reaction === 'like' ? 'active' : ''}" 
                        data-comment-id="${comment.id}">
                    👍 <span class="like-count">${comment.likes || 0}</span>
                </button>
                <button type="button" class="comment-dislike-btn ${comment.user_reaction === 'dislike' ? 'active' : ''}" 
                        data-comment-id="${comment.id}">
                    👎 <span class="dislike-count">${comment.dislikes || 0}</span>
                </button>
                <button type="button" class="comment-reply-btn" data-comment-id="${comment.id}">
                    Ответить
                </button>
                <button type="button" class="comment-report-btn" data-comment-id="${comment.id}">
                    Пожаловаться
                </button>
            </div>
        `;
        
        return div;
    }
    
    showReplyForm(commentId) {
        if (!this.checkAuthentication()) {
            return;
        }
        
        // Hide other reply forms
        document.querySelectorAll('.reply-form').forEach(form => {
            form.style.display = 'none';
        });
        
        const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
        if (!commentElement) return;
        
        let replyForm = commentElement.querySelector('.reply-form');
        if (!replyForm) {
            replyForm = this.createReplyForm(commentId);
            commentElement.appendChild(replyForm);
        }
        
        replyForm.style.display = 'block';
        replyForm.querySelector('textarea').focus();
    }
    
    createReplyForm(parentId) {
        const form = document.createElement('div');
        form.className = 'reply-form';
        form.innerHTML = `
            <form class="comment-form">
                <input type="hidden" name="post_id" value="${this.getPostId()}">
                <input type="hidden" name="parent_id" value="${parentId}">
                <input type="hidden" name="csrf_token" value="${this.options.csrfToken}">
                <div class="form-group">
                    <textarea name="content" placeholder="Написать ответ..." rows="3" maxlength="2000"></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary reply-cancel">Отмена</button>
                    <button type="submit" class="btn btn-primary">Ответить</button>
                </div>
            </form>
        `;
        
        // Handle cancel button
        form.querySelector('.reply-cancel').addEventListener('click', () => {
            form.style.display = 'none';
        });
        
        // Handle form submission
        form.querySelector('form').addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitComment(e.target);
        });
        
        return form;
    }
    
    async loadMoreComments() {
        const loadMoreBtn = document.getElementById('load-more-comments');
        const commentsList = document.getElementById('comments-list');
        
        if (!loadMoreBtn || !commentsList) return;
        
        const offset = commentsList.querySelectorAll('.comment-item').length;
        
        loadMoreBtn.disabled = true;
        loadMoreBtn.textContent = 'Загрузка...';
        
        try {
            const response = await this.apiRequest('GET', {
                action: 'get',
                post_id: this.getPostId(),
                offset: offset,
                limit: 20
            });
            
            if (response.success && response.comments.length > 0) {
                response.comments.forEach(comment => {
                    const commentElement = this.createCommentElement(comment);
                    commentsList.appendChild(commentElement);
                });
                
                if (response.comments.length < 20) {
                    loadMoreBtn.style.display = 'none';
                }
            } else {
                loadMoreBtn.style.display = 'none';
            }
        } catch (error) {
            this.showMessage('Ошибка при загрузке комментариев', 'error');
        } finally {
            loadMoreBtn.disabled = false;
            loadMoreBtn.textContent = 'Загрузить еще';
        }
    }
    
    moveCommentToTop(commentId) {
        const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
        const commentsList = document.getElementById('comments-list');
        
        if (commentElement && commentsList) {
            commentsList.insertBefore(commentElement, commentsList.firstChild);
            
            // Add pinned badge if not present
            if (!commentElement.querySelector('.comment-pinned-badge')) {
                const badge = document.createElement('span');
                badge.className = 'comment-pinned-badge';
                badge.textContent = '📌';
                commentElement.querySelector('.comment-author').appendChild(badge);
            }
        }
    }
    
    startAutoRefresh() {
        setInterval(() => {
            this.refreshComments();
        }, this.options.refreshInterval);
    }
    
    async refreshComments() {
        // Implement auto-refresh logic if needed
        // This would reload comments and update the display
    }
    
    async apiRequest(method, data) {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        };
        
        if (method === 'POST') {
            data.csrf_token = this.options.csrfToken;
            options.body = JSON.stringify(data);
        } else {
            const params = new URLSearchParams(data);
            this.options.apiUrl += '?' + params.toString();
        }
        
        const response = await fetch(this.options.apiUrl, options);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    }
    
    checkAuthentication() {
        if (!document.body.dataset.userId) {
            this.showMessage('Для выполнения этого действия необходимо войти в систему', 'error');
            return false;
        }
        return true;
    }
    
    showMessage(message, type = 'info') {
        // Remove existing messages
        document.querySelectorAll('.comment-message').forEach(msg => msg.remove());
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `comment-message comment-message-${type}`;
        messageDiv.textContent = message;
        
        const container = document.querySelector('.comments-container') || document.body;
        container.insertBefore(messageDiv, container.firstChild);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
    
    getCSRFToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }
    
    getPostId() {
        const meta = document.querySelector('meta[name="post-id"]');
        return meta ? meta.getAttribute('content') : '';
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.comments-container')) {
        new EnhancedComments();
    }
});