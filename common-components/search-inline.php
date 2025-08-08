<?php
/**
 * Search Inline Component - Fixed version
 * Displays an inline search form
 */

function renderSearchInline($options = []) {
    // Default options
    $placeholder = isset($options['placeholder']) ? $options['placeholder'] : 'Поиск...';
    $buttonText = isset($options['buttonText']) ? $options['buttonText'] : 'Найти';
    $paramName = isset($options['paramName']) ? $options['paramName'] : 'search';
    $width = isset($options['width']) ? $options['width'] : 'auto';
    $action = isset($options['action']) ? $options['action'] : '/search';
    
    // Get current search value
    $currentSearch = isset($_GET[$paramName]) ? htmlspecialchars($_GET[$paramName]) : '';
    
    ?>
    <form action="<?php echo htmlspecialchars($action); ?>" method="get" style="display: inline-flex; align-items: center; gap: 10px; width: <?php echo $width; ?>;">
        <div style="position: relative; flex: 1;">
            <input type="text" 
                   name="<?php echo htmlspecialchars($paramName); ?>" 
                   placeholder="<?php echo htmlspecialchars($placeholder); ?>"
                   value="<?php echo $currentSearch; ?>"
                   style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 25px; font-size: 14px; outline: none; transition: all 0.3s ease;"
                   onfocus="this.style.borderColor='#0066cc'; this.style.boxShadow='0 0 0 3px rgba(0,102,204,0.1)';"
                   onblur="this.style.borderColor='#ddd'; this.style.boxShadow='none';">
        </div>
        <button type="submit" 
                style="padding: 10px 20px; background: #0066cc; color: white; border: none; border-radius: 25px; font-size: 14px; cursor: pointer; transition: all 0.3s ease; white-space: nowrap;"
                onmouseover="this.style.background='#0052a3';"
                onmouseout="this.style.background='#0066cc';">
            <?php echo htmlspecialchars($buttonText); ?>
        </button>
    </form>
    <?php
}
?>