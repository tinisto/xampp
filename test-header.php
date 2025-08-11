<?php
// Test page to preview the new header
session_start();
$page_title = "Test Header - 11klassniki.ru";
require_once 'includes/header_modern.php';
?>

<div style="padding: 40px; text-align: center;">
    <h1>Header Test Page</h1>
    <p>This page demonstrates the new header with logo and favicon implementation.</p>
    
    <div style="margin-top: 40px; padding: 40px; background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2>Logo Implementation Complete ✓</h2>
        <p>The header now includes:</p>
        <ul style="list-style: none; padding: 0; margin-top: 20px; text-align: left; max-width: 400px; margin-left: auto; margin-right: auto;">
            <li style="padding: 10px 0;">✓ Clean swoosh logo design</li>
            <li style="padding: 10px 0;">✓ Russian flag colors (blue #0039A6, red #D52B1E)</li>
            <li style="padding: 10px 0;">✓ Responsive navigation</li>
            <li style="padding: 10px 0;">✓ Favicon implementation</li>
        </ul>
    </div>
    
    <div style="margin-top: 30px;">
        <h3>Favicon Preview</h3>
        <p>Check your browser tab to see the new "11" favicon in action!</p>
    </div>
</div>

<?php require_once 'includes/footer_modern.php'; ?>