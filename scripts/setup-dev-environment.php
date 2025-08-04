<?php
/**
 * Development Environment Setup Script
 * Automates the setup of a development environment for 11klassniki
 */

echo "🚀 11klassniki Development Environment Setup\n";
echo "============================================\n\n";

// Check if running in CLI
if (php_sapi_name() !== 'cli') {
    die('This script must be run from the command line.');
}

// Define project root
$projectRoot = dirname(__DIR__);
$errors = [];
$warnings = [];
$success = [];

/**
 * Helper functions
 */
function checkRequirement($name, $condition, $errorMsg = '', $successMsg = '') {
    global $errors, $success;
    
    echo "Checking {$name}... ";
    
    if ($condition) {
        echo "✅ OK\n";
        if ($successMsg) {
            $success[] = $successMsg;
        }
        return true;
    } else {
        echo "❌ FAILED\n";
        if ($errorMsg) {
            $errors[] = $errorMsg;
        }
        return false;
    }
}

function createDirectory($path, $description = '') {
    global $success, $errors;
    
    $desc = $description ?: basename($path);
    echo "Creating {$desc} directory... ";
    
    if (is_dir($path)) {
        echo "✓ Already exists\n";
        return true;
    }
    
    if (mkdir($path, 0755, true)) {
        echo "✅ Created\n";
        $success[] = "Created {$desc} directory";
        return true;
    } else {
        echo "❌ Failed\n";
        $errors[] = "Failed to create {$desc} directory: {$path}";
        return false;
    }
}

function createFile($path, $content, $description = '') {
    global $success, $errors;
    
    $desc = $description ?: basename($path);
    echo "Creating {$desc}... ";
    
    if (file_exists($path)) {
        echo "✓ Already exists\n";
        return true;
    }
    
    if (file_put_contents($path, $content) !== false) {
        echo "✅ Created\n";
        $success[] = "Created {$desc}";
        return true;
    } else {
        echo "❌ Failed\n";
        $errors[] = "Failed to create {$desc}";
        return false;
    }
}

// Step 1: Check PHP Requirements
echo "📋 Step 1: Checking PHP Requirements\n";
echo "------------------------------------\n";

checkRequirement(
    'PHP Version', 
    version_compare(PHP_VERSION, '7.4.0', '>='),
    'PHP 7.4.0 or higher is required. Current version: ' . PHP_VERSION
);

checkRequirement(
    'MySQLi Extension',
    extension_loaded('mysqli'),
    'MySQLi extension is required for database connectivity'
);

checkRequirement(
    'GD Extension',
    extension_loaded('gd'),
    'GD extension is required for image processing'
);

checkRequirement(
    'cURL Extension',
    extension_loaded('curl'),
    'cURL extension is required for external API calls'
);

checkRequirement(
    'JSON Extension',
    extension_loaded('json'),
    'JSON extension is required'
);

checkRequirement(
    'Session Support',
    function_exists('session_start'),
    'Session support is required'
);

echo "\n";

// Step 2: Check Directory Permissions
echo "📁 Step 2: Checking Directory Permissions\n";
echo "------------------------------------------\n";

$requiredDirs = [
    $projectRoot => 'Project root',
    $projectRoot . '/cache' => 'Cache directory',
    $projectRoot . '/logs' => 'Logs directory',
    $projectRoot . '/uploads' => 'Uploads directory',
    $projectRoot . '/build' => 'Build directory'
];

foreach ($requiredDirs as $dir => $description) {
    checkRequirement(
        "{$description} writable",
        is_writable($dir) || !file_exists($dir),
        "Directory {$dir} is not writable"
    );
}

echo "\n";

// Step 3: Create Required Directories
echo "🏗️  Step 3: Creating Required Directories\n";
echo "-----------------------------------------\n";

$dirsToCreate = [
    $projectRoot . '/cache/pages' => 'Page cache',
    $projectRoot . '/cache/queries' => 'Query cache', 
    $projectRoot . '/logs/errors' => 'Error logs',
    $projectRoot . '/logs/performance' => 'Performance logs',
    $projectRoot . '/uploads/temp' => 'Temporary uploads',
    $projectRoot . '/build/assets' => 'Build assets',
    $projectRoot . '/tests/coverage' => 'Test coverage',
    $projectRoot . '/docs/api' => 'API documentation'
];

foreach ($dirsToCreate as $dir => $description) {
    createDirectory($dir, $description);
}

echo "\n";

// Step 4: Create Configuration Files
echo "⚙️  Step 4: Creating Configuration Files\n";
echo "----------------------------------------\n";

// Create .env.example if it doesn't exist
$envExample = <<<ENV
# Database Configuration
DB_HOST=localhost
DB_USERNAME=your_username
DB_PASSWORD=your_password
DB_DATABASE=your_database

# Security Settings
CSRF_TOKEN_TTL=3600
RATE_LIMIT_ENABLED=true
SECURITY_HEADERS_ENABLED=true

# Cache Settings
CACHE_ENABLED=true
CACHE_TTL=3600

# Performance Monitoring
PERFORMANCE_MONITORING=true
ERROR_LOGGING=true

# Development Settings
DEBUG_MODE=false
DISPLAY_ERRORS=false
LOG_ERRORS=true

# FTP Settings (for deployment)
FTP_HOST=your_ftp_host
FTP_USERNAME=your_ftp_username
FTP_PASSWORD=your_ftp_password
FTP_PATH=/public_html/

ENV;

createFile($projectRoot . '/.env.example', $envExample, '.env.example');

// Create .gitignore if it doesn't exist
$gitignore = <<<GITIGNORE
# Environment files
.env
.env.local
.env.production

# Cache files
/cache/*
!/cache/.gitkeep

# Log files
/logs/*
!/logs/.gitkeep

# Uploads
/uploads/*
!/uploads/.gitkeep

# Build artifacts
/build/assets/*
!/build/assets/.gitkeep

# Test coverage
/tests/coverage/*

# Temporary files
*.tmp
*.temp
.DS_Store
Thumbs.db

# IDE files
.vscode/
.idea/
*.swp
*.swo

# Vendor files (if using Composer differently)
/vendor/

# Node modules (if using npm)
node_modules/

GITIGNORE;

createFile($projectRoot . '/.gitignore', $gitignore, '.gitignore');

// Create development configuration
$devConfig = <<<PHP
<?php
/**
 * Development Configuration
 * Override settings for development environment
 */

// Enable error reporting in development
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Development database settings
\$dev_db_config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => '11klassniki_dev'
];

// Disable caching in development
define('DEV_CACHE_DISABLED', true);

// Enable debug mode
define('DEBUG_MODE', true);

// Development-specific paths
define('DEV_UPLOADS_PATH', __DIR__ . '/uploads/dev/');
define('DEV_CACHE_PATH', __DIR__ . '/cache/dev/');

// Development utilities
function dd(\$data) {
    echo '<pre>';
    var_dump(\$data);
    echo '</pre>';
    die();
}

function debug_log(\$message, \$data = null) {
    \$logEntry = date('Y-m-d H:i:s') . ' - ' . \$message;
    if (\$data) {
        \$logEntry .= ' - ' . json_encode(\$data, JSON_PRETTY_PRINT);
    }
    error_log(\$logEntry . "\\n", 3, __DIR__ . '/logs/debug.log');
}
?>
PHP;

createFile($projectRoot . '/config/development.php', $devConfig, 'Development config');

echo "\n";

// Step 5: Install Dependencies
echo "📦 Step 5: Installing Dependencies\n";
echo "----------------------------------\n";

// Check if Composer is available
echo "Checking for Composer... ";
$composerPath = null;

// Try different common paths for Composer
$composerPaths = [
    'composer',           // Global composer
    'composer.phar',      // Local composer.phar
    '/usr/local/bin/composer',
    '/usr/bin/composer'
];

foreach ($composerPaths as $path) {
    $output = shell_exec("which {$path} 2>/dev/null");
    if (!empty($output)) {
        $composerPath = trim($output);
        break;
    }
}

if ($composerPath) {
    echo "✅ Found at {$composerPath}\n";
    
    echo "Installing PHP dependencies... ";
    $output = shell_exec("cd {$projectRoot} && {$composerPath} install --dev 2>&1");
    
    if (strpos($output, 'error') === false && strpos($output, 'Error') === false) {
        echo "✅ Installed\n";
        $success[] = "PHP dependencies installed";
    } else {
        echo "❌ Failed\n";
        $errors[] = "Failed to install PHP dependencies: " . $output;
    }
} else {
    echo "⚠️  Not found\n";
    $warnings[] = "Composer not found. Please install dependencies manually: composer install --dev";
}

echo "\n";

// Step 6: Database Setup
echo "🗄️  Step 6: Database Setup\n";
echo "---------------------------\n";

echo "Checking database connection... ";

// Try to connect to database (assuming localhost MySQL)
$connection = @mysqli_connect('localhost', 'root', '');

if ($connection) {
    echo "✅ MySQL connection successful\n";
    
    // Check if database exists
    $dbName = '11klassniki_dev';
    $result = mysqli_query($connection, "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$dbName}'");
    
    if (mysqli_num_rows($result) == 0) {
        echo "Creating development database... ";
        if (mysqli_query($connection, "CREATE DATABASE `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
            echo "✅ Created\n";
            $success[] = "Development database created";
        } else {
            echo "❌ Failed\n";
            $errors[] = "Failed to create development database";
        }
    } else {
        echo "Development database already exists ✓\n";
    }
    
    mysqli_close($connection);
} else {
    echo "❌ Failed\n";
    $warnings[] = "Could not connect to MySQL. Please ensure MySQL is running and create database manually.";
}

echo "\n";

// Step 7: Create Development Scripts
echo "🔧 Step 7: Creating Development Scripts\n";
echo "---------------------------------------\n";

// Create start development server script
$startScript = <<<BASH
#!/bin/bash
# Start development server

echo "🚀 Starting 11klassniki Development Server"
echo "=========================================="

# Start PHP built-in server
php -S localhost:8080 -t . &
SERVER_PID=\$!

echo "✅ Server started at http://localhost:8080"
echo "📊 Admin panel: http://localhost:8080/admin/"
echo "🔧 Cache management: http://localhost:8080/admin/cache-management.php"
echo ""
echo "Press Ctrl+C to stop the server"

# Wait for Ctrl+C
trap "echo ''; echo '🛑 Stopping server...'; kill \$SERVER_PID; exit 0" INT
wait \$SERVER_PID

BASH;

createFile($projectRoot . '/scripts/start-dev-server.sh', $startScript, 'Development server script');

// Make script executable
if (file_exists($projectRoot . '/scripts/start-dev-server.sh')) {
    chmod($projectRoot . '/scripts/start-dev-server.sh', 0755);
}

// Create database reset script
$dbResetScript = <<<PHP
<?php
/**
 * Database Reset Script for Development
 * Resets database to clean state
 */

require_once __DIR__ . '/../config/loadEnv.php';
require_once __DIR__ . '/../database/db_connections.php';
require_once __DIR__ . '/../includes/database/migration_manager.php';

echo "🗄️  Resetting Development Database\\n";
echo "==================================\\n\\n";

\$migrationManager = new MigrationManager(\$connection);

// Reset migrations
echo "Resetting migrations...\\n";
\$migrationManager->reset();
echo "✅ Migrations reset\\n\\n";

// Run all migrations
echo "Running migrations...\\n";
\$results = \$migrationManager->migrate();

foreach (\$results as \$result) {
    \$status = \$result['success'] ? '✅' : '❌';
    echo "{\$status} {\$result['migration']}: {\$result['message']}\\n";
}

echo "\\n✅ Database reset complete!\\n";
?>
PHP;

createFile($projectRoot . '/scripts/reset-database.php', $dbResetScript, 'Database reset script');

echo "\n";

// Step 8: Generate Summary
echo "📋 Step 8: Setup Summary\n";
echo "------------------------\n";

if (!empty($success)) {
    echo "✅ Successful operations:\n";
    foreach ($success as $item) {
        echo "   • {$item}\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠️  Warnings:\n";
    foreach ($warnings as $warning) {
        echo "   • {$warning}\n";
    }
    echo "\n";
}

if (!empty($errors)) {
    echo "❌ Errors:\n";
    foreach ($errors as $error) {
        echo "   • {$error}\n";
    }
    echo "\n";
} else {
    echo "🎉 Setup completed successfully!\n\n";
}

// Final instructions
echo "🚀 Next Steps:\n";
echo "--------------\n";
echo "1. Copy .env.example to .env and configure your settings\n";
echo "2. Update database credentials in .env file\n";
echo "3. Run migrations: php database/migrate.php migrate\n";
echo "4. Start development server: ./scripts/start-dev-server.sh\n";
echo "5. Visit http://localhost:8080 to view your application\n";
echo "6. Access admin panel at http://localhost:8080/admin/\n";
echo "\n";

echo "📚 Available Commands:\n";
echo "----------------------\n";
echo "• make help          - Show available make commands\n";
echo "• make test          - Run test suite\n";
echo "• make lint          - Run code quality checks\n";
echo "• make minify        - Minify CSS/JS assets\n";
echo "• make migrate       - Run database migrations\n";
echo "\n";

echo "🔧 Development Tools:\n";
echo "---------------------\n";
echo "• Cache Management:   /admin/cache-management.php\n";
echo "• Error Monitoring:   /admin/monitoring.php\n";
echo "• Migration Tool:     php database/migrate.php\n";
echo "• Asset Minifier:     php build/minify-assets.php\n";
echo "\n";

if (empty($errors)) {
    echo "✨ Happy coding! Your development environment is ready.\n";
    exit(0);
} else {
    echo "⚠️  Please fix the errors above before proceeding.\n";
    exit(1);
}
?>