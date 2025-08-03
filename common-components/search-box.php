<?php
/**
 * Reusable Search Box Component
 * 
 * Usage:
 * include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/search-box.php';
 * renderSearchBox([
 *     'id' => 'mySearch',
 *     'placeholder' => 'Поиск...',
 *     'action' => '/search',
 *     'method' => 'GET',
 *     'inputName' => 'q',
 *     'size' => 'large', // 'small', 'medium', 'large'
 *     'showButton' => true,
 *     'autofocus' => false,
 *     'value' => $_GET['q'] ?? ''
 * ]);
 */

function renderSearchBox($options = []) {
    // Default options
    $defaults = [
        'id' => 'searchBox',
        'placeholder' => 'Поиск...',
        'action' => '/search',
        'method' => 'GET',
        'inputName' => 'q',
        'size' => 'medium',
        'showButton' => true,
        'autofocus' => false,
        'value' => '',
        'onSubmit' => null,
        'onChange' => null
    ];
    
    $options = array_merge($defaults, $options);
    
    // Size classes
    $sizeClasses = [
        'small' => 'search-box-small',
        'medium' => 'search-box-medium',
        'large' => 'search-box-large'
    ];
    
    $sizeClass = $sizeClasses[$options['size']] ?? $sizeClasses['medium'];
    ?>
    
    <!-- Search Box Component -->
    <form class="search-box-form <?= $sizeClass ?>" 
          action="<?= htmlspecialchars($options['action']) ?>" 
          method="<?= htmlspecialchars($options['method']) ?>"
          <?= $options['onSubmit'] ? 'onsubmit="' . htmlspecialchars($options['onSubmit']) . '"' : '' ?>>
        
        <div class="search-box-container">
            <input type="text" 
                   id="<?= htmlspecialchars($options['id']) ?>" 
                   name="<?= htmlspecialchars($options['inputName']) ?>"
                   class="search-box-input" 
                   placeholder="<?= htmlspecialchars($options['placeholder']) ?>"
                   value="<?= htmlspecialchars($options['value']) ?>"
                   <?= $options['autofocus'] ? 'autofocus' : '' ?>
                   <?= $options['onChange'] ? 'onchange="' . htmlspecialchars($options['onChange']) . '"' : '' ?>>
            
            <button type="button" 
                    class="search-box-clear" 
                    id="<?= htmlspecialchars($options['id']) ?>Clear"
                    aria-label="Очистить поиск">
                <i class="fas fa-times"></i>
            </button>
            
            <?php if ($options['showButton']): ?>
                <button type="submit" 
                        class="search-box-button"
                        aria-label="Поиск">
                    <i class="fas fa-search"></i>
                </button>
            <?php endif; ?>
        </div>
    </form>
    
    <script>
    (function() {
        const searchInput = document.getElementById('<?= htmlspecialchars($options['id']) ?>');
        const clearButton = document.getElementById('<?= htmlspecialchars($options['id']) ?>Clear');
        
        if (searchInput && clearButton) {
            // Show/hide clear button based on input content
            function toggleClearButton() {
                if (searchInput.value.length > 0) {
                    clearButton.style.display = 'block';
                } else {
                    clearButton.style.display = 'none';
                }
            }
            
            // Handle input changes
            searchInput.addEventListener('input', toggleClearButton);
            searchInput.addEventListener('keyup', toggleClearButton);
            
            // Handle clear button click
            clearButton.addEventListener('click', function() {
                searchInput.value = '';
                toggleClearButton();
                searchInput.focus();
                
                // Trigger input event
                const event = new Event('input', { bubbles: true });
                searchInput.dispatchEvent(event);
                
                // Also trigger change event
                const changeEvent = new Event('change', { bubbles: true });
                searchInput.dispatchEvent(changeEvent);
            });
            
            // Initial check
            toggleClearButton();
        }
    })();
    </script>
    <?php
}

// Include CSS only once
if (!defined('SEARCH_BOX_CSS_INCLUDED')) {
    define('SEARCH_BOX_CSS_INCLUDED', true);
    ?>
    <style>
        /* Search Box Styles */
        .search-box-form {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .search-box-container {
            position: relative;
            display: flex;
            align-items: center;
            background: white;
            border-radius: 50px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: box-shadow 0.3s ease;
        }
        
        .search-box-container:focus-within {
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }
        
        .search-box-input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            font-size: 16px;
            padding: 15px 50px 15px 25px;
            border-radius: 50px;
            width: 100%;
            color: #333; /* Fix white text on white background */
        }
        
        .search-box-input::placeholder {
            color: #999;
        }
        
        .search-box-clear {
            position: absolute;
            right: 60px;
            background: none;
            border: none;
            font-size: 18px;
            color: #999;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.2s ease;
            display: none;
            z-index: 2;
        }
        
        /* Removed hover effect for close button */
        
        .search-box-button {
            background: #28a745;
            border: none;
            color: white;
            font-size: 18px;
            padding: 15px 25px;
            border-radius: 0 50px 50px 0;
            cursor: pointer;
            transition: background 0.3s ease;
            margin-right: -1px;
        }
        
        .search-box-button:hover {
            background: #218838;
        }
        
        /* When no button, adjust clear button position */
        .search-box-form:not(:has(.search-box-button)) .search-box-clear {
            right: 20px;
        }
        
        /* Size variations */
        .search-box-small .search-box-input {
            font-size: 14px;
            padding: 10px 40px 10px 20px;
        }
        
        .search-box-small .search-box-clear {
            right: 45px;
            font-size: 16px;
            padding: 6px;
        }
        
        .search-box-small .search-box-button {
            font-size: 16px;
            padding: 10px 20px;
        }
        
        .search-box-small:not(:has(.search-box-button)) .search-box-clear {
            right: 15px;
        }
        
        .search-box-large .search-box-input {
            font-size: 16px;
            padding: 14px 50px 14px 25px;
        }
        
        .search-box-large .search-box-clear {
            right: 60px;
            font-size: 18px;
            padding: 8px;
        }
        
        .search-box-large .search-box-button {
            font-size: 18px;
            padding: 14px 25px;
        }
        
        .search-box-large:not(:has(.search-box-button)) .search-box-clear {
            right: 25px;
        }
        
        /* Dark mode styles */
        [data-bs-theme="dark"] .search-box-container {
            background: #2d3748;
            border-color: #4a5568;
        }
        [data-bs-theme="dark"] .search-box-container:focus-within {
            box-shadow: 0 4px 16px rgba(0,0,0,0.3);
            border-color: #28a745;
        }
        [data-bs-theme="dark"] .search-box-input {
            color: #e4e6eb;
        }
        [data-bs-theme="dark"] .search-box-input::placeholder {
            color: #a0aec0;
        }
        [data-bs-theme="dark"] .search-box-clear {
            color: #a0aec0;
        }
        [data-bs-theme="dark"] .search-box-clear:hover {
            color: #e4e6eb;
        }
        [data-bs-theme="dark"] .search-box-button {
            color: #e4e6eb;
        }
        [data-bs-theme="dark"] .search-box-button:hover {
            background: rgba(255,255,255,0.1);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .search-box-input {
                font-size: 14px;
                padding: 12px 45px 12px 20px;
            }
            
            .search-box-button {
                padding: 12px 20px;
                font-size: 16px;
            }
            
            .search-box-clear {
                right: 50px;
                font-size: 16px;
            }
            
            .search-box-form:not(:has(.search-box-button)) .search-box-clear {
                right: 15px;
            }
        }
    </style>
    <?php
}
?>