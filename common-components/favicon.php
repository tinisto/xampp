<?php
/**
 * Reusable Favicon Component
 * 
 * Generates favicon link tags that match the site icon design
 * Uses inline SVG with the same green gradient and "11" text as site-icon.php
 */

function renderFavicon() {
    // SVG favicon matching site icon design with cache buster
    $faviconSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
        <defs>
            <linearGradient id="favicon-gradient-v2" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#28a745" />
                <stop offset="100%" style="stop-color:#20c997" />
            </linearGradient>
        </defs>
        <rect width="32" height="32" rx="6" fill="url(#favicon-gradient-v2)"/>
        <text x="16" y="22" text-anchor="middle" fill="white" font-size="16" font-weight="bold" font-family="system-ui">11</text>
    </svg>';
    
    $encodedSvg = urlencode($faviconSvg);
    $cacheKey = '?v=' . time(); // Cache buster
    
    echo '<link rel="icon" href="data:image/svg+xml,' . $encodedSvg . '" type="image/svg+xml">' . "\n";
    echo '    <link rel="icon" type="image/x-icon" href="/favicon.ico' . $cacheKey . '">' . "\n"; // Fallback with cache buster
}

function renderAppleTouchIcon() {
    echo '<link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png">' . "\n";
}

function renderAllFavicons() {
    renderFavicon();
    renderAppleTouchIcon();
}
?>