<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel='stylesheet' type='text/css' href='/css/authorization.css'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <script src="https://www.google.com/recaptcha/api.js"></script>
  <title>Регистрация - 11-классники</title>
  
  <!-- Favicon -->
  <?php 
  include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/favicon.php';
  renderFavicon();
  ?>
</head>

<body class="d-flex flex-column min-vh-100">
  <main class="container">

    <body class="d-flex flex-column min-vh-100">

      <main class="container my-4">
        <div class="d-flex justify-content-center align-items-center min-vh-100">

          <?php include $mainContent; ?>

        </div>
      </main>

      <!-- Include JavaScript files -->
      <script src="/scripts/validation.js"></script>
      <script src="/scripts/toggleAvatarInput.js"></script>
      <script src="/scripts/togglePasswordVisibility.js"></script>

      <!-- Include bootstrap JavaScript files -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    </body>

</html>