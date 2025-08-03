<?php
/**
 * Update .env to use 11klassniki_new database
 */

echo "<h1>🔧 Update .env to Use 11klassniki_new Database</h1>";
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

// Check current .env
$env_path = $_SERVER['DOCUMENT_ROOT'] . '/.env';
$current_env = file_exists($env_path) ? file_get_contents($env_path) : '';

echo "<h2>Current .env content:</h2>";
if ($current_env) {
    echo "<div class='code'>" . htmlspecialchars($current_env) . "</div>";
} else {
    echo "<p class='warning'>⚠️ No .env file found</p>";
}

// New .env content for 11klassniki_new
$new_env_content = "# Production environment variables
DB_HOST=11klassnikiru67871.ipagemysql.com
DB_USER=admin_claude
DB_PASS=Secure9#Klass
DB_NAME=11klassniki_new

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

echo "<h2>New .env content (using 11klassniki_new):</h2>";
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
        echo "<p>Your site is now configured to use the <strong>11klassniki_new</strong> database with:</p>";
        echo "<ul>";
        echo "<li>Clean table names (universities, colleges)</li>";
        echo "<li>Consistent column naming</li>";
        echo "<li>All migrated data</li>";
        echo "</ul>";
        echo "</div>";
        
        echo "<h3>🚀 What to do next:</h3>";
        echo "<ol>";
        echo "<li><strong>Test the site</strong> - Some pages may show errors until code is updated</li>";
        echo "<li><strong>Update code</strong> - Replace 'vpo' with 'universities', 'spo' with 'colleges'</li>";
        echo "<li><strong>Fix any errors</strong> - Update column names in queries</li>";
        echo "</ol>";
        
        echo "<p><a href='/' class='button'>Test Homepage</a> ";
        echo "<a href='/update_app_for_claude_db.php' class='button'>View Code Changes Guide</a></p>";
    } else {
        echo "<p class='error'>❌ Failed to update .env file. Please check file permissions.</p>";
    }
} else {
    echo "<h2>Ready to update?</h2>";
    echo "<div style='background: #fff3cd; padding: 20px; border: 1px solid #ffeaa7; border-radius: 5px;'>";
    echo "<p><strong>This will:</strong></p>";
    echo "<ul>";
    echo "<li>Switch your database from <strong>11klassniki_ru</strong> to <strong>11klassniki_new</strong></li>";
    echo "<li>Use the new clean table structure (universities/colleges instead of vpo/spo)</li>";
    echo "<li>Require code updates to work properly</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<p><a href='?action=update' class='button danger' onclick='return confirm(\"Are you sure? This will switch to the new database structure.\")'>UPDATE .ENV FILE</a></p>";
    echo "<p><a href='/use_new_database.php'>← Back</a></p>";
}

echo "<h2>📋 Manual Update Option:</h2>";
echo "<p>If automatic update fails, manually edit your .env file and change:</p>";
echo "<div class='code'>DB_NAME=11klassniki_ru
DB_USER=11klone_user
DB_PASS=K8HqqBV3hTf4mha</div>";
echo "<p>To:</p>";
echo "<div class='code'>DB_NAME=11klassniki_new
DB_USER=admin_claude
DB_PASS=Secure9#Klass</div>";
?>