<?php
// Get the current URL
$currentUrl = $_SERVER['REQUEST_URI'];

// Extract the numeric part from the URL
preg_match('/\/school\/(\d+)/', $currentUrl, $matches);

// Check if the numeric part is found
if (isset($matches[1])) {
    // Assign the school ID to $id_school
    $id_school = $matches[1];
}
?>

<form method="post" action="/comments/process_comments.php" class="comment-form-reply">
    <input type="hidden" name="parent_id" value="<?php echo $comment['id']; ?>">
    <input type="hidden" name="entity_type" value="<?php echo htmlspecialchars($entity_type); ?>">
    <input type="hidden" name="id_entity" value="<?php echo htmlspecialchars($id_entity); ?>">
    <textarea class="form-control form-control-sm" id="comment_reply" name="comment" rows="2" maxlength="2000"
        placeholder="Введите Ваш ответ (макс. 2000 символов)" required></textarea>

    <div class="d-flex flex-row align-items-center justify-content-around mt-2">
        <small class="text-muted char-count">Осталось символов: 2000</small>
        <button type="submit" class="submit-button" id="submitButtonReply" disabled>Отправить ответ</button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Attach the event listener to a static parent element
        document.body.addEventListener('input', function(event) {
            // Check if the target of the event is a child of a form with the class 'comment-form-reply'
            var closestCommentForm = event.target.closest('.comment-form-reply');

            if (closestCommentForm) {
                var commentInput = closestCommentForm.querySelector('.form-control');
                var submitButton = closestCommentForm.querySelector('.submit-button');
                var charCountElement = closestCommentForm.querySelector('.char-count');

                if (commentInput && submitButton && charCountElement) {
                    var charCount = 2000 - commentInput.value.length;
                    charCountElement.textContent = 'Осталось символов: ' + charCount;
                    commentInput.rows = Math.ceil((commentInput.scrollHeight - 20) / 20);
                    submitButton.disabled = charCount <= 0 || charCount === 2000;
                }
            }
        });
    });
</script>