<?php



include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/check-user-suspend.php';

// Display login message if the user is not logged in
if (!isset($_SESSION['email'])) {
    echo '<div class="d-flex justify-content-center mt-4"><p class="custom-info-message">Чтобы добавить комментарий или задать вопрос, <a class="link-custom" href="/login?redirect=' . urlencode($_SERVER['REQUEST_URI']) . '" class="">авторизуйтесь</a>. Не стесняйтесь!</p></div>';
    include 'display_comments.php';
} else {
    // Check if the user is suspended
    $userEmail = $_SESSION['email'];
    $isSuspended = getUserSuspensionStatus($userEmail);

    if ($isSuspended) {
        // Display a message for suspended users
        echo '<div class="d-flex justify-content-center mt-4"><p class="custom-alert">Ваш аккаунт заблокирован. Если у вас есть вопросы, свяжитесь с поддержкой: support@11klassniki.ru</p></div>';
        include 'display_comments.php';
    } else {
        // Include the comments display if the user is not suspended
        include 'comment_form.php';
        include 'display_comments.php';
    }
}
