<div class="col-md-6">
  <div class="card">
    <div class="card-body" style="font-size: 14px;">
      <a href="/" class="link-custom"><img src="../images/logo.png" alt="Avatar" class="rounded-circle mx-auto d-block" width="50" /></a>
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
