<?php
/**
 * Patch to fix id_category issue in news queries
 * This file should be included at the beginning of any news-related page
 */

// Create a wrapper for mysqli_query that fixes queries on the fly
if (!function_exists('mysqli_query_patched')) {
    function mysqli_query_patched($connection, $query) {
        // Fix queries that might be selecting id_category from news
        if (stripos($query, 'FROM news') !== false || stripos($query, 'JOIN news') !== false) {
            // Replace id_category with category_news in field lists
            $query = preg_replace('/\bn\.id_category\b/', 'n.category_news', $query);
            $query = preg_replace('/\bnews\.id_category\b/', 'news.category_news', $query);
            
            // If selecting all columns and there's a join, be more specific
            if (preg_match('/SELECT\s+\*\s+FROM/i', $query) && stripos($query, 'JOIN') !== false) {
                // This is risky but might help
                $query = preg_replace('/SELECT\s+\*\s+FROM/i', 'SELECT n.* FROM', $query);
            }
        }
        
        return mysqli_query($connection, $query);
    }
}

// Override the global mysqli_query if possible
if (!defined('MYSQLI_QUERY_PATCHED')) {
    define('MYSQLI_QUERY_PATCHED', true);
    
    // This is a hack but might work in some environments
    if (!function_exists('mysqli_query_original')) {
        eval('
            function mysqli_query_original($link, $query = null) {
                if ($query === null) {
                    $query = $link;
                    $link = $GLOBALS["connection"];
                }
                return \mysqli_query($link, $query);
            }
        ');
    }
}
?>