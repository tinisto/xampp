<div class="auth-container">
  <div class="auth-card">
    <div class="auth-header">
      <div class="auth-logo">
        <div class="logo-icon">11</div>
      </div>
      <h1 class="auth-title">Забыли пароль?</h1>
      <p class="auth-subtitle">Мы поможем вам восстановить доступ к аккаунту</p>
    </div>

    <div class="auth-body">
      <?php
      // Success message
      if (isset($_GET['success']) && $_GET['success'] === 'true') {
        echo '<div class="alert alert-success">';
        echo '<i class="fas fa-check-circle"></i>';
        echo '<div>';
        echo '<h4>Письмо отправлено!</h4>';
        echo '<p>Мы отправили инструкции по восстановлению пароля на указанный email адрес. Проверьте свою почту и следуйте инструкциям в письме.</p>';
        echo '<p class="small">Не получили письмо? Проверьте папку "Спам" или попробуйте еще раз.</p>';
        echo '</div>';
        echo '</div>';
      }

      // Error message
      if (isset($_GET['error'])) {
        echo '<div class="alert alert-error">';
        echo '<i class="fas fa-exclamation-circle"></i>';
        echo '<div>';
        
        $error = $_GET['error'];
        if ($error === 'not_found') {
          echo '<p>Пользователь с таким email адресом не найден. Проверьте правильность введенного адреса или <a href="/registration" class="link">зарегистрируйтесь</a>.</p>';
        } elseif ($error === 'rate_limit') {
          echo '<p>Слишком много запросов. Попробуйте снова через несколько минут.</p>';
        } else {
          echo '<p>Произошла ошибка при отправке письма. Пожалуйста, попробуйте позже.</p>';
        }
        
        echo '</div>';
        echo '</div>';
      }
      ?>

      <form action="/pages/forgot-password/forgot-password-process.php" method="post" class="auth-form" id="forgotPasswordForm">
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
              value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>"
            >
          </div>
          <div class="form-hint">
            <i class="fas fa-info-circle"></i>
            Мы отправим ссылку для восстановления пароля на этот адрес
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-full" id="submitBtn">
          <span class="btn-text">Отправить инструкции</span>
          <span class="btn-loader" style="display: none;">
            <i class="fas fa-spinner fa-spin"></i>
          </span>
        </button>
      </form>

      <div class="auth-divider">
        <span>или</span>
      </div>

      <div class="auth-alternatives">
        <a href="/login" class="btn btn-outline btn-full">
          <i class="fas fa-arrow-left"></i>
          <span>Вернуться к входу</span>
        </a>
      </div>
    </div>

    <div class="auth-footer">
      <p>Нет аккаунта? <a href="/registration" class="link">Зарегистрироваться</a></p>
    </div>
  </div>

  <!-- Security Tips -->
  <div class="auth-help">
    <div class="help-card">
      <h3>Советы по безопасности</h3>
      <ul>
        <li><i class="fas fa-shield-alt"></i> Используйте надежный пароль (минимум 8 символов)</li>
        <li><i class="fas fa-key"></i> Включите цифры, буквы и специальные символы</li>
        <li><i class="fas fa-user-shield"></i> Не используйте один пароль для разных сайтов</li>
        <li><i class="fas fa-mobile-alt"></i> Рассмотрите использование менеджера паролей</li>
      </ul>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('forgotPasswordForm');
  const emailInput = document.getElementById('email');
  const submitBtn = document.getElementById('submitBtn');

  // Form submission
  form.addEventListener('submit', function(e) {
    const email = emailInput.value.trim();
    
    if (!email || !isValidEmail(email)) {
      e.preventDefault();
      emailInput.classList.add('error');
      emailInput.focus();
      return;
    }

    // Show loading state
    submitBtn.disabled = true;
    submitBtn.querySelector('.btn-text').style.display = 'none';
    submitBtn.querySelector('.btn-loader').style.display = 'inline-block';
  });

  // Email validation
  emailInput.addEventListener('blur', function() {
    validateField(this);
  });

  emailInput.addEventListener('input', function() {
    if (this.classList.contains('error')) {
      validateField(this);
    }
  });

  function validateField(field) {
    const value = field.value.trim();
    const isValid = field.checkValidity() && isValidEmail(value);
    
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

  function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  // Auto-focus on email input
  if (emailInput.value === '') {
    emailInput.focus();
  }
});
</script>