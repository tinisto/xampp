<form action="/pages/registration/registration_process.php" method="post" id="registrationForm">
  <input type="hidden" name="timezone" id="timezone" value="">
  
  <div class="mb-3">
    <input type="email" id="email" name="email" class="form-control" placeholder="Введите ваш email" 
           value="<?php echo htmlspecialchars($oldData['email']); ?>" required>
  </div>

  <div class="mb-3">
    <div class="input-group">
      <input type="password" id="password" name="password" class="form-control" placeholder="Введите пароль" 
             minlength="6" required>
      <span class="input-group-text" id="showPassword">
        <i class="fas fa-eye"></i>
      </span>
    </div>
  </div>

  <div class="mb-3">
    <div class="input-group">
      <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" 
             placeholder="Подтвердите пароль" required>
      <span class="input-group-text" id="showConfirmPassword">
        <i class="fas fa-eye"></i>
      </span>
    </div>
  </div>

  <div class="mb-3">
    <select id="occupation" name="occupation" class="form-control" required>
      <option value="">Выберите род деятельности</option>
      <option value="Ученик" <?php echo ($oldData['occupation'] === 'Ученик') ? 'selected' : ''; ?>>Ученик</option>
      <option value="Студент" <?php echo ($oldData['occupation'] === 'Студент') ? 'selected' : ''; ?>>Студент</option>
      <option value="Родитель" <?php echo ($oldData['occupation'] === 'Родитель') ? 'selected' : ''; ?>>Родитель</option>
      <option value="Преподаватель" <?php echo ($oldData['occupation'] === 'Преподаватель') ? 'selected' : ''; ?>>Преподаватель</option>
      <option value="Другое" <?php echo ($oldData['occupation'] === 'Другое') ? 'selected' : ''; ?>>Другое</option>
    </select>
  </div>

  <div class="mb-3">
    <div class="form-check">
      <input type="checkbox" class="form-check-input" id="termsCheck" required>
      <label class="form-check-label" for="termsCheck">
        Я согласен с <a href="/terms" class="link-custom" target="_blank">условиями использования</a> 
        и <a href="/privacy" class="link-custom" target="_blank">политикой конфиденциальности</a>
      </label>
    </div>
  </div>

  <!-- reCAPTCHA -->
  <div class="mb-3">
    <div id="recaptcha" class="g-recaptcha" data-sitekey="6LcBTE4pAAAAAMrQQOxhyhCAhB7HL_QX8dN-W4_K"></div>
  </div>

  <div class="d-grid">
    <button type="submit" class="btn btn-danger" id="submitBtn">
      <span class="fw-bold">Зарегистрироваться</span>
    </button>
  </div>
</form>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>