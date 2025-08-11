<?php
/**
 * Reusable logo component for 11klassniki.ru
 * Can be used in header, authentication pages, etc.
 */

function renderLogo($size = 'normal', $includeTagline = false, $link = '/', $classes = '') {
    // Size configurations
    $sizes = [
        'small' => [
            'font_size' => '24px',
            'svg_width' => '32px',
            'svg_height' => '10px',
            'svg_path' => 'M 2 8 Q 18 5 28 7'
        ],
        'normal' => [
            'font_size' => '28px',
            'svg_width' => '45px',
            'svg_height' => '15px',
            'svg_path' => 'M 3 12 Q 25 7 42 10'
        ],
        'large' => [
            'font_size' => '36px',
            'svg_width' => '55px',
            'svg_height' => '18px',
            'svg_path' => 'M 4 14 Q 30 8 50 12'
        ]
    ];
    
    $config = $sizes[$size] ?? $sizes['normal'];
    
    ob_start();
    ?>
    <a href="<?= htmlspecialchars($link) ?>" class="logo <?= htmlspecialchars($classes) ?>" style="
        font-size: <?= $config['font_size'] ?>;
        position: relative;
        display: inline-block;
        font-family: Arial, sans-serif;
        font-weight: 400;
        color: #333;
        text-decoration: none;
    ">
        <span class="eleven" style="font-weight: 700; color: #667eea;">11</span>klassniki<span class="ru" style="color: #764ba2; font-weight: 500;">.ru</span>
        <svg style="
            position: absolute;
            bottom: -3px;
            left: -2px;
            width: <?= $config['svg_width'] ?>;
            height: <?= $config['svg_height'] ?>;
        ">
            <path d="<?= $config['svg_path'] ?>" 
                  stroke="#667eea" 
                  stroke-width="2" 
                  fill="none" 
                  stroke-linecap="round" 
                  opacity="0.8"/>
        </svg>
        <?php if ($includeTagline): ?>
            <div style="font-size: 14px; color: #666; margin-top: 5px; font-weight: 400;">
                Российское образование
            </div>
        <?php endif; ?>
    </a>
    
    <style>
        .logo:hover {
            opacity: 0.9;
            text-decoration: none;
        }
        
        /* Dark mode support */
        body.dark-mode .logo {
            color: #e0e0e0;
        }
        
        body.dark-mode .logo .eleven {
            color: #4299e1;
        }
        
        body.dark-mode .logo .ru {
            color: #9f7aea;
        }
        
        body.dark-mode .logo svg path {
            stroke: #4299e1;
        }
        
        body.dark-mode .logo div {
            color: #b0b0b0;
        }
    </style>
    <?php
    return ob_get_clean();
}

// Simple function for just outputting the logo
function logo($size = 'normal', $includeTagline = false, $link = '/', $classes = '') {
    echo renderLogo($size, $includeTagline, $link, $classes);
}
?>