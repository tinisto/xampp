<?php
// Content recommendation engine

class RecommendationEngine {
    
    // Get recommendations for a user
    public static function getRecommendations($userId, $limit = 10) {
        $recommendations = [];
        
        // Get user's reading history and preferences
        $userHistory = self::getUserHistory($userId);
        
        if (!$userHistory) {
            // For new users, show popular content
            return self::getPopularContent($limit);
        }
        
        // Get recommendations based on different strategies
        $categoryBased = self::getCategoryBasedRecommendations($userId, $userHistory, $limit / 3);
        $ratingBased = self::getRatingBasedRecommendations($userId, $userHistory, $limit / 3);
        $popularBased = self::getPopularContent($limit / 3);
        
        // Merge and deduplicate recommendations
        $recommendations = array_merge($categoryBased, $ratingBased, $popularBased);
        $recommendations = self::removeDuplicates($recommendations);
        $recommendations = self::removeAlreadyRead($recommendations, $userId);
        
        return array_slice($recommendations, 0, $limit);
    }
    
    // Get user's reading history
    private static function getUserHistory($userId) {
        return db_fetch_all("
            SELECT 
                'reading_list' as source,
                rli.item_type,
                rli.item_id,
                CASE 
                    WHEN rli.item_type = 'news' THEN c1.id
                    WHEN rli.item_type = 'post' THEN c2.id
                END as category_id,
                rli.added_at as interaction_date
            FROM reading_list_items rli
            JOIN reading_lists rl ON rli.list_id = rl.id
            LEFT JOIN news n ON rli.item_type = 'news' AND rli.item_id = n.id_news
            LEFT JOIN categories c1 ON n.category_id = c1.id
            LEFT JOIN posts p ON rli.item_type = 'post' AND rli.item_id = p.id
            LEFT JOIN categories c2 ON p.category = c2.id
            WHERE rl.user_id = ?
            
            UNION
            
            SELECT 
                'rating' as source,
                r.item_type,
                r.item_id,
                CASE 
                    WHEN r.item_type = 'news' THEN c1.id
                    WHEN r.item_type = 'post' THEN c2.id
                END as category_id,
                r.created_at as interaction_date
            FROM ratings r
            LEFT JOIN news n ON r.item_type = 'news' AND r.item_id = n.id_news
            LEFT JOIN categories c1 ON n.category_id = c1.id
            LEFT JOIN posts p ON r.item_type = 'post' AND r.item_id = p.id
            LEFT JOIN categories c2 ON p.category = c2.id
            WHERE r.user_id = ? AND r.rating >= 4
            
            UNION
            
            SELECT 
                'favorite' as source,
                f.item_type,
                f.item_id,
                CASE 
                    WHEN f.item_type = 'news' THEN c1.id
                    WHEN f.item_type = 'post' THEN c2.id
                END as category_id,
                f.created_at as interaction_date
            FROM favorites f
            LEFT JOIN news n ON f.item_type = 'news' AND f.item_id = n.id_news
            LEFT JOIN categories c1 ON n.category_id = c1.id
            LEFT JOIN posts p ON f.item_type = 'post' AND f.item_id = p.id
            LEFT JOIN categories c2 ON p.category = c2.id
            WHERE f.user_id = ?
            
            ORDER BY interaction_date DESC
            LIMIT 50
        ", [$userId, $userId, $userId]);
    }
    
    // Get recommendations based on categories user likes
    private static function getCategoryBasedRecommendations($userId, $userHistory, $limit) {
        // Find user's preferred categories
        $categoryStats = [];
        foreach ($userHistory as $item) {
            if ($item['category_id']) {
                $categoryStats[$item['category_id']] = ($categoryStats[$item['category_id']] ?? 0) + 1;
            }
        }
        
        arsort($categoryStats);
        $topCategories = array_keys(array_slice($categoryStats, 0, 3));
        
        if (empty($topCategories)) {
            return [];
        }
        
        $placeholders = str_repeat('?,', count($topCategories) - 1) . '?';
        
        // Get recent content from preferred categories
        $news = db_fetch_all("
            SELECT 'news' as type, id_news as id, title_news as title, 
                   url_news as url, created_at, views, category_id
            FROM news 
            WHERE category_id IN ({$placeholders}) 
            AND is_published = 1
            ORDER BY created_at DESC, views DESC
            LIMIT ?
        ", array_merge($topCategories, [intval($limit / 2)]));
        
        $posts = db_fetch_all("
            SELECT 'post' as type, id, title_post as title, 
                   url_slug as url, date_post as created_at, views, category
            FROM posts 
            WHERE category IN ({$placeholders}) 
            AND is_published = 1
            ORDER BY date_post DESC, views DESC
            LIMIT ?
        ", array_merge($topCategories, [intval($limit / 2)]));
        
        return array_merge($news, $posts);
    }
    
    // Get recommendations based on highly-rated content
    private static function getRatingBasedRecommendations($userId, $userHistory, $limit) {
        return db_fetch_all("
            SELECT 
                r.item_type as type,
                r.item_id as id,
                CASE 
                    WHEN r.item_type = 'news' THEN n.title_news
                    WHEN r.item_type = 'post' THEN p.title_post
                END as title,
                CASE 
                    WHEN r.item_type = 'news' THEN n.url_news
                    WHEN r.item_type = 'post' THEN p.url_slug
                END as url,
                CASE 
                    WHEN r.item_type = 'news' THEN n.created_at
                    WHEN r.item_type = 'post' THEN p.date_post
                END as created_at,
                CASE 
                    WHEN r.item_type = 'news' THEN n.views
                    WHEN r.item_type = 'post' THEN p.views
                END as views,
                AVG(r.rating) as avg_rating,
                COUNT(r.id) as rating_count
            FROM ratings r
            LEFT JOIN news n ON r.item_type = 'news' AND r.item_id = n.id_news
            LEFT JOIN posts p ON r.item_type = 'post' AND r.item_id = p.id
            WHERE r.user_id != ?
            GROUP BY r.item_type, r.item_id
            HAVING avg_rating >= 4.0 AND rating_count >= 2
            ORDER BY avg_rating DESC, rating_count DESC
            LIMIT ?
        ", [$userId, intval($limit)]);
    }
    
    // Get popular content
    private static function getPopularContent($limit) {
        $news = db_fetch_all("
            SELECT 'news' as type, id_news as id, title_news as title, 
                   url_news as url, created_at, views, category_id
            FROM news 
            WHERE is_published = 1
            ORDER BY views DESC, created_at DESC
            LIMIT ?
        ", [intval($limit / 2)]);
        
        $posts = db_fetch_all("
            SELECT 'post' as type, id, title_post as title, 
                   url_slug as url, date_post as created_at, views, category
            FROM posts 
            WHERE is_published = 1
            ORDER BY views DESC, date_post DESC
            LIMIT ?
        ", [intval($limit / 2)]);
        
        return array_merge($news, $posts);
    }
    
    // Remove duplicate recommendations
    private static function removeDuplicates($recommendations) {
        $seen = [];
        $unique = [];
        
        foreach ($recommendations as $item) {
            $key = $item['type'] . '_' . $item['id'];
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $unique[] = $item;
            }
        }
        
        return $unique;
    }
    
    // Remove content user has already interacted with
    private static function removeAlreadyRead($recommendations, $userId) {
        // Get user's interaction history
        $interactions = db_fetch_all("
            SELECT DISTINCT item_type, item_id
            FROM (
                SELECT item_type, item_id FROM favorites WHERE user_id = ?
                UNION
                SELECT item_type, item_id FROM ratings WHERE user_id = ?
                UNION
                SELECT rli.item_type, rli.item_id 
                FROM reading_list_items rli
                JOIN reading_lists rl ON rli.list_id = rl.id
                WHERE rl.user_id = ?
            ) as user_interactions
        ", [$userId, $userId, $userId]);
        
        $interactedItems = [];
        foreach ($interactions as $interaction) {
            $interactedItems[$interaction['item_type'] . '_' . $interaction['item_id']] = true;
        }
        
        $filtered = [];
        foreach ($recommendations as $item) {
            $key = $item['type'] . '_' . $item['id'];
            if (!isset($interactedItems[$key])) {
                $filtered[] = $item;
            }
        }
        
        return $filtered;
    }
    
    // Get similar content based on an item
    public static function getSimilarContent($itemType, $itemId, $limit = 5) {
        // Get the category of the current item
        $category = null;
        if ($itemType === 'news') {
            $category = db_fetch_column("SELECT category_id FROM news WHERE id_news = ?", [$itemId]);
        } elseif ($itemType === 'post') {
            $category = db_fetch_column("SELECT category FROM posts WHERE id = ?", [$itemId]);
        }
        
        if (!$category) {
            return self::getPopularContent($limit);
        }
        
        // Get similar content from the same category
        $similar = [];
        
        if ($itemType === 'news') {
            $similar = array_merge($similar, db_fetch_all("
                SELECT 'news' as type, id_news as id, title_news as title, 
                       url_news as url, created_at, views
                FROM news 
                WHERE category_id = ? AND id_news != ? AND is_published = 1
                ORDER BY views DESC, created_at DESC
                LIMIT ?
            ", [$category, $itemId, intval($limit / 2)]));
        }
        
        if ($itemType === 'post') {
            $similar = array_merge($similar, db_fetch_all("
                SELECT 'post' as type, id, title_post as title, 
                       url_slug as url, date_post as created_at, views
                FROM posts 
                WHERE category = ? AND id != ? AND is_published = 1
                ORDER BY views DESC, date_post DESC
                LIMIT ?
            ", [$category, $itemId, intval($limit / 2)]));
        }
        
        // Add content from the other type in the same category
        if ($itemType === 'news') {
            $similar = array_merge($similar, db_fetch_all("
                SELECT 'post' as type, id, title_post as title, 
                       url_slug as url, date_post as created_at, views
                FROM posts 
                WHERE category = ? AND is_published = 1
                ORDER BY views DESC, date_post DESC
                LIMIT ?
            ", [$category, intval($limit / 2)]));
        } else {
            $similar = array_merge($similar, db_fetch_all("
                SELECT 'news' as type, id_news as id, title_news as title, 
                       url_news as url, created_at, views
                FROM news 
                WHERE category_id = ? AND is_published = 1
                ORDER BY views DESC, created_at DESC
                LIMIT ?
            ", [$category, intval($limit / 2)]));
        }
        
        return array_slice($similar, 0, $limit);
    }
}

// Helper function to include recommendations widget
function include_recommendations_widget($userId, $title = 'Рекомендуем к прочтению', $limit = 6) {
    $recommendations = RecommendationEngine::getRecommendations($userId, $limit);
    
    if (empty($recommendations)) {
        return;
    }
    
    ?>
    <div style="background: var(--bg-secondary); border-radius: 12px; padding: 25px; margin: 20px 0;">
        <h3 style="margin: 0 0 20px 0; font-size: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-lightbulb" style="color: #ffc107;"></i>
            <?= htmlspecialchars($title) ?>
        </h3>
        
        <div style="display: grid; gap: 15px;">
            <?php foreach ($recommendations as $item): ?>
            <div style="display: flex; gap: 15px; padding: 15px; background: var(--bg-primary); 
                        border-radius: 8px; border: 1px solid var(--border-color);">
                <div style="flex-shrink: 0;">
                    <?php
                    $icon = $item['type'] === 'news' ? 'fa-newspaper' : 'fa-book';
                    $color = $item['type'] === 'news' ? '#007bff' : '#28a745';
                    $link = $item['type'] === 'news' ? "/news/{$item['url']}" : "/post/{$item['url']}";
                    ?>
                    <i class="fas <?= $icon ?>" style="color: <?= $color ?>; font-size: 18px; margin-top: 2px;"></i>
                </div>
                
                <div style="flex: 1; min-width: 0;">
                    <h4 style="margin: 0 0 8px 0; font-size: 16px;">
                        <a href="<?= $link ?>" style="color: var(--link-color); text-decoration: none; 
                                                     display: -webkit-box; -webkit-line-clamp: 2; 
                                                     -webkit-box-orient: vertical; overflow: hidden;">
                            <?= htmlspecialchars($item['title']) ?>
                        </a>
                    </h4>
                    
                    <div style="display: flex; gap: 15px; font-size: 14px; color: var(--text-secondary);">
                        <span>
                            <i class="fas fa-calendar"></i> 
                            <?= date('d.m.Y', strtotime($item['created_at'])) ?>
                        </span>
                        <?php if (isset($item['views']) && $item['views'] > 0): ?>
                        <span>
                            <i class="fas fa-eye"></i> 
                            <?= number_format($item['views']) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="/recommendations" style="color: var(--link-color); text-decoration: none; font-size: 14px;">
                <i class="fas fa-arrow-right"></i> Смотреть все рекомендации
            </a>
        </div>
    </div>
    <?php
}

// Helper function to include similar content widget
function include_similar_content_widget($itemType, $itemId, $limit = 5) {
    $similar = RecommendationEngine::getSimilarContent($itemType, $itemId, $limit);
    
    if (empty($similar)) {
        return;
    }
    
    ?>
    <div style="background: var(--bg-secondary); border-radius: 12px; padding: 25px; margin: 20px 0;">
        <h3 style="margin: 0 0 20px 0; font-size: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-thumbs-up" style="color: #28a745;"></i>
            Похожие материалы
        </h3>
        
        <div style="display: grid; gap: 15px;">
            <?php foreach ($similar as $item): ?>
            <div style="display: flex; gap: 15px; padding: 15px; background: var(--bg-primary); 
                        border-radius: 8px; border: 1px solid var(--border-color);">
                <div style="flex-shrink: 0;">
                    <?php
                    $icon = $item['type'] === 'news' ? 'fa-newspaper' : 'fa-book';
                    $color = $item['type'] === 'news' ? '#007bff' : '#28a745';
                    $link = $item['type'] === 'news' ? "/news/{$item['url']}" : "/post/{$item['url']}";
                    ?>
                    <i class="fas <?= $icon ?>" style="color: <?= $color ?>; font-size: 16px; margin-top: 2px;"></i>
                </div>
                
                <div style="flex: 1; min-width: 0;">
                    <h4 style="margin: 0 0 5px 0; font-size: 15px;">
                        <a href="<?= $link ?>" style="color: var(--link-color); text-decoration: none; 
                                                     display: -webkit-box; -webkit-line-clamp: 2; 
                                                     -webkit-box-orient: vertical; overflow: hidden;">
                            <?= htmlspecialchars($item['title']) ?>
                        </a>
                    </h4>
                    
                    <div style="font-size: 13px; color: var(--text-secondary);">
                        <i class="fas fa-eye"></i> <?= number_format($item['views'] ?? 0) ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}
?>