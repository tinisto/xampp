<div class="d-flex justify-content-center gap-5">
  <?php
  $avatarUrl = $userData['avatar'];
  $avatarPath = getAvatar($avatarUrl);
  echo '<img src="' . htmlspecialchars($avatarPath) . '" alt="Avatar" style="width: 150px; height: 150px; border-radius: 50%;">';
  ?>

  <?php if (!empty($userData['avatar']) && file_exists("../../images/avatars/{$userData['avatar']}")): ?>
    <div class="d-flex align-items-center justify-content-center">
      <!-- Only show delete avatar button -->
      <a href="/pages/account/avatar/delete-avatar.php" class="btn btn-danger btn-sm">Удалить аватар</a>
    </div>
  <?php else: ?>
    <div class="row justify-content-center">
      <div class="col-md-8">
        <form action="/pages/account/avatar/create-avatar.php" method="post" enctype="multipart/form-data" id="avatarForm">
          <!-- File input for avatar -->
          <input type="file" id="newAvatar" name="newAvatar" accept="image/*" class="form-control" required>

          <!-- Validation message for file formats and size -->
          <div class="form-text my-3 text-start">
            Допустимые форматы: <strong>jpg, jpeg, png, gif</strong>. Максимальный размер файла: <strong>5 MB</strong>.
          </div>

          <!-- Submit button (initially disabled) -->
          <div class="d-flex justify-content-center">
            <button type="submit" id="submitButton" class="btn btn-success btn-sm" disabled>Загрузить изображение</button>
          </div>

          <!-- Error message container -->
          <div id="errorMessage" class="text-danger mt-3" style="display: none;"></div>
        </form>
      </div>
    </div>
  <?php endif; ?>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('newAvatar'); // File input field
    const submitButton = document.getElementById('submitButton'); // Submit button
    const errorMessage = document.getElementById('errorMessage'); // Error message container

    // Ensure the elements exist before adding event listeners
    if (fileInput && submitButton) {
      // Function to check if file is selected and enable/disable button
      fileInput.addEventListener('change', function() {
        const file = fileInput.files[0];
        if (file) {
          // Check file type
          const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
          if (!validTypes.includes(file.type)) {
            errorMessage.textContent = 'Недопустимый формат файла. Пожалуйста, выберите изображение формата jpg, jpeg, png или gif.';
            errorMessage.style.display = 'block';
            submitButton.disabled = true;
            return;
          }

          // Check file size (5 MB)
          const maxSize = 5 * 1024 * 1024; // 5 MB in bytes
          if (file.size > maxSize) {
            errorMessage.textContent = 'Файл слишком большой. Максимальный размер файла: 5 MB.';
            errorMessage.style.display = 'block';
            submitButton.disabled = true;
            return;
          }

          // If file is valid, enable the submit button and hide any error messages
          submitButton.disabled = false;
          errorMessage.style.display = 'none';
        } else {
          // If no file is selected, keep the submit button disabled
          submitButton.disabled = true;
        }
      });

      // Add submit event listener to the form
      document.getElementById('avatarForm').addEventListener('submit', function(event) {
        // Clear any previous error messages
        errorMessage.style.display = 'none';
      });
    }
  });
</script>