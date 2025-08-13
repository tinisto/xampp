<div class="auth-container">
  <div class="auth-card">
    <div class="auth-header">
      <div class="auth-logo">
        <div class="logo-icon">11</div>
      </div>
      <h1 class="auth-title">Создать аккаунт</h1>
      <p class="auth-subtitle">Присоединяйтесь к 11классники.ru</p>
    </div>

    <div class="auth-body">
      <?php
      // Display errors if any
      if (isset($_GET['errors']) && !empty($_GET['errors'])) {
        echo '<div class="alert alert-error">';
        echo '<i class="fas fa-exclamation-circle"></i>';
        echo '<div>';
        foreach ($_GET['errors'] as $error) {
          echo '<p>' . htmlspecialchars($error) . '</p>';
        }
        echo '</div>';
        echo '</div>';
      }

      // Retrieve previously entered data
      $oldData = [
        'firstname' => isset($_GET['firstname']) ? htmlspecialchars($_GET['firstname']) : '',
        'lastname' => isset($_GET['lastname']) ? htmlspecialchars($_GET['lastname']) : '',
        'email' => isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '',
        'occupation' => isset($_GET['occupation']) ? htmlspecialchars($_GET['occupation']) : ''
      ];
      ?>

      <form action="/pages/registration/registration_process.php" method="post" class="auth-form" id="registrationForm">
        <div class="form-row">
          <div class="form-group">
            <label for="firstname" class="form-label">Имя</label>
            <div class="input-group">
              <span class="input-icon">
                <i class="fas fa-user"></i>
              </span>
              <input 
                type="text" 
                id="firstname" 
                name="firstname" 
                class="form-input" 
                placeholder="Введите ваше имя"
                value="<?php echo $oldData['firstname']; ?>"
                required
                autocomplete="given-name"
              >
            </div>
          </div>

          <div class="form-group">
            <label for="lastname" class="form-label">Фамилия</label>
            <div class="input-group">
              <span class="input-icon">
                <i class="fas fa-user"></i>
              </span>
              <input 
                type="text" 
                id="lastname" 
                name="lastname" 
                class="form-input" 
                placeholder="Введите вашу фамилию"
                value="<?php echo $oldData['lastname']; ?>"
                required
                autocomplete="family-name"
              >
            </div>
          </div>
        </div>

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
              placeholder="example@email.com"
              value="<?php echo $oldData['email']; ?>"
              required
              autocomplete="email"
            >
          </div>
          <div class="form-hint">
            <i class="fas fa-info-circle"></i>
            Мы отправим письмо для подтверждения на этот адрес
          </div>
        </div>

        <div class="form-group">
          <label for="occupation" class="form-label">Кто вы?</label>
          <div class="input-group">
            <span class="input-icon">
              <i class="fas fa-graduation-cap"></i>
            </span>
            <select id="occupation" name="occupation" class="form-select" required>
              <option value="">Выберите вашу роль</option>
              <option value="student" <?php echo $oldData['occupation'] === 'student' ? 'selected' : ''; ?>>Ученик</option>
              <option value="parent" <?php echo $oldData['occupation'] === 'parent' ? 'selected' : ''; ?>>Родитель</option>
              <option value="teacher" <?php echo $oldData['occupation'] === 'teacher' ? 'selected' : ''; ?>>Учитель</option>
              <option value="counselor" <?php echo $oldData['occupation'] === 'counselor' ? 'selected' : ''; ?>>Консультант</option>
              <option value="other" <?php echo $oldData['occupation'] === 'other' ? 'selected' : ''; ?>>Другое</option>
            </select>
          </div>
        </div>

        <div class="form-row">
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
                placeholder="Создайте надежный пароль"
                required
                autocomplete="new-password"
                minlength="8"
              >
              <button type="button" class="input-action" id="togglePassword">
                <i class="fas fa-eye"></i>
              </button>
            </div>
            <div class="password-strength" id="passwordStrength">
              <div class="strength-bar">
                <div class="strength-fill"></div>
              </div>
              <div class="strength-text">Сила пароля: <span>Слабый</span></div>
            </div>
          </div>

          <div class="form-group">
            <label for="confirmPassword" class="form-label">Повторите пароль</label>
            <div class="input-group">
              <span class="input-icon">
                <i class="fas fa-lock"></i>
              </span>
              <input 
                type="password" 
                id="confirmPassword" 
                name="confirmPassword" 
                class="form-input" 
                placeholder="Повторите пароль"
                required
                autocomplete="new-password"
              >
              <button type="button" class="input-action" id="toggleConfirmPassword">
                <i class="fas fa-eye"></i>
              </button>
            </div>
            <div class="form-feedback" id="passwordMatch"></div>
          </div>
        </div>

        <div class="form-group">
          <div class="checkbox-group">
            <input type="checkbox" id="terms" name="terms" class="form-checkbox" required>
            <label for="terms" class="checkbox-label">
              Я согласен с <a href="/terms" target="_blank" class="link">Условиями использования</a> 
              и <a href="/privacy" target="_blank" class="link">Политикой конфиденциальности</a>
            </label>
          </div>
        </div>

        <div class="form-group">
          <div class="checkbox-group">
            <input type="checkbox" id="newsletter" name="newsletter" class="form-checkbox">
            <label for="newsletter" class="checkbox-label">
              Получать новости и обновления по email
            </label>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-full" id="submitBtn">
          <span class="btn-text">Создать аккаунт</span>
          <span class="btn-loader" style="display: none;">
            <i class="fas fa-spinner fa-spin"></i>
          </span>
        </button>
      </form>
    </div>

    <div class="auth-footer">
      <p>Уже есть аккаунт? <a href="/login" class="link">Войти</a></p>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('registrationForm');
  const passwordInput = document.getElementById('password');
  const confirmPasswordInput = document.getElementById('confirmPassword');
  const togglePassword = document.getElementById('togglePassword');
  const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
  const strengthIndicator = document.getElementById('passwordStrength');
  const passwordMatchIndicator = document.getElementById('passwordMatch');
  const submitBtn = document.getElementById('submitBtn');

  // Password visibility toggles
  togglePassword.addEventListener('click', function() {
    togglePasswordVisibility(passwordInput, this);
  });

  toggleConfirmPassword.addEventListener('click', function() {
    togglePasswordVisibility(confirmPasswordInput, this);
  });

  function togglePasswordVisibility(input, button) {
    const icon = button.querySelector('i');
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    } else {
      input.type = 'password';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    }
  }

  // Password strength checker
  passwordInput.addEventListener('input', function() {
    const password = this.value;
    const strength = calculatePasswordStrength(password);
    updatePasswordStrength(strength);
    checkPasswordMatch();
  });

  confirmPasswordInput.addEventListener('input', checkPasswordMatch);

  function calculatePasswordStrength(password) {
    let score = 0;
    const checks = {
      length: password.length >= 8,
      lowercase: /[a-z]/.test(password),
      uppercase: /[A-Z]/.test(password),
      numbers: /\d/.test(password),
      special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
    };

    score = Object.values(checks).filter(Boolean).length;
    
    return {
      score: score,
      checks: checks,
      text: score === 0 ? 'Очень слабый' : 
            score === 1 ? 'Слабый' :
            score === 2 ? 'Удовлетворительный' :
            score === 3 ? 'Хороший' :
            score === 4 ? 'Сильный' : 'Очень сильный'
    };
  }

  function updatePasswordStrength(strength) {
    const strengthFill = strengthIndicator.querySelector('.strength-fill');
    const strengthText = strengthIndicator.querySelector('.strength-text span');
    
    const percentage = (strength.score / 5) * 100;
    strengthFill.style.width = percentage + '%';
    strengthText.textContent = strength.text;

    strengthFill.className = 'strength-fill strength-' + 
      (strength.score <= 1 ? 'weak' :
       strength.score <= 2 ? 'fair' :
       strength.score <= 3 ? 'good' :
       strength.score <= 4 ? 'strong' : 'excellent');
  }

  function checkPasswordMatch() {
    const password = passwordInput.value;
    const confirmPassword = confirmPasswordInput.value;
    
    if (confirmPassword === '') {
      passwordMatchIndicator.innerHTML = '';
      return;
    }

    if (password === confirmPassword) {
      passwordMatchIndicator.innerHTML = '<i class="fas fa-check text-success"></i> Пароли совпадают';
      passwordMatchIndicator.className = 'form-feedback success';
    } else {
      passwordMatchIndicator.innerHTML = '<i class="fas fa-times text-error"></i> Пароли не совпадают';
      passwordMatchIndicator.className = 'form-feedback error';
    }
  }

  // Form submission
  form.addEventListener('submit', function(e) {
    const password = passwordInput.value;
    const confirmPassword = confirmPasswordInput.value;
    
    if (password !== confirmPassword) {
      e.preventDefault();
      alert('Пароли не совпадают');
      return;
    }

    const strength = calculatePasswordStrength(password);
    if (strength.score < 2) {
      e.preventDefault();
      alert('Пожалуйста, создайте более надежный пароль');
      return;
    }

    // Show loading state
    submitBtn.disabled = true;
    submitBtn.querySelector('.btn-text').style.display = 'none';
    submitBtn.querySelector('.btn-loader').style.display = 'inline-block';
  });

  // Real-time validation for all inputs
  const inputs = form.querySelectorAll('input, select');
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
});
</script>