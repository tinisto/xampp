<?php
// Script to add CSRF protection to all forms

$rootDir = dirname(__DIR__);

function processFormFile($filePath) {
    $content = file_get_contents($filePath);
    $modified = false;
    
    // Check if already has CSRF protection
    if (strpos($content, 'csrf_field()') !== false || 
        strpos($content, 'csrf_token') !== false ||
        strpos($content, 'Security::getCSRFField') !== false) {
        return false;
    }
    
    // Add helpers include if not present
    if (strpos($content, 'helpers.php') === false) {
        $phpTagPos = strpos($content, '<?php');
        if ($phpTagPos !== false) {
            $insertPos = $phpTagPos + 5;
            $afterTag = substr($content, $insertPos, 100);
            if (!preg_match('/^\s*(require|include)/', $afterTag)) {
                $helperInclude = "\nrequire_once __DIR__ . '/../../../includes/helpers.php';\n";
                $content = substr($content, 0, $insertPos) . $helperInclude . substr($content, $insertPos);
                $modified = true;
            }
        }
    }
    
    // Add CSRF field after form opening tag
    $pattern = '/(<form[^>]*method\s*=\s*["\']post["\'][^>]*>)/i';
    $replacement = '$1' . "\n    <?php echo csrf_field(); ?>";
    
    $newContent = preg_replace($pattern, $replacement, $content);
    if ($newContent !== $content) {
        $content = $newContent;
        $modified = true;
    }
    
    if ($modified) {
        file_put_contents($filePath, $content);
        return true;
    }
    
    return false;
}

// Get all form files
$formFiles = [
    '/pages/login/login_content.php',
    '/pages/registration/registration_form.php',
    '/pages/account/password-change/password-change.php',
    '/pages/account/personal-data-change/personal-data-change.php',
    '/pages/account/delete-account/delete-account.php',
    '/pages/write/write-form.php',
    '/pages/common/create-form.php',
    '/pages/common/schools/schools-create-form.php',
];

$modifiedFiles = [];

foreach ($formFiles as $file) {
    $filePath = $rootDir . $file;
    if (file_exists($filePath)) {
        if (processFormFile($filePath)) {
            $modifiedFiles[] = $file;
        }
    }
}

echo "Added CSRF protection to " . count($modifiedFiles) . " files:\n";
foreach ($modifiedFiles as $file) {
    echo "- $file\n";
}

echo "\nCSRF protection added successfully!\n";