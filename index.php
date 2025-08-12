<?php
// Include security headers first
require_once __DIR__ . '/includes/security-headers.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Main page content
$page_title = '11klassniki.ru - Российское образование';
require_once __DIR__ . '/template.php';

// Your main page content here
?>
<div class="container mt-4">
    <h1>Добро пожаловать на 11klassniki.ru</h1>
    <p>Портал российского образования</p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>