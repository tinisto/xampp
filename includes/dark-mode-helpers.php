<?php
/**
 * Dark Mode Helper Functions
 * 
 * This file provides utility functions to replace inline styles with CSS classes
 * that support dark mode throughout the application.
 */

/**
 * Replace inline color styles with adaptive CSS classes
 * 
 * @param string $html The HTML content to process
 * @return string The processed HTML with CSS classes
 */
function apply_dark_mode_classes($html) {
    // Map of inline styles to CSS classes
    $replacements = [
        // Text colors
        'color: #000' => 'class="text-primary-adaptive"',
        'color: #333' => 'class="text-primary-adaptive"',
        'color: #666' => 'class="text-secondary-adaptive"',
        'color: #999' => 'class="text-muted-adaptive"',
        'color: #555' => 'class="text-secondary-adaptive"',
        'color: #717171' => 'class="text-secondary-adaptive"',
        'color: #222222' => 'class="text-heading-adaptive"',
        'color: #000000' => 'class="text-heading-adaptive"',
        
        // Link colors
        'color: #007bff' => 'class="text-link-adaptive"',
        'color: #0066cc' => 'class="text-link-adaptive"',
        'color: #28a745' => 'class="text-link-adaptive"',
        'color: #667eea' => 'class="text-link-adaptive"',
        
        // Background colors
        'background: #f8f9fa' => 'class="bg-secondary-adaptive"',
        'background-color: #f8f9fa' => 'class="bg-secondary-adaptive"',
        'background: white' => 'class="bg-primary-adaptive"',
        'background-color: white' => 'class="bg-primary-adaptive"',
        'background: #ffffff' => 'class="bg-primary-adaptive"',
        'background-color: #ffffff' => 'class="bg-primary-adaptive"',
        'background: #e9ecef' => 'class="bg-tertiary-adaptive"',
        'background-color: #e9ecef' => 'class="bg-tertiary-adaptive"',
    ];
    
    // Apply replacements
    foreach ($replacements as $search => $replace) {
        // For style attributes that only contain the color
        $html = preg_replace('/style="' . preg_quote($search, '/') . '"/', $replace, $html);
        
        // For style attributes that contain the color along with other styles
        $html = preg_replace('/style="([^"]*?)' . preg_quote($search, '/') . '([^"]*?)"/', 'style="$1$2" ' . $replace, $html);
    }
    
    // Handle combined styles - extract color styles and add classes
    $html = preg_replace_callback('/style="([^"]*)(color:\s*#[0-9a-fA-F]{3,6}|color:\s*(?:black|white|grey|gray))([^"]*)"/', function($matches) {
        $beforeColor = $matches[1];
        $colorStyle = $matches[2];
        $afterColor = $matches[3];
        
        // Map color values to classes
        $colorMap = [
            'color: #000' => 'text-primary-adaptive',
            'color: #333' => 'text-primary-adaptive',
            'color: #666' => 'text-secondary-adaptive',
            'color: #999' => 'text-muted-adaptive',
            'color: black' => 'text-primary-adaptive',
            'color: white' => 'text-white',
        ];
        
        $class = '';
        foreach ($colorMap as $style => $className) {
            if (stripos($colorStyle, trim($style, ' ')) !== false) {
                $class = $className;
                break;
            }
        }
        
        // Combine remaining styles
        $remainingStyle = trim($beforeColor . $afterColor);
        
        if ($class && $remainingStyle) {
            return 'style="' . $remainingStyle . '" class="' . $class . '"';
        } elseif ($class) {
            return 'class="' . $class . '"';
        } else {
            return 'style="' . $remainingStyle . '"';
        }
    }, $html);
    
    return $html;
}

/**
 * Wrap content with adaptive section classes
 * 
 * @param string $content The content to wrap
 * @param string $type The section type ('primary' or 'secondary')
 * @return string The wrapped content
 */
function wrap_adaptive_section($content, $type = 'primary') {
    $class = $type === 'secondary' ? 'section-alt-adaptive' : 'section-adaptive';
    return '<div class="' . $class . '">' . $content . '</div>';
}

/**
 * Convert inline button styles to button classes
 * 
 * @param string $html The HTML content
 * @return string The processed HTML
 */
function convert_button_styles($html) {
    // Convert primary buttons
    $html = preg_replace(
        '/<a([^>]*?)style="[^"]*background:\s*#28a745[^"]*"([^>]*?)>/',
        '<a$1class="nav-button-primary" style="background: #28a745;"$2>',
        $html
    );
    
    // Convert share buttons
    $html = preg_replace(
        '/<a([^>]*?)style="[^"]*background:\s*#4267B2[^"]*"([^>]*?)>/',
        '<a$1class="share-button share-button-vk"$2>',
        $html
    );
    
    $html = preg_replace(
        '/<a([^>]*?)style="[^"]*background:\s*#0088cc[^"]*"([^>]*?)>/',
        '<a$1class="share-button share-button-telegram"$2>',
        $html
    );
    
    return $html;
}

/**
 * Add dark mode CSS to page head
 * 
 * @return string The CSS link tag
 */
function include_dark_mode_css() {
    return '<link rel="stylesheet" href="/css/dark-mode-fix.css">';
}

/**
 * Process a full page content for dark mode compatibility
 * 
 * @param string $content The page content
 * @return string The processed content
 */
function process_for_dark_mode($content) {
    // Add CSS if not already included
    if (strpos($content, 'dark-mode-fix.css') === false) {
        $content = str_replace('</head>', include_dark_mode_css() . "\n</head>", $content);
    }
    
    // Apply all transformations
    $content = apply_dark_mode_classes($content);
    $content = convert_button_styles($content);
    
    return $content;
}
?>