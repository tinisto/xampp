<?php
// Script to find and report files with id_region references that need fixing

$root_dir = $_SERVER['DOCUMENT_ROOT'] ?? __DIR__;
$files_to_check = [];
$issues_found = [];

// Function to recursively scan directories
function scanDirectory($dir, &$files) {
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        
        $path = $dir . '/' . $item;
        
        // Skip certain directories
        if (is_dir($path)) {
            $skip_dirs = ['vendor', 'node_modules', '.git', 'cache', 'logs', '_cleanup', 'uploads'];
            if (in_array($item, $skip_dirs)) continue;
            
            scanDirectory($path, $files);
        } elseif (is_file($path) && pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            $files[] = $path;
        }
    }
}

// Scan the project
scanDirectory($root_dir, $files_to_check);

echo "<h2>Scanning for id_region references in SQL queries...</h2>\n";
echo "<p>Total PHP files to check: " . count($files_to_check) . "</p>\n";

// Patterns to look for
$patterns = [
    '/SELECT\s+.*\bid_region\b/i',
    '/FROM\s+regions.*\bid_region\b/i',
    '/WHERE\s+.*\bid_region\b/i',
    '/JOIN\s+.*ON\s+.*\bid_region\b/i',
    '/\$row\[[\'"]id_region[\'"]\]/i',
    '/id_school\b/i',
    '/id_college\b/i',
    '/id_university\b/i'
];

foreach ($files_to_check as $file) {
    $content = file_get_contents($file);
    $relative_path = str_replace($root_dir, '', $file);
    
    foreach ($patterns as $pattern) {
        if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $line_number = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                $issues_found[] = [
                    'file' => $relative_path,
                    'line' => $line_number,
                    'match' => $match[0],
                    'pattern' => $pattern
                ];
            }
        }
    }
}

// Display results
echo "<h3>Issues Found:</h3>\n";
if (empty($issues_found)) {
    echo "<p style='color: green;'>No id_region references found!</p>\n";
} else {
    echo "<p style='color: red;'>Found " . count($issues_found) . " potential issues:</p>\n";
    echo "<table border='1' cellpadding='5' cellspacing='0'>\n";
    echo "<tr><th>File</th><th>Line</th><th>Issue</th></tr>\n";
    
    foreach ($issues_found as $issue) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($issue['file']) . "</td>";
        echo "<td>" . $issue['line'] . "</td>";
        echo "<td><code>" . htmlspecialchars($issue['match']) . "</code></td>";
        echo "</tr>\n";
    }
    echo "</table>\n";
    
    // Group by file for summary
    $files_with_issues = [];
    foreach ($issues_found as $issue) {
        $files_with_issues[$issue['file']] = ($files_with_issues[$issue['file']] ?? 0) + 1;
    }
    
    echo "<h3>Files that need attention (" . count($files_with_issues) . " files):</h3>\n";
    echo "<ul>\n";
    foreach ($files_with_issues as $file => $count) {
        echo "<li>" . htmlspecialchars($file) . " - $count issues</li>\n";
    }
    echo "</ul>\n";
}

echo "<h3>Recommended fixes:</h3>\n";
echo "<ul>\n";
echo "<li>Replace 'id_region' with 'region_id' in SQL queries</li>\n";
echo "<li>Replace 'id_school' with 'id' for schools table</li>\n";
echo "<li>Replace 'id_college' with 'id' for colleges table</li>\n";
echo "<li>Replace 'id_university' with 'id' for universities table</li>\n";
echo "<li>Update any PHP array references from \$row['id_region'] to \$row['id'] or \$row['region_id'] as appropriate</li>\n";
echo "</ul>\n";
?>