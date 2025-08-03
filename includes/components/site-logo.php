<?php
/**
 * Reusable site logo component
 * Provides consistent logo rendering across all pages
 */

function renderSiteLogo($options = []) {
    // Default options
    $defaults = [
        'size' => 40,           // Logo size in pixels
        'link' => '/',          // Link destination
        'showText' => false,    // Show "11классники" text
        'class' => '',          // Additional CSS classes
        'style' => 'svg',       // 'svg' or 'image'
        'darkMode' => false     // Dark mode variant
    ];
    
    $settings = array_merge($defaults, $options);
    
    // Start output
    $output = '';
    
    // Wrapper with link
    if ($settings['link']) {
        $output .= '<a href="' . htmlspecialchars($settings['link']) . '" class="logo-link">';
    }
    
    // Logo container
    $output .= '<div class="site-logo ' . htmlspecialchars($settings['class']) . '">';
    
    if ($settings['style'] === 'svg') {
        // Modern SVG implementation
        $output .= '<svg width="' . $settings['size'] . '" height="' . $settings['size'] . '" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" class="logo-svg">';
        $output .= '<circle cx="20" cy="20" r="18" stroke="currentColor" stroke-width="2"/>';
        $output .= '<text x="20" y="26" text-anchor="middle" fill="currentColor" font-size="18" font-weight="bold">11</text>';
        $output .= '</svg>';
    } else {
        // Fallback image implementation
        $output .= '<img src="/images/logo.png" alt="11классники" width="' . $settings['size'] . '" height="' . $settings['size'] . '" class="logo-image">';
    }
    
    // Optional text
    if ($settings['showText']) {
        $output .= '<span class="logo-text">11классники</span>';
    }
    
    $output .= '</div>';
    
    // Close link wrapper
    if ($settings['link']) {
        $output .= '</a>';
    }
    
    return $output;
}

// Convenience functions for common sizes
function renderSiteLogoSmall($options = []) {
    return renderSiteLogo(array_merge(['size' => 30], $options));
}

function renderSiteLogoMedium($options = []) {
    return renderSiteLogo(array_merge(['size' => 40], $options));
}

function renderSiteLogoLarge($options = []) {
    return renderSiteLogo(array_merge(['size' => 60], $options));
}
?>