<div class="col-md-6">
  <div class="card">
    <div class="card-body" style="font-size: 14px;">

      <?php
      // Retrieve errors from the URL if present
      $errors = isset($_GET['errors']) ? $_GET['errors'] : [];

      // Retrieve previously entered data from the URL if present
      $oldData = [
        'firstname' => isset($_GET['firstname']) ? $_GET['firstname'] : '',
        'lastname' => isset($_GET['lastname']) ? $_GET['lastname'] : '',
        'occupation' => isset($_GET['occupation']) ? $_GET['occupation'] : '',
        'avatar' => isset($_GET['avatar']) ? $_GET['avatar'] : '',
        'newPassword' => '',
        'confirmPassword' => '',
        'email' => isset($_GET['email']) ? $_GET['email'] : '',
      ];
      ?>

      <div class="text-center mb-3">
          <a href="/" class="text-decoration-none">
              <i class="fas fa-home text-success me-2"></i>
              <span class="text-muted small">Вернуться на главную</span>
          </a>
      </div>
      
      <a href="/" class="link-custom"><img src="../images/logo.png" alt="Avatar" class="rounded-circle mx-auto d-block"
          width="50" /></a>

      <h4 class="card-title text-center fw-bold my-3">Регистрация</h4>
      <p class="mt-3 text-center">Уже есть аккаунт? <a href="/login" class="link-custom">Войдите
          здесь</a>.</p>
      <?php
      // Display errors if any
      if (!empty($errors)) {
        echo '<div class="alert alert-danger" role="alert">';
        foreach ($errors as $error) {
          echo $error . '<br>';
        }
        echo '</div>';
      }
      ?>

      <?php include 'registration_form_include.php'; ?>
      <script src="registration.js"></script>
    </div>
  </div>
</div>