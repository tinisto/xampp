<?php
// Check which critical pages still need migration

$critical_pages = [
    'test-result-handler' => '/pages/tests/result-handler.php',
    'unauthorized' => '/pages/unauthorized/unauthorized.php',
    'thank-you' => '/pages/thank-you/thank-you.php',
    'registration' => '/pages/registration/registration.php',
    'reset-password-confirm' => '/pages/account/reset-password/reset-password-confirm.php',
    'comments-user-edit' => '/pages/account/comments-user/comments-user-edit/comments-user-edit.php',
    'dashboard-create-content' => '/pages/dashboard/posts-dashboard/posts-create/posts-create.php',
];

$already_migrated = [
    '404-new.php',
    'search-new.php',
    'login-template.php',
    'registration-new.php',
    'error-new.php',
    'forgot-password-new.php',
    'account-new.php',
    'privacy-new.php',
    'terms-new.php',
    'thank-you-new.php',
    'unauthorized-new.php',
    'password-change-new.php',
    'account-edit-new.php',
    'account-comments-new.php',
    'educational-institutions-in-town-new.php',
    'test-result-new.php',
    'test-full-new.php',
    'search-process-new.php',
    'reset-password-new.php',
    'dashboard-professional-new.php',
    'dashboard-users-new.php',
    'dashboard-news-new.php',
    'dashboard-posts-new.php',
    'dashboard-comments-new.php',
    'dashboard-schools-new.php',
    'dashboard-vpo-new.php',
    'dashboard-spo-new.php',
];

echo "Critical Pages Still Using Old Template:\n";
echo "=" . str_repeat("=", 50) . "\n\n";

foreach ($critical_pages as $name => $path) {
    $full_path = $_SERVER['DOCUMENT_ROOT'] . $path;
    if (file_exists($full_path)) {
        $content = file_get_contents($full_path);
        if (strpos($content, 'template-engine-ultimate.php') !== false) {
            echo "❌ $name ($path) - NEEDS MIGRATION\n";
        } else {
            echo "✅ $name ($path) - Already migrated or doesn't use old template\n";
        }
    } else {
        echo "⚠️  $name ($path) - File doesn't exist\n";
    }
}

echo "\n\nMigrated Pages Summary:\n";
echo "=" . str_repeat("=", 50) . "\n";
echo "Total migrated: " . count($already_migrated) . " pages\n\n";

// Check which files exist
echo "Checking migrated files exist:\n";
$existing = 0;
foreach ($already_migrated as $file) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $file)) {
        $existing++;
    } else {
        echo "Missing: $file\n";
    }
}
echo "\nExisting migrated files: $existing/" . count($already_migrated) . "\n";
?>