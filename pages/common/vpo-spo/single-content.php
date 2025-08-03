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
$idEntityField = 'id'; // Using 'id' for both universities and colleges
$urlField = 'url_slug'; // Using 'url_slug' for both
$imagePrefix = ''; // No prefix needed, using image_1, image_2, image_3
$editFormUrl = $type === 'vpo' ? '/vpo-edit-form.php' : '/spo-edit-form.php';
$deleteFunction = $type === 'vpo' ? 'deleteVPO' : 'deleteSPO';
$sendEmailsUrl = '/pages/common/vpo-spo/send_emails.php';

?>

<style>
    /* Hide any institution badges containing ССУЗ or ВУЗ */
    .institution-badge,
    div.institution-badge,
    .institution-type-badge,
    .spo-badge,
    .vpo-badge {
        display: none !important;
    }
    
    /* Target badge right before h1 */
    h1.display-6:first-of-type::before {
        display: none !important;
    }
    
    /* Specifically target the div you mentioned */
    div.institution-badge {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
        width: 0 !important;
        overflow: hidden !important;
        position: absolute !important;
        left: -9999px !important;
    }
    
    /* Institution title styling - make it smaller */
    .institution-title {
        font-size: 1.75rem !important;
        font-weight: 600;
        line-height: 1.3;
        margin-bottom: 1rem;
        color: var(--text-primary, #333);
    }
    
    /* Responsive title sizing */
    @media (max-width: 768px) {
        .institution-title {
            font-size: 1.5rem !important;
            line-height: 1.2;
        }
    }
    
    @media (max-width: 480px) {
        .institution-title {
            font-size: 1.25rem !important;
        }
    }
    
    /* Dark mode support for title */
    [data-theme="dark"] .institution-title {
        color: var(--text-primary, #f9fafb);
    }
    
    /* Fix contact information colors */
    .tab-content {
        color: var(--text-primary, #333) !important;
    }
    
    .tab-content p {
        color: var(--text-primary, #333) !important;
    }
    
    .tab-content strong {
        color: var(--text-primary, #333) !important;
    }
    
    /* Dark mode support */
    [data-theme="dark"] .tab-content {
        color: var(--text-primary, #f9fafb) !important;
    }
    
    [data-theme="dark"] .tab-content p {
        color: var(--text-primary, #f9fafb) !important;
    }
    
    [data-theme="dark"] .tab-content strong {
        color: var(--text-primary, #f9fafb) !important;
    }
    
    /* Fix nav tabs for dark mode */
    [data-theme="dark"] .nav-tabs {
        border-bottom-color: var(--border-color, #4a5568);
    }
    
    [data-theme="dark"] .nav-tabs .nav-link {
        color: var(--text-primary, #f9fafb) !important;
        border-color: transparent;
    }
    
    [data-theme="dark"] .nav-tabs .nav-link:hover {
        border-color: var(--border-color, #4a5568);
        background-color: var(--surface-hover, #374151);
    }
    
    [data-theme="dark"] .nav-tabs .nav-link.active {
        background-color: var(--surface, #2d3748);
        border-color: var(--border-color, #4a5568) var(--border-color, #4a5568) var(--surface, #2d3748);
        color: var(--text-primary, #f9fafb) !important;
    }
</style>

<div class="container mt-4" style="font-size: 14px;">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/pages/common/vpo-spo/single-header-links.php'; ?>
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/location_info.php'; ?>
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/getEntityIdFromURL.php'; ?>

    <?php
    $entityType = $type === 'vpo' ? 'university' : 'college';
    $idEntity = getEntityIdFromURL($connection, $type);
    ?>

    <h1 class="institution-title">
        <?php 
        // Remove any ССУЗ or ВУЗ prefix from the title if it exists
        $cleanTitle = $pageTitle;
        // More robust removal - handle different encodings and variations
        $cleanTitle = preg_replace('/^(ССУЗ|ВУЗ|ссуз|вуз)\s*/ui', '', $cleanTitle);
        // Also try with different quote marks and spaces
        $cleanTitle = preg_replace('/^(ССУЗ|ВУЗ|ссуз|вуз)[\s\-\–\—]*/ui', '', $cleanTitle);
        // Trim any remaining whitespace
        $cleanTitle = trim($cleanTitle);
        echo htmlspecialchars($cleanTitle); 
        ?>
    </h1>

    <div class="row">
        <?php for ($i = 1; $i <= 3; $i++) : ?>
            <?php if (!empty($row["image_$i"])) : ?>
                <div class="col-md-4 mb-3">
                    <img src="../images/<?= $type ?>-images/<?= htmlspecialchars($row["image_$i"]); ?>"
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
                    <input type="hidden" name="url_slug" value="<?php echo $row['url_slug']; ?>">
                    <input type="hidden" name="email" value="<?php echo $row['email']; ?>">
                    <input type="hidden" name="admission_email" value="<?php echo $row['admission_email']; ?>">
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
                <?php displayIfNotEmptyDate($row['updated_at']); ?>
            </span>
            <span class='ms-2'>
                <?php echo '<a href="' . $editFormUrl . '?id_' . $entityType . '=' . $row[$idEntityField] . '" class="edit-icon" style="color: red;"><i class="fa fa-pencil"></i></a>'; ?>
            </span>
        </div>
        <div class="d-flex align-items-center">
            <span class="me-1"><i class='fas fa-eye'></i></span>
            <?php echo $row['view_count']; ?>
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
    // Immediate execution - remove institution badge as soon as possible
    (function() {
        const removeInstitutionBadge = function() {
            const badges = document.querySelectorAll('div.institution-badge, .institution-badge');
            badges.forEach(el => el.remove());
        };
        
        // Try to remove immediately
        removeInstitutionBadge();
        
        // Try again after a short delay
        setTimeout(removeInstitutionBadge, 10);
        setTimeout(removeInstitutionBadge, 100);
        setTimeout(removeInstitutionBadge, 500);
    })();

    function <?= $deleteFunction ?>(id) {
        if (confirm('Are you sure you want to delete this <?= $entity_type ?>?')) {
            window.location.href = '/pages/dashboard/<?= $entity_type ?>-dashboard/<?= $entity_type ?>-delete/<?= $entity_type ?>-delete.php?id=' + id;
        }
    }
    
    // Remove any standalone ССУЗ or ВУЗ badges
    document.addEventListener('DOMContentLoaded', function() {
        // PRIORITY: Remove div.institution-badge specifically
        const institutionBadges = document.querySelectorAll('div.institution-badge, .institution-badge');
        institutionBadges.forEach(el => el.remove());
        
        // Method 1: Remove any badge elements containing ССУЗ or ВУЗ
        document.querySelectorAll('.badge, [class*="badge"], span, div').forEach(el => {
            const text = el.textContent.trim();
            if (text === 'ССУЗ' || text === 'ВУЗ' || text === 'ссуз' || text === 'вуз') {
                el.remove();
            }
        });
        
        // Method 2: Find and remove based on orange/yellow background
        document.querySelectorAll('*').forEach(el => {
            const styles = window.getComputedStyle(el);
            const bgColor = styles.backgroundColor;
            const text = el.textContent.trim();
            
            // Check for orange/yellow colors and ССУЗ/ВУЗ text
            if ((bgColor.includes('255, 193') || bgColor.includes('255, 165') || 
                 bgColor.includes('253, 126') || bgColor.includes('255, 243')) && 
                (text === 'ССУЗ' || text === 'ВУЗ')) {
                el.remove();
            }
        });
        
        // Method 3: Remove any element right before h1 that contains ССУЗ/ВУЗ
        const h1 = document.querySelector('h1.display-6');
        if (h1) {
            let prev = h1.previousElementSibling;
            while (prev) {
                const text = prev.textContent.trim();
                if (text === 'ССУЗ' || text === 'ВУЗ') {
                    const toRemove = prev;
                    prev = prev.previousElementSibling;
                    toRemove.remove();
                } else {
                    prev = prev.previousElementSibling;
                }
            }
        }
        
        // Method 4: Hide using CSS injection for any remaining badges
        const style = document.createElement('style');
        style.textContent = `
            .badge:not(:has(a)), 
            span.badge, 
            .text-bg-warning:not(:has(a)),
            [class*="badge"]:not(:has(a)) {
                display: none !important;
            }
        `;
        document.head.appendChild(style);
    });
</script>