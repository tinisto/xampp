<div class="col-md-6">
  <div class="card">
    <div class="card-body" style="font-size: 14px;">
      <?php 
      include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/site-icon.php';
      echo '<div class="text-center mb-3">';
      renderSiteIcon('medium', '/', 'reset-logo');
      echo '</div>';
      ?>
      <h4 class="card-title text-center fw-bold my-3">Подтверждение сброса пароля</h4>
      <?php
      $token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      $email = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_EMAIL);

      $isValidToken = true; // Placeholder for database validation

      if ($isValidToken) {
        ?>
        <form action="/reset-password-confirm-process" method="post" id="resetPasswordForm">
          <input type="hidden" name="token" value="<?php echo $token; ?>">
          <input type="hidden" name="email" value="<?php echo $email; ?>">

          <div class="mb-3">
            <div class="input-group">
              <input type="password" id="password" name="password" class="form-control"
                placeholder="Введите ваш новый пароль" required minlength="8">
              <span class="input-group-text" id="togglePassword">
                <i class="fas fa-eye"></i>
              </span>
            </div>
          </div>

          <div class="mb-3">
            <div class="input-group">
              <input type="password" id="confirmPassword" name="confirmPassword" class="form-control"
                placeholder="Повторите ваш новый пароль" required minlength="8">
              <span class="input-group-text" id="toggleConfirmPassword">
                <i class="fas fa-eye"></i>
              </span>
            </div>
          </div>

          <div id="passwordError" class="text-danger mb-3" style="display: none;">Пароли не совпадают.</div>
          <div class="text-muted small mb-3">
            Пароль должен содержать минимум 8 символов, включая заглавную букву, строчную букву, цифру и специальный символ
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-danger"><span class="fw-bold">Сохранить новый пароль</span></button>
          </div>
        </form>
        <?php
      } else {
        ?>
        <div class="alert alert-danger" role="alert">Неверный или устаревший токен. Пожалуйста, запросите сброс пароля
          снова.</div>
        <?php
      }
      ?>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Toggle password visibility
    function togglePasswordVisibility(passwordId, toggleId) {
      var passwordInput = document.getElementById(passwordId);
      var icon = document.getElementById(toggleId);

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.innerHTML = '<i class="fas fa-eye-slash"></i>';
      } else {
        passwordInput.type = 'password';
        icon.innerHTML = '<i class="fas fa-eye"></i>';
      }
    }

    document.getElementById('togglePassword').addEventListener('click', function () {
      togglePasswordVisibility('password', 'togglePassword');
    });

    document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
      togglePasswordVisibility('confirmPassword', 'toggleConfirmPassword');
    });

    // Validate password match
    const form = document.getElementById('resetPasswordForm');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirmPassword');
    const passwordError = document.getElementById('passwordError');

    form.addEventListener('submit', function (event) {
      let error = '';
      const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,}$/;
      
      if (password.value.length < 8) {
        error = 'Пароль должен содержать минимум 8 символов.';
      } else if (!passwordRegex.test(password.value)) {
        error = 'Пароль должен содержать минимум одну строчную букву, одну заглавную букву, одну цифру и один специальный символ.';
      } else if (password.value !== confirmPassword.value) {
        error = 'Пароли не совпадают.';
      }
      
      if (error) {
        passwordError.textContent = error;
        passwordError.style.display = 'block';
        event.preventDefault();
      } else {
        passwordError.style.display = 'none';
      }
    });
  });
</script>