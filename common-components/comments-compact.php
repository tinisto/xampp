<?php
/**
 * Compact Comments Component
 * 
 * Minimal, space-efficient comment system
 * 
 * Usage:
 * include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/comments-compact.php';
 * renderCompactComments($entityType, $entityId, $options);
 */

// Include required components
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/button.php';

function renderCompactComments($entityType, $entityId, $options = []) {
    global $connection;
    
    // Start session if not started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Default options
    $defaults = [
        'showTitle' => true,
        'showStats' => true,
        'collapsed' => true,
        'maxPreviewComments' => 3
    ];
    
    $options = array_merge($defaults, $options);
    
    // Get comments count
    $commentCount = 0;
    if (isset($connection)) {
        try {
            $stmt = $connection->prepare("SELECT COUNT(*) as total FROM comments WHERE entity_type = ? AND entity_id = ?");
            $stmt->bind_param("ss", $entityType, $entityId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $commentCount = (int)$row['total'];
            }
            $stmt->close();
        } catch (Exception $e) {
            $commentCount = 0;
        }
    }
    
    $commentsId = 'comments-' . uniqid();
    ?>
    
    <div class="compact-comments-component" id="<?= htmlspecialchars($commentsId) ?>">
        <!-- Comments Header -->
        <div class="comments-header" onclick="toggleComments('<?= htmlspecialchars($commentsId) ?>')" 
             style="display: flex; align-items: center; justify-content: space-between; padding: 15px 20px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
            
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-comments" style="color: #6c757d; font-size: 18px;"></i>
                <?php if ($options['showTitle']): ?>
                    <span style="font-weight: 600; color: #495057;">Обсуждение</span>
                <?php endif; ?>
                <?php if ($options['showStats']): ?>
                    <span style="color: #6c757d; font-size: 14px;">
                        <?= $commentCount ?> комментари<?= $commentCount == 1 ? 'й' : ($commentCount < 5 ? 'я' : 'ев') ?>
                    </span>
                <?php endif; ?>
            </div>
            
            <div style="display: flex; align-items: center; gap: 10px;">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span style="color: #28a745; font-size: 12px;">
                        <i class="fas fa-plus-circle"></i> Добавить
                    </span>
                <?php else: ?>
                    <span style="color: #6c757d; font-size: 12px;">
                        <i class="fas fa-sign-in-alt"></i> Войти
                    </span>
                <?php endif; ?>
                
                <i class="fas fa-chevron-down comments-toggle-icon" style="color: #6c757d; transition: transform 0.3s ease;"></i>
            </div>
        </div>
        
        <!-- Comments Body (Initially Hidden) -->
        <div class="comments-body" id="<?= htmlspecialchars($commentsId) ?>-body" 
             style="display: none; border: 1px solid #dee2e6; border-top: none; border-radius: 0 0 8px 8px; background: white;">
            
            <!-- Login Prompt or Comment Form -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Comment Form for Logged Users -->
                <div style="padding: 20px; border-bottom: 1px solid #f0f0f0;">
                    <form id="comment-form-<?= htmlspecialchars($commentsId) ?>" onsubmit="submitComment(event, '<?= htmlspecialchars($commentsId) ?>')">
                        <input type="hidden" name="entity_type" value="<?= htmlspecialchars($entityType) ?>">
                        <input type="hidden" name="entity_id" value="<?= htmlspecialchars($entityId) ?>">
                        
                        <div style="display: flex; gap: 12px; align-items: flex-start;">
                            <div style="width: 32px; height: 32px; background: #007bff; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; flex-shrink: 0;">
                                <?= htmlspecialchars(substr($_SESSION['name'] ?? 'U', 0, 1)) ?>
                            </div>
                            
                            <div style="flex: 1;">
                                <textarea name="comment_text" 
                                         placeholder="Написать комментарий..." 
                                         required
                                         style="width: 100%; min-height: 60px; padding: 8px 12px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px; resize: none; font-family: inherit;"
                                         maxlength="500"></textarea>
                                
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px;">
                                    <small style="color: #6c757d; font-size: 12px;">Максимум 500 символов</small>
                                    
                                    <?php renderButton('Отправить', '#', [
                                        'type' => 'primary',
                                        'size' => 'small',
                                        'onclick' => 'this.closest("form").submit()'
                                    ]); ?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <!-- Login Prompt -->
                <div style="padding: 20px; text-align: center; border-bottom: 1px solid #f0f0f0;">
                    <p style="color: #6c757d; margin-bottom: 15px; font-size: 14px;">Войдите, чтобы участвовать в обсуждении</p>
                    <?php 
                    renderButton('Войти', '/login', ['type' => 'primary', 'size' => 'small']);
                    echo ' ';
                    renderButton('Регистрация', '/registration', ['type' => 'secondary', 'size' => 'small']);
                    ?>
                </div>
            <?php endif; ?>
            
            <!-- Comments List -->
            <div id="comments-list-<?= htmlspecialchars($commentsId) ?>" style="max-height: 300px; overflow-y: auto;">
                <?php if ($commentCount == 0): ?>
                    <div style="padding: 30px; text-align: center; color: #6c757d;">
                        <i class="fas fa-comments" style="font-size: 32px; margin-bottom: 10px; opacity: 0.5;"></i>
                        <p style="margin: 0; font-size: 14px;">Пока нет комментариев</p>
                    </div>
                <?php else: ?>
                    <!-- Comments will be loaded here via AJAX -->
                    <div style="padding: 20px; color: #6c757d; text-align: center; font-size: 14px;">
                        <i class="fas fa-spinner fa-spin"></i> Загружаем комментарии...
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
    function toggleComments(commentsId) {
        const body = document.getElementById(commentsId + '-body');
        const icon = document.querySelector('#' + commentsId + ' .comments-toggle-icon');
        
        if (body.style.display === 'none') {
            body.style.display = 'block';
            icon.style.transform = 'rotate(180deg)';
            
            // Load comments if not already loaded
            loadComments(commentsId);
        } else {
            body.style.display = 'none';
            icon.style.transform = 'rotate(0deg)';
        }
    }
    
    function loadComments(commentsId) {
        const commentsList = document.getElementById('comments-list-' + commentsId);
        
        // Simple placeholder for now - in real implementation, this would be AJAX
        if (commentsList.innerHTML.includes('Загружаем комментарии')) {
            setTimeout(() => {
                commentsList.innerHTML = '<div style="padding: 20px; color: #6c757d; text-align: center; font-size: 14px;">Комментарии загружены</div>';
            }, 1000);
        }
    }
    
    function submitComment(event, commentsId) {
        event.preventDefault();
        
        const form = event.target;
        const textarea = form.querySelector('textarea');
        
        if (textarea.value.trim()) {
            // Simple success feedback - in real implementation, this would be AJAX
            textarea.value = '';
            alert('Комментарий отправлен!');
        }
    }
    </script>
    
    <style>
    .compact-comments-component .comments-header:hover {
        background: #e9ecef !important;
    }
    
    /* Dark mode support */
    [data-bs-theme="dark"] .compact-comments-component .comments-header {
        background: #2d2d2d !important;
        border-color: #444 !important;
        color: #e0e0e0 !important;
    }
    
    [data-bs-theme="dark"] .compact-comments-component .comments-body {
        background: #1a1a1a !important;
        border-color: #444 !important;
        color: #e0e0e0 !important;
    }
    
    [data-bs-theme="dark"] .compact-comments-component textarea {
        background: #2d2d2d !important;
        border-color: #444 !important;
        color: #e0e0e0 !important;
    }
    </style>
    
    <?php
}
?>