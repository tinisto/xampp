<?php
session_start();

// Simple CSRF token generation
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Retrieve errors and old data
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$oldData = isset($_SESSION['oldData']) ? $_SESSION['oldData'] : [];

// Clear session variables after retrieving them
unset($_SESSION['errors']);
unset($_SESSION['oldData']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Регистрация - 11-классники</title>
  
  <!-- Favicon -->
  <?php 
  include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/favicon.php';
  renderFavicon();
  ?>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <!-- <link rel='stylesheet' type='text/css' href='/css/authorization.css'> -->
  
  <style>
    body {
      background-color: #ffffff !important;
    }
    main, .container {
      background-color: #ffffff !important;
    }
    .sign-up-custom { 
      font-weight: 600; 
      color: #212529; 
    }
    .input-group > .form-control {
      border-right: none;
    }
    .input-group-text {
      background: #ffffff;
      border-left: none;
      cursor: pointer;
    }
    /* Remove side-by-side on smaller screens */
    @media (max-width: 768px) {
      .row .col-md-6 {
        margin-bottom: 1rem;
      }
    }
  </style>
</head>

<body class="d-flex flex-column min-vh-100">
  <main class="container my-4">
    <div class="d-flex justify-content-center align-items-center">
      <section class="d-flex justify-content-center align-items-center my-4">
        <div class="col-lg-6 col-md-8 col-sm-10 mx-auto">
          <div class="border border-dark-subtle rounded-3 mb-2 pb-2 px-2" style="background-color: #ffffff;">
            <div class="p-3 shadow-sm" style="background-color: #ffffff;">
              <h5 class="text-center mb-3 sign-up-custom" style="color: #28a745;">Регистрация</h5>
              
              <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                  <?php foreach ($errors as $error): ?>
                    <p class="mb-1"><?= htmlspecialchars($error) ?></p>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <form id="registrationForm" method="post" action="/pages/registration/registration_process.php" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div class="mb-3">
                  <input type="text" id="firstname" name="firstname" class="form-control"
                    value="<?= isset($oldData['firstname']) ? htmlspecialchars($oldData['firstname']) : '' ?>"
                    placeholder="Имя" required>
                </div>
                
                <div class="mb-3">
                  <input type="text" id="lastname" name="lastname" class="form-control"
                    value="<?= isset($oldData['lastname']) ? htmlspecialchars($oldData['lastname']) : '' ?>"
                    placeholder="Фамилия" required>
                </div>

                <div class="mb-3">
                  <select name="occupation" id="occupation" class="form-select" required>
                    <option value="">Выберите род деятельности</option>
                    <option value="Представитель ВУЗа" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Представитель ВУЗа' ? 'selected' : '' ?>>Представитель ВУЗа</option>
                    <option value="Представитель ССУЗа" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Представитель ССУЗа' ? 'selected' : '' ?>>Представитель ССУЗа</option>
                    <option value="Представитель школы" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Представитель школы' ? 'selected' : '' ?>>Представитель школы</option>
                    <option value="Родитель" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Родитель' ? 'selected' : '' ?>>Родитель</option>
                    <option value="Учащийся/учащаяся" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Учащийся/учащаяся' ? 'selected' : '' ?>>Учащийся</option>
                    <option value="Другое" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Другое' ? 'selected' : '' ?>>Другое</option>
                  </select>
                </div>

                <div class="mb-3">
                  <input type="email" id="email" name="email" class="form-control"
                    value="<?= isset($oldData['email']) ? htmlspecialchars($oldData['email']) : '' ?>"
                    placeholder="Email адрес" required>
                </div>

                <div class="mb-3">
                  <div class="input-group">
                    <input type="password" id="newPassword" name="newPassword" class="form-control" placeholder="Пароль" required>
                    <span class="input-group-text" id="togglePassword1">
                      <i class="fa fa-eye" id="toggleIcon1"></i>
                    </span>
                  </div>
                </div>
                
                <div class="mb-3">
                  <div class="input-group">
                    <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Подтвердите пароль" required>
                    <span class="input-group-text" id="togglePassword2">
                      <i class="fa fa-eye" id="toggleIcon2"></i>
                    </span>
                  </div>
                </div>

                <div class="mb-3">
                  <label for="avatar" class="form-label">Аватар (необязательно)</label>
                  <input type="file" id="avatar" name="avatar" class="form-control" accept="image/*">
                </div>

                <input type="hidden" name="timezone" id="timezone" value="">

                <button type="submit" class="btn btn-success d-block mx-auto mt-3" style="background-color: #28a745; border-color: #28a745; padding: 10px 30px;">
                  <span class="fw-bold">Зарегистрироваться</span>
                </button>
              </form>
              
              <p class="mt-3 text-center">Уже есть аккаунт? <a href="/login" class="text-decoration-none" style="color: #28a745;">Войдите здесь</a></p>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>

  <script>
  // Timezone detection
  function detectTimezone() {
      const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
      document.getElementById('timezone').value = timezone;
  }
  window.addEventListener('load', detectTimezone);

  // Password visibility toggles
  function setupPasswordToggle(passwordId, toggleId, iconId) {
      document.getElementById(toggleId).addEventListener('click', function() {
          const passwordInput = document.getElementById(passwordId);
          const toggleIcon = document.getElementById(iconId);
          
          if (passwordInput.type === 'password') {
              passwordInput.type = 'text';
              toggleIcon.classList.remove('fa-eye');
              toggleIcon.classList.add('fa-eye-slash');
          } else {
              passwordInput.type = 'password';
              toggleIcon.classList.remove('fa-eye-slash');
              toggleIcon.classList.add('fa-eye');
          }
      });
  }

  // Setup both password toggles
  setupPasswordToggle('newPassword', 'togglePassword1', 'toggleIcon1');
  setupPasswordToggle('confirmPassword', 'togglePassword2', 'toggleIcon2');

  // Form validation
  document.getElementById('registrationForm').addEventListener('submit', function(event) {
      const firstname = document.getElementById('firstname').value.trim();
      const lastname = document.getElementById('lastname').value.trim();
      const email = document.getElementById('email').value.trim();
      const newPassword = document.getElementById('newPassword').value;
      const confirmPassword = document.getElementById('confirmPassword').value;
      const occupation = document.getElementById('occupation').value;

      // Check required fields
      if (!firstname || !lastname || !email || !newPassword || !confirmPassword || !occupation) {
          alert("Пожалуйста, заполните все обязательные поля.");
          event.preventDefault();
          return false;
      }
      
      // Check passwords match
      if (newPassword !== confirmPassword) {
          alert("Пароли не совпадают.");
          event.preventDefault();
          return false;
      }

      // Check password strength
      if (newPassword.length < 8) {
          alert("Пароль должен содержать минимум 8 символов.");
          event.preventDefault();
          return false;
      }

      return true;
  });
  </script>

  <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>