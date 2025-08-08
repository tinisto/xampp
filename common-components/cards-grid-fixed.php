<?php
/**
 * Cards Grid Component - Fixed version
 * Displays cards in a responsive grid layout
 */

function renderCardsGrid($items, $type = 'news', $options = []) {
    // Default options
    $columns = isset($options['columns']) ? $options['columns'] : 4;
    $gap = isset($options['gap']) ? $options['gap'] : 20;
    $showBadge = isset($options['showBadge']) ? $options['showBadge'] : false;
    
    // Start grid container
    echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: ' . $gap . 'px; padding: 20px;">';
    
    foreach ($items as $item) {
        // Determine URL based on type
        $url = '';
        switch ($type) {
            case 'post':
                $url = '/post/' . $item['url_news'];
                break;
            case 'news':
                $url = '/news/' . $item['url_news'];
                break;
            case 'test':
                $url = '/test/' . (isset($item['url_test']) ? $item['url_test'] : $item['url_news']);
                break;
            default:
                $url = '/' . $type . '/' . $item['url_news'];
        }
        
        // Card HTML
        echo '<div class="card" style="background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.transform=\'translateY(-4px)\'; this.style.boxShadow=\'0 4px 12px rgba(0,0,0,0.1)\';" onmouseout="this.style.transform=\'translateY(0)\'; this.style.boxShadow=\'none\';">';
        
        // Image
        $image = isset($item['image_news']) ? $item['image_news'] : '/images/default-news.jpg';
        echo '<a href="' . htmlspecialchars($url) . '" style="text-decoration: none;">';
        echo '<div style="width: 100%; height: 200px; background: url(\'' . htmlspecialchars($image) . '\') center/cover; position: relative;">';
        
        // Category badge
        if ($showBadge && isset($item['category_title'])) {
            echo '<span style="position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.7); color: white; padding: 5px 12px; border-radius: 4px; font-size: 12px;">';
            echo htmlspecialchars($item['category_title']);
            echo '</span>';
        }
        
        echo '</div>';
        echo '</a>';
        
        // Content
        echo '<div style="padding: 20px;">';
        
        // Title
        echo '<h3 style="margin: 0 0 10px 0; font-size: 18px; line-height: 1.4;">';
        echo '<a href="' . htmlspecialchars($url) . '" style="color: #333; text-decoration: none; hover: color: #0066cc;">';
        echo htmlspecialchars($item['title_news']);
        echo '</a>';
        echo '</h3>';
        
        // Meta info
        echo '<div style="color: #666; font-size: 14px;">';
        
        // Date
        if (isset($item['created_at'])) {
            $date = date('d.m.Y', strtotime($item['created_at']));
            echo '<span>' . $date . '</span>';
        }
        
        // Views if available
        if (isset($item['views'])) {
            echo '<span style="margin-left: 15px;">👁 ' . $item['views'] . '</span>';
        }
        
        echo '</div>';
        
        // Description if available
        if (isset($item['description'])) {
            echo '<p style="margin: 10px 0 0 0; color: #555; font-size: 14px; line-height: 1.5;">';
            echo htmlspecialchars(mb_substr($item['description'], 0, 100)) . '...';
            echo '</p>';
        }
        
        echo '</div>';
        echo '</div>';
    }
    
    echo '</div>';
}
?>