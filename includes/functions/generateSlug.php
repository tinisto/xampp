<?php 

// Include the transliteration function
include_once __DIR__ . '/transliterateToLatin.php';

function generateSlug($title) {
    // Ensure the input is a string
    if (!is_string($title)) {
        return '';
    }

    // Transliterate text
    $title = transliterateToLatin($title);

    // Convert to lowercase (multibyte safe)
    $title = mb_strtolower($title, 'UTF-8');

    // Remove invalid characters and replace spaces/dashes with single dashes
    $title = preg_replace('/[^a-z0-9\s-]/', '', $title); // Remove non-alphanumeric characters
    $title = preg_replace('/[\s-]+/', '-', $title);      // Replace spaces and dashes with single dash

    // Trim any leading or trailing dashes
    $title = trim($title, '-');

    return $title;
}

?>
