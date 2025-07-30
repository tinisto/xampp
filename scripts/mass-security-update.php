<?php
// Mass security update script
echo "🔒 Mass Security Update Script\n";
echo "==============================\n\n";

$rootDir = dirname(__DIR__);
$updates = [];

// Define file mappings for critical updates
$fileUpdates = [
    // Login system
    '/pages/login/login_content.php' => '/secure-updates/login_content_secure.php',
    '/pages/login/login_process.php' => '/secure-updates/login_process_secure.php',
    
    // Search system  
    '/pages/search/search-process.php' => '/secure-updates/search-process-secure.php',
    
    // Registration
    '/pages/registration/registration_form.php' => 'ADD_SECURITY',
    '/pages/registration/registration_process.php' => 'ADD_SECURITY',
    
    // Account pages
    '/pages/account/password-change/password-change.php' => 'ADD_SECURITY',
    '/pages/account/personal-data-change/personal-data-change.php' => 'ADD_SECURITY',
    '/pages/account/delete-account/delete-account.php' => 'ADD_SECURITY',
];

// Function to add security to a file
function addSecurityToFile($filePath) {
    if (!file_exists($filePath)) {
        return false;
    }
    
    $content = file_get_contents($filePath);
    $modified = false;
    
    // Add init.php if not present
    if (strpos($content, 'includes/init.php') === false) {
        $phpPos = strpos($content, '<?php');
        if ($phpPos !== false) {
            $depth = substr_count($filePath, '/') - 2;
            $prefix = str_repeat('../', $depth);
            $initInclude = "\nrequire_once __DIR__ . '/{$prefix}includes/init.php';\n";
            $content = substr($content, 0, $phpPos + 5) . $initInclude . substr($content, $phpPos + 5);
            $modified = true;
        }
    }
    
    // Add CSRF to forms
    if (preg_match('/<form[^>]*method=["\']post["\'][^>]*>/i', $content)) {
        if (strpos($content, 'csrf_field()') === false) {
            $content = preg_replace(
                '/(<form[^>]*method=["\']post["\'][^>]*>)/i',
                '$1' . "\n    <?php echo csrf_field(); ?>",
                $content
            );
            $modified = true;
        }
    }
    
    // Update echo statements to use h()
    if (strpos($content, 'echo $') !== false || strpos($content, '<?= $') !== false) {
        // Simple variable echoes
        $content = preg_replace('/\<\?=\s*\$([a-zA-Z_][a-zA-Z0-9_]*)\s*\?\>/', '<?= h($\1) ?>', $content);
        $content = preg_replace('/echo\s+\$([a-zA-Z_][a-zA-Z0-9_]*)\s*;/', 'echo h($\1);', $content);
        $modified = true;
    }
    
    // Update database queries
    if (strpos($content, 'mysqli_query') !== false) {
        // Add $db instance
        if (strpos($content, '$db = Database::getInstance()') === false) {
            $content = str_replace(
                'require_once __DIR__',
                '$db = Database::getInstance($connection);' . "\n" . 'require_once __DIR__',
                $content
            );
        }
        
        // Simple SELECT queries
        $content = preg_replace(
            '/mysqli_query\s*\(\s*\$connection\s*,\s*"SELECT\s+\*\s+FROM\s+(\w+)"\s*\)/',
            '\$db->queryAll("SELECT * FROM $1")',
            $content
        );
        
        $modified = true;
    }
    
    if ($modified) {
        // Backup original
        copy($filePath, $filePath . '.backup');
        file_put_contents($filePath, $content);
        return true;
    }
    
    return false;
}

// Process all updates
foreach ($fileUpdates as $targetFile => $action) {
    $targetPath = $rootDir . $targetFile;
    
    echo "Processing: $targetFile\n";
    
    if ($action === 'ADD_SECURITY') {
        // Add security features to existing file
        if (addSecurityToFile($targetPath)) {
            echo "  ✅ Security features added\n";
            $updates[] = $targetFile;
        } else {
            echo "  ⏭️  Already secure or not found\n";
        }
    } else {
        // Replace with secure version
        $sourcePath = $rootDir . $action;
        if (file_exists($sourcePath) && file_exists($targetPath)) {
            copy($targetPath, $targetPath . '.backup');
            copy($sourcePath, $targetPath);
            echo "  ✅ Replaced with secure version\n";
            $updates[] = $targetFile;
        } else {
            echo "  ❌ Source or target not found\n";
        }
    }
}

// Create a universal patch for all PHP files
echo "\n📝 Creating universal security patch...\n";

$patchContent = '<?php
// Universal Security Patch
// Add this to the top of any PHP file that needs security

// Check if init.php is already included
if (!defined("SECURITY_INITIALIZED")) {
    $depth = substr_count(__FILE__, "/") - substr_count($_SERVER["DOCUMENT_ROOT"], "/") - 1;
    $prefix = str_repeat("../", $depth);
    
    if (file_exists(__DIR__ . "/" . $prefix . "includes/init.php")) {
        require_once __DIR__ . "/" . $prefix . "includes/init.php";
        define("SECURITY_INITIALIZED", true);
    }
}

// Auto-add CSRF to forms via output buffering
if (!defined("CSRF_AUTO_ADDED")) {
    ob_start(function($buffer) {
        if (strpos($_SERVER["REQUEST_URI"], ".php") !== false) {
            $buffer = preg_replace(
                "/(<form[^>]*method=[\"\\"]post[\"\\"][^>]*>)/i",
                "$1\\n    <?php echo csrf_field(); ?>",
                $buffer
            );
        }
        return $buffer;
    });
    define("CSRF_AUTO_ADDED", true);
}
?>';

file_put_contents($rootDir . '/includes/universal-security-patch.php', $patchContent);

echo "\n✅ Updated " . count($updates) . " files\n";
echo "📁 Backup files created with .backup extension\n";
echo "🔒 Universal security patch created at: /includes/universal-security-patch.php\n";

// Create deployment package
echo "\n📦 Creating deployment package...\n";

$deployFiles = array_merge($updates, [
    '/includes/Database.php',
    '/includes/Security.php',
    '/includes/helpers.php',
    '/includes/Cache.php',
    '/includes/ErrorHandler.php',
    '/includes/init.php',
    '/includes/csrf-middleware.php',
    '/includes/universal-security-patch.php'
]);

file_put_contents($rootDir . '/deploy-files.json', json_encode($deployFiles, JSON_PRETTY_PRINT));
echo "📋 Deployment file list saved to: deploy-files.json\n";