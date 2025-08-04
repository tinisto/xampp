<?php
/**
 * Modern Reusable Comments Component
 * Matches the design of the post/news content with professional styling
 */

// Include necessary functions
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/check-user-suspend.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/get_avatar.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/comments/comment_functions.php';

// Get the current URL for entity detection
$currentUrl = $_SERVER['REQUEST_URI'];

// Extract entity information from URL
preg_match('/\/school\/(\d+)/', $currentUrl, $schoolMatches);
preg_match('/\/vpo\/([\w-]+)/', $currentUrl, $universityMatches);
preg_match('/\/spo\/([\w-]+)/', $currentUrl, $collegeMatches);
preg_match('/\/post\/([\w-]+)/', $currentUrl, $postMatches);

// Determine entity type and ID
$entity_type = 'unknown';
$entity_id = null;

if (isset($schoolMatches[1])) {
    $entity_type = 'school';
    $entity_id = $schoolMatches[1];
} elseif (isset($universityMatches[1])) {
    $entity_type = 'vpo';
    $entity_id = $universityMatches[1];
} elseif (isset($collegeMatches[1])) {
    $entity_type = 'spo';
    $entity_id = $collegeMatches[1];
} elseif (isset($postMatches[1])) {
    $entity_type = 'post';
    $url_slug = $postMatches[1];
    
    // Get post ID directly from database
    $postQuery = "SELECT id_post FROM posts WHERE url_post = ?";
    $postStmt = $connection->prepare($postQuery);
    if ($postStmt) {
        $postStmt->bind_param("s", $url_slug);
        $postStmt->execute();
        $postResult = $postStmt->get_result();
        
        if ($postResult->num_rows > 0) {
            $post = $postResult->fetch_assoc();
            $entity_id = $post['id_post'];
        }
        $postStmt->close();
    }
}

// Get existing comments count
$commentsQuery = "SELECT COUNT(*) as count FROM comments WHERE id_entity = ? AND entity_type = ?";
$commentsStmt = $connection->prepare($commentsQuery);
$commentsStmt->bind_param("is", $entity_id, $entity_type);
$commentsStmt->execute();
$commentsResult = $commentsStmt->get_result();
$commentsCount = $commentsResult->fetch_assoc()['count'];
?>

<style>
:root {
    --comments-primary: #3b82f6;
    --comments-secondary: #6b7280;
    --comments-success: #10b981;
    --comments-border: #e5e7eb;
    --comments-light: #f9fafb;
    --comments-text: #1f2937;
    --comments-text-light: #6b7280;
    --comments-shadow: rgba(0, 0, 0, 0.1);
}

.comments-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px var(--comments-shadow);
    overflow: hidden;
    margin-top: 2rem;
}

.comments-header {
    background: linear-gradient(135deg, var(--comments-primary), #1d4ed8);
    color: white;
    padding: 1.5rem;
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
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><path d="M0,10 Q50,0 100,10 L100,20 L0,20 Z" fill="rgba(255,255,255,0.1)"/></svg>');
    opacity: 0.3;
}

.comments-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.comments-count {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
}

.comments-form {
    padding: 2rem;
    border-bottom: 1px solid var(--comments-border);
    background: var(--comments-light);
}

.comments-form-group {
    margin-bottom: 1.5rem;
}

.comments-textarea {
    width: 100%;
    min-height: 120px;
    padding: 1rem;
    border: 2px solid var(--comments-border);
    border-radius: 8px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 0.875rem;
    line-height: 1.5;
    resize: vertical;
    transition: all 0.2s ease;
    background: white;
}

.comments-textarea:focus {
    outline: none;
    border-color: var(--comments-primary);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.comments-textarea::placeholder {
    color: var(--comments-text-light);
}

.comments-char-count {
    text-align: right;
    font-size: 0.75rem;
    color: var(--comments-text-light);
    margin-top: 0.5rem;
}

.comments-char-count.warning {
    color: #f59e0b;
}

.comments-char-count.danger {
    color: #ef4444;
}

.comments-submit-btn {
    background: linear-gradient(135deg, var(--comments-success), #059669);
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
}

.comments-submit-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
}

.comments-submit-btn:active {
    transform: translateY(0);
}

.comments-login-message {
    background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
    border: 1px solid var(--comments-border);
    border-radius: 8px;
    padding: 1.5rem;
    text-align: center;
    color: var(--comments-text);
}

.comments-login-link {
    color: var(--comments-primary);
    text-decoration: none;
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    transition: all 0.2s ease;
    display: inline-block;
    margin-top: 0.5rem;
}

.comments-login-link:hover {
    background: var(--comments-primary);
    color: white;
    text-decoration: none;
}

.comments-list {
    padding: 0;
}

.comments-empty {
    padding: 3rem 2rem;
    text-align: center;
    color: var(--comments-text-light);
    font-style: italic;
    background: white;
}

.comment-item {
    border-bottom: 1px solid var(--comments-border);
    padding: 1.5rem 2rem;
    background: white;
    transition: background-color 0.2s ease;
}

.comment-item:hover {
    background: #fafbfc;
}

.comment-item:last-child {
    border-bottom: none;
}

.comment-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--comments-border);
    margin-right: 1rem;
}

.comment-content {
    flex: 1;
}

.comment-author {
    font-weight: 600;
    color: var(--comments-text);
    margin-bottom: 0.25rem;
}

.comment-text {
    color: var(--comments-text);
    line-height: 1.6;
    margin-bottom: 0.75rem;
}

.comment-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 0.75rem;
    color: var(--comments-text-light);
}

.comment-time {
    cursor: help;
}

.comment-reply-btn {
    background: none;
    border: none;
    color: var(--comments-primary);
    font-size: 0.75rem;
    font-weight: 500;
    cursor: pointer;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.comment-reply-btn:hover {
    background: rgba(59, 130, 246, 0.1);
}

@media (max-width: 768px) {
    .comments-form {
        padding: 1.5rem;
    }
    
    .comment-item {
        padding: 1rem 1.5rem;
    }
    
    .comments-header {
        padding: 1rem 1.5rem;
    }
}
</style>

<div class="comments-container">
    <!-- Comments Header -->
    <div class="comments-header">
        <h3>
            💬 Комментарии
            <?php if ($commentsCount > 0): ?>
                <span class="comments-count"><?= $commentsCount ?></span>
            <?php endif; ?>
        </h3>
    </div>

    <!-- Success Message -->
    <?php if (isset($_GET['comment_success']) && $_GET['comment_success'] == '1'): ?>
        <div id="commentSuccessMessage" style="
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 1rem 2rem;
            text-align: center;
            font-size: 0.875rem;
            font-weight: 500;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        ">
            ✅ Ваш комментарий успешно добавлен!
        </div>
    <?php endif; ?>

    <!-- Comments Form or Login Message -->
    <?php if (!isset($_SESSION['email'])): ?>
        <div class="comments-form">
            <div class="comments-login-message">
                <p style="margin: 0 0 0.5rem 0; font-size: 0.875rem;">
                    Чтобы добавить комментарий или задать вопрос
                </p>
                <a href="/login?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="comments-login-link">
                    Войти в аккаунт
                </a>
            </div>
        </div>
    <?php else: ?>
        <?php
        $userEmail = $_SESSION['email'];
        $isSuspended = getUserSuspensionStatus($userEmail);
        
        if ($isSuspended): ?>
            <div class="comments-form">
                <div class="comments-login-message" style="background: linear-gradient(135deg, #fef2f2, #fee2e2); border-color: #fecaca; color: #991b1b;">
                    <p style="margin: 0; font-size: 0.875rem;">
                        Ваш аккаунт заблокирован. Если у вас есть вопросы, свяжитесь с поддержкой: support@11klassniki.ru
                    </p>
                </div>
            </div>
        <?php else: ?>
            <!-- Comment Form -->
            <div class="comments-form">
                <form method="post" action="/comments/process_comments.php">
                    <input type="hidden" name="entity_type" value="<?= htmlspecialchars($entity_type) ?>">
                    <input type="hidden" name="id_entity" value="<?= htmlspecialchars($entity_id) ?>">
                    <input type="hidden" name="parent_id" value="0">
                    
                    <div class="comments-form-group">
                        <textarea 
                            name="comment" 
                            class="comments-textarea" 
                            placeholder="Введите ваш комментарий или вопрос..." 
                            maxlength="2000" 
                            required
                            oninput="updateCharCount(this)"
                            id="commentTextarea"
                        ></textarea>
                        <div class="comments-char-count" id="charCount">
                            Осталось символов: <span id="remainingChars">2000</span>
                        </div>
                    </div>
                    
                    <div style="min-height: 45px; display: flex; align-items: center;">
                        <button type="submit" class="comments-submit-btn" id="submitBtn" style="opacity: 0; visibility: hidden; transform: translateY(10px); transition: all 0.3s ease;">
                            Отправить комментарий
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Comments List -->
    <div class="comments-list">
        <?php if ($commentsCount > 0): ?>
            <?php include $_SERVER['DOCUMENT_ROOT'] . '/comments/load_comments_simple.php'; ?>
        <?php else: ?>
            <div class="comments-empty">
                Пока нет комментариев. Будьте первым!
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function updateCharCount(textarea) {
    const maxLength = 2000;
    const currentLength = textarea.value.length;
    const remaining = maxLength - currentLength;
    const remainingSpan = document.getElementById('remainingChars');
    const charCountDiv = document.getElementById('charCount');
    const submitBtn = document.getElementById('submitBtn');
    
    remainingSpan.textContent = remaining;
    
    // Update color based on remaining characters
    charCountDiv.className = 'comments-char-count';
    if (remaining < 100) {
        charCountDiv.classList.add('danger');
    } else if (remaining < 300) {
        charCountDiv.classList.add('warning');
    }
    
    // Show/hide submit button based on content
    if (currentLength > 0) {
        submitBtn.style.opacity = '1';
        submitBtn.style.visibility = 'visible';
        submitBtn.style.transform = 'translateY(0)';
    } else {
        submitBtn.style.opacity = '0';
        submitBtn.style.visibility = 'hidden';
        submitBtn.style.transform = 'translateY(10px)';
    }
}

// Auto-scroll to comments after successful submission
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('comment_success') === '1') {
        // Wait a bit for the page to fully load
        setTimeout(() => {
            const commentsSection = document.querySelector('.comments-container');
            if (commentsSection) {
                // Smooth scroll to comments section
                commentsSection.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
                
                // Optional: Add a subtle flash effect to highlight new content
                commentsSection.style.transition = 'box-shadow 0.5s ease';
                commentsSection.style.boxShadow = '0 0 20px rgba(59, 130, 246, 0.3)';
                
                setTimeout(() => {
                    commentsSection.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
                }, 2000);
                
                // Hide success message after 5 seconds
                const successMessage = document.getElementById('commentSuccessMessage');
                if (successMessage) {
                    setTimeout(() => {
                        successMessage.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                        successMessage.style.opacity = '0';
                        successMessage.style.transform = 'translateY(-10px)';
                        setTimeout(() => {
                            successMessage.style.display = 'none';
                        }, 500);
                    }, 4000);
                }
                
                // Clean URL by removing the success parameter after delay
                setTimeout(() => {
                    const url = new URL(window.location);
                    url.searchParams.delete('comment_success');
                    window.history.replaceState({}, document.title, url.toString());
                }, 5000);
            }
        }, 500);
    }
});
</script>