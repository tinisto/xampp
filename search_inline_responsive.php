<?php
/**
 * Inline Search Component
 * Self-contained - does not include real_components.php
 */

if (!function_exists('renderSearchInline')) {
    function renderSearchInline($options = []) {
        $placeholder = $options['placeholder'] ?? 'Поиск...';
        $buttonText = $options['buttonText'] ?? 'Найти';
        $action = $options['action'] ?? '/search';
        $method = $options['method'] ?? 'get';
        $paramName = $options['paramName'] ?? 'q';
        $width = $options['width'] ?? '300px';
        $value = $options['value'] ?? '';
        
        ?>
        <form action="<?= htmlspecialchars($action) ?>" method="<?= htmlspecialchars($method) ?>" class="search-inline-form" style="display: inline-flex; gap: 10px; align-items: center;">
            <input type="text" 
                   name="<?= htmlspecialchars($paramName) ?>" 
                   placeholder="<?= htmlspecialchars($placeholder) ?>" 
                   value="<?= htmlspecialchars($value) ?>"
                   class="form-control" 
                   style="width: <?= htmlspecialchars($width) ?>; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
            <button type="submit" class="btn btn-success" style="padding: 8px 20px; white-space: nowrap;">
                <?= htmlspecialchars($buttonText) ?>
            </button>
        </form>
        <?php
    }
}
?>