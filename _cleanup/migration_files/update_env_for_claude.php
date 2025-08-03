<?php
/**
 * Update .env file to use claude database
 */

echo "<h1>🔧 Update .env for Claude Database</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .code { background: #f5f5f5; padding: 15px; border: 1px solid #ddd; font-family: monospace; margin: 10px 0; }
</style>";

// Check if .env exists
$env_path = $_SERVER['DOCUMENT_ROOT'] . '/.env';
$env_exists = file_exists($env_path);

if ($env_exists) {
    $current_env = file_get_contents($env_path);
    echo "<h2>Current .env content:</h2>";
    echo "<div class='code'>" . htmlspecialchars($current_env) . "</div>";
}

// New .env content
$new_env_content = "# Production environment variables
DB_HOST=11klassnikiru67871.ipagemysql.com
DB_USER=admin_claude
DB_PASS=Secure9#Klass
DB_NAME=11klassniki_claude

# Old database (kept for reference - DO NOT USE)
# OLD_DB_NAME=11klassniki_ru
# OLD_DB_USER=11klone_user
# OLD_DB_PASS=K8HqqBV3hTf4mha

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

# Email settings (if needed)
# SMTP_HOST=
# SMTP_PORT=
# SMTP_USER=
# SMTP_PASS=
";

echo "<h2>New .env content (for claude database):</h2>";
echo "<div class='code'>" . htmlspecialchars($new_env_content) . "</div>";

if (isset($_GET['update']) && $_GET['update'] === 'yes') {
    // Backup current .env
    if ($env_exists) {
        $backup_path = $_SERVER['DOCUMENT_ROOT'] . '/.env.backup_' . date('Y-m-d_H-i-s');
        copy($env_path, $backup_path);
        echo "<p class='success'>✅ Backed up current .env to: " . basename($backup_path) . "</p>";
    }
    
    // Write new .env
    if (file_put_contents($env_path, $new_env_content)) {
        echo "<p class='success'>✅ Successfully updated .env file!</p>";
        echo "<p class='warning'>⚠️ The site is now using the 11klassniki_claude database.</p>";
        echo "<p>Please test all functionality to ensure everything works correctly.</p>";
    } else {
        echo "<p class='error'>❌ Failed to update .env file. Please update it manually.</p>";
    }
} else {
    echo "<h2>Ready to update?</h2>";
    echo "<p class='warning'>⚠️ This will switch your site to use the new claude database.</p>";
    echo "<p>Make sure you have:</p>";
    echo "<ul>";
    echo "<li>Completed all data migration to 11klassniki_claude</li>";
    echo "<li>Backed up your current .env file</li>";
    echo "<li>Are ready to test the site with new database</li>";
    echo "</ul>";
    echo "<p><a href='?update=yes' style='background: red; color: white; padding: 10px; text-decoration: none;' onclick='return confirm(\"Are you sure you want to switch to claude database?\")'>UPDATE .ENV FILE</a></p>";
}

echo "<h2>Manual Update Instructions:</h2>";
echo "<p>If automatic update doesn't work, manually edit your .env file and change:</p>";
echo "<div class='code'>DB_NAME=11klassniki_ru</div>";
echo "<p>to:</p>";
echo "<div class='code'>DB_NAME=11klassniki_claude
DB_USER=admin_claude
DB_PASS=Secure9#Klass</div>";

echo "<p><a href='/test_claude_db_connection.php'>Test Connection</a> | <a href='/'>Back to Home</a></p>";
?>