<?php
/**
 * Reusable Badge Component
 * 
 * Usage:
 * include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/badge.php';
 * renderBadge('Badge Text', ['type' => 'primary', 'size' => 'small']);
 */

function renderBadge($text, $options = []) {
    // Default options
    $type = $options['type'] ?? 'primary';
    $size = $options['size'] ?? 'medium';
    $position = $options['position'] ?? 'top-left';
    $icon = $options['icon'] ?? '';
    $customColor = $options['customColor'] ?? '';
    $customBgColor = $options['customBgColor'] ?? '';
    $rounded = $options['rounded'] ?? true;
    
    // Badge type colors
    $colors = [
        'primary' => ['bg' => '#007bff', 'text' => '#ffffff'],
        'secondary' => ['bg' => '#6c757d', 'text' => '#ffffff'],
        'success' => ['bg' => '#28a745', 'text' => '#ffffff'],
        'danger' => ['bg' => '#dc3545', 'text' => '#ffffff'],
        'warning' => ['bg' => '#ffc107', 'text' => '#212529'],
        'info' => ['bg' => '#17a2b8', 'text' => '#ffffff'],
        'light' => ['bg' => '#f8f9fa', 'text' => '#495057'],
        'dark' => ['bg' => '#343a40', 'text' => '#ffffff'],
        'category' => ['bg' => 'rgba(255,255,255,0.9)', 'text' => '#333'],
        'new' => ['bg' => '#28a745', 'text' => '#ffffff'],
        'hot' => ['bg' => '#ff4757', 'text' => '#ffffff'],
        'featured' => ['bg' => '#ffd700', 'text' => '#333']
    ];
    
    // Badge sizes
    $sizes = [
        'small' => ['padding' => '2px 6px', 'font' => '10px'],
        'medium' => ['padding' => '4px 8px', 'font' => '12px'],
        'large' => ['padding' => '6px 12px', 'font' => '14px']
    ];
    
    $colorSet = $colors[$type] ?? $colors['primary'];
    $sizeSet = $sizes[$size] ?? $sizes['medium'];
    
    // Override colors if custom colors provided
    $bgColor = $customBgColor ?: $colorSet['bg'];
    $textColor = $customColor ?: $colorSet['text'];
    
    $padding = $sizeSet['padding'];
    $fontSize = $sizeSet['font'];
    
    // Position styles
    $positionStyles = [
        'top-left' => 'position: absolute; top: 10px; left: 10px; z-index: 10;',
        'top-right' => 'position: absolute; top: 10px; right: 10px; z-index: 10;',
        'bottom-left' => 'position: absolute; bottom: 10px; left: 10px; z-index: 10;',
        'bottom-right' => 'position: absolute; bottom: 10px; right: 10px; z-index: 10;',
        'inline' => 'display: inline-block; vertical-align: middle;',
        'static' => ''
    ];
    
    $positionStyle = $positionStyles[$position] ?? $positionStyles['top-left'];
    
    // Build styles
    $styles = [
        'background' => $bgColor,
        'color' => $textColor,
        'padding' => $padding,
        'font-size' => $fontSize,
        'font-weight' => '500',
        'line-height' => '1',
        'white-space' => 'nowrap',
        'border-radius' => $rounded ? '12px' : '4px',
        'text-align' => 'center',
        'transition' => 'all 0.2s ease'
    ];
    
    $styleString = '';
    foreach ($styles as $prop => $value) {
        $styleString .= $prop . ': ' . $value . '; ';
    }
    $styleString .= $positionStyle;
    
    // Generate badge HTML
    echo '<span class="reusable-badge badge-' . htmlspecialchars($type) . '" style="' . $styleString . '">';
    
    // Icon if provided
    if ($icon) {
        echo '<i class="' . htmlspecialchars($icon) . '" style="margin-right: 4px;"></i>';
    }
    
    // Badge text
    echo htmlspecialchars($text);
    
    echo '</span>';
}

/**
 * Helper function to render category badges based on category data
 */
function renderCategoryBadge($categoryId, $categoryName = '', $options = []) {
    // Category color mapping - comprehensive list for articles/posts
    $categoryColors = [
        1 => ['type' => 'primary', 'name' => '11-классники'],
        2 => ['type' => 'success', 'name' => 'ЕГЭ'],
        3 => ['type' => 'info', 'name' => 'ОГЭ'],
        4 => ['type' => 'warning', 'name' => 'Профориентация'],
        5 => ['type' => 'danger', 'name' => 'Новости'],
        6 => ['type' => 'secondary', 'name' => 'Абитуриентам'],
        7 => ['type' => 'dark', 'name' => 'Тесты'],
        8 => ['type' => 'light', 'name' => 'Образование'],
        9 => ['type' => 'featured', 'name' => 'Статьи'],
        10 => ['type' => 'hot', 'name' => 'Актуально'],
        11 => ['type' => 'new', 'name' => 'Новое'],
        12 => ['type' => 'success', 'name' => 'Подготовка'],
        13 => ['type' => 'info', 'name' => 'Советы'],
        14 => ['type' => 'warning', 'name' => 'Важное'],
        15 => ['type' => 'primary', 'name' => 'Обучение']
    ];
    
    $category = $categoryColors[$categoryId] ?? ['type' => 'light', 'name' => $categoryName ?: 'Общее'];
    
    $badgeOptions = array_merge([
        'type' => $category['type'],
        'size' => 'small',
        'position' => 'top-left'
    ], $options);
    
    renderBadge($category['name'], $badgeOptions);
}

// Include CSS only once
if (!defined('REUSABLE_BADGE_CSS_INCLUDED')) {
    define('REUSABLE_BADGE_CSS_INCLUDED', true);
    ?>
    <style>
        /* Reusable Badge Component Styles */
        .reusable-badge {
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
        }
        
        /* Hover effects for interactive badges */
        .reusable-badge:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        /* Dark mode support */
        [data-bs-theme="dark"] .badge-light {
            background: #495057 !important;
            color: #f8f9fa !important;
        }
        
        [data-bs-theme="dark"] .badge-category {
            background: rgba(255,255,255,0.2) !important;
            color: #f8f9fa !important;
        }
        
        /* Animation for new badges */
        .badge-new {
            animation: pulse-badge 2s infinite;
        }
        
        @keyframes pulse-badge {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
        
        /* Hot badge with glow effect */
        .badge-hot {
            box-shadow: 0 0 10px rgba(255, 71, 87, 0.5);
        }
        
        /* Featured badge with gold shine */
        .badge-featured {
            background: linear-gradient(45deg, #ffd700, #ffed4e) !important;
            box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
        }
    </style>
    <?php
}
?>