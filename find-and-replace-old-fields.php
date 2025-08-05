<?php
// Find and replace old database field names in PHP files
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Find and Replace Old Database Field Names</h1>";

// Define the replacements needed based on database analysis
$replacements = [
    // Primary key replacements
    'area_id' => [
        'old' => ['area_id', 'id_area'],
        'new' => 'id',
        'context' => 'areas table primary key'
    ],
    'country_id_pk' => [
        'old' => ['country_id'],
        'new' => 'id', 
        'context' => 'countries table primary key (be careful with foreign keys)'
    ],
    'region_id_pk' => [
        'old' => ['region_id'],
        'new' => 'id',
        'context' => 'regions table primary key (be careful with foreign keys)'
    ],
    'town_id_pk' => [
        'old' => ['town_id'],
        'new' => 'id',
        'context' => 'towns table primary key (be careful with foreign keys)'
    ],
    
    // Foreign key replacements
    'entity_id' => [
        'old' => ['id_entity'],
        'new' => 'entity_id',
        'context' => 'comments table foreign key'
    ],
    'vpo_id' => [
        'old' => ['id_vpo'],
        'new' => 'vpo_id',
        'context' => 'news table foreign key'
    ],
    'spo_id' => [
        'old' => ['id_spo'],
        'new' => 'spo_id',
        'context' => 'news table foreign key'
    ],
    'school_id' => [
        'old' => ['id_school'],
        'new' => 'school_id',
        'context' => 'news/schools table foreign key'
    ],
    'rono_id' => [
        'old' => ['id_rono'],
        'new' => 'rono_id',
        'context' => 'schools table foreign key'
    ],
    'indeks_id' => [
        'old' => ['id_indeks'],
        'new' => 'indeks_id',
        'context' => 'schools table foreign key'
    ],
    'country_id_fk' => [
        'old' => ['id_country'],
        'new' => 'country_id',
        'context' => 'foreign key in schools/spo/vpo tables'
    ],
];

// Function to search for patterns in files
function searchInFile($file, $patterns) {
    $content = file_get_contents($file);
    $found = [];
    
    foreach ($patterns as $pattern) {
        // Search for SQL queries
        if (preg_match_all("/['\"`]?\b" . preg_quote($pattern, '/') . "\b['\"`]?/i", $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                $found[] = [
                    'pattern' => $pattern,
                    'line' => $line,
                    'context' => getLineContext($content, $match[1])
                ];
            }
        }
    }
    
    return $found;
}

// Function to get context around a match
function getLineContext($content, $offset) {
    $start = strrpos(substr($content, 0, $offset), "\n");
    $end = strpos($content, "\n", $offset);
    
    if ($start === false) $start = 0;
    if ($end === false) $end = strlen($content);
    
    return trim(substr($content, $start, $end - $start));
}

// Directories to search
$searchDirs = [
    $_SERVER['DOCUMENT_ROOT'] . '/pages',
    $_SERVER['DOCUMENT_ROOT'] . '/includes',
    $_SERVER['DOCUMENT_ROOT'] . '/api',
    $_SERVER['DOCUMENT_ROOT'] . '/comments',
    $_SERVER['DOCUMENT_ROOT'] . '/common-components',
];

// File extensions to search
$extensions = ['php'];

echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .container { max-width: 1400px; margin: 0 auto; }
    .file-section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .replacement { background: #fff3cd; padding: 10px; margin: 10px 0; border-radius: 5px; }
    .pattern { color: #d00; font-weight: bold; }
    .new-pattern { color: #080; font-weight: bold; }
    .line-number { color: #666; }
    .context { background: #f8f9fa; padding: 5px; font-family: monospace; font-size: 12px; margin: 5px 0; }
    table { width: 100%; border-collapse: collapse; margin: 10px 0; }
    th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
    th { background: #f8f9fa; }
    .summary { background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0; }
</style>";

echo "<div class='container'>";

$totalFiles = 0;
$totalIssues = 0;
$fileIssues = [];

// Search through directories
foreach ($searchDirs as $dir) {
    if (!is_dir($dir)) continue;
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $file) {
        if (!$file->isFile()) continue;
        
        $ext = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
        if (!in_array($ext, $extensions)) continue;
        
        $totalFiles++;
        $filePath = $file->getPathname();
        $relativePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $filePath);
        
        // Search for each replacement pattern
        foreach ($replacements as $key => $replacement) {
            $found = searchInFile($filePath, $replacement['old']);
            
            if (!empty($found)) {
                if (!isset($fileIssues[$relativePath])) {
                    $fileIssues[$relativePath] = [];
                }
                
                foreach ($found as $issue) {
                    $fileIssues[$relativePath][] = [
                        'pattern' => $issue['pattern'],
                        'new' => $replacement['new'],
                        'line' => $issue['line'],
                        'context' => $issue['context'],
                        'description' => $replacement['context']
                    ];
                    $totalIssues++;
                }
            }
        }
    }
}

// Display results
echo "<div class='summary'>";
echo "<h2>📊 Search Summary</h2>";
echo "<p>Files searched: $totalFiles</p>";
echo "<p>Total issues found: $totalIssues</p>";
echo "<p>Files with issues: " . count($fileIssues) . "</p>";
echo "</div>";

if (!empty($fileIssues)) {
    echo "<h2>📋 Files Requiring Updates</h2>";
    
    foreach ($fileIssues as $file => $issues) {
        echo "<div class='file-section'>";
        echo "<h3>📄 $file</h3>";
        echo "<table>";
        echo "<tr><th>Line</th><th>Old Pattern</th><th>New Pattern</th><th>Context</th><th>Code</th></tr>";
        
        foreach ($issues as $issue) {
            echo "<tr>";
            echo "<td class='line-number'>{$issue['line']}</td>";
            echo "<td class='pattern'>{$issue['pattern']}</td>";
            echo "<td class='new-pattern'>{$issue['new']}</td>";
            echo "<td>{$issue['description']}</td>";
            echo "<td><div class='context'>" . htmlspecialchars($issue['context']) . "</div></td>";
            echo "</tr>";
        }
        
        echo "</table>";
        echo "</div>";
    }
    
    // Generate replacement commands
    echo "<div class='file-section'>";
    echo "<h2>🔧 Replacement Commands</h2>";
    echo "<p>Here are the sed commands to fix these issues:</p>";
    echo "<pre style='background: #f8f9fa; padding: 15px; overflow-x: auto;'>";
    
    foreach ($replacements as $key => $replacement) {
        foreach ($replacement['old'] as $old) {
            echo "# Replace $old with {$replacement['new']} ({$replacement['context']})\n";
            echo "find . -name '*.php' -type f -exec sed -i '' 's/\\b$old\\b/{$replacement['new']}/g' {} +\n\n";
        }
    }
    
    echo "</pre>";
    echo "</div>";
} else {
    echo "<div class='summary' style='background: #d4edda; color: #155724;'>";
    echo "<h2>✅ No Issues Found!</h2>";
    echo "<p>All files appear to be using the correct field names.</p>";
    echo "</div>";
}

echo "</div>";
?>