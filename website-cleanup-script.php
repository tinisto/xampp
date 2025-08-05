<?php
/**
 * Website Cleanup Script
 * Safely removes or reorganizes temporary, test, and development files
 * 
 * Usage: php website-cleanup-script.php [--dry-run] [--interactive]
 */

$options = getopt('', ['dry-run', 'interactive', 'help']);
$dryRun = isset($options['dry-run']);
$interactive = isset($options['interactive']);

if (isset($options['help'])) {
    echo "Website Cleanup Script\n\n";
    echo "Usage: php website-cleanup-script.php [options]\n\n";
    echo "Options:\n";
    echo "  --dry-run      Show what would be deleted without actually deleting\n";
    echo "  --interactive  Ask for confirmation before each deletion\n";
    echo "  --help         Show this help message\n";
    exit(0);
}

// Define cleanup categories with safety levels
$cleanupCategories = [
    'safe_to_delete' => [
        'description' => 'Files that are safe to delete immediately',
        'patterns' => [
            '*.bak',
            '*.backup',
            '*.old',
            '*.tmp',
            'test-*.php',
            'check-*.php',
            'debug-*.php',
            'temp-*.php',
            'upload-*.py',
            'fix-*.py',
            'verify-*.py',
            'move-*.py',
            'find-*.py'
        ],
        'directories' => [
            '_cleanup/',
            '_old/',
            '_backup/',
            'test/',
            'temp/',
            'tmp/'
        ]
    ],
    'needs_review' => [
        'description' => 'Files that need manual review before deletion',
        'patterns' => [
            '*under_construction*.php',
            '*migration*.php',
            '*cleanup*.php',
            'safe-*.php',
            'prioritized-*.php'
        ],
        'files' => [
            'dashboard-professional.php',
            'dashboard/comments-simple.php',
            'dashboard/comments.php',
            'dashboard/database-text-cleanup.php',
            'admin/database-text-cleanup.php'
        ]
    ],
    'reorganize' => [
        'description' => 'Files that should be moved to appropriate locations',
        'moves' => [
            'admin/*.php' => 'dashboard/admin-tools/',
            'pages/dashboard/admin/*.php' => 'dashboard/admin-tools/',
            'migrations/*.php' => 'database/migrations/',
            'test_*.php' => 'tests/',
            'check_*.php' => 'diagnostics/'
        ]
    ]
];

// Initialize counters
$stats = [
    'files_found' => 0,
    'files_deleted' => 0,
    'files_moved' => 0,
    'space_freed' => 0,
    'errors' => []
];

// Color codes for terminal output
$colors = [
    'green' => "\033[0;32m",
    'yellow' => "\033[0;33m",
    'red' => "\033[0;31m",
    'blue' => "\033[0;34m",
    'reset' => "\033[0m"
];

function colorize($text, $color) {
    global $colors;
    return $colors[$color] . $text . $colors['reset'];
}

function formatBytes($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

function confirmAction($message) {
    echo $message . " (y/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    return trim(strtolower($line)) === 'y';
}

function findFiles($pattern, $baseDir = '.') {
    $files = [];
    
    // Check if directory exists
    if (!is_dir($baseDir)) {
        return $files;
    }
    
    try {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && fnmatch($pattern, $file->getFilename())) {
                $files[] = $file->getPathname();
            }
        }
    } catch (Exception $e) {
        // Directory doesn't exist or can't be accessed
        return $files;
    }
    
    return $files;
}

function deleteFile($file, $dryRun, $interactive) {
    global $stats;
    
    if (!file_exists($file)) {
        return false;
    }
    
    $size = filesize($file);
    $displayPath = str_replace($_SERVER['DOCUMENT_ROOT'] . '/', '', $file);
    
    if ($interactive && !$dryRun) {
        if (!confirmAction("Delete $displayPath (" . formatBytes($size) . ")?")) {
            echo colorize("  Skipped: $displayPath\n", 'yellow');
            return false;
        }
    }
    
    if ($dryRun) {
        echo colorize("  [DRY RUN] Would delete: $displayPath (" . formatBytes($size) . ")\n", 'blue');
        $stats['files_found']++;
        $stats['space_freed'] += $size;
    } else {
        if (unlink($file)) {
            echo colorize("  ✓ Deleted: $displayPath (" . formatBytes($size) . ")\n", 'green');
            $stats['files_deleted']++;
            $stats['space_freed'] += $size;
        } else {
            echo colorize("  ✗ Failed to delete: $displayPath\n", 'red');
            $stats['errors'][] = "Failed to delete: $file";
        }
    }
    
    return true;
}

function moveFile($source, $destination, $dryRun, $interactive) {
    global $stats;
    
    if (!file_exists($source)) {
        return false;
    }
    
    $displaySource = str_replace($_SERVER['DOCUMENT_ROOT'] . '/', '', $source);
    $displayDest = str_replace($_SERVER['DOCUMENT_ROOT'] . '/', '', $destination);
    
    if ($interactive && !$dryRun) {
        if (!confirmAction("Move $displaySource to $displayDest?")) {
            echo colorize("  Skipped: $displaySource\n", 'yellow');
            return false;
        }
    }
    
    if ($dryRun) {
        echo colorize("  [DRY RUN] Would move: $displaySource → $displayDest\n", 'blue');
        $stats['files_found']++;
    } else {
        // Create destination directory if needed
        $destDir = dirname($destination);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }
        
        if (rename($source, $destination)) {
            echo colorize("  ✓ Moved: $displaySource → $displayDest\n", 'green');
            $stats['files_moved']++;
        } else {
            echo colorize("  ✗ Failed to move: $displaySource\n", 'red');
            $stats['errors'][] = "Failed to move: $source to $destination";
        }
    }
    
    return true;
}

// Main cleanup process
echo colorize("\n=== Website Cleanup Script ===\n", 'blue');
if ($dryRun) {
    echo colorize("Running in DRY RUN mode - no files will be deleted\n", 'yellow');
}
if ($interactive) {
    echo colorize("Running in INTERACTIVE mode - will ask for confirmation\n", 'yellow');
}
echo "\n";

// Process safe to delete files
echo colorize("1. Processing files safe to delete...\n", 'green');
foreach ($cleanupCategories['safe_to_delete']['patterns'] as $pattern) {
    $files = findFiles($pattern);
    if (!empty($files)) {
        echo "\nPattern: $pattern\n";
        foreach ($files as $file) {
            deleteFile($file, $dryRun, $interactive);
        }
    }
}

// Process directories safe to delete
foreach ($cleanupCategories['safe_to_delete']['directories'] as $dir) {
    if (is_dir($dir)) {
        echo "\nDirectory: $dir\n";
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                deleteFile($file->getPathname(), $dryRun, $interactive);
            }
        }
        
        // Remove empty directory
        if (!$dryRun && count(scandir($dir)) == 2) {
            rmdir($dir);
            echo colorize("  ✓ Removed empty directory: $dir\n", 'green');
        }
    }
}

// Process files needing review
echo colorize("\n2. Files needing manual review...\n", 'yellow');
$reviewFiles = [];

foreach ($cleanupCategories['needs_review']['patterns'] as $pattern) {
    $files = findFiles($pattern);
    $reviewFiles = array_merge($reviewFiles, $files);
}

foreach ($cleanupCategories['needs_review']['files'] as $file) {
    if (file_exists($file)) {
        $reviewFiles[] = $file;
    }
}

if (!empty($reviewFiles)) {
    echo "\nThe following files need manual review:\n";
    foreach ($reviewFiles as $file) {
        $displayPath = str_replace($_SERVER['DOCUMENT_ROOT'] . '/', '', $file);
        $size = file_exists($file) ? formatBytes(filesize($file)) : 'N/A';
        echo "  • $displayPath ($size)\n";
        
        if ($interactive && !$dryRun) {
            if (confirmAction("    Delete this file?")) {
                deleteFile($file, false, false);
            }
        }
    }
}

// Process files to reorganize
echo colorize("\n3. Reorganizing files...\n", 'blue');
foreach ($cleanupCategories['reorganize']['moves'] as $pattern => $destination) {
    $basePath = dirname($pattern);
    $filePattern = basename($pattern);
    
    if ($basePath === '.') {
        $files = findFiles($filePattern);
    } else {
        $files = findFiles($filePattern, $basePath);
    }
    
    if (!empty($files)) {
        echo "\nPattern: $pattern → $destination\n";
        foreach ($files as $file) {
            $filename = basename($file);
            $destPath = $destination . $filename;
            moveFile($file, $destPath, $dryRun, $interactive);
        }
    }
}

// Generate report
echo colorize("\n=== Cleanup Summary ===\n", 'blue');
echo "Files found: " . $stats['files_found'] . "\n";
echo "Files deleted: " . $stats['files_deleted'] . "\n";
echo "Files moved: " . $stats['files_moved'] . "\n";
echo "Space freed: " . formatBytes($stats['space_freed']) . "\n";

if (!empty($stats['errors'])) {
    echo colorize("\nErrors encountered:\n", 'red');
    foreach ($stats['errors'] as $error) {
        echo "  • $error\n";
    }
}

// Save cleanup log
$logFile = 'cleanup-log-' . date('Y-m-d-His') . '.txt';
$logContent = "Website Cleanup Log - " . date('Y-m-d H:i:s') . "\n";
$logContent .= str_repeat('=', 50) . "\n";
$logContent .= "Mode: " . ($dryRun ? 'DRY RUN' : 'ACTUAL') . "\n";
$logContent .= "Files found: " . $stats['files_found'] . "\n";
$logContent .= "Files deleted: " . $stats['files_deleted'] . "\n";
$logContent .= "Files moved: " . $stats['files_moved'] . "\n";
$logContent .= "Space freed: " . formatBytes($stats['space_freed']) . "\n";

if (!empty($reviewFiles)) {
    $logContent .= "\nFiles marked for review:\n";
    foreach ($reviewFiles as $file) {
        $logContent .= "  - $file\n";
    }
}

if (!empty($stats['errors'])) {
    $logContent .= "\nErrors:\n";
    foreach ($stats['errors'] as $error) {
        $logContent .= "  - $error\n";
    }
}

file_put_contents($logFile, $logContent);
echo colorize("\nLog saved to: $logFile\n", 'green');

// Recommendations
echo colorize("\n=== Recommendations ===\n", 'blue');
echo "1. Review the files marked for manual review\n";
echo "2. Consider creating a .gitignore file to prevent temporary files\n";
echo "3. Set up automated cleanup in cron for regular maintenance\n";
echo "4. Archive important migrations before deletion\n";

if ($dryRun) {
    echo colorize("\nTo perform actual cleanup, run without --dry-run flag\n", 'yellow');
}
?>