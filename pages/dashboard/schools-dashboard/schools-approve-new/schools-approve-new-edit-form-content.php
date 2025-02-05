<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/buttons/form-buttons.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/requiredAsterisk.php";

// Validate and sanitize input parameters
if (isset($_GET["id_school"]) && isset($_GET["user_id"])) {

  $schoolId = filter_var($_GET["id_school"], FILTER_SANITIZE_NUMBER_INT);
  $userId = filter_var($_GET["user_id"], FILTER_SANITIZE_NUMBER_INT);

  // Query the database for school details
  $query = "SELECT * FROM schools WHERE id_school = ?";
  $stmt = $connection->prepare($query);

  if (!$stmt) {
    redirectToErrorPage($connection->error, __FILE__, __LINE__);
  }

  $stmt->bind_param("i", $schoolId);
  if (!$stmt->execute()) {
    redirectToErrorPage($connection->error, __FILE__, __LINE__);
  }

  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
  } else {
    redirectToErrorPage($connection->error, __FILE__, __LINE__);
  }

  $stmt->close();
} else {
  header("Location: /error");
  exit();
}
?>

<h3 class="text-center text-white mb-3">
  <?= htmlspecialchars($pageTitle) ?>
  <br>
  <span class='text-primary'><?= htmlspecialchars($row["school_name"] . ' / ' . $schoolId); ?></span>
</h3>
<p class='text-center text-danger'>Fields marked with an asterisk (*) are required.</p>

<div class="container mt-4">
  <div class="row justify-content-center">
    <form action="admin-approve-school-edit-form-process.php" method="post">
      <!-- Hidden field for school ID -->
      <input type="hidden" name="id_school" value="<?= htmlspecialchars($schoolId); ?>">
      <input type="hidden" name="user_id" value="<?= htmlspecialchars($userId); ?>">

      <!-- School Name -->
      <div class="form-floating mb-3">
        <textarea class="form-control" id="school_name" name="school_name" style="height: 85px; font-size: 14px;" required placeholder="Name"><?= htmlspecialchars($row["school_name"]); ?></textarea>
        <label for="school_name">Name <?php echo requiredAsterisk(); ?></label>
      </div>

      <!-- Full Name -->
      <div class="form-floating mb-3">
        <textarea class="form-control" id="full_name" name="full_name" style="height: 85px; font-size: 14px;" required placeholder="Full Name"><?= htmlspecialchars($row["full_name"]); ?></textarea>
        <label for="full_name">Full Name <?php echo requiredAsterisk(); ?></label>
      </div>

      <!-- Short Name -->
      <div class="form-floating mb-3">
        <input type="text" class="form-control" id="short_name" name="short_name" value="<?= htmlspecialchars($row["short_name"]); ?>" required placeholder="Short Name" style="font-size: 14px;">
        <label for="short_name">Short Name <?php echo requiredAsterisk(); ?></label>
      </div>

      <!-- Website and Email -->
      <div class="row">
        <div class="col">
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="site" name="site" value="<?= htmlspecialchars($row["site"]); ?>" placeholder="Website" style="font-size: 14px;">
            <label for="site">Website <?php echo requiredAsterisk(); ?></label>
          </div>
        </div>
        <div class="col">
          <div class="form-floating mb-3">
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($row["email"]); ?>" placeholder="Email" style="font-size: 14px;">
            <label for="email">Email <?php echo requiredAsterisk(); ?></label>
          </div>
        </div>
      </div>

      <!-- Director Details -->
      <div class="row">
        <div class="col">
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="director_role" name="director_role" value="<?= htmlspecialchars($row["director_role"]); ?>" placeholder="Director Role" style="font-size: 14px;">
            <label for="director_role">Director Role</label>
          </div>
        </div>
        <div class="col">
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="director_name" name="director_name" value="<?= htmlspecialchars($row["director_name"]); ?>" placeholder="Director Name" style="font-size: 14px;">
            <label for="director_name">Director Name</label>
          </div>
        </div>
      </div>

      <!-- History -->
      <div class="form-floating mb-3">
        <textarea class="form-control" id="history" name="history" style="height: 200px; font-size: 14px;" placeholder="School History"><?= htmlspecialchars_decode($row["history"]); ?></textarea>
        <label for="history">School History</label>
      </div>

      <!-- Approve Checkbox -->
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="approveSchool" id="approveSchool" <?= $row['approved'] == 1 ? 'checked' : ''; ?>>
        <label class="form-check-label" for="approveSchool">Approve</label>
      </div>
      <input type="hidden" name="approved" value="<?= $row['approved']; ?>">

      <!-- Submit Button -->
      <div class="d-flex justify-content-center align-items-center mt-3 gap-3">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="/dashboard" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>

<script>
  document.getElementById("approveSchool").addEventListener("change", function() {
    document.querySelector("input[name='approved']").value = this.checked ? 1 : 0;
  });
</script>