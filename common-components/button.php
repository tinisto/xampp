<?php
/**
 * Reusable Button Component
 * 
 * Usage:
 * include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/button.php';
 * renderButton('Button Text', '/url', ['type' => 'primary', 'size' => 'medium']);
 */

function renderButton($text, $href = '#', $options = []) {
    // Default options
    $type = $options['type'] ?? 'primary';
    $size = $options['size'] ?? 'medium';
    $onclick = $options['onclick'] ?? '';
    $id = $options['id'] ?? '';
    $class = $options['class'] ?? '';
    $target = $options['target'] ?? '';
    $disabled = $options['disabled'] ?? false;
    $fullWidth = $options['fullWidth'] ?? false;
    $icon = $options['icon'] ?? '';
    $style = $options['style'] ?? '';
    
    // Button type colors
    $colors = [
        'primary' => ['bg' => '#0039A6', 'hover' => '#002D87'],
        'secondary' => ['bg' => '#6c757d', 'hover' => '#5a6268'],
        'success' => ['bg' => '#28a745', 'hover' => '#218838'],
        'danger' => ['bg' => '#dc3545', 'hover' => '#c82333'],
        'warning' => ['bg' => '#ffc107', 'hover' => '#e0a800'],
        'info' => ['bg' => '#17a2b8', 'hover' => '#138496'],
        'light' => ['bg' => '#f8f9fa', 'hover' => '#e2e6ea', 'text' => '#333'],
        'dark' => ['bg' => '#343a40', 'hover' => '#23272b'],
        'transparent' => ['bg' => 'rgba(255,255,255,0.2)', 'hover' => 'rgba(255,255,255,0.3)']
    ];
    
    // Button sizes
    $sizes = [
        'small' => ['padding' => '6px 12px', 'font' => '14px'],
        'medium' => ['padding' => '10px 20px', 'font' => '16px'],
        'large' => ['padding' => '12px 24px', 'font' => '18px']
    ];
    
    $colorSet = $colors[$type] ?? $colors['primary'];
    $sizeSet = $sizes[$size] ?? $sizes['medium'];
    
    $bgColor = $colorSet['bg'];
    $hoverColor = $colorSet['hover'];
    $textColor = $colorSet['text'] ?? '#ffffff';
    
    $padding = $sizeSet['padding'];
    $fontSize = $sizeSet['font'];
    
    // Build attributes
    $attributes = [];
    if ($id) $attributes[] = 'id="' . htmlspecialchars($id) . '"';
    if ($onclick) $attributes[] = 'onclick="' . htmlspecialchars($onclick) . '"';
    if ($target) $attributes[] = 'target="' . htmlspecialchars($target) . '"';
    if ($disabled) $attributes[] = 'disabled';
    
    $attributeString = implode(' ', $attributes);
    
    // CSS classes
    $cssClasses = ['reusable-button'];
    if ($class) $cssClasses[] = $class;
    $cssClass = implode(' ', $cssClasses);
    
    // Inline styles
    $inlineStyles = [
        'background' => $bgColor,
        'color' => $textColor,
        'border' => 'none',
        'padding' => $padding,
        'font-size' => $fontSize,
        'font-weight' => '500',
        'border-radius' => '6px',
        'cursor' => ($disabled ? 'not-allowed' : 'pointer'),
        'text-decoration' => 'none',
        'display' => ($fullWidth ? 'block' : 'inline-block'),
        'width' => ($fullWidth ? '100%' : 'auto'),
        'text-align' => 'center',
        'transition' => 'all 0.3s ease',
        'opacity' => ($disabled ? '0.6' : '1')
    ];
    
    if ($style) {
        $inlineStyles[] = $style;
    }
    
    $styleString = '';
    foreach ($inlineStyles as $prop => $value) {
        if (is_numeric($prop)) {
            $styleString .= $value . '; ';
        } else {
            $styleString .= $prop . ': ' . $value . '; ';
        }
    }
    
    // Generate button HTML
    if ($href === '#' || $onclick) {
        // Button element
        echo '<button class="' . htmlspecialchars($cssClass) . '" style="' . $styleString . '" ' . $attributeString;
        if (!$disabled) {
            echo ' onmouseover="this.style.background=\'' . $hoverColor . '\'"';
            echo ' onmouseout="this.style.background=\'' . $bgColor . '\'"';
        }
        echo '>';
    } else {
        // Link element styled as button
        echo '<a href="' . htmlspecialchars($href) . '" class="' . htmlspecialchars($cssClass) . '" style="' . $styleString . '" ' . $attributeString;
        if (!$disabled) {
            echo ' onmouseover="this.style.background=\'' . $hoverColor . '\'"';
            echo ' onmouseout="this.style.background=\'' . $bgColor . '\'"';
        }
        echo '>';
    }
    
    // Icon if provided
    if ($icon) {
        echo '<i class="' . htmlspecialchars($icon) . '" style="margin-right: 8px;"></i>';
    }
    
    // Button text
    echo htmlspecialchars($text);
    
    // Close tag
    if ($href === '#' || $onclick) {
        echo '</button>';
    } else {
        echo '</a>';
    }
}

// Include CSS only once
if (!defined('REUSABLE_BUTTON_CSS_INCLUDED')) {
    define('REUSABLE_BUTTON_CSS_INCLUDED', true);
    ?>
    <style>
        /* Reusable Button Component Styles */
        .reusable-button {
            box-sizing: border-box;
            line-height: 1.4;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
        }
        
        .reusable-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        
        .reusable-button:active {
            transform: translateY(0);
            box-shadow: 0 1px 4px rgba(0,0,0,0.15);
        }
        
        .reusable-button:disabled {
            transform: none !important;
            box-shadow: none !important;
        }
        
        /* Dark mode support */
        [data-bs-theme="dark"] .reusable-button {
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        
        [data-bs-theme="dark"] .reusable-button:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
        }
        
        /* Focus styles for accessibility */
        .reusable-button:focus {
            outline: 2px solid #007bff;
            outline-offset: 2px;
        }
        
        /* Loading state (optional) */
        .reusable-button.loading {
            pointer-events: none;
            opacity: 0.7;
        }
        
        .reusable-button.loading::after {
            content: '';
            display: inline-block;
            width: 14px;
            height: 14px;
            margin-left: 8px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: button-loading 0.8s linear infinite;
        }
        
        @keyframes button-loading {
            to {
                transform: rotate(360deg);
            }
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .reusable-button {
                min-height: 44px; /* Touch-friendly minimum */
            }
        }
    </style>
    <?php
}
?>