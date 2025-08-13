<form method="post" action="/comments/process_comments.php" enctype="multipart/form-data" 
      style="background: var(--bg-primary); padding: 1rem; border-radius: 12px; border: 1px solid var(--border-color); margin-top: 1rem;">
    <div style="display: flex; gap: 1rem; align-items: flex-start;">
        <!-- Avatar -->
        <div class="comment-avatar" style="flex-shrink: 0; width: 40px; height: 40px;">
            <?php
            if (isset($_SESSION['avatar'])) {
                $avatarUrl = $_SESSION['avatar'] ?? '';
                $avatarPath = getAvatar($avatarUrl);
                if ($avatarPath) {
                    echo '<img src="' . htmlspecialchars($avatarPath) . '" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">';
                } else {
                    // Show first letter of email if no avatar
                    $firstLetter = strtoupper(substr($_SESSION['email'] ?? 'U', 0, 1));
                    echo '<div style="width: 100%; height: 100%; background: var(--gradient); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">' . $firstLetter . '</div>';
                }
            }
            ?>
        </div>
        
        <!-- Reply Input -->
        <div style="flex: 1;">
            <textarea 
                name="comment" 
                rows="2"
                maxlength="2000" 
                placeholder="Введите ваш ответ..." 
                required
                style="background: var(--bg-secondary); border: 1px solid var(--border-color); 
                       color: var(--text-primary); border-radius: 8px; padding: 0.75rem; 
                       width: 100%; resize: vertical; font-family: inherit;"
            ></textarea>
            
            <input type="hidden" name="parent_id" value="<?php echo $comment['id']; ?>">
            <input type="hidden" name="id_entity" value="<?php echo $id_entity; ?>">
            <input type="hidden" name="entity_type" value="<?php echo $entity_type; ?>">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                <button type="button" 
                        onclick="toggleReplyForm(<?php echo $comment['id']; ?>)"
                        style="background: transparent; color: var(--text-secondary); border: 1px solid var(--border-color); 
                               padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer;">
                    Отмена
                </button>
                <button type="submit" 
                        style="background: var(--gradient); color: white; border: none; 
                               padding: 0.5rem 1.5rem; border-radius: 8px; cursor: pointer;">
                    Отправить
                </button>
            </div>
        </div>
    </div>
</form>