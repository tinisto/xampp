<?php
/**
 * Filters Dropdown Component - Fixed version
 * Displays a dropdown for sorting/filtering options
 */

function renderFiltersDropdown($options = []) {
    // Default options
    $sortOptions = isset($options['sortOptions']) ? $options['sortOptions'] : [
        'date_desc' => 'По дате (новые)',
        'date_asc' => 'По дате (старые)',
        'popular' => 'По популярности'
    ];
    $paramName = isset($options['paramName']) ? $options['paramName'] : 'sort';
    $label = isset($options['label']) ? $options['label'] : 'Сортировка:';
    
    // Get current sort value
    $currentSort = isset($_GET[$paramName]) ? $_GET[$paramName] : 'date_desc';
    
    ?>
    <div style="display: inline-flex; align-items: center; gap: 10px;">
        <label style="color: #666; font-size: 14px;"><?php echo htmlspecialchars($label); ?></label>
        <select name="<?php echo htmlspecialchars($paramName); ?>" 
                onchange="updateSort(this.value)"
                style="padding: 8px 15px; border: 1px solid #ddd; border-radius: 20px; font-size: 14px; background: white; cursor: pointer; outline: none; transition: all 0.3s ease;"
                onfocus="this.style.borderColor='#0066cc';"
                onblur="this.style.borderColor='#ddd';">
            <?php foreach ($sortOptions as $value => $text): ?>
                <option value="<?php echo htmlspecialchars($value); ?>" 
                        <?php echo $currentSort == $value ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($text); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <script>
    function updateSort(value) {
        // Get current URL
        var url = new URL(window.location.href);
        
        // Update sort parameter
        url.searchParams.set('<?php echo htmlspecialchars($paramName); ?>', value);
        
        // Reset to page 1 when sorting changes
        url.searchParams.set('page', '1');
        
        // Redirect to new URL
        window.location.href = url.toString();
    }
    </script>
    <?php
}
?>