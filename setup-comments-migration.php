<?php
/**
 * Standalone migration script for initial setup
 * Run this once to set up the comment system database schema
 * Delete this file after successful migration for security
 */

// Database connection
require_once __DIR__ . '/database/db_connections.php';

// Security check - only allow from command line or with special token
$token = $_GET['token'] ?? '';
$validToken = 'setup2025secure'; // Change this to something secure

if (php_sapi_name() !== 'cli' && $token !== $validToken) {
    die('Access denied. Run from command line or provide valid token.');
}

echo "Starting Comment System Database Migration...\n\n";

// Read the migration SQL file
$migrationFile = __DIR__ . '/database/migrations/update_comments_threaded_system.sql';
if (!file_exists($migrationFile)) {
    die("Error: Migration file not found at: $migrationFile\n");
}

$sql = file_get_contents($migrationFile);
if (empty($sql)) {
    die("Error: Migration file is empty\n");
}

// Split SQL into individual statements
$statements = array_filter(array_map('trim', explode(';', $sql)));

$successCount = 0;
$errorCount = 0;
$errors = [];

foreach ($statements as $statement) {
    if (empty($statement)) continue;
    
    echo "Executing: " . substr($statement, 0, 50) . "...\n";
    
    try {
        if ($connection->query($statement)) {
            $successCount++;
            echo "✓ Success\n\n";
        } else {
            $errorCount++;
            $error = $connection->error;
            $errors[] = $error;
            echo "✗ Error: $error\n\n";
        }
    } catch (Exception $e) {
        $errorCount++;
        $error = $e->getMessage();
        $errors[] = $error;
        echo "✗ Exception: $error\n\n";
    }
}

echo "\n========================================\n";
echo "Migration Complete!\n";
echo "✓ Successful statements: $successCount\n";
echo "✗ Failed statements: $errorCount\n";

if ($errorCount > 0) {
    echo "\nErrors encountered:\n";
    foreach ($errors as $error) {
        echo "- $error\n";
    }
    echo "\nNote: Some errors may be expected (e.g., 'Column already exists')\n";
}

echo "\n⚠️  IMPORTANT: Delete this file after migration for security!\n";
echo "Run: rm " . __FILE__ . "\n";

// If running from web, provide a nicer output
if (php_sapi_name() !== 'cli') {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Comment System Migration</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                max-width: 800px;
                margin: 50px auto;
                padding: 20px;
                background: #f5f5f5;
            }
            .container {
                background: white;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            h1 {
                color: #333;
                margin-bottom: 20px;
            }
            .success {
                color: #28a745;
                font-weight: bold;
            }
            .error {
                color: #dc3545;
                font-weight: bold;
            }
            .warning {
                background: #fff3cd;
                border: 1px solid #ffeeba;
                color: #856404;
                padding: 15px;
                border-radius: 5px;
                margin-top: 20px;
            }
            pre {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 5px;
                overflow-x: auto;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Comment System Database Migration</h1>
            <p class="success">✓ Successful statements: <?= $successCount ?></p>
            <p class="error">✗ Failed statements: <?= $errorCount ?></p>
            
            <?php if ($errorCount > 0): ?>
            <h3>Errors encountered:</h3>
            <pre><?= htmlspecialchars(implode("\n", $errors)) ?></pre>
            <p><em>Note: Some errors may be expected (e.g., 'Column already exists' if running migration multiple times)</em></p>
            <?php endif; ?>
            
            <div class="warning">
                <strong>⚠️ Security Warning:</strong><br>
                Delete this file immediately after migration!<br>
                <code>rm <?= __FILE__ ?></code>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>