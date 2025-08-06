<?php
/**
 * Reusable Site Icon Component
 * 
 * A clean, modern site icon with just "11-классники"
 * 
 * Usage:
 * include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/site-icon.php';
 * renderSiteIcon('medium'); // small, medium, large
 */

function renderSiteIcon($size = 'medium', $linkUrl = '/', $additionalClasses = '') {
    // Size configurations
    $sizes = [
        'small' => [
            'fontSize' => '1.2rem',
            'padding' => '8px 12px',
            'borderRadius' => '8px'
        ],
        'medium' => [
            'fontSize' => '1.5rem',
            'padding' => '10px 16px',
            'borderRadius' => '10px'
        ],
        'large' => [
            'fontSize' => '2rem',
            'padding' => '12px 20px',
            'borderRadius' => '12px'
        ]
    ];
    
    $config = $sizes[$size] ?? $sizes['medium'];
    $instanceId = 'site_icon_v2_' . uniqid(); // Updated version
    ?>
    
    <a href="<?php echo htmlspecialchars($linkUrl); ?>" class="site-icon <?php echo $additionalClasses; ?>" id="<?php echo $instanceId; ?>">
        <span class="site-icon-text">11-классники</span>
    </a>
    
    <style>
        #<?php echo $instanceId; ?> {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-weight: 700;
            font-size: <?php echo $config['fontSize']; ?>;
            padding: <?php echo $config['padding']; ?>;
            border-radius: <?php echo $config['borderRadius']; ?>;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white !important;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.2);
            transition: all 0.3s ease;
            border: none;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            line-height: 1;
        }
        
        #<?php echo $instanceId; ?>:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
            text-decoration: none;
            color: white !important;
        }
        
        #<?php echo $instanceId; ?>:active {
            transform: translateY(0);
        }
        
        #<?php echo $instanceId; ?> .site-icon-text {
            color: white;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        /* Dark mode - no changes needed, always visible */
        [data-theme="dark"] #<?php echo $instanceId; ?> {
            /* Icon stays the same in dark mode for consistency */
        }
        
        /* Mobile responsive */
        @media (max-width: 768px) {
            <?php if ($size === 'medium'): ?>
            #<?php echo $instanceId; ?> {
                font-size: 1.3rem;
                padding: 8px 14px;
            }
            <?php elseif ($size === 'large'): ?>
            #<?php echo $instanceId; ?> {
                font-size: 1.6rem;
                padding: 10px 16px;
            }
            <?php endif; ?>
        }
    </style>
    
    <?php
}

// Variant for footer - smaller and more subtle
function renderSiteIconFooter() {
    renderSiteIcon('small', '/', 'footer-icon');
}

// Variant for forms - SVG version like login page
function renderSiteIconSVG($size = 'medium', $linkUrl = null) {
    $sizes = [
        'small' => ['width' => '32', 'height' => '32', 'fontSize' => '14'],
        'medium' => ['width' => '40', 'height' => '40', 'fontSize' => '18'],
        'large' => ['width' => '48', 'height' => '48', 'fontSize' => '22']
    ];
    
    $config = $sizes[$size] ?? $sizes['medium'];
    $instanceId = 'site_icon_svg_' . uniqid();
    
    $svgElement = '<svg width="' . $config['width'] . '" height="' . $config['height'] . '" viewBox="0 0 ' . $config['width'] . ' ' . $config['height'] . '" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="' . ($config['width']/2) . '" cy="' . ($config['height']/2) . '" r="' . ($config['width']/2 - 2) . '" stroke="#28a745" stroke-width="2"/>
        <text x="' . ($config['width']/2) . '" y="' . ($config['height']/2 + 6) . '" text-anchor="middle" fill="#28a745" font-size="' . $config['fontSize'] . '" font-weight="bold">11</text>
    </svg>';
    
    if ($linkUrl) {
        ?>
        <a href="<?php echo htmlspecialchars($linkUrl); ?>" class="site-icon-svg-link" id="<?php echo $instanceId; ?>">
            <?php echo $svgElement; ?>
        </a>
        <style>
            #<?php echo $instanceId; ?> {
                display: inline-block;
                text-decoration: none;
                transition: transform 0.2s ease;
            }
            #<?php echo $instanceId; ?>:hover {
                transform: scale(1.05);
            }
        </style>
        <?php
    } else {
        ?>
        <div class="site-icon-svg" id="<?php echo $instanceId; ?>">
            <?php echo $svgElement; ?>
        </div>
        <?php
    }
}

// Variant for forms - no link
function renderSiteIconStatic($size = 'medium') {
    $sizes = [
        'small' => ['fontSize' => '1.2rem', 'padding' => '8px 12px'],
        'medium' => ['fontSize' => '1.5rem', 'padding' => '10px 16px'],
        'large' => ['fontSize' => '2rem', 'padding' => '12px 20px']
    ];
    
    $config = $sizes[$size] ?? $sizes['medium'];
    $instanceId = 'site_icon_static_' . uniqid();
    ?>
    
    <div class="site-icon-static" id="<?php echo $instanceId; ?>">
        <span class="site-icon-text">11-классники</span>
    </div>
    
    <style>
        #<?php echo $instanceId; ?> {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: <?php echo $config['fontSize']; ?>;
            padding: <?php echo $config['padding']; ?>;
            border-radius: 10px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.2);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            line-height: 1;
        }
        
        #<?php echo $instanceId; ?> .site-icon-text {
            color: white;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }
    </style>
    
    <?php
}
?>