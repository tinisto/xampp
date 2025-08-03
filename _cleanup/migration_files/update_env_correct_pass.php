<?php
/**
 * Update .env with correct admin_claude password
 */

echo "<h1>🔧 Update .env with Correct Credentials</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    .code { background: #f5f5f5; padding: 15px; border: 1px solid #ddd; font-family: monospace; margin: 10px 0; white-space: pre-wrap; }
    .button { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0; }
    .button:hover { background: #0056b3; }
    .danger { background: #dc3545; }
    .danger:hover { background: #c82333; }
</style>";

// First test which database we can access
echo "<h2>🔍 Testing Database Access...</h2>";

$can_access_claude = false;
$can_access_new = false;

// Test 11klassniki_claude
try {
    $test1 = new mysqli('11klassnikiru67871.ipagemysql.com', 'admin_claude', 'W4eZ!#9uwLmrMay', '11klassniki_claude');
    if (!$test1->connect_error) {
        $can_access_claude = true;
        $test1->close();
        echo "<p class='success'>✅ Can access 11klassniki_claude database</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Cannot access 11klassniki_claude: " . $e->getMessage() . "</p>";
}

// Test 11klassniki_new
try {
    $test2 = new mysqli('11klassnikiru67871.ipagemysql.com', 'admin_claude', 'W4eZ!#9uwLmrMay', '11klassniki_new');
    if (!$test2->connect_error) {
        $can_access_new = true;
        $test2->close();
        echo "<p class='success'>✅ Can access 11klassniki_new database</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Cannot access 11klassniki_new: " . $e->getMessage() . "</p>";
}

// Determine which database to use
$recommended_db = '';
if ($can_access_claude) {
    $recommended_db = '11klassniki_claude';
} elseif ($can_access_new) {
    $recommended_db = '11klassniki_new';
}

if (!$recommended_db) {
    die("<p class='error'>❌ Cannot access any database with these credentials. Please check with hosting provider.</p>");
}

echo "<div style='background: #d4edda; padding: 20px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px 0;'>";
echo "<p class='success'><strong>✅ Recommended database: $recommended_db</strong></p>";
echo "</div>";

// New .env content
$new_env_content = "# Production environment variables
DB_HOST=11klassnikiru67871.ipagemysql.com
DB_USER=admin_claude
DB_PASS=W4eZ!#9uwLmrMay
DB_NAME=$recommended_db

# Site settings
SITE_URL=https://11klassniki.ru
SITE_NAME=11 Классники

# Debug mode (set to false in production)
DEBUG_MODE=false

# Session settings
SESSION_LIFETIME=3600

# Upload settings
MAX_UPLOAD_SIZE=10485760
ALLOWED_IMAGE_TYPES=jpg,jpeg,png,gif,webp

# Old database reference (DO NOT USE)
# OLD_DB_NAME=11klassniki_ru
# OLD_DB_USER=11klone_user
# OLD_DB_PASS=K8HqqBV3hTf4mha";

// Check current .env
$env_path = $_SERVER['DOCUMENT_ROOT'] . '/.env';
$current_env = file_exists($env_path) ? file_get_contents($env_path) : '';

echo "<h2>Current .env content:</h2>";
if ($current_env) {
    echo "<div class='code'>" . htmlspecialchars($current_env) . "</div>";
} else {
    echo "<p class='warning'>⚠️ No .env file found</p>";
}

echo "<h2>New .env content (with correct password):</h2>";
echo "<div class='code'>" . htmlspecialchars($new_env_content) . "</div>";

if (isset($_GET['action']) && $_GET['action'] === 'update') {
    // Backup current .env
    if ($current_env) {
        $backup_name = '.env.backup_' . date('Y-m-d_H-i-s');
        $backup_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $backup_name;
        if (file_put_contents($backup_path, $current_env)) {
            echo "<p class='success'>✅ Backed up current .env to: $backup_name</p>";
        }
    }
    
    // Write new .env
    if (file_put_contents($env_path, $new_env_content)) {
        echo "<div style='background: #d4edda; padding: 20px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3 class='success'>✅ Successfully updated .env file!</h3>";
        echo "<p>Your site is now configured to use:</p>";
        echo "<ul>";
        echo "<li>Database: <strong>$recommended_db</strong></li>";
        echo "<li>User: <strong>admin_claude</strong></li>";
        echo "<li>Password: <strong>W4eZ!#9uwLmrMay</strong></li>";
        echo "</ul>";
        echo "</div>";
        
        echo "<h3>🚀 Next Steps:</h3>";
        echo "<ol>";
        echo "<li><strong>Test the site</strong> - Check if pages load correctly</li>";
        echo "<li><strong>Update code</strong> - Change queries to use new table names</li>";
        echo "<li><strong>Follow the migration guide</strong> - Update all references to vpo/spo</li>";
        echo "</ol>";
        
        echo "<p><a href='/' class='button'>Test Homepage</a> ";
        echo "<a href='/update_app_for_claude_db.php' class='button'>View Migration Guide</a></p>";
    } else {
        echo "<p class='error'>❌ Failed to update .env file. Please check file permissions.</p>";
    }
} else {
    echo "<h2>Ready to update?</h2>";
    echo "<div style='background: #fff3cd; padding: 20px; border: 1px solid #ffeaa7; border-radius: 5px;'>";
    echo "<p><strong>This will update your .env file with:</strong></p>";
    echo "<ul>";
    echo "<li>Correct password for admin_claude</li>";
    echo "<li>Use database: <strong>$recommended_db</strong></li>";
    echo "<li>Switch from old database structure to new clean structure</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<p><a href='?action=update' class='button danger' onclick='return confirm(\"Update .env with correct credentials?\")'>UPDATE .ENV FILE</a></p>";
}

echo "<h2>📋 Manual Update:</h2>";
echo "<p>If automatic update fails, manually edit your .env file and use:</p>";
echo "<div class='code'>DB_HOST=11klassnikiru67871.ipagemysql.com
DB_USER=admin_claude
DB_PASS=W4eZ!#9uwLmrMay
DB_NAME=$recommended_db</div>";
?>