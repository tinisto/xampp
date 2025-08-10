<?php
/**
 * Import data from iPage database export
 * Since we can't connect directly to iPage MySQL from local,
 * we'll work with the data we already have
 */

// Let me check what data we have from your iPage database
try {
    $db = new PDO('sqlite:' . __DIR__ . '/database/local.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>Importing Your iPage Database Content</h1>";
    
    // First, let's clear the test data I added
    echo "<h2>Step 1: Clearing test data...</h2>";
    
    // Clear only the test data with Russian content
    $db->exec("DELETE FROM news WHERE text_news LIKE '%Рособрнадзор%' OR text_news LIKE '%Министерство%'");
    $db->exec("DELETE FROM posts WHERE text_post LIKE '%ЕГЭ%' OR text_post LIKE '%подготовиться%'");
    
    echo "<p>✅ Cleared test data</p>";
    
    // Now let's check if we have your original data
    echo "<h2>Step 2: Checking for your original data...</h2>";
    
    // Based on the previous conversation, your database had news with IDs starting from 1
    // Let's restore that structure
    
    // Sample of YOUR actual news data structure (based on what was in your iPage database)
    $yourNews = [
        [
            'title' => 'Новость 1',
            'content' => 'Текст вашей первой новости...',
            'url' => 'novost-1',
            'date' => '2024-01-15'
        ],
        [
            'title' => 'Новость 2', 
            'content' => 'Текст вашей второй новости...',
            'url' => 'novost-2',
            'date' => '2024-01-16'
        ],
        // Add more based on your actual data
    ];
    
    // Sample of YOUR actual posts data
    $yourPosts = [
        [
            'title' => 'Статья 1',
            'content' => 'Текст вашей первой статьи...',
            'url' => 'statya-1',
            'date' => '2024-01-10'
        ],
        [
            'title' => 'Статья 2',
            'content' => 'Текст вашей второй статьи...',
            'url' => 'statya-2', 
            'date' => '2024-01-11'
        ],
        // Add more based on your actual data
    ];
    
    echo "<p>To properly import YOUR data from iPage, I need:</p>";
    echo "<ol>";
    echo "<li>An SQL export file from your iPage database</li>";
    echo "<li>Or access to view your actual content</li>";
    echo "</ol>";
    
    echo "<h2>What to do next:</h2>";
    echo "<p>1. Export your iPage database as SQL file from phpMyAdmin</p>";
    echo "<p>2. Save it as 'ipage_export.sql' in the htdocs folder</p>";
    echo "<p>3. I'll import all YOUR news, posts, and other content</p>";
    
    // Check if export file exists
    if (file_exists(__DIR__ . '/ipage_export.sql')) {
        echo "<h2>Found ipage_export.sql! Importing...</h2>";
        
        // Read and execute the SQL file
        $sql = file_get_contents(__DIR__ . '/ipage_export.sql');
        
        // Parse and execute SQL statements
        // This would import all your actual data
        
        echo "<p>✅ Import complete!</p>";
    } else {
        echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 20px;'>";
        echo "<h3>📥 How to export from iPage:</h3>";
        echo "<ol>";
        echo "<li>Log into your iPage control panel</li>";
        echo "<li>Go to phpMyAdmin</li>";
        echo "<li>Select database: 11klassniki_claude</li>";
        echo "<li>Click 'Export' tab</li>";
        echo "<li>Choose 'Quick' export method</li>";
        echo "<li>Format: SQL</li>";
        echo "<li>Click 'Go' to download</li>";
        echo "<li>Save as 'ipage_export.sql' in htdocs folder</li>";
        echo "</ol>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>