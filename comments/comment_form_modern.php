<?php
// Get the current URL
$currentUrl = $_SERVER['REQUEST_URI'];

// Extract the numeric part from the URL for schools
preg_match('/\/school\/(\d+)/', $currentUrl, $schoolMatches);

// Extract the part from the URL for universities
preg_match('/\/vpo\/([\w-]+)/', $currentUrl, $universityMatches);

// Extract the part from the URL for spo
preg_match('/\/spo\/([\w-]+)/', $currentUrl, $collegeMatches);

// Extract the part from the URL for spo
preg_match('/\/post\/([\w-]+)/', $currentUrl, $postMatches);

// Check if the numeric part for id_school is found
$id_school = isset($schoolMatches[1]) ? $schoolMatches[1] : null;

// Check if the part for vpo_name_en is found
$vpo_name_en = isset($universityMatches[1]) ? $universityMatches[1] : null;

// Check if the part for spo_name_en is found
$spo_name_en = isset($collegeMatches[1]) ? $collegeMatches[1] : null;

// Check if the part for url_post is found
$url_post = isset($postMatches[1]) ? $postMatches[1] : null;

// Determine the entity type based on the URL structure
if (isset($schoolMatches[1])) {
  $entity_type = 'school';
} elseif (isset($universityMatches[1])) {
  $entity_type = 'vpo';
} elseif (isset($collegeMatches[1])) {
  $entity_type = 'spo';
} elseif (isset($postMatches[1])) {
  $entity_type = 'post';
} else {
  // Set a default entity type or handle the case where it's not determined
  $entity_type = 'default';
}
?>

<form method="post" action="/comments/process_comments.php" enctype="multipart/form-data" class="comment-form">
    <div style="display: flex; gap: 1rem; align-items: flex-start;">
        <!-- Avatar -->
        <div class="comment-avatar" style="flex-shrink: 0;">
            <?php
            // Get the user's avatar URL, fetch the full avatar path, and display the image
            $avatarUrl = $_SESSION['avatar'] ?? '';
            $avatarPath = getAvatar($avatarUrl);
            if ($avatarPath) {
                echo '<img src="' . htmlspecialchars($avatarPath) . '" alt="Avatar">';
            } else {
                // Show first letter of email if no avatar
                $firstLetter = strtoupper(substr($_SESSION['email'] ?? 'U', 0, 1));
                echo $firstLetter;
            }
            ?>
        </div>
        
        <!-- Comment Input -->
        <div style="flex: 1;">
            <textarea 
                id="comment" 
                name="comment" 
                rows="3"
                maxlength="2000" 
                placeholder="Введите Ваш комментарий или вопрос (макс. 2000 символов)" 
                required
            ></textarea>
            
            <input type="hidden" name="id_school" value="<?php echo htmlspecialchars($id_school ?? ''); ?>">
            <input type="hidden" name="vpo_name_en" value="<?php echo htmlspecialchars($vpo_name_en ?? ''); ?>">
            <input type="hidden" name="spo_name_en" value="<?php echo htmlspecialchars($spo_name_en ?? ''); ?>">
            <input type="hidden" name="url_post" value="<?php echo htmlspecialchars($url_post ?? ''); ?>">
            <input type="hidden" name="parent_id" value="<?php echo htmlspecialchars($_GET['parent_id'] ?? 0); ?>">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>">
            <input type="hidden" name="entity_type" value="<?php echo htmlspecialchars($entity_type ?? ''); ?>">
            <input type="hidden" name="id_entity" value="<?php echo htmlspecialchars($id_entity ?? ''); ?>">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                <small style="color: var(--text-muted);" id="charCount">Осталось символов: 2000</small>
                <button type="submit" id="submitButton" disabled>Отправить</button>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var commentInput = document.getElementById('comment');
    var submitButton = document.getElementById('submitButton');
    var charCountElement = document.getElementById('charCount');

    if (commentInput && submitButton && charCountElement) {
        commentInput.addEventListener('input', function() {
            // Replace newline characters with an empty string before counting characters
            var sanitizedText = this.value.replace(/\n/g, '');
            var charCount = 2000 - sanitizedText.length;

            charCountElement.textContent = 'Осталось символов: ' + charCount;
            submitButton.disabled = charCount < 0 || charCount === 2000;
            
            // Auto-resize textarea
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }
});
</script>