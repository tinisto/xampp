<?php
// Include getAvatar function and database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/get_avatar.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/getEntityIdFromURL.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf-protection.php';

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

// Check if the part for url_slug is found
$url_slug = isset($postMatches[1]) ? $postMatches[1] : null;

// Determine the entity type and ID based on the URL structure (only if not already set by parent page)
if (!isset($id_entity) || !isset($entity_type)) {
  $id_entity = null;
  if (isset($schoolMatches[1])) {
    $entity_type = 'school';
    $id_entity = $schoolMatches[1]; // For schools, ID is directly in URL
  } elseif (isset($universityMatches[1])) {
    $entity_type = 'vpo';
    // For VPO, need to get ID from database using the URL slug
    if (isset($connection)) {
      $result = getEntityIdFromURL($connection, 'vpo');
      $id_entity = $result['entity_id'];
    }
  } elseif (isset($collegeMatches[1])) {
    $entity_type = 'spo';
    // For SPO, need to get ID from database using the URL slug
    if (isset($connection)) {
      $result = getEntityIdFromURL($connection, 'spo');
      $id_entity = $result['entity_id'];
    }
  } elseif (isset($postMatches[1])) {
    $entity_type = 'post';
    // For posts, need to get ID from database using the URL slug
    if (isset($connection)) {
      $result = getEntityIdFromURL($connection, 'post');
      $id_entity = $result['entity_id'];
    }
  } else {
    // Set a default entity type or handle the case where it's not determined
    $entity_type = 'default';
  }
}
?>

<form method="post" action="/comments/process_comments.php" enctype="multipart/form-data">
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="row">
          <!-- Avatar column -->
          <div class="col-md-2 text-end">
            <!-- Display avatar if available -->
            <?php
            // Get the user's avatar URL, fetch the full avatar path, and display the image with styling
            $avatarUrl = $_SESSION['avatar'] ?? '';
            $avatarPath = getAvatar($avatarUrl);
            echo '<img src="' . htmlspecialchars($avatarPath ?? '') . '" alt="Avatar" style="width: 55px; height: 55px; border-radius: 50%;">';
            ?>
          </div>

          <div class="col-md-10">
            <div class="mb-2">
              <textarea class="form-control form-control-sm rounded" id="comment" name="comment" rows="2"
                maxlength="2000" placeholder="Введите Ваш комментарий или вопрос (макс. 2000 символов)" required
                style="background-color: var(--surface-variant, #f5f5f5); color: var(--text-primary, black);"></textarea>
            </div>

            <?php echo csrfField(); ?>
            <input type="hidden" name="id_school" value="<?php echo htmlspecialchars($id_school ?? ''); ?>">
            <input type="hidden" name="vpo_name_en" value="<?php echo htmlspecialchars($vpo_name_en ?? ''); ?>">
            <input type="hidden" name="spo_name_en" value="<?php echo htmlspecialchars($spo_name_en ?? ''); ?>">
            <input type="hidden" name="url_slug" value="<?php echo htmlspecialchars($url_slug ?? ''); ?>">
            <input type="hidden" name="parent_id" value="<?php echo htmlspecialchars($_GET['parent_id'] ?? 0); ?>">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>">
            <input type="hidden" name="entity_type" value="<?php echo htmlspecialchars($entity_type ?? ''); ?>">
            <input type="hidden" name="id_entity" value="<?php echo htmlspecialchars($id_entity ?? ''); ?>">

            <div class="d-flex flex-row align-items-center justify-content-around">
              <small class="text-muted" id="charCount">Осталось символов: 2000</small>
              <button type="submit" class="submit-button" id="submitButton" disabled>Отправить</button>
            </div>
          </div>
        </div>
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
        commentInput.rows = Math.ceil((commentInput.scrollHeight - 20) / 20);
        submitButton.disabled = charCount < 0 || charCount === 2000;
      });
    }
  });
</script>