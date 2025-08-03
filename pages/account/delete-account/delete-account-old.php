<?php
require_once __DIR__ . '/../../../includes/init.php';
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/check_user.php';

// Check if user is admin
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Prevent admin self-deletion
if ($isAdmin) {
    $_SESSION['error'] = 'Администраторы не могут удалить свой собственный аккаунт. Обратитесь к другому администратору.';
    header('Location: /account');
    exit();
}
?>

<!-- Add the script for password visibility toggle -->
<script>
  function togglePasswordVisibility(passwordId) {
    var passwordInput = document.getElementById(passwordId);
    var icon = document.getElementById("showPassword"); // Update id here

    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      icon.innerHTML = '<i class="fas fa-eye-slash"></i>';
    } else {
      passwordInput.type = "password";
      icon.innerHTML = '<i class="fas fa-eye"></i>';
    }
  }
</script>

<div>
  <!-- Display the password form here -->
  <?php
  // Display confirmation message
  echo '<div class="alert alert-warning" role="alert">
      Вы уверены, что хотите удалить свой аккаунт?<br> 
      Вся связанная с аккаунтом информация будет безвозвратно удалена.
      После удаления аккаунта вы не сможете восстановить свои данные.
    </div>';
  ?>

  <form action="/pages/account/delete-account/delete-account-process.php" method="post">
    <?php echo csrf_field(); ?>
    <!-- Include a hidden field for CSRF protection -->
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

    <div class="mb-3">
      <div class="input-group">
        <input type="password" id="password" name="password" class="form-control" placeholder="Введите ваш пароль"
          required>
        <button type="button" id="showPassword" onclick="togglePasswordVisibility('password')"
          class="btn btn-outline-secondary">
          <i class="fas fa-eye"></i>
        </button>
      </div>
    </div>

    <?= renderButtonBlock("Подтвердить удаление", "Отмена", "/user/profile.php"); ?>

  </form>
</div>