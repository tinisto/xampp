<?php
/**
 * Fix for id_category issue in news queries
 * Include this file in any page that queries news data
 */

// Store original mysqli_query function reference
if (!function_exists('_original_mysqli_query')) {
    function _original_mysqli_query($link, $query) {
        return \mysqli_query($link, $query);
    }
}

// Override mysqli_query for news-related pages
function mysqli_query($link, $query = null) {
    // Handle both parameter orders
    if ($query === null) {
        $query = $link;
        $link = $GLOBALS['connection'] ?? null;
    }
    
    // Fix queries that reference id_category in news context
    if (stripos($query, 'news') !== false) {
        // Log problematic queries for debugging
        if (preg_match('/\bid_category\b(?!_news)/i', $query)) {
            error_log("Found problematic query with id_category: " . $query);
            
            // Attempt to fix common patterns
            $query = preg_replace('/\bn\.id_category\b/i', 'n.category_news', $query);
            $query = preg_replace('/\bnews\.id_category\b/i', 'news.category_news', $query);
            $query = preg_replace('/SELECT\s+id_category\s+FROM\s+news/i', 'SELECT category_news FROM news', $query);
            
            error_log("Fixed query: " . $query);
        }
    }
    
    return _original_mysqli_query($link, $query);
}
?>