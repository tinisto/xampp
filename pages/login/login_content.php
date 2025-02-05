<div class="col-md-6">
  <div class="card">
    <div class="card-body" style="font-size: 14px;">

      <?php
      if (isset($_GET['activation_success']) && $_GET['activation_success'] === 'true') {
        // Проверяем, было ли уже показано сообщение
        if (!isset($_SESSION['activation_success_message_shown'])) {
          echo '<div class="alert alert-success" role="alert">';
          echo 'Аккаунт успешно активирован. Теперь вы можете войти в свой аккаунт.';
          echo '</div>';

          // Устанавливаем флаг в сессии, чтобы показать, что сообщение было показано
          $_SESSION['activation_success_message_shown'] = true;
        }
      }
      ?>

      <?php
      $registrationSuccess = isset($_GET['registration_success']) && $_GET['registration_success'] === 'true';
      $successMessage = isset($_GET['message']) ? urldecode($_GET['message']) : '';

      if ($registrationSuccess) {
        echo '<div class="alert alert-success" role="alert">';
        echo $successMessage;
        echo '</div>';
      }

      // Check for login errors
      if (isset($_GET['error'])) {
        $error = $_GET['error'];
        if ($error === '1') {
          echo '<div class="alert alert-danger" role="alert">Неверная электронная почта или пароль. Пожалуйста, попробуйте еще раз.</div>';
        } elseif ($error === '2') {
          // Check if there's an activation error message in the session
          if (isset($_SESSION['activation_error'])) {
            echo '<div class="alert alert-danger" role="alert">' . $_SESSION['activation_error'] . '</div>';
            unset($_SESSION['activation_error']); // Clear the session variable after displaying the message
          } else {
            echo '<div class="alert alert-danger" role="alert">Ваш аккаунт не активирован. Пожалуйста, проверьте свою электронную почту для активации.</div>';
          }
        }
      }
      ?>
      <a href="/" class="link-custom"><img src="../images/logo.png" alt="Avatar" class="rounded-circle mx-auto d-block"
          width="50" /></a>
      <h4 class="card-title text-center fw-bold my-3">Войдите в свой аккаунт</h4>
      <p class="text-center">Если у вас нет аккаунта, <a href="/registration" class="link-custom">зарегистрируйтесь
          здесь</a>.</p>

      <form action="/pages/login/login_process.php" method="post">
        <div class="mb-3">
          <input type="email" id="email" name="email" class="form-control" placeholder="Введите ваш email" required>
        </div>

        <div class="mb-3">
          <div class="input-group">
            <input type="password" id="Password" name="password" class="form-control" placeholder="Введите ваш пароль"
              required>
            <span class="input-group-text" id="showPassword">
              <i class="fas fa-eye"></i>
            </span>
          </div>

          <p class="mt-3 text-center"><a href="/reset-password" class="link-custom">Забыли
              пароль?</a></p>

        </div>
        <!-- Include a hidden input field to capture the redirect URL -->
        <?php
        if (isset($_GET['redirect'])) {
          echo '<input type="hidden" name="redirect" value="' . htmlspecialchars($_GET['redirect']) . '">';
        }
        ?>
        <div class="d-grid">
          <button type="submit" class="btn btn-danger"><span class="fw-bold">Войти</span></button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Toggle password visibility
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
    document.getElementById('showPassword').addEventListener('click', function () {
      togglePasswordVisibility('Password');
    });
  });
</script>