<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <title>
    <?php echo $pageTitle; ?>
  </title>
  <link rel="icon" href="/favicon.ico" type="image/x-icon">
</head>

<body class="d-flex flex-column min-vh-100">
  <?php include $_SERVER["DOCUMENT_ROOT"] . "/common-components/header.php"; ?>

  <main class="container my-4">
    <!-- <?php include "admin-comments-header-links.php"; ?> -->
    <h2 class='text-center'>
      <?php echo $pageTitle; ?>
    </h2>
    <div>
      <?php include $mainContent; ?>
    </div>
  </main>
  <?php include $_SERVER["DOCUMENT_ROOT"] . "/common-components/footer.php"; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>