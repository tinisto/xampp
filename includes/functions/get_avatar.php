<?php
if (!function_exists('getAvatar')) {
    function getAvatar($avatarUrl)
    {
        // Construct the absolute path to the avatar image
        $avatarPath = $_SERVER["DOCUMENT_ROOT"] . '/images/avatars/' . $avatarUrl;

        // Check if the avatar file exists
        if (isset($avatarUrl) && $avatarUrl && file_exists($avatarPath)) {
            // Return the relative path to the image
            return "/images/avatars/" . htmlspecialchars($avatarUrl);
        } else {
            // Return the default avatar if it doesn't exist
            return "/images/avatars/default_avatar.jpg";
        }
    }
}
