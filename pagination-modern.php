<?php
/**
 * Modern Pagination Component
 * Self-contained - does not include real_components.php
 */

if (!function_exists('renderPaginationModern')) {
    function renderPaginationModern($currentPage = 1, $totalPages = 1, $baseUrl = '') {
        if ($totalPages <= 1) return;
        
        $range = 2; // Pages to show on each side of current
        ?>
        <nav class="pagination-modern" style="display: flex; justify-content: center; align-items: center; gap: 5px; margin: 30px 0;">
            <?php if ($currentPage > 1): ?>
                <a href="<?= htmlspecialchars($baseUrl) ?>?page=<?= $currentPage - 1 ?>" 
                   class="page-link" 
                   style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #333;">
                    ←
                </a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == 1 || $i == $totalPages || ($i >= $currentPage - $range && $i <= $currentPage + $range)): ?>
                    <a href="<?= htmlspecialchars($baseUrl) ?>?page=<?= $i ?>" 
                       class="page-link <?= $i == $currentPage ? 'active' : '' ?>"
                       style="padding: 8px 12px; border: 1px solid <?= $i == $currentPage ? '#28a745' : '#ddd' ?>; 
                              border-radius: 4px; text-decoration: none; 
                              color: <?= $i == $currentPage ? 'white' : '#333' ?>;
                              background: <?= $i == $currentPage ? '#28a745' : 'white' ?>;">
                        <?= $i ?>
                    </a>
                <?php elseif ($i == $currentPage - $range - 1 || $i == $currentPage + $range + 1): ?>
                    <span style="padding: 8px;">...</span>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php if ($currentPage < $totalPages): ?>
                <a href="<?= htmlspecialchars($baseUrl) ?>?page=<?= $currentPage + 1 ?>" 
                   class="page-link" 
                   style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #333;">
                    →
                </a>
            <?php endif; ?>
        </nav>
        <?php
    }
}
?>