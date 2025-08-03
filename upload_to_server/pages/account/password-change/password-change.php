<?php
require_once __DIR__ . '/../../../includes/init.php';
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/buttons/form-buttons.php";

// Check user authentication
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";

// Initialize variables
$userId = $_SESSION['user_id'] ?? null;

// Fetch user data if needed
if ($userId) {
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $connection->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('i', $userId);
        if ($stmt->execute()) {
            $userData = $stmt->get_result()->fetch_assoc();
        }
    }
}
?>

<div>

  <form action="/pages/account/password-change/password-change-process.php" method="post">
    <?php echo csrf_field(); ?>
    <div class="mb-3">
      <div class="input-group">
        <input type="password" id="oldPassword" name="oldPassword" class="form-control"
          placeholder="Введите ваш старый пароль" required>
        <span class="input-group-text" id="showOldPassword">
          <i class="fas fa-eye"></i>
        </span>
      </div>
    </div>

    <div class="mb-3">
      <div class="input-group">
        <input type="password" id="newPassword" name="newPassword" class="form-control"
          placeholder="Введите ваш новый пароль" required>
        <span class="input-group-text" id="showNewPassword">
          <i class="fas fa-eye"></i>
        </span>
      </div>
    </div>

    <div class="mb-3">
      <div class="input-group">
        <input type="password" id="confirmPassword" name="confirmPassword" class="form-control"
          placeholder="Введите ваш новый пароль еще раз" required>
        <span class="input-group-text" id="showConfirmPassword">
          <i class="fas fa-eye"></i>
        </span>
      </div>
    </div>

    <input type="hidden" name="email" value="<?= htmlspecialchars($userData['email']) ?>">
    <input type="hidden" name="changePassword" value="1">

    <?= renderButtonBlock("Сохранить изменения", "Отмена", "/user/profile.php"); ?>
  </form>

</div>

<script>
  // Toggle password visibility
  document.getElementById('showOldPassword').addEventListener('click', function() {
    togglePasswordVisibility('oldPassword');
  });

  document.getElementById('showNewPassword').addEventListener('click', function() {
    togglePasswordVisibility('newPassword');
  });

  document.getElementById('showConfirmPassword').addEventListener('click', function() {
    togglePasswordVisibility('confirmPassword');
  });

  function togglePasswordVisibility(passwordId) {
    var passwordInput = document.getElementById(passwordId);
    var icon = document.getElementById('show' + passwordId);

    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      icon.innerHTML = '<i class="fas fa-eye-slash"></i>';
    } else {
      passwordInput.type = 'password';
      icon.innerHTML = '<i class="fas fa-eye"></i>';
    }
  }
</script>