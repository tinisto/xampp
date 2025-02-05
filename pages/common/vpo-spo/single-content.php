<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Ensure $additionalData is defined
if (isset($additionalData) && is_array($additionalData)) {
    $row = $additionalData['row'];
} else {
    $row = []; // Default to an empty array if $additionalData is not set
}

if (!include $_SERVER['DOCUMENT_ROOT'] . '/pages/common/vpo-spo/single-functions.php') {
    header("Location: /error");
    exit();
}

// Determine the type (vpo or spo) based on the URL
$requestUri = $_SERVER['REQUEST_URI'];
$type = strpos($requestUri, '/vpo/') !== false ? 'vpo' : 'spo';
$entityType = $type === 'vpo' ? 'university' : 'college';
$idEntityField = $type === 'vpo' ? 'id_vpo' : 'id_spo';
$urlField = $type === 'vpo' ? 'vpo_url' : 'spo_url';
$imagePrefix = $type === 'vpo' ? 'vpo' : 'spo';
$editFormUrl = $type === 'vpo' ? '/vpo-edit-form.php' : '/spo-edit-form.php';
$deleteFunction = $type === 'vpo' ? 'deleteVPO' : 'deleteSPO';
$sendEmailsUrl = '/pages/common/vpo-spo/send_emails.php';

?>

<div class="container mt-4" style="font-size: 14px;">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/pages/common/vpo-spo/single-header-links.php'; ?>
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/location_info.php'; ?>
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/getEntityIdFromURL.php'; ?>

    <?php
    $entityType = $type === 'vpo' ? 'university' : 'college';
    $idEntity = getEntityIdFromURL($connection, $type);
    ?>

    <h1 class="display-6">
        <?php echo $pageTitle; ?>
    </h1>

    <div class="row">
        <?php for ($i = 1; $i <= 3; $i++) : ?>
            <?php if (!empty($row["image_{$imagePrefix}_$i"])) : ?>
                <div class="col-md-4 mb-3">
                    <img src="../images/<?= $imagePrefix ?>-images/<?= htmlspecialchars($row["image_{$imagePrefix}_$i"]); ?>"
                        class="img-fluid img-thumbnail" alt="Image <?= $i ?>">
                </div>
            <?php endif; ?>
        <?php endfor; ?>
    </div>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
        <div class="d-flex justify-content-evenly align-items-center bg-warning-subtle p-2 my-2 border border-danger">
            <div>
                <?php echo '<h3>' . $row[$idEntityField] . '</h3>'; ?>
            </div>
            <div>
                <form method="post" action='<?= $sendEmailsUrl ?>' target="_blank">
                    <input type="hidden" name="<?= $urlField ?>" value="<?php echo $row[$urlField]; ?>">
                    <input type="hidden" name="email" value="<?php echo $row['email']; ?>">
                    <input type="hidden" name="email_pk" value="<?php echo $row['email_pk']; ?>">
                    <input type="hidden" name="director_email" value="<?php echo $row['director_email']; ?>">
                    <button type="submit" name="send_emails" class="custom-button">Send Emails to <?= strtoupper($type) ?></button>
                </form>
            </div>
            <div>
                <i class="fas fa-trash" onclick="<?= $deleteFunction ?>('<?php echo $row[$idEntityField]; ?>')"
                    style="color: red; cursor: pointer;"></i>
            </div>
        </div>
    <?php endif; ?>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/pages/common/vpo-spo/generic-tabs.php'; ?>

    <div class="d-flex flex-row text-muted d-flex justify-content-between" style="font-size: 12px;">
        <div class='d-flex align-items-center'>
            <span>
                <?php displayIfNotEmptyDate($row['updated']); ?>
            </span>
            <span class='ms-2'>
                <?php echo '<a href="' . $editFormUrl . '?id_' . $entityType . '=' . $row[$idEntityField] . '" class="edit-icon" style="color: red;"><i class="fa fa-pencil"></i></a>'; ?>
            </span>
        </div>
        <div class="d-flex align-items-center">
            <span class="me-1"><i class='fas fa-eye'></i></span>
            <?php echo $row['view']; ?>
        </div>
    </div>
</div>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/getEntityIdFromURL.php';
$result = getEntityIdFromURL($connection, $type);
$id_entity = $result['id_entity'];
$entity_type = $result['entity_type'];

// Check if the user is logged in
if (isset($_SESSION['email']) && isset($_SESSION['avatar'])) {
    $user = $_SESSION['email'];
    $avatar = $_SESSION['avatar'];
}

require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/get_avatar.php";



include $_SERVER['DOCUMENT_ROOT'] . '/comments/user_comments.php';
?>

<script>
    function <?= $deleteFunction ?>(id) {
        if (confirm('Are you sure you want to delete this <?= $entity_type ?>?')) {
            window.location.href = '/pages/dashboard/<?= $entity_type ?>-dashboard/<?= $entity_type ?>-delete/<?= $entity_type ?>-delete.php?id=' + id;
        }
    }
</script>