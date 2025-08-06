<?php
/**
 * Reusable Search Bar Component
 * 
 * A standalone search component that can be placed anywhere
 * 
 * Usage:
 * include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/search-bar.php';
 * 
 * renderSearchBar([
 *     'placeholder' => 'Поиск школ, вузов, статей...',
 *     'action' => '/search',
 *     'name' => 'query',
 *     'style' => 'default' // or 'compact', 'large'
 * ]);
 */

function renderSearchBar($config = []) {
    // Default configuration
    $defaults = [
        'placeholder' => 'Поиск...',
        'action' => '/search',
        'name' => 'query',
        'value' => $_GET[$config['name'] ?? 'query'] ?? '',
        'style' => 'default', // default, compact, large
        'background' => 'white', // white, green, transparent
        'width' => '600px'
    ];
    
    // Merge with provided config
    $config = array_merge($defaults, $config);
    
    // Generate unique ID for this instance
    $instanceId = 'search_' . uniqid();
    ?>
    
    <div class="search-bar-component search-style-<?php echo $config['style']; ?>" id="<?php echo $instanceId; ?>">
        <form class="search-bar-form" action="<?php echo htmlspecialchars($config['action']); ?>" method="get">
            <div class="search-bar-wrapper">
                <input 
                    type="text" 
                    name="<?php echo htmlspecialchars($config['name']); ?>" 
                    placeholder="<?php echo htmlspecialchars($config['placeholder']); ?>"
                    value="<?php echo htmlspecialchars($config['value']); ?>"
                    class="search-bar-input"
                    id="search-input-<?php echo $instanceId; ?>"
                >
                <button type="button" class="search-bar-clear" id="clear-btn-<?php echo $instanceId; ?>" style="display: none;" title="Очистить поиск">
                    ✕
                </button>
            </div>
        </form>
    </div>
    
    <script>
    // Search bar functionality for instance <?php echo $instanceId; ?>
    (function() {
        const searchInput = document.getElementById('search-input-<?php echo $instanceId; ?>');
        const clearBtn = document.getElementById('clear-btn-<?php echo $instanceId; ?>');
        const form = searchInput ? searchInput.closest('form') : null;
        
        // Search bar initialized
        
        if (!searchInput || !clearBtn) {
            return;
        }
        
        function toggleClearButton() {
            if (searchInput.value.length > 0) {
                clearBtn.style.display = 'flex';
            } else {
                clearBtn.style.display = 'none';
            }
        }
        
        // Show/hide clear button on input
        searchInput.addEventListener('input', toggleClearButton);
        
        // Clear input when X is clicked
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            clearBtn.style.display = 'none';
            searchInput.focus();
        });
        
        // Submit form on Enter key
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (searchInput.value.trim().length > 0) {
                    form.submit();
                }
            }
        });
        
        // Initialize clear button state
        toggleClearButton();
    })();
    </script>
    
    <?php
}

// Include CSS only once
if (!defined('SEARCH_BAR_CSS_INCLUDED')) {
    define('SEARCH_BAR_CSS_INCLUDED', true);
    ?>
    <style>
        /* Search Bar Component Styles */
        .search-bar-component {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px 0;
        }
        
        .search-bar-form {
            width: 100%;
        }
        
        .search-bar-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            background: white;
            border-radius: 50px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }
        
        .search-bar-wrapper:focus-within {
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.15);
        }
        
        .search-bar-input {
            flex: 1;
            border: none;
            padding: 16px 50px 16px 24px;
            font-size: 16px;
            background: transparent;
            color: #333;
            outline: none;
        }
        
        .search-bar-input::placeholder {
            color: #6c757d;
        }
        
        .search-bar-clear {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 32px;
            height: 32px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .search-bar-clear:hover {
            background: #c82333;
            transform: translateY(-50%) scale(1.1);
        }
        
        .search-bar-clear {
            font-size: 18px;
            font-weight: bold;
            line-height: 1;
        }
        
        /* Style Variations */
        
        /* Compact Style */
        .search-style-compact {
            padding: 10px 0;
        }
        
        .search-style-compact .search-bar-input {
            padding: 12px 40px 12px 20px;
            font-size: 14px;
        }
        
        .search-style-compact .search-bar-clear {
            right: 12px;
            width: 28px;
            height: 28px;
        }
        
        .search-style-compact .search-bar-clear i {
            font-size: 12px;
        }
        
        /* Large Style */
        .search-style-large .search-bar-input {
            padding: 20px 60px 20px 30px;
            font-size: 18px;
        }
        
        .search-style-large .search-bar-clear {
            right: 20px;
            width: 36px;
            height: 36px;
        }
        
        .search-style-large .search-bar-clear i {
            font-size: 16px;
        }
        
        /* Background on green */
        .search-on-green .search-bar-wrapper {
            background: rgba(255, 255, 255, 0.95);
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .search-bar-component {
                padding: 15px 20px;
                max-width: 100%;
            }
            
            .search-bar-wrapper {
                border-radius: 30px;
            }
            
            .search-bar-input {
                padding: 12px 40px 12px 20px;
                font-size: 14px;
            }
            
            .search-bar-clear {
                right: 12px;
                width: 28px;
                height: 28px;
            }
            
            .search-bar-clear i {
                font-size: 12px;
            }
        }
        
        @media (max-width: 480px) {
            .search-bar-component {
                padding: 10px 16px;
            }
            
            .search-bar-input {
                padding: 10px 35px 10px 16px;
                font-size: 14px;
            }
            
            .search-bar-clear {
                right: 10px;
                width: 24px;
                height: 24px;
            }
        }
        
        /* Dark mode support */
        [data-theme="dark"] .search-bar-wrapper {
            background: #2d3748;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        
        [data-theme="dark"] .search-bar-wrapper:focus-within {
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.4);
        }
        
        [data-theme="dark"] .search-bar-input {
            color: #e4e6eb;
        }
        
        [data-theme="dark"] .search-bar-input::placeholder {
            color: #a0aec0;
        }
        
        [data-theme="dark"] .search-bar-clear {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            color: #e4e6eb;
        }
        
        [data-theme="dark"] .search-bar-clear:hover {
            background: #dc3545;
            border-color: #dc3545;
            color: white;
        }
    </style>
    <?php
}
?>