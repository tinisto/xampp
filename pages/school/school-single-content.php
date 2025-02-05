<?php $row = $additionalData['row']; ?>
<?php if (!include 'school-single-functions.php') {
    header("Location: /error");
    exit();
} ?>

<div class="container mt-4" style="font-size: 14px;">
    <?php include 'school-single-header-links.php' ?>

    <!-- Assuming $row is already defined or fetched;
    Include the location_info.php file   -->
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/location_info.php'; ?>

    <h1 class="display-6">
        <?php echo $pageTitle; ?>
    </h1>

    <div class="row">
        <?php if (!empty($row['image_school_1'])) : ?>
            <div class="col-md-4 mb-3">
                <img src="../images/schools-images/<?= htmlspecialchars($row['image_school_1']); ?>"
                    class="img-fluid img-thumbnail" alt="Image 1">
            </div>
        <?php endif; ?>

        <?php if (!empty($row['image_school_2'])) : ?>
            <div class="col-md-4 mb-3">
                <img src="../images/schools-images/<?= htmlspecialchars($row['image_school_2']); ?>"
                    class="img-fluid img-thumbnail" alt="Image 2">
            </div>
        <?php endif; ?>

        <?php if (!empty($row['image_school_3'])) : ?>
            <div class="col-md-4 mb-3">
                <img src="../images/schools-images/<?= htmlspecialchars($row['image_school_3']); ?>"
                    class="img-fluid img-thumbnail" alt="Image 3">
            </div>
        <?php endif; ?>
    </div>

    <?php
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    ?>
        <div class="d-flex justify-content-evenly align-items-center bg-warning-subtle p-2 my-2 border border-danger">
            <div>
                <?php
                echo '<h3>' . $row['id_school'] . '</h3>';
                ?>
            </div>
            <!-- Form with target="_blank" to open a new window and send data -->
            <div>
                <form method="post" action='/pages/school/send-emails-to-current-school.php' target="_blank">
                    <!-- Hidden input fields for additional data -->
                    <input type="hidden" name="id_school" value="<?php echo $row['id_school']; ?>">
                    <input type="hidden" name="email" value="<?php echo $row['email']; ?>">
                    <input type="hidden" name="director_email" value="<?php echo $row['director_email']; ?>">
                    <button type="submit" name="send_emails" class="custom-button">Send Emails to School</button>
                </form>
            </div>
            <div>
                <i class="fas fa-trash" onclick="deleteSchool(<?php echo $row['id_school']; ?>)"
                    style="color: red; cursor: pointer;"></i>
            </div>
        </div>
    <?php
    }

    require('school-tabs.php');
    ?>

    <div class="d-flex flex-row text-muted d-flex justify-content-between" style="font-size: 12px;">
        <div><span>
                <?php displayIfNotEmptyDate($row['updated']); ?>
            </span>
            <span class='ms-2'>
                <?php echo '<a href="/pages/school/edit/school-edit-form.php?id_school=' . $row['id_school'] . '" class="edit-icon" style="color: red;"><i class="fa fa-pencil"></i></a>'; ?>
            </span>
        </div>
        <div class="d-flex align-items-center">
            <span class="me-1"><i class='fas fa-eye'></i></span>
            <?php echo $row['view']; ?>
        </div>
    </div>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/getEntityIdFromURL.php'; ?>
<?php
$result = extractSchoolIdEntityFromURL();

$id_entity = $result['id_entity'];
$entity_type = $result['entity_type'];

?>

<?php
include $_SERVER['DOCUMENT_ROOT'] . '/comments/user_comments.php';
?>

<script>
    function deleteSchool(schoolId) {
        // You can implement the logic to delete the post using AJAX or a form submission
        // For simplicity, let's use a confirm dialog
        if (confirm('Are you sure you want to delete this college?')) {
            // Use window.location.href to redirect to the delete page with the post ID
            window.location.href = '/pages/dashboard/schools-dashboard/schools-delete/schools-delete.php?id=' + schoolId;
        }
    }
</script>
