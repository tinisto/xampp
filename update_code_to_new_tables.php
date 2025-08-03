<?php
// Script to show which files need updating for new table names
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Code Update Requirements for New Database Tables</h1>";

// Files that need updating
$files_to_update = [
    // Educational institutions pages
    'pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php' => [
        'old' => ['vpo', 'spo'],
        'new' => ['universities', 'colleges'],
        'changes' => [
            "\$table = 'spo';" => "\$table = 'colleges';",
            "\$table = 'vpo';" => "\$table = 'universities';",
        ]
    ],
    'pages/common/educational-institutions-in-region/educational-institutions-in-region.php' => [
        'old' => ['vpo', 'spo'],
        'new' => ['universities', 'colleges'],
        'changes' => [
            "\$table = 'spo';" => "\$table = 'colleges';",
            "\$table = 'vpo';" => "\$table = 'universities';",
        ]
    ],
    'pages/common/educational-institutions-in-region/outputEducationalInstitutions.php' => [
        'old' => ['vpo_name', 'spo_name', 'vpo_url', 'spo_url'],
        'new' => ['name', 'name', 'url', 'url'],
        'changes' => [
            "['vpo_name']" => "['name']",
            "['spo_name']" => "['name']",
            "['vpo_url']" => "['url']",
            "['spo_url']" => "['url']",
        ]
    ],
    'pages/common/vpo-spo/single-simplified.php' => [
        'old' => ['vpo', 'spo'],
        'new' => ['universities', 'colleges'],
        'changes' => [
            "FROM \$type WHERE" => "FROM " . '($type === \'vpo\' ? \'universities\' : \'colleges\')' . " WHERE",
            "\$urlField = \$type === 'vpo' ? 'vpo_url' : 'spo_url';" => "\$urlField = 'url';",
            "\$nameField = \$type === 'vpo' ? 'vpo_name' : 'spo_name';" => "\$nameField = 'name';",
        ]
    ],
];

echo "<h2>Files That Need Updating:</h2>";
echo "<ol>";
foreach ($files_to_update as $file => $info) {
    echo "<li><strong>$file</strong>";
    echo "<ul>";
    echo "<li>Old tables/fields: " . implode(', ', $info['old']) . "</li>";
    echo "<li>New tables/fields: " . implode(', ', $info['new']) . "</li>";
    echo "</ul>";
    echo "</li>";
}
echo "</ol>";

// Create update script
echo "<h2>Generated Update Script:</h2>";
echo "<pre style='background: #f0f0f0; padding: 10px; overflow-x: auto;'>";

foreach ($files_to_update as $file => $info) {
    echo "\n// Updating: $file\n";
    foreach ($info['changes'] as $old => $new) {
        echo "sed -i '' 's/" . str_replace('/', '\/', $old) . "/" . str_replace('/', '\/', $new) . "/g' $file\n";
    }
}

echo "</pre>";

// Environment update
echo "<h2>Environment Configuration Update:</h2>";
echo "<p>Update the .env file:</p>";
echo "<pre style='background: #f0f0f0; padding: 10px;'>";
echo "DB_NAME=11klassniki_claude\n";
echo "</pre>";

// Database connection update
echo "<h2>Database Connection Update:</h2>";
echo "<p>In database/db_connections.php, change:</p>";
echo "<pre style='background: #f0f0f0; padding: 10px;'>";
echo '$force_new_db = false; // Change from true to false';
echo "</pre>";

// Migration completion script
echo "<h2>Complete Migration Script:</h2>";
echo "<textarea style='width: 100%; height: 200px; font-family: monospace;'>";
echo "#!/bin/bash\n";
echo "# Complete migration to new database\n\n";
echo "# 1. Run missing records migration\n";
echo "echo 'Migrating missing records...'\n";
echo "# Visit: https://11klassniki.ru/fix_missing_records.php\n\n";
echo "# 2. Update code to use new tables\n";
echo "echo 'Updating code...'\n";
foreach ($files_to_update as $file => $info) {
    foreach ($info['changes'] as $old => $new) {
        echo "sed -i '' 's/" . str_replace('/', '\/', $old) . "/" . str_replace('/', '\/', $new) . "/g' $file\n";
    }
}
echo "\n# 3. Update .env file\n";
echo "echo 'Updating .env...'\n";
echo "sed -i '' 's/DB_NAME=11klassniki_ru/DB_NAME=11klassniki_claude/g' .env\n";
echo "\n# 4. Update database connection\n";
echo "echo 'Updating db_connections.php...'\n";
echo "sed -i '' 's/\$force_new_db = true;/\$force_new_db = false;/g' database/db_connections.php\n";
echo "\necho 'Migration complete!'\n";
echo "</textarea>";

echo "<hr>";
echo "<p><a href='/check_current_database.php'>← Back to Database Analysis</a></p>";
?>