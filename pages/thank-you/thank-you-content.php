<?php
// Check if the session message exists
$message = isset($_SESSION["message"]) ? $_SESSION["message"] : null;

// Clear the session message after it's shown (optional, to avoid showing it again)
unset($_SESSION["message"]);
?>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <?php if ($message): ?> <!-- Only show alert if a message exists -->
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <h4 class="alert-heading">Спасибо!</h4>
          <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="redirectToIndex()"></button>
        </div>
      <?php else: ?> <!-- If no session message exists, show default message -->
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <h4 class="alert-heading">Спасибо!</h4>
          <p>Ваше сообщение успешно отправлено. Мы его рассмотрим в ближайшее время.</p>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="redirectToIndex()"></button>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  function redirectToIndex() {
    // Redirect to the index page
    window.location.href = '/';
  }
</script>