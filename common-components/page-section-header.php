<?php
/**
 * Reusable Page Section Header Component
 * 
 * A green-styled section header with optional search functionality
 * 
 * Usage:
 * include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/page-section-header.php';
 * 
 * renderPageSectionHeader([
 *     'title' => 'Поиск школ, вузов, статей',
 *     'showSearch' => true,
 *     'searchPlaceholder' => 'Поиск школ, вузов, статей...',
 *     'searchAction' => '/search',
 *     'searchName' => 'query'
 * ]);
 */

function renderPageSectionHeader($config = []) {
    // Default configuration
    $defaults = [
        'title' => '',
        'showSearch' => false,
        'searchPlaceholder' => 'Поиск...',
        'searchAction' => '/search',
        'searchName' => 'query',
        'searchValue' => $_GET[$config['searchName'] ?? 'query'] ?? ''
    ];
    
    // Merge with provided config
    $config = array_merge($defaults, $config);
    
    // Generate unique ID for this instance
    $instanceId = 'psh_' . uniqid();
    ?>
    
    <div class="page-section-header <?php echo $config['showSearch'] ? 'with-search' : 'no-search'; ?>" id="<?php echo $instanceId; ?>" style="<?php echo $config['showSearch'] ? 'padding-bottom: 60px !important;' : ''; ?>">
        <div class="page-section-header-container">
            <?php if (!empty($config['title'])): ?>
                <h1 class="page-section-title">
                    <?php echo htmlspecialchars($config['title']); ?>
                    <?php if (!empty($config['badge'])): ?>
                        <span class="page-section-badge"><?php echo htmlspecialchars($config['badge']); ?></span>
                    <?php endif; ?>
                </h1>
            <?php endif; ?>
            
            <?php if ($config['showSearch']): ?>
                <form class="page-section-search" action="<?php echo htmlspecialchars($config['searchAction']); ?>" method="get">
                    <div class="search-input-wrapper">
                        <input 
                            type="text" 
                            name="<?php echo htmlspecialchars($config['searchName']); ?>" 
                            placeholder="<?php echo htmlspecialchars($config['searchPlaceholder']); ?>"
                            value="<?php echo htmlspecialchars($config['searchValue']); ?>"
                            class="search-input"
                        >
                        <button type="submit" class="search-button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
    
    <?php
}

// Include CSS only once
if (!defined('PAGE_SECTION_HEADER_CSS_INCLUDED')) {
    define('PAGE_SECTION_HEADER_CSS_INCLUDED', true);
    ?>
    <style>
        /* Page Section Header Styles */
        .page-section-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            padding: 30px 0; /* REDUCED: Smaller padding */
            margin: 0; /* FIXED: Remove all margins */
            position: relative;
            z-index: 10; /* ABOVE RED CONTENT WRAPPER */
            overflow: hidden;
            width: 100vw; /* FIXED: Force full viewport width */
            margin-left: calc(-50vw + 50%); /* FIXED: Break out of container */
        }
        
        .page-section-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 40%;
            height: 200%;
            background: rgba(255, 255, 255, 0.05);
            transform: rotate(35deg);
            pointer-events: none;
        }
        
        .page-section-header-container {
            max-width: none; /* FIXED: Remove width constraint for full width */
            margin: 0;
            padding: 0 40px; /* Increased padding for better spacing */
            position: relative;
            z-index: 1;
        }
        
        .page-section-title {
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0 0 20px 0;
            text-align: center;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .page-section-badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            font-size: 0.875rem;
            font-weight: 500;
            padding: 6px 16px;
            border-radius: 20px;
            display: inline-block;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            text-shadow: none;
            letter-spacing: 0.02em;
            transition: all 0.3s ease;
        }
        
        .page-section-badge:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .page-section-search {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .search-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            background: white;
            border-radius: 50px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }
        
        .search-input-wrapper:focus-within {
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.15);
        }
        
        .search-input {
            flex: 1;
            border: none;
            padding: 16px 24px;
            font-size: 16px;
            background: transparent;
            color: #333;
            outline: none;
        }
        
        .search-input::placeholder {
            color: #6c757d;
        }
        
        .search-button {
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
        
        .search-button:hover {
            background: #218838;
            transform: scale(1.05);
        }
        
        .search-button:active {
            transform: scale(0.95);
        }
        
        .search-button i {
            font-size: 18px;
        }
        
        /* When search is not shown, center the title better */
        .page-section-header.no-search .page-section-title {
            margin-bottom: 0;
        }
        
        /* When search IS shown, add extra bottom padding to push red content further down */
        .page-section-header.with-search {
            padding-bottom: 50px !important; /* REDUCED: Less padding when search present */
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .page-section-header {
                padding: 20px 0; /* REDUCED: Smaller mobile padding */
                margin-bottom: 20px;
            }
            
            .page-section-title {
                font-size: 1.75rem;
                margin-bottom: 16px;
            }
            
            .search-input-wrapper {
                border-radius: 30px;
            }
            
            .search-input {
                padding: 12px 20px;
                font-size: 14px;
            }
            
            .search-button {
                padding: 12px 20px;
            }
            
            .search-button i {
                font-size: 16px;
            }
        }
        
        @media (max-width: 480px) {
            .page-section-header {
                padding: 15px 0; /* REDUCED: Even smaller padding on very small screens */
                margin-bottom: 15px;
            }
            
            .page-section-title {
                font-size: 1.5rem;
            }
            
            .page-section-header-container {
                padding: 0 16px;
            }
        }
        
        /* Dark mode support */
        [data-theme="dark"] .page-section-header {
            background: linear-gradient(135deg, #1a5f2e 0%, #17735a 100%);
        }
        
        [data-theme="dark"] .search-input-wrapper {
            background: #2d3748;
        }
        
        [data-theme="dark"] .search-input {
            color: #e4e6eb;
        }
        
        [data-theme="dark"] .search-input::placeholder {
            color: #a0aec0;
        }
        
        [data-theme="dark"] .search-button {
            background: #28a745;
        }
        
        [data-theme="dark"] .search-button:hover {
            background: #22c55e;
        }
    </style>
    <?php
}
?>