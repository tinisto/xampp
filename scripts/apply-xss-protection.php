<?php
// Script to apply XSS protection to all PHP files

$rootDir = dirname(__DIR__);
$helperInclude = "require_once __DIR__ . '/../includes/helpers.php';\n";

function processFile($filePath) {
    $content = file_get_contents($filePath);
    $modified = false;
    
    // Pattern to find echo statements with variables
    $patterns = [
        // Echo with <?= $var ?>
        '/\<\?=\s*\$([a-zA-Z_][a-zA-Z0-9_]*)\s*\?\>/' => '<?= h($\1) ?>',
        
        // Echo with <?php echo $var ?>
        '/\<\?php\s+echo\s+\$([a-zA-Z_][a-zA-Z0-9_]*)\s*;?\s*\?\>/' => '<?php echo h($\1); ?>',
        
        // Direct echo of variables
        '/echo\s+\$([a-zA-Z_][a-zA-Z0-9_]*)\s*;/' => 'echo h($\1);'
    ];
    
    // Skip files that already have helpers included
    if (strpos($content, 'helpers.php') !== false || strpos($content, 'Security::cleanOutput') !== false) {
        return false;
    }
    
    // Apply patterns
    foreach ($patterns as $pattern => $replacement) {
        $newContent = preg_replace($pattern, $replacement, $content);
        if ($newContent !== $content) {
            $content = $newContent;
            $modified = true;
        }
    }
    
    // Add helper include if modifications were made
    if ($modified) {
        // Find the first <?php tag
        $phpTagPos = strpos($content, '<?php');
        if ($phpTagPos !== false) {
            $insertPos = $phpTagPos + 5; // After <?php
            
            // Check if there's already a require/include at the beginning
            $afterTag = substr($content, $insertPos, 100);
            if (!preg_match('/^\s*(require|include)/', $afterTag)) {
                $content = substr($content, 0, $insertPos) . "\n" . $helperInclude . substr($content, $insertPos);
            }
        }
        
        file_put_contents($filePath, $content);
        return true;
    }
    
    return false;
}

function scanDirectory($dir) {
    $modifiedFiles = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $filePath = $file->getPathname();
            
            // Skip vendor and certain directories
            if (strpos($filePath, '/vendor/') !== false ||
                strpos($filePath, '/scripts/') !== false ||
                strpos($filePath, '/includes/') !== false) {
                continue;
            }
            
            if (processFile($filePath)) {
                $modifiedFiles[] = $filePath;
            }
        }
    }
    
    return $modifiedFiles;
}

// Run the script
echo "Starting XSS protection application...\n";
$modifiedFiles = scanDirectory($rootDir . '/pages');

echo "\nModified " . count($modifiedFiles) . " files:\n";
foreach ($modifiedFiles as $file) {
    echo "- " . str_replace($rootDir, '', $file) . "\n";
}

echo "\nXSS protection applied successfully!\n";