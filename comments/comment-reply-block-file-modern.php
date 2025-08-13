<div style="display: flex; gap: 1rem; align-items: center; margin-top: 0.5rem;">
    <a href="javascript:void(0);" 
       onclick="toggleReplyForm(<?php echo $comment['id']; ?>)" 
       style="color: var(--accent-primary); text-decoration: none; font-size: 0.875rem;">
        <i class="fas fa-reply"></i> Ответить
    </a>
    
    <?php if ($timestamp): ?>
        <span style="color: var(--text-muted); font-size: 0.75rem;">
            <?php echo getRelativeTime($timestamp); ?>
        </span>
    <?php endif; ?>
</div>