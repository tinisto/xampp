<?php
/**
 * Reusable Card Badge Component
 * 
 * Usage:
 * include $_SERVER['DOCUMENT_ROOT'] . '/common-components/card-badge.php';
 * renderCardBadge('Новости ВУЗов', '/news/novosti-vuzov', 'overlay'); // for image overlay
 * renderCardBadge('Категория', '/category/url', 'bottom'); // for bottom placement
 */

function renderCardBadge($text, $url = '', $position = 'overlay', $color = 'green') {
    if (empty($text)) return;
    
    // Include CSS only once
    if (!defined('CARD_BADGE_CSS_INCLUDED')) {
        define('CARD_BADGE_CSS_INCLUDED', true);
        ?>
        <style>
            .card-badge-overlay {
                background: rgba(0, 0, 0, 0.7);
                color: white;
                padding: 5px 10px;
                border-radius: 4px;
                font-size: 11px;
                font-weight: 500;
                text-decoration: none;
                transition: all 0.3s ease;
                position: absolute;
                top: 8px;
                left: 8px;
                z-index: 2;
                box-shadow: 0 1px 3px rgba(0,0,0,0.3);
                display: inline-block;
                line-height: 1.3;
                max-width: calc(100% - 16px);
                text-align: center;
                word-wrap: break-word;
                white-space: normal;
            }
            .card-badge-overlay:hover {
                background: rgba(0, 0, 0, 0.85);
                color: white;
                transform: scale(1.02);
                text-decoration: none;
            }
            .card-badge-bottom {
                background: #6c757d;
                color: white;
                padding: 2px 8px;
                border-radius: 10px;
                font-size: 10px;
                font-weight: 500;
                text-decoration: none;
                transition: all 0.3s ease;
                display: inline-block;
                line-height: 1.2;
                max-width: 100px;
                text-align: center;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .card-badge-bottom:hover {
                background: #5a6268;
                color: white;
                text-decoration: none;
            }
        </style>
        <?php
    }
    
    $colors = [
        'green' => '#6c757d',
        'blue' => '#007bff',
        'red' => '#dc3545',
        'orange' => '#fd7e14',
        'purple' => '#6f42c1',
        'teal' => '#20c997'
    ];
    
    $bgColor = $colors[$color] ?? $colors['green'];
    $hoverColor = adjustBrightness($bgColor, -20);
    
    $badgeClass = $position === 'overlay' ? 'card-badge-overlay' : 'card-badge-bottom';
    
    // Add inline styles for dynamic colors only when needed
    $inlineStyle = '';
    if ($position === 'bottom' && $color !== 'green') {
        $inlineStyle = 'style="background: ' . $bgColor . ';"';
        $inlineHoverStyle = 'onmouseover="this.style.background=\'' . $hoverColor . '\'" onmouseout="this.style.background=\'' . $bgColor . '\'"';
    } else {
        $inlineHoverStyle = '';
    }
    ?>
    
    <?php if ($url): ?>
        <a href="<?= htmlspecialchars($url) ?>" class="<?= $badgeClass ?>" <?= $inlineStyle ?> <?= $inlineHoverStyle ?> title="<?= htmlspecialchars($text) ?>" onclick="event.stopPropagation();">
            <?= htmlspecialchars($text) ?>
        </a>
    <?php else: ?>
        <span class="<?= $badgeClass ?>" <?= $inlineStyle ?> <?= $inlineHoverStyle ?> title="<?= htmlspecialchars($text) ?>">
            <?= htmlspecialchars($text) ?>
        </span>
    <?php endif; ?>
    <?php
}

function adjustBrightness($hexColor, $percent) {
    // Remove # if present
    $hex = ltrim($hexColor, '#');
    
    // Convert to RGB
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    // Adjust brightness
    $r = max(0, min(255, $r + ($r * $percent / 100)));
    $g = max(0, min(255, $g + ($g * $percent / 100)));
    $b = max(0, min(255, $b + ($b * $percent / 100)));
    
    // Convert back to hex
    return sprintf('#%02x%02x%02x', $r, $g, $b);
}
?>