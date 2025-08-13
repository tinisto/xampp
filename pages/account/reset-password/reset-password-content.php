<div class="col-md-6">
  <div class="card">
    <div class="card-body" style="font-size: 14px;">
      <div class="text-center mb-3">
        <a href="/" class="auth-logo">
          <div style="
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
          ">
            11
          </div>
          <span>классники</span>
        </a>
      </div>
      <h4 class="card-title text-center fw-bold my-3">Сброс пароля</h4>

      <?php if ($resetSuccess): ?>
        <div class="alert alert-success" role="alert">Ссылка для сброса пароля отправлена на вашу электронную почту.</div>
      <?php elseif (isset($_POST['email'])): // Check if email was submitted ?>
        <div class="alert alert-danger" role="alert">Адрес электронной почты не найден в базе данных.</div>
      <?php else: ?>
        <p>Не помните пароль? Введите ваш адрес электронной почты, и мы вышлем вам письмо для создания нового пароля.</p>

        <form action="reset-password" method="post">
          <div class="mb-3">
            <input type="email" id="email" name="email" class="form-control" placeholder="Адрес электронной почты" required>
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-danger"><span class="fw-bold">Отправить ссылку на сброс</span></button>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>
</div>
