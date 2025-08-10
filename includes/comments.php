<?php
// Comments component
// Usage: include_comments('news', $newsId);

function include_comments($itemType, $itemId) {
    // Fetch comments
    $comments = db_fetch_all("
        SELECT c.*, u.name as user_name
        FROM comments c
        JOIN users u ON c.user_id = u.id
        WHERE c.item_type = ? AND c.item_id = ? AND c.is_approved = 1
        ORDER BY c.created_at DESC
    ", [$itemType, $itemId]);
    
    // Count comments
    $commentCount = count($comments);
    ?>
    
    <div id="comments-section" style="margin-top: 60px; padding-top: 40px; border-top: 2px solid #e9ecef;">
        <h3 style="font-size: 24px; font-weight: 600; margin-bottom: 30px;">
            Комментарии (<?= $commentCount ?>)
        </h3>
        
        <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Comment form -->
        <div style="background: #f8f9fa; padding: 25px; border-radius: 12px; margin-bottom: 30px;">
            <form id="comment-form" onsubmit="postComment(event)">
                <input type="hidden" name="item_type" value="<?= htmlspecialchars($itemType) ?>">
                <input type="hidden" name="item_id" value="<?= htmlspecialchars($itemId) ?>">
                
                <div style="margin-bottom: 15px;">
                    <textarea name="comment_text" 
                              placeholder="Написать комментарий..." 
                              required
                              style="width: 100%; min-height: 100px; padding: 12px; border: 1px solid #dee2e6; 
                                     border-radius: 8px; font-size: 16px; resize: vertical;"></textarea>
                </div>
                
                <button type="submit" 
                        style="background: #007bff; color: white; border: none; padding: 10px 24px; 
                               border-radius: 8px; font-size: 16px; font-weight: 500; cursor: pointer;">
                    <i class="fas fa-paper-plane"></i> Отправить
                </button>
            </form>
        </div>
        <?php else: ?>
        <div style="background: #e3f2fd; padding: 20px; border-radius: 8px; margin-bottom: 30px; text-align: center;">
            <p style="margin: 0; color: #1976d2;">
                <a href="/login" style="color: #1976d2; font-weight: 600;">Войдите</a>, 
                чтобы оставлять комментарии
            </p>
        </div>
        <?php endif; ?>
        
        <!-- Comments list -->
        <div id="comments-list">
            <?php if (empty($comments)): ?>
            <div style="text-align: center; padding: 40px; color: #999;">
                <i class="far fa-comments" style="font-size: 48px; margin-bottom: 20px;"></i>
                <p>Пока нет комментариев. Будьте первым!</p>
            </div>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                <div class="comment" style="margin-bottom: 25px; padding: 20px; background: white; 
                                           border: 1px solid #e9ecef; border-radius: 8px;">
                    <div style="display: flex; gap: 15px;">
                        <div style="width: 48px; height: 48px; background: #e9ecef; border-radius: 50%; 
                                    display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-user" style="color: #6c757d;"></i>
                        </div>
                        
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                <strong style="color: #333;"><?= htmlspecialchars($comment['user_name']) ?></strong>
                                <span style="color: #999; font-size: 14px;">
                                    <?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?>
                                </span>
                                
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']): ?>
                                <button onclick="deleteComment(<?= $comment['id'] ?>)" 
                                        style="margin-left: auto; background: none; border: none; 
                                               color: #dc3545; cursor: pointer; font-size: 14px;">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                            
                            <div style="color: #555; line-height: 1.6;">
                                <?= nl2br(htmlspecialchars($comment['comment_text'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
    function postComment(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        
        fetch('/api/comments/add', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.error || 'Ошибка при добавлении комментария');
            }
        })
        .catch(error => {
            alert('Произошла ошибка');
        });
    }
    
    function deleteComment(commentId) {
        if (confirm('Удалить комментарий?')) {
            fetch('/api/comments/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: commentId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    }
    </script>
    <?php
}
?>