<?php
/**
 * Reusable Logo Component
 * Modern, minimal logo for 11klassniki
 */

function renderLogo($size = 'normal', $showText = true) {
    $sizes = [
        'small' => ['icon' => 24, 'text' => '1rem'],
        'normal' => ['icon' => 32, 'text' => '1.25rem'],
        'large' => ['icon' => 48, 'text' => '1.75rem']
    ];
    
    $currentSize = $sizes[$size] ?? $sizes['normal'];
    ?>
    <a href="/" style="
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
        color: #fff;
        font-weight: 700;
        font-size: <?php echo $currentSize['text']; ?>;
        transition: opacity 0.2s;
    "
    onmouseover="this.style.opacity='0.8';"
    onmouseout="this.style.opacity='1';">
        <div style="
            width: <?php echo $currentSize['icon']; ?>px;
            height: <?php echo $currentSize['icon']; ?>px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            font-size: <?php echo $currentSize['icon'] * 0.5; ?>px;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        ">
            11
        </div>
        <?php if ($showText): ?>
            <span>классники</span>
        <?php endif; ?>
    </a>
    <?php
}

/**
 * Render favicon link tags
 */
function renderFavicon() {
    ?>
    <style>
        @media (prefers-color-scheme: dark) {
            .favicon-svg { fill: #667eea; }
        }
        @media (prefers-color-scheme: light) {
            .favicon-svg { fill: #764ba2; }
        }
    </style>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Cdefs%3E%3ClinearGradient id='g' x1='0%25' y1='0%25' x2='100%25' y2='100%25'%3E%3Cstop offset='0%25' style='stop-color:%23667eea'/%3E%3Cstop offset='100%25' style='stop-color:%23764ba2'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='32' height='32' rx='8' fill='url(%23g)'/%3E%3Ctext x='16' y='22' font-family='system-ui' font-size='18' font-weight='bold' text-anchor='middle' fill='white'%3E11%3C/text%3E%3C/svg%3E">
    <link rel="apple-touch-icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 180 180'%3E%3Cdefs%3E%3ClinearGradient id='g' x1='0%25' y1='0%25' x2='100%25' y2='100%25'%3E%3Cstop offset='0%25' style='stop-color:%23667eea'/%3E%3Cstop offset='100%25' style='stop-color:%23764ba2'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='180' height='180' rx='40' fill='url(%23g)'/%3E%3Ctext x='90' y='120' font-family='system-ui' font-size='100' font-weight='bold' text-anchor='middle' fill='white'%3E11%3C/text%3E%3C/svg%3E">
    <?php
}
?>