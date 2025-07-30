<?php
// Script to integrate security features into all PHP files

$rootDir = dirname(__DIR__);
$updatedFiles = [];
$errors = [];

// Function to add init.php include
function addInitInclude($content, $filePath) {
    // Check if already has init.php
    if (strpos($content, 'includes/init.php') !== false) {
        return $content;
    }
    
    // Find the first <?php tag
    $phpPos = strpos($content, '<?php');
    if ($phpPos === false) {
        return $content;
    }
    
    // Calculate relative path to includes
    $depth = substr_count(str_replace($GLOBALS['rootDir'], '', $filePath), '/') - 1;
    $relativePath = str_repeat('../', $depth);
    
    // Insert after <?php
    $initInclude = "\nrequire_once __DIR__ . '/{$relativePath}includes/init.php';\n";
    
    return substr($content, 0, $phpPos + 5) . $initInclude . substr($content, $phpPos + 5);
}

// Function to update echo statements to use h()
function addXSSProtection($content) {
    // Already using helpers?
    if (strpos($content, 'helpers.php') === false && strpos($content, 'init.php') === false) {
        return $content;
    }
    
    // Pattern to find echo statements with variables
    $patterns = [
        // <?= $var ?>
        '/\<\?=\s*\$([a-zA-Z_][a-zA-Z0-9_\[\]\'"\-\>]*)\s*\?\>/' => '<?= h($\1) ?>',
        
        // echo $var;
        '/echo\s+\$([a-zA-Z_][a-zA-Z0-9_\[\]\'"\-\>]*)\s*;/' => 'echo h($\1);',
        
        // echo $var
        '/echo\s+\$([a-zA-Z_][a-zA-Z0-9_\[\]\'"\-\>]*)\s*\?>/' => 'echo h($\1) ?>',
    ];
    
    foreach ($patterns as $pattern => $replacement) {
        // Skip if already using h()
        if (strpos($content, 'h($') === false) {
            $content = preg_replace($pattern, $replacement, $content);
        }
    }
    
    return $content;
}

// Function to add CSRF to forms
function addCSRFToForms($content) {
    // Pattern to find form tags with method="post"
    $pattern = '/<form([^>]*method\s*=\s*["\']post["\'][^>]*)>/i';
    
    // Check if form already has CSRF
    if (strpos($content, 'csrf_field()') !== false || strpos($content, 'csrf_token') !== false) {
        return $content;
    }
    
    $replacement = '<form$1>' . "\n    <?php echo csrf_field(); ?>";
    $content = preg_replace($pattern, $replacement, $content);
    
    return $content;
}

// Function to update database queries
function updateDatabaseQueries($content) {
    // Skip if already using Database class
    if (strpos($content, 'Database::getInstance') !== false) {
        return $content;
    }
    
    // Add database instance at the beginning of functions/files that use queries
    if (preg_match('/mysqli_query\s*\(/', $content)) {
        // Find a good place to add $db initialization
        $pattern = '/(function\s+\w+\s*\([^)]*\)\s*{)/';
        $replacement = '$1' . "\n    \$db = Database::getInstance();";
        $content = preg_replace($pattern, $replacement, $content);
    }
    
    // Update simple SELECT queries
    $content = preg_replace(
        '/\$result\s*=\s*mysqli_query\s*\(\s*\$connection\s*,\s*"SELECT\s+\*\s+FROM\s+(\w+)\s+WHERE\s+(\w+)\s*=\s*\'\s*"\s*\.\s*\$(\w+)\s*\.\s*"\s*\'"\s*\)/',
        '$result = $db->query("SELECT * FROM $1 WHERE $2 = ?", [$3])',
        $content
    );
    
    return $content;
}

// Function to process a single file
function processFile($filePath) {
    global $updatedFiles, $errors;
    
    // Skip non-PHP files
    if (pathinfo($filePath, PATHINFO_EXTENSION) !== 'php') {
        return;
    }
    
    // Read file content
    $content = file_get_contents($filePath);
    $originalContent = $content;
    
    // Apply updates
    $content = addInitInclude($content, $filePath);
    $content = addXSSProtection($content);
    $content = addCSRFToForms($content);
    $content = updateDatabaseQueries($content);
    
    // Check if content changed
    if ($content !== $originalContent) {
        // Backup original
        $backupPath = $filePath . '.backup.' . date('Y-m-d-H-i-s');
        file_put_contents($backupPath, $originalContent);
        
        // Write updated content
        if (file_put_contents($filePath, $content)) {
            $updatedFiles[] = str_replace($GLOBALS['rootDir'], '', $filePath);
        } else {
            $errors[] = "Failed to update: $filePath";
        }
    }
}

// Process all PHP files in pages directory
function processDirectory($dir) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            processFile($file->getPathname());
        }
    }
}

echo "Security Integration Script\n";
echo "==========================\n\n";

// Process pages directory
echo "Processing pages directory...\n";
processDirectory($rootDir . '/pages');

// Process root index.php if exists
if (file_exists($rootDir . '/index.php')) {
    echo "Processing root index.php...\n";
    processFile($rootDir . '/index.php');
}

echo "\n✅ Updated " . count($updatedFiles) . " files:\n";
foreach ($updatedFiles as $file) {
    echo "  - $file\n";
}

if (!empty($errors)) {
    echo "\n❌ Errors:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
}

echo "\n📁 Backup files created with .backup extension\n";
echo "🔒 Security features integrated!\n";