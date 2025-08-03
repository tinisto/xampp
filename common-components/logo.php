<?php
function renderLogo($size = 'medium', $linkTo = '/', $showText = false) {
    $sizes = [
        'small' => ['width' => '40px', 'height' => '40px', 'font' => '1.2rem'],
        'medium' => ['width' => '60px', 'height' => '60px', 'font' => '1.8rem'],
        'large' => ['width' => '80px', 'height' => '80px', 'font' => '2rem']
    ];
    
    $config = $sizes[$size] ?? $sizes['medium'];
    
    echo '<a href="' . htmlspecialchars($linkTo) . '" class="logo-link" style="text-decoration: none; display: inline-block;">';
    echo '<div class="logo-icon" style="
        width: ' . $config['width'] . ';
        height: ' . $config['height'] . ';
        background: #333;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: ' . $config['font'] . ';
        font-weight: bold;
        border-radius: 50%;
        transition: transform 0.3s ease;
    ">11</div>';
    if ($showText) {
        echo '<span style="margin-left: 10px; font-weight: bold; color: #333;">11klassniki</span>';
    }
    echo '</a>';
    
    // Add hover effect
    echo '<style>
    .logo-link:hover .logo-icon {
        transform: scale(1.1);
    }
    </style>';
}
?>