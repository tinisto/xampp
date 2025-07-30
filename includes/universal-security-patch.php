<?php
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
                "/(<form[^>]*method=[\"\"]post[\"\"][^>]*>)/i",
                "$1\n    <?php echo csrf_field(); ?>",
                $buffer
            );
        }
        return $buffer;
    });
    define("CSRF_AUTO_ADDED", true);
}
?>