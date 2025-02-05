<?php
function renderButtonBlock($submitText = "Создать страницу", $cancelText = "Отмена", $link = '/') {
    // Escape the link to ensure it doesn't break the JavaScript
    $link = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');  // Escape special characters

    // Prepare the JavaScript code for redirection
    $redirectScript = "window.location.href = '$link';";

    return "
    <div class='d-flex justify-content-center my-3 gap-3'>
        <button type='submit' class='btn btn-success btn-sm'>$submitText</button>
        <button type='button' class='btn btn-danger btn-sm' onclick=\"window.location.href = '$link';\">$cancelText</button>
    </div>";
}
?>