<?php
// Comprehensive Database Field Mismatch Scanner
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', 300); // 5 minutes

echo "<h1>Database Field Mismatch Scanner</h1>";
echo "<p>This tool scans all PHP files and checks for database field mismatches.</p>";

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

if (!$connection) {
    die("❌ Database connection failed");
}

// First, get all table structures
echo "<h2>1. Getting Database Schema...</h2>";
$tables = ['news', 'categories', 'users', 'posts', 'schools', 'vpo', 'spo', 'comments', 'tests'];
$tableFields = [];

foreach ($tables as $table) {
    echo "<h3>Table: $table</h3>";
    $query = "DESCRIBE $table";
    $result = mysqli_query($connection, $query);
    
    if ($result) {
        $fields = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $fields[] = $row['Field'];
        }
        $tableFields[$table] = $fields;
        echo "<p>✅ Fields: " . implode(', ', $fields) . "</p>";
    } else {
        echo "<p>❌ Table '$table' not found or error: " . mysqli_error($connection) . "</p>";
        $tableFields[$table] = [];
    }
}

// Now scan PHP files for potential field references
echo "<h2>2. Scanning PHP Files...</h2>";

$scanDirectories = [
    '/Applications/XAMPP/xamppfiles/htdocs/pages',
    '/Applications/XAMPP/xamppfiles/htdocs/common-components', 
    '/Applications/XAMPP/xamppfiles/htdocs',
];

$suspiciousPatterns = [
    // Common field reference patterns
    '/\$[a-zA-Z_]+\[\'([a-zA-Z_]+)\'\]/',           // $row['field_name']
    '/\$[a-zA-Z_]+\["([a-zA-Z_]+)"\]/',             // $row["field_name"]  
    '/\$[a-zA-Z_]+-&gt;([a-zA-Z_]+)/',              // $obj->field_name
    '/SELECT\s+[^F]*?([a-zA-Z_]+\.[a-zA-Z_]+)/',    // SELECT table.field
    '/WHERE\s+[^=]*?([a-zA-Z_]+\.[a-zA-Z_]+)/',     // WHERE table.field
    '/UPDATE\s+[^S]*?SET\s+([a-zA-Z_]+)\s*=/',      // UPDATE SET field =
    '/INSERT\s+INTO\s+[^(]*?\(([^)]+)\)/',          // INSERT INTO table (fields)
];

function scanFile($filePath, $tableFields) {
    $content = file_get_contents($filePath);
    $issues = [];
    
    // Look for potential field references
    $patterns = [
        '/\$[a-zA-Z_]+\[\'([a-zA-Z_]+)\'\]/' => 'array_access',
        '/\$[a-zA-Z_]+\["([a-zA-Z_]+)"\]/' => 'array_access',
        '/(id_news|url_news|content_news|created_at|status|views|category_id|author_id|username)/' => 'suspicious_field'
    ];
    
    foreach ($patterns as $pattern => $type) {
        if (preg_match_all($pattern, $content, $matches)) {
            foreach ($matches[1] as $field) {
                // Check if this field exists in any table
                $foundInTable = false;
                foreach ($tableFields as $table => $fields) {
                    if (in_array($field, $fields)) {
                        $foundInTable = $table;
                        break;
                    }
                }
                
                if (!$foundInTable && $type === 'suspicious_field') {
                    $issues[] = [
                        'field' => $field,
                        'type' => $type,
                        'status' => 'not_found',
                        'suggestion' => getSuggestion($field, $tableFields)
                    ];
                } elseif (!$foundInTable && $type === 'array_access') {
                    $issues[] = [
                        'field' => $field,
                        'type' => $type, 
                        'status' => 'not_found',
                        'suggestion' => getSuggestion($field, $tableFields)
                    ];
                }
            }
        }
    }
    
    return $issues;
}

function getSuggestion($field, $tableFields) {
    $suggestions = [
        'id_news' => 'id (in news table)',
        'url_news' => 'url_slug (in news table)',
        'content_news' => 'text_news (in news table)',
        'created_at' => 'date_news (in news table)',
        'views' => 'view_news (in news table)',
        'status' => 'approved (in news table)',
        'category_id' => 'category_news (in news table)',
        'author_id' => 'user_id (in news table)',
        'username' => 'author_news (in news table) or check users table structure'
    ];
    
    if (isset($suggestions[$field])) {
        return $suggestions[$field];
    }
    
    // Try to find similar field names
    foreach ($tableFields as $table => $fields) {
        foreach ($fields as $realField) {
            if (levenshtein($field, $realField) <= 2 && strlen($field) > 3) {
                return "$realField (in $table table)";
            }
        }
    }
    
    return 'No suggestion';
}

function scanDirectory($dir, $tableFields, &$allIssues) {
    if (!is_dir($dir)) return;
    
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    
    foreach ($files as $file) {
        if ($file->getExtension() === 'php' && !strpos($file->getPathname(), '.old')) {
            $relativePath = str_replace('/Applications/XAMPP/xamppfiles/htdocs', '', $file->getPathname());
            $issues = scanFile($file->getPathname(), $tableFields);
            
            if (!empty($issues)) {
                $allIssues[$relativePath] = $issues;
            }
        }
    }
}

$allIssues = [];
foreach ($scanDirectories as $dir) {
    if (is_dir($dir)) {
        scanDirectory($dir, $tableFields, $allIssues);
    }
}

echo "<h2>3. Scan Results</h2>";

if (empty($allIssues)) {
    echo "<p style='color: green; font-size: 18px;'>✅ No obvious database field mismatches found!</p>";
} else {
    echo "<p style='color: orange;'>⚠️ Found " . count($allIssues) . " files with potential issues:</p>";
    
    foreach ($allIssues as $file => $issues) {
        echo "<div style='border: 1px solid #ccc; margin: 10px 0; padding: 10px;'>";
        echo "<h3>📁 $file</h3>";
        
        foreach ($issues as $issue) {
            $color = $issue['status'] === 'not_found' ? 'red' : 'orange';
            echo "<div style='margin: 5px 0; padding: 5px; background: #f9f9f9;'>";
            echo "<strong style='color: $color;'>Field:</strong> <code>{$issue['field']}</code><br>";
            echo "<strong>Type:</strong> {$issue['type']}<br>";
            echo "<strong>Status:</strong> {$issue['status']}<br>";
            echo "<strong>Suggestion:</strong> {$issue['suggestion']}<br>";
            echo "</div>";
        }
        
        echo "</div>";
    }
}

echo "<h2>4. Common Field Mapping Reference</h2>";
echo "<div style='background: #f0f8ff; padding: 15px; margin: 10px 0;'>";
echo "<h3>News Table Field Mappings:</h3>";
echo "<ul>";
echo "<li><code>id_news</code> → <code>id</code></li>";
echo "<li><code>url_news</code> → <code>url_slug</code></li>";
echo "<li><code>content_news</code> → <code>text_news</code></li>";
echo "<li><code>created_at</code> → <code>date_news</code></li>";
echo "<li><code>views</code> → <code>view_news</code></li>";
echo "<li><code>status</code> → <code>approved</code> (1 for published)</li>";
echo "<li><code>category_id</code> → <code>category_news</code></li>";
echo "<li><code>author_id</code> → <code>user_id</code></li>";
echo "<li><code>username</code> → <code>author_news</code> or check users table</li>";
echo "</ul>";
echo "</div>";

mysqli_close($connection);
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { border-collapse: collapse; width: 100%; margin: 10px 0; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
code { background-color: #f4f4f4; padding: 2px 4px; border-radius: 3px; }
</style>