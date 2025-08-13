<div class="auth-container">
  <div class="auth-card">
    <div class="auth-header">
      <div class="auth-logo">
        <div class="logo-icon">11</div>
      </div>
      <h1 class="auth-title">Добро пожаловать</h1>
      <p class="auth-subtitle">Войдите в ваш аккаунт</p>
    </div>

    <div class="auth-body">
      <?php
      // Success messages
      if (isset($_GET['activation_success']) && $_GET['activation_success'] === 'true') {
        if (!isset($_SESSION['activation_success_message_shown'])) {
          echo '<div class="alert alert-success">';
          echo '<i class="fas fa-check-circle"></i>';
          echo '<div>';
          echo '<p>Аккаунт успешно активирован. Теперь вы можете войти в свой аккаунт.</p>';
          echo '</div>';
          echo '</div>';
          $_SESSION['activation_success_message_shown'] = true;
        }
      }

      $registrationSuccess = isset($_GET['registration_success']) && $_GET['registration_success'] === 'true';
      $successMessage = isset($_GET['message']) ? urldecode($_GET['message']) : '';

      if ($registrationSuccess) {
        echo '<div class="alert alert-success">';
        echo '<i class="fas fa-check-circle"></i>';
        echo '<div>';
        echo '<p>' . htmlspecialchars($successMessage) . '</p>';
        echo '</div>';
        echo '</div>';
      }

      // Error messages
      if (isset($_GET['error'])) {
        $error = $_GET['error'];
        echo '<div class="alert alert-error">';
        echo '<i class="fas fa-exclamation-circle"></i>';
        echo '<div>';
        
        if ($error === '1') {
          echo '<p>Неверная электронная почта или пароль. Пожалуйста, попробуйте еще раз.</p>';
        } elseif ($error === '2') {
          if (isset($_SESSION['activation_error'])) {
            echo '<p>' . $_SESSION['activation_error'] . '</p>';
            unset($_SESSION['activation_error']);
          } else {
            echo '<p>Ваш аккаунт не активирован. Пожалуйста, проверьте свою электронную почту для активации.</p>';
          }
        }
        
        echo '</div>';
        echo '</div>';
      }
      ?>

      <form action="/pages/login/login_process.php" method="post" class="auth-form" id="loginForm">
        <div class="form-group">
          <label for="email" class="form-label">Email адрес</label>
          <div class="input-group">
            <span class="input-icon">
              <i class="fas fa-envelope"></i>
            </span>
            <input 
              type="email" 
              id="email" 
              name="email" 
              class="form-input" 
              placeholder="Введите ваш email"
              required
              autocomplete="email"
              autofocus
            >
          </div>
        </div>

        <div class="form-group">
          <label for="password" class="form-label">Пароль</label>
          <div class="input-group">
            <span class="input-icon">
              <i class="fas fa-lock"></i>
            </span>
            <input 
              type="password" 
              id="password" 
              name="password" 
              class="form-input" 
              placeholder="Введите ваш пароль"
              required
              autocomplete="current-password"
            >
            <button type="button" class="input-action" id="togglePassword">
              <i class="fas fa-eye"></i>
            </button>
          </div>
        </div>

        <div class="form-options">
          <div class="checkbox-group">
            <input type="checkbox" id="remember" name="remember" class="form-checkbox">
            <label for="remember" class="checkbox-label">Запомнить меня</label>
          </div>
          <a href="/forgot-password" class="link">Забыли пароль?</a>
        </div>

        <?php
        if (isset($_GET['redirect'])) {
          echo '<input type="hidden" name="redirect" value="' . htmlspecialchars($_GET['redirect']) . '">';
        }
        ?>

        <button type="submit" class="btn btn-primary btn-full" id="submitBtn">
          <span class="btn-text">Войти</span>
          <span class="btn-loader" style="display: none;">
            <i class="fas fa-spinner fa-spin"></i>
          </span>
        </button>
      </form>

      <div class="auth-divider">
        <span>или</span>
      </div>

      <div class="social-login">
        <button type="button" class="btn btn-social btn-google" disabled>
          <i class="fab fa-google"></i>
          <span>Войти через Google</span>
        </button>
        <button type="button" class="btn btn-social btn-vk" disabled>
          <i class="fab fa-vk"></i>
          <span>Войти через VK</span>
        </button>
      </div>
    </div>

    <div class="auth-footer">
      <p>Нет аккаунта? <a href="/registration" class="link">Зарегистрироваться</a></p>
    </div>
  </div>

  <!-- Quick Login Help -->
  <div class="auth-help">
    <div class="help-card">
      <h3>Нужна помощь?</h3>
      <ul>
        <li><i class="fas fa-question-circle"></i> <a href="/help/login" class="link">Проблемы со входом</a></li>
        <li><i class="fas fa-envelope"></i> <a href="/forgot-password" class="link">Восстановить пароль</a></li>
        <li><i class="fas fa-user-plus"></i> <a href="/registration" class="link">Создать новый аккаунт</a></li>
      </ul>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('loginForm');
  const passwordInput = document.getElementById('password');
  const togglePassword = document.getElementById('togglePassword');
  const submitBtn = document.getElementById('submitBtn');

  // Password visibility toggle
  togglePassword.addEventListener('click', function() {
    const icon = this.querySelector('i');
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    } else {
      passwordInput.type = 'password';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    }
  });

  // Form submission
  form.addEventListener('submit', function(e) {
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.querySelector('.btn-text').style.display = 'none';
    submitBtn.querySelector('.btn-loader').style.display = 'inline-block';
  });

  // Real-time validation for inputs
  const inputs = form.querySelectorAll('input[required]');
  inputs.forEach(input => {
    input.addEventListener('blur', function() {
      validateField(this);
    });

    input.addEventListener('input', function() {
      if (this.classList.contains('error')) {
        validateField(this);
      }
    });
  });

  function validateField(field) {
    const value = field.value.trim();
    const isValid = field.checkValidity();
    
    if (isValid && value !== '') {
      field.classList.remove('error');
      field.classList.add('success');
    } else if (!isValid && value !== '') {
      field.classList.remove('success');
      field.classList.add('error');
    } else {
      field.classList.remove('success', 'error');
    }
  }

  // Auto-focus on email if empty
  const emailInput = document.getElementById('email');
  if (emailInput.value === '') {
    emailInput.focus();
  }
});
</script>