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
                >
                <button type="submit" class="search-bar-button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
    
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
            padding: 16px 24px;
            font-size: 16px;
            background: transparent;
            color: #333;
            outline: none;
        }
        
        .search-bar-input::placeholder {
            color: #6c757d;
        }
        
        .search-bar-button {
            background: #28a745;
            border: none;
            padding: 16px 24px;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .search-bar-button:hover {
            background: #218838;
            transform: scale(1.05);
        }
        
        .search-bar-button:active {
            transform: scale(0.95);
        }
        
        .search-bar-button i {
            font-size: 18px;
        }
        
        /* Style Variations */
        
        /* Compact Style */
        .search-style-compact {
            padding: 10px 0;
        }
        
        .search-style-compact .search-bar-input {
            padding: 12px 20px;
            font-size: 14px;
        }
        
        .search-style-compact .search-bar-button {
            padding: 12px 20px;
        }
        
        .search-style-compact .search-bar-button i {
            font-size: 16px;
        }
        
        /* Large Style */
        .search-style-large .search-bar-input {
            padding: 20px 30px;
            font-size: 18px;
        }
        
        .search-style-large .search-bar-button {
            padding: 20px 30px;
        }
        
        .search-style-large .search-bar-button i {
            font-size: 20px;
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
                padding: 12px 20px;
                font-size: 14px;
            }
            
            .search-bar-button {
                padding: 12px 20px;
            }
            
            .search-bar-button i {
                font-size: 16px;
            }
        }
        
        @media (max-width: 480px) {
            .search-bar-component {
                padding: 10px 16px;
            }
            
            .search-bar-input {
                padding: 10px 16px;
                font-size: 14px;
            }
            
            .search-bar-button {
                padding: 10px 16px;
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
        
        [data-theme="dark"] .search-bar-button {
            background: #28a745;
        }
        
        [data-theme="dark"] .search-bar-button:hover {
            background: #22c55e;
        }
    </style>
    <?php
}
?>