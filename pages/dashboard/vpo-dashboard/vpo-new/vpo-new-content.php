<h3 class="text-center text-white mb-3"><?= htmlspecialchars($pageTitle); ?></h3>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/auth.php";

// Query to fetch college data
$query = "SELECT * FROM vpo WHERE approved IS NULL OR approved != 1";
$stmtSelectCollege = $connection->prepare($query);

if (!$stmtSelectCollege) {
    header("Location: /error");
    exit();
}

$stmtSelectCollege->execute();
$result = $stmtSelectCollege->get_result();

if ($result->num_rows > 0): ?>
  <div class="d-flex justify-content-center">
    <table class="table table-hover table-sm table-bordered align-middle text-center" style="font-size: 13px;">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Approved</th>
          <th>Name</th>
          <th>Full Name</th>
          <th>Short Name</th>
          <th>Old Name</th>
          <th>Director Name</th>
          <th>Director Info</th>
          <th>Director Phone</th>
          <th>Director Email</th>
          <th>Site</th>
          <th>Email</th>
          <th>Tel PK</th>
          <th>Site PK</th>
          <th>Email PK</th>
          <th>History</th>
          <th>URL</th>
          <th>Edit</th>
          <th>Delete</th>
          <th>Created By ID</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row["id_vpo"]); ?></td>
            <td><?= htmlspecialchars($row["approved"]); ?></td>
            <td><?= htmlspecialchars($row["vpo_name"]); ?></td>
            <td><?= htmlspecialchars($row["full_name"]); ?></td>
            <td><?= htmlspecialchars($row["short_name"]); ?></td>
            <td><?= htmlspecialchars($row["old_name"]); ?></td>
            <td><?= htmlspecialchars($row["director_name"]); ?></td>
            <td><?= htmlspecialchars($row["director_info"]); ?></td>
            <td><?= htmlspecialchars($row["director_phone"]); ?></td>
            <td><?= htmlspecialchars($row["director_email"]); ?></td>
            <td><?= htmlspecialchars($row["site"]); ?></td>
            <td><?= htmlspecialchars($row["email"]); ?></td>
            <td><?= htmlspecialchars($row["tel_pk"]); ?></td>
            <td><?= htmlspecialchars($row["site_pk"]); ?></td>
            <td><?= htmlspecialchars($row["email_pk"]); ?></td>
            <td><?= htmlspecialchars($row["history"]); ?></td>
            <td><?= htmlspecialchars($row["vpo_url"]); ?></td>
            <td>
              <a href="/dashboard/admin-approve-VPO-edit-form.php?id_vpo=<?= urlencode($row["id_vpo"]); ?>&user_id=<?= urlencode($row["user_id"]); ?>" class="edit-icon">
                <i class="fas fa-edit" style="color: green;"></i>
              </a>
            </td>
            <td>
              <i class="fas fa-trash" onclick="deleteCollege(<?= htmlspecialchars($row['id_vpo'], ENT_QUOTES, 'UTF-8'); ?>)" style="color: red; cursor: pointer;"></i>
            </td>
            <td><?= htmlspecialchars($row["user_id"]); ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <h4 class="text-center text-white">Nothing to approve for the SPOs.</h4>
<?php endif; ?>

<script>
  function deleteCollege(id_vpo) {
    if (confirm('Are you sure you want to delete this VPO?')) {
      window.location.href = '/dashboard/admin-approve-VPO-delete.php?id_vpo=' + encodeURIComponent(id_vpo);
    }
  }
</script>
