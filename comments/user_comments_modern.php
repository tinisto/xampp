<?php
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/check-user-suspend.php';

// Display login message if the user is not logged in
if (!isset($_SESSION['email'])) {
    echo '<div class="comment-form">
            <p style="color: var(--text-secondary); text-align: center;">
                Чтобы добавить комментарий или задать вопрос, 
                <a href="/login?redirect=' . urlencode($_SERVER['REQUEST_URI']) . '" style="color: var(--accent-primary); text-decoration: none;">авторизуйтесь</a>. 
                Не стесняйтесь!
            </p>
          </div>';
    include 'display_comments_modern.php';
} else {
    // Check if the user is suspended
    $userEmail = $_SESSION['email'];
    $isSuspended = getUserSuspensionStatus($userEmail);

    if ($isSuspended) {
        // Display a message for suspended users
        echo '<div class="comment-form">
                <p style="color: #ef4444; text-align: center;">
                    Ваш аккаунт заблокирован. Если у вас есть вопросы, свяжитесь с поддержкой: support@11klassniki.ru
                </p>
              </div>';
        include 'display_comments_modern.php';
    } else {
        // Include the comments display if the user is not suspended
        include 'comment_form_modern.php';
        include 'display_comments_modern.php';
    }
}
?>