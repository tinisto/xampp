<div class="col-md-6">
  <div class="card">
    <div class="card-body" style="font-size: 14px;">

      <?php
      // Debug: Check if CSRF function exists
      if (!function_exists('csrf_field')) {
          echo "<p>Error: csrf_field function not found</p>";
      }
      
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

      <!-- Simple inline form for testing -->
      <form id="demo-form" action="/pages/registration/registration_process.php" method="post" enctype="multipart/form-data">
          <?php if (function_exists('csrf_field')) echo csrf_field(); ?>
          
          <div class="mb-3">
              <input type="text" id="firstname" name="firstname" class="form-control"
                  value="<?= isset($oldData['firstname']) ? htmlspecialchars($oldData['firstname']) : '' ?>"
                  placeholder="Введите ваше имя" required>
          </div>

          <div class="mb-3">
              <input type="text" id="lastname" name="lastname" class="form-control"
                  value="<?= isset($oldData['lastname']) ? htmlspecialchars($oldData['lastname']) : '' ?>"
                  placeholder="Введите вашу фамилию" required>
          </div>

          <div class="mb-3">
              <select name="occupation" id="occupation" class="form-select" required>
                  <option value="">Выберите род деятельности</option>
                  <option value="Представитель ВУЗа">Представитель ВУЗа</option>
                  <option value="Представитель ССУЗа">Представитель ССУЗа</option>
                  <option value="Представитель школы">Представитель школы</option>
                  <option value="Родитель">Родитель</option>
                  <option value="Учащийся/учащаяся">Учащийся</option>
                  <option value="Другое">Другое</option>
              </select>
          </div>

          <div class="mb-3">
              <input type="email" id="email" name="email" class="form-control"
                  value="<?= isset($oldData['email']) ? htmlspecialchars($oldData['email']) : '' ?>"
                  placeholder="Введите ваш email" required>
          </div>

          <div class="mb-3">
              <input type="password" id="newPassword" name="newPassword" class="form-control" placeholder="Введите ваш пароль" required>
          </div>
          
          <div class="mb-3">
              <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Подтвердите пароль" required>
          </div>

          <div class="mb-3">
              <label for="avatar" class="form-label">Загрузить аватар (необязательно)</label>
              <input type="file" id="avatar" name="avatar" class="form-control" accept="image/*">
          </div>

          <input type="hidden" name="timezone" id="timezone" value="">

          <div class="d-grid">
              <button id="registrationButton" class="btn btn-danger">
                  <span class="fw-bold">Зарегистрироваться</span>
              </button>
          </div>
      </form>

      <script src="registration.js"></script>
    </div>
  </div>
</div>