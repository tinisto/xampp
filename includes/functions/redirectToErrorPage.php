<?php
function redirectToErrorPage($errorMessage, $file, $line) {
    $errorDetails = [
        'message' => $errorMessage,
        'file' => $file,
        'line' => $line
    ];
    header("Location: /error.php?error=" . urlencode(json_encode($errorDetails)));
    exit();
}
?>
