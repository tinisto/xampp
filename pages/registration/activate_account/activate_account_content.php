<div class="col-md-6">
  <div class="card">
    <div class="card-body">
      <h1 class="text-center mb-4">Активация аккаунта</h1>
      <form action="activate_account.php" method="post" class="mb-4">
        <div class="form-group mb-3">
          <input type="email" id="email" name="email" class="form-control" placeholder="Введите ваш email для активации" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>" required>
        </div>
        <div class="form-group mb-3">
          <input type="text" id="activation_code" name="activation_code" class="form-control" placeholder="Введите код активации" required>
        </div>
        <div class="d-grid">
          <button type="submit" class="submit-button">Активировать аккаунт</button>
        </div>
        <?php if (!empty($message)): ?>
          <div class="alert alert-danger mt-3" role="alert">
            <?php echo $message; ?>
          </div>
        <?php endif; ?>
      </form>
      <p class="mt-3 text-center"><a href="resend_activation.php" class="text-decoration-none">Не получили код активации? Отправить снова.</a></p>
      <p class="mt-3 text-center"><a href="/" class="text-decoration-none">Вернуться на сайт</a>.</p>
    </div>
  </div>
</div>
