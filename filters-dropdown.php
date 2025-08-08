<?php
/**
 * Filters Dropdown Component
 * Self-contained - does not include real_components.php
 */

if (!function_exists('renderFiltersDropdown')) {
    function renderFiltersDropdown($options = []) {
        $sortOptions = $options['sortOptions'] ?? [
            'date_desc' => 'По дате (новые)',
            'date_asc' => 'По дате (старые)',
            'popular' => 'По популярности'
        ];
        $currentSort = $options['currentSort'] ?? 'date_desc';
        $label = $options['label'] ?? 'Сортировать:';
        
        ?>
        <div class="filters-dropdown" style="display: inline-flex; align-items: center; gap: 10px;">
            <?php if ($label): ?>
                <label style="color: #666; font-size: 14px;"><?= htmlspecialchars($label) ?></label>
            <?php endif; ?>
            <select class="form-select" style="width: auto; padding: 6px 12px; border: 1px solid #ddd; border-radius: 4px; background: white; color: #333;" 
                    onchange="window.location.href = updateQueryStringParameter(window.location.href, 'sort', this.value)">
                <?php foreach ($sortOptions as $value => $text): ?>
                    <option value="<?= htmlspecialchars($value) ?>" <?= $currentSort === $value ? 'selected' : '' ?>>
                        <?= htmlspecialchars($text) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <script>
        function updateQueryStringParameter(uri, key, value) {
            var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
            var separator = uri.indexOf('?') !== -1 ? "&" : "?";
            if (uri.match(re)) {
                return uri.replace(re, '$1' + key + "=" + value + '$2');
            } else {
                return uri + separator + key + "=" + value;
            }
        }
        </script>
        <?php
    }
}
?>