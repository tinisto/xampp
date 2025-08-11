<?php
session_start();
$page_title = 'Test New Branding - 11klassniki.ru';
require_once __DIR__ . '/includes/header_modern.php';
?>

<div style="padding: 40px; text-align: center;">
    <h1>New Branding Test Page</h1>
    <p>This page shows the new logo, header, and footer design.</p>
    
    <div style="margin: 40px 0; padding: 30px; background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2>✅ New Features Active</h2>
        <ul style="list-style: none; padding: 0;">
            <li>🎨 New logo with swoosh</li>
            <li>🌐 Favicon implemented</li>
            <li>📱 Mobile responsive</li>
            <li>🇷🇺 Russian flag colors</li>
            <li>📝 New slogan in footer</li>
        </ul>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer_modern.php'; ?>