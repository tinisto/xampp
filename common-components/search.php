<?php
/**
 * Unified Search Component
 * 
 * Single reusable search component to replace all existing search implementations
 * Combines best features from search-inline.php, search-bar.php, and search-box.php
 * 
 * Usage:
 * include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/unified-search.php';
 * renderUnifiedSearch([
 *     'placeholder' => 'Поиск...',
 *     'action' => '/search',
 *     'name' => 'query',
 *     'value' => $_GET['query'] ?? '',
 *     'style' => 'default', // 'default', 'compact', 'large', 'dashboard'
 *     'showButton' => true,
 *     'buttonText' => 'Поиск',
 *     'width' => '100%',
 *     'autofocus' => false,
 *     'clearButton' => true
 * ]);
 */

function renderUnifiedSearch($config = []) {
    // Default configuration
    $defaults = [
        'placeholder' => 'Поиск...',
        'action' => '/search',
        'method' => 'GET',
        'name' => 'query',
        'value' => $_GET[$config['name'] ?? 'query'] ?? '',
        'style' => 'default', // default, compact, large, dashboard
        'showButton' => true,
        'buttonText' => 'Поиск',
        'width' => '100%',
        'maxWidth' => '600px',
        'autofocus' => false,
        'clearButton' => true,
        'id' => 'unified-search-' . uniqid()
    ];
    
    // Merge with provided config
    $config = array_merge($defaults, $config);
    
    // Style class mapping
    $styleClasses = [
        'default' => 'unified-search-default',
        'compact' => 'unified-search-compact', 
        'large' => 'unified-search-large',
        'dashboard' => 'unified-search-dashboard'
    ];
    
    $styleClass = $styleClasses[$config['style']] ?? $styleClasses['default'];
    ?>
    
    <div class="unified-search-component <?= $styleClass ?>" 
         style="width: <?= htmlspecialchars($config['width']) ?>; max-width: <?= htmlspecialchars($config['maxWidth']) ?>;">
        
        <form class="unified-search-form" 
              action="<?= htmlspecialchars($config['action']) ?>" 
              method="<?= htmlspecialchars($config['method']) ?>">
            
            <div class="unified-search-wrapper">
                <input type="text" 
                       id="<?= htmlspecialchars($config['id']) ?>" 
                       name="<?= htmlspecialchars($config['name']) ?>" 
                       class="unified-search-input"
                       placeholder="<?= htmlspecialchars($config['placeholder']) ?>"
                       value="<?= htmlspecialchars($config['value']) ?>"
                       <?= $config['autofocus'] ? 'autofocus' : '' ?>>
                
                <?php if ($config['clearButton']): ?>
                    <button type="button" 
                            class="unified-search-clear" 
                            id="<?= htmlspecialchars($config['id']) ?>-clear"
                            title="Очистить поиск"
                            style="display: none;">
                        ✕
                    </button>
                <?php endif; ?>
                
                <?php if ($config['showButton']): ?>
                    <button type="submit" 
                            class="unified-search-button"
                            title="Поиск">
                        <?= htmlspecialchars($config['buttonText']) ?>
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <script>
    // Unified Search Component JavaScript for <?= htmlspecialchars($config['id']) ?>
    (function() {
        const searchId = '<?= htmlspecialchars($config['id']) ?>';
        const searchInput = document.getElementById(searchId);
        const clearButton = document.getElementById(searchId + '-clear');
        const form = searchInput ? searchInput.closest('form') : null;
        
        if (!searchInput) return;
        
        // Toggle clear button visibility
        function toggleClearButton() {
            if (clearButton) {
                if (searchInput.value.length > 0) {
                    clearButton.style.display = 'block';
                } else {
                    clearButton.style.display = 'none';
                }
            }
        }
        
        // Handle input changes
        searchInput.addEventListener('input', toggleClearButton);
        searchInput.addEventListener('keyup', toggleClearButton);
        
        // Handle clear button click
        if (clearButton) {
            clearButton.addEventListener('click', function() {
                searchInput.value = '';
                toggleClearButton();
                searchInput.focus();
                
                // Trigger input and change events
                const inputEvent = new Event('input', { bubbles: true });
                const changeEvent = new Event('change', { bubbles: true });
                searchInput.dispatchEvent(inputEvent);
                searchInput.dispatchEvent(changeEvent);
            });
        }
        
        // Handle Enter key submission
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (searchInput.value.trim().length > 0 && form) {
                    form.submit();
                }
            }
        });
        
        // Initial state
        toggleClearButton();
    })();
    </script>
    <?php
}

// Include CSS only once
if (!defined('UNIFIED_SEARCH_CSS_INCLUDED')) {
    define('UNIFIED_SEARCH_CSS_INCLUDED', true);
    ?>
    <style>
        /* Unified Search Component Styles */
        .unified-search-component {
            margin: 0 auto;
        }
        
        .unified-search-form {
            width: 100%;
        }
        
        .unified-search-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            background: #ffffff;
            border-radius: 50px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .unified-search-wrapper:focus-within {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            transform: translateY(-1px);
        }
        
        .unified-search-input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            font-size: 16px;
            padding: 14px 20px;
            color: #333;
            width: 100%;
        }
        
        .unified-search-input::placeholder {
            color: #6c757d;
        }
        
        .unified-search-clear {
            position: absolute;
            right: 60px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            display: none;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 10;
        }
        
        .unified-search-clear:hover {
            background: #c82333;
            transform: scale(1.1);
        }
        
        .unified-search-button {
            background: #28a745;
            color: white;
            border: none;
            padding: 14px 24px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border-radius: 0 50px 50px 0;
            transition: all 0.3s ease;
            white-space: nowrap;
            margin-left: auto;
        }
        
        .unified-search-button:hover {
            background: #218838;
        }
        
        /* When no button, adjust clear button position */
        .unified-search-wrapper:not(:has(.unified-search-button)) .unified-search-clear {
            right: 20px;
        }
        
        /* Style Variations */
        
        /* Compact Style */
        .unified-search-compact .unified-search-input {
            font-size: 14px;
            padding: 10px 16px;
        }
        
        .unified-search-compact .unified-search-button {
            font-size: 13px;
            padding: 10px 20px;
        }
        
        .unified-search-compact .unified-search-clear {
            width: 24px;
            height: 24px;
            font-size: 12px;
            right: 50px;
        }
        
        .unified-search-compact .unified-search-wrapper:not(:has(.unified-search-button)) .unified-search-clear {
            right: 16px;
        }
        
        /* Large Style */
        .unified-search-large .unified-search-input {
            font-size: 18px;
            padding: 18px 24px;
        }
        
        .unified-search-large .unified-search-button {
            font-size: 16px;
            padding: 18px 30px;
        }
        
        .unified-search-large .unified-search-clear {
            width: 32px;
            height: 32px;
            font-size: 16px;
            right: 70px;
        }
        
        .unified-search-large .unified-search-wrapper:not(:has(.unified-search-button)) .unified-search-clear {
            right: 24px;
        }
        
        /* Dashboard Style */
        .unified-search-dashboard .unified-search-wrapper {
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }
        
        .unified-search-dashboard .unified-search-input {
            font-size: 14px;
            padding: 12px 16px;
        }
        
        .unified-search-dashboard .unified-search-button {
            border-radius: 0 7px 7px 0;
            font-size: 14px;
            padding: 12px 20px;
            background: #007bff;
        }
        
        .unified-search-dashboard .unified-search-button:hover {
            background: #0056b3;
        }
        
        .unified-search-dashboard .unified-search-clear {
            right: 50px;
            background: #6c757d;
        }
        
        .unified-search-dashboard .unified-search-clear:hover {
            background: #5a6268;
        }
        
        .unified-search-dashboard .unified-search-wrapper:not(:has(.unified-search-button)) .unified-search-clear {
            right: 16px;
        }
        
        /* Dark Mode Support */
        [data-theme="dark"] .unified-search-wrapper {
            background: var(--surface, #2d3748);
            border-color: var(--border-color, #4a5568);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.2);
        }
        
        [data-theme="dark"] .unified-search-wrapper:focus-within {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        }
        
        [data-theme="dark"] .unified-search-input {
            color: var(--text-primary, #e4e6eb);
        }
        
        [data-theme="dark"] .unified-search-input::placeholder {
            color: var(--text-secondary, #a0aec0);
        }
        
        [data-theme="dark"] .unified-search-button {
            background: var(--primary-color, #28a745);
            color: var(--surface, #ffffff);
        }
        
        [data-theme="dark"] .unified-search-button:hover {
            background: #218838;
        }
        
        [data-theme="dark"] .unified-search-dashboard .unified-search-wrapper {
            border-color: var(--border-color, #4a5568);
            background: var(--surface, #2d3748);
        }
        
        [data-theme="dark"] .unified-search-dashboard .unified-search-button {
            background: var(--primary-color, #007bff);
        }
        
        [data-theme="dark"] .unified-search-dashboard .unified-search-button:hover {
            background: #0056b3;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .unified-search-input {
                font-size: 16px !important; /* Prevents iOS zoom */
                padding: 12px 16px;
            }
            
            .unified-search-button {
                padding: 12px 20px;
                font-size: 14px;
            }
            
            .unified-search-clear {
                width: 26px;
                height: 26px;
                font-size: 13px;
                right: 50px;
            }
            
            .unified-search-wrapper:not(:has(.unified-search-button)) .unified-search-clear {
                right: 16px;
            }
            
            .unified-search-large .unified-search-input {
                font-size: 16px !important;
                padding: 14px 20px;
            }
        }
        
        @media (max-width: 480px) {
            .unified-search-component {
                margin: 0 10px;
            }
            
            .unified-search-input {
                padding: 10px 14px;
            }
            
            .unified-search-button {
                padding: 10px 16px;
                font-size: 13px;
            }
        }
        
        /* Focus and Accessibility */
        .unified-search-input:focus {
            outline: none;
        }
        
        .unified-search-clear:focus,
        .unified-search-button:focus {
            outline: 2px solid #007bff;
            outline-offset: 2px;
        }
        
        /* Animation for smooth interactions */
        .unified-search-clear {
            transition: all 0.2s ease;
        }
        
        .unified-search-clear.show {
            display: flex !important;
            opacity: 1;
        }
        
        .unified-search-clear.hide {
            opacity: 0;
            display: none !important;
        }
    </style>
    <?php
}
?>