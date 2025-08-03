<?php
session_start();
require_once '../includes/db_connection.php';

// VK OAuth Configuration
$vk_app_id = 'YOUR_VK_APP_ID'; // Replace with actual VK App ID
$vk_secret_key = 'YOUR_VK_SECRET_KEY'; // Replace with actual VK Secret Key
$redirect_uri = 'https://11klassniki.ru/auth/vk_callback.php';

// Step 1: Redirect to VK OAuth
$vk_oauth_url = 'https://oauth.vk.com/authorize?' . http_build_query([
    'client_id' => $vk_app_id,
    'redirect_uri' => $redirect_uri,
    'display' => 'page',
    'scope' => 'email',
    'response_type' => 'code',
    'v' => '5.131'
]);

// Redirect to VK OAuth
header('Location: ' . $vk_oauth_url);
exit;
?>