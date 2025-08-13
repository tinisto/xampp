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

<style>
    /* Modern VPO/SPO Page Styling */
    .vpo-header {
        background: var(--bg-secondary);
        border-radius: 20px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        border: 1px solid var(--border-color);
    }
    
    .vpo-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        background: var(--gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        line-height: 1.2;
    }
    
    .vpo-meta {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
        color: var(--text-secondary);
        font-size: 0.9rem;
    }
    
    .vpo-meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .vpo-meta-item i {
        color: var(--accent-primary);
        font-size: 1rem;
    }
    
    .vpo-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .vpo-image {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        height: 250px;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        transition: all 0.3s;
    }
    
    .vpo-image:hover {
        transform: scale(1.02);
        border-color: var(--accent-primary);
    }
    
    .vpo-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .vpo-tabs {
        background: var(--bg-secondary);
        border-radius: 20px;
        padding: 1rem;
        margin-bottom: 2rem;
        border: 1px solid var(--border-color);
    }
    
    .nav-tabs {
        border: none;
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .nav-tabs .nav-link {
        background: transparent;
        border: none;
        color: var(--text-secondary);
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    .nav-tabs .nav-link:hover {
        background: var(--bg-tertiary);
        color: var(--text-primary);
    }
    
    .nav-tabs .nav-link.active {
        background: var(--gradient);
        color: white;
    }
    
    .tab-content {
        background: var(--bg-secondary);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        border: 1px solid var(--border-color);
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }
    
    .info-card {
        background: var(--bg-primary);
        padding: 1.5rem;
        border-radius: 16px;
        border: 1px solid var(--border-color);
    }
    
    .info-card h6 {
        color: var(--accent-primary);
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.75rem;
        font-weight: 600;
    }
    
    .info-card p {
        color: var(--text-primary);
        margin: 0;
        line-height: 1.6;
    }
    
    .contact-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }
    
    .contact-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--bg-primary);
        border-radius: 12px;
        border: 1px solid var(--border-color);
        transition: all 0.3s;
    }
    
    .contact-item:hover {
        border-color: var(--accent-primary);
        transform: translateX(4px);
    }
    
    .contact-icon {
        width: 40px;
        height: 40px;
        background: var(--gradient);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
    }
    
    .contact-text {
        flex: 1;
        overflow: hidden;
    }
    
    .contact-text small {
        color: var(--text-muted);
        font-size: 0.75rem;
    }
    
    .contact-text p {
        margin: 0;
        color: var(--text-primary);
        word-break: break-word;
    }
    
    .admin-panel {
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.3);
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .admin-panel button {
        background: var(--gradient);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .admin-panel button:hover {
        transform: scale(1.05);
    }
    
    @media (max-width: 768px) {
        .vpo-title {
            font-size: 2rem;
        }
        
        .vpo-header {
            padding: 1.5rem;
        }
        
        .tab-content {
            padding: 1.5rem;
        }
    }
</style>

<div class="container mt-4">
    <!-- Header Section -->
    <div class="vpo-header">
        <h1 class="vpo-title"><?php echo htmlspecialchars($pageTitle); ?></h1>
        
        <div class="vpo-meta">
            <?php if (!empty($row['region_name'])): ?>
                <div class="vpo-meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?php echo htmlspecialchars($row['region_name']); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($row['town_name'])): ?>
                <div class="vpo-meta-item">
                    <i class="fas fa-city"></i>
                    <span><?php echo htmlspecialchars($row['town_name']); ?></span>
                </div>
            <?php endif; ?>
            
            <div class="vpo-meta-item">
                <i class="fas fa-eye"></i>
                <span><?php echo number_format($row['view']); ?> просмотров</span>
            </div>
            
            <?php if (!empty($row['updated'])): ?>
                <div class="vpo-meta-item">
                    <i class="fas fa-clock"></i>
                    <span>Обновлено: <?php displayIfNotEmptyDate($row['updated']); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Admin Panel -->
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
        <div class="admin-panel">
            <div>ID: <?php echo $row[$idEntityField]; ?></div>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <form method="post" action='<?= $sendEmailsUrl ?>' target="_blank" style="margin: 0;">
                    <input type="hidden" name="<?= $urlField ?>" value="<?php echo $row[$urlField]; ?>">
                    <input type="hidden" name="email" value="<?php echo $row['email']; ?>">
                    <input type="hidden" name="email_pk" value="<?php echo $row['email_pk']; ?>">
                    <input type="hidden" name="director_email" value="<?php echo $row['director_email']; ?>">
                    <button type="submit" name="send_emails">Send Emails</button>
                </form>
                <a href="<?php echo $editFormUrl . '?id_' . $entityType . '=' . $row[$idEntityField]; ?>" style="color: var(--accent-primary);">
                    <i class="fas fa-edit"></i>
                </a>
                <i class="fas fa-trash" onclick="<?= $deleteFunction ?>('<?php echo $row[$idEntityField]; ?>')"
                   style="color: #ef4444; cursor: pointer;"></i>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Image Gallery -->
    <?php
    $hasImages = false;
    for ($i = 1; $i <= 3; $i++) {
        if (!empty($row["image_{$imagePrefix}_$i"])) {
            $hasImages = true;
            break;
        }
    }
    
    if ($hasImages): ?>
        <div class="vpo-gallery">
            <?php for ($i = 1; $i <= 3; $i++) : ?>
                <?php if (!empty($row["image_{$imagePrefix}_$i"])) : ?>
                    <div class="vpo-image">
                        <img src="/images/<?= $imagePrefix ?>-images/<?= htmlspecialchars($row["image_{$imagePrefix}_$i"]); ?>"
                             alt="<?php echo htmlspecialchars($pageTitle); ?> - Фото <?= $i ?>">
                    </div>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
    
    <!-- Tabs Navigation -->
    <div class="vpo-tabs">
        <ul class="nav nav-tabs" id="vpoTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">
                    <i class="fas fa-info-circle"></i> Информация
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="contacts-tab" data-bs-toggle="tab" data-bs-target="#contacts" type="button" role="tab">
                    <i class="fas fa-phone"></i> Контакты
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="leadership-tab" data-bs-toggle="tab" data-bs-target="#leadership" type="button" role="tab">
                    <i class="fas fa-user-tie"></i> Руководство
                </button>
            </li>
            <?php if (!empty($row['text'])): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">
                        <i class="fas fa-file-alt"></i> Описание
                    </button>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    
    <!-- Tab Content -->
    <div class="tab-content" id="vpoTabContent">
        <!-- Info Tab -->
        <div class="tab-pane fade show active" id="info" role="tabpanel">
            <div class="info-grid">
                <?php if (!empty($row['full_name'])): ?>
                    <div class="info-card">
                        <h6>Полное название</h6>
                        <p><?php echo htmlspecialchars($row['full_name']); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($row['address'])): ?>
                    <div class="info-card">
                        <h6>Адрес</h6>
                        <p><?php echo htmlspecialchars($row['address']); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($row['form_training'])): ?>
                    <div class="info-card">
                        <h6>Форма обучения</h6>
                        <p><?php echo htmlspecialchars($row['form_training']); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($row['type'])): ?>
                    <div class="info-card">
                        <h6>Тип учреждения</h6>
                        <p><?php echo htmlspecialchars($row['type']); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Contacts Tab -->
        <div class="tab-pane fade" id="contacts" role="tabpanel">
            <div class="contact-grid">
                <?php if (!empty($row['phone'])): ?>
                    <a href="tel:<?php echo htmlspecialchars($row['phone']); ?>" class="contact-item text-decoration-none">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-text">
                            <small>Телефон</small>
                            <p><?php echo htmlspecialchars($row['phone']); ?></p>
                        </div>
                    </a>
                <?php endif; ?>
                
                <?php if (!empty($row['phone_pk'])): ?>
                    <a href="tel:<?php echo htmlspecialchars($row['phone_pk']); ?>" class="contact-item text-decoration-none">
                        <div class="contact-icon">
                            <i class="fas fa-phone-square"></i>
                        </div>
                        <div class="contact-text">
                            <small>Приемная комиссия</small>
                            <p><?php echo htmlspecialchars($row['phone_pk']); ?></p>
                        </div>
                    </a>
                <?php endif; ?>
                
                <?php if (!empty($row['email'])): ?>
                    <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>" class="contact-item text-decoration-none">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-text">
                            <small>Email</small>
                            <p><?php echo htmlspecialchars($row['email']); ?></p>
                        </div>
                    </a>
                <?php endif; ?>
                
                <?php if (!empty($row['site'])): ?>
                    <a href="<?php echo htmlspecialchars($row['site']); ?>" target="_blank" class="contact-item text-decoration-none">
                        <div class="contact-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="contact-text">
                            <small>Веб-сайт</small>
                            <p><?php echo htmlspecialchars($row['site']); ?></p>
                        </div>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Leadership Tab -->
        <div class="tab-pane fade" id="leadership" role="tabpanel">
            <div class="info-grid">
                <?php if (!empty($row['director'])): ?>
                    <div class="info-card">
                        <h6>Директор</h6>
                        <p><?php echo htmlspecialchars($row['director']); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($row['director_email'])): ?>
                    <div class="info-card">
                        <h6>Email директора</h6>
                        <p><a href="mailto:<?php echo htmlspecialchars($row['director_email']); ?>" style="color: var(--accent-primary);">
                            <?php echo htmlspecialchars($row['director_email']); ?>
                        </a></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Description Tab -->
        <?php if (!empty($row['text'])): ?>
            <div class="tab-pane fade" id="description" role="tabpanel">
                <div style="color: var(--text-primary); line-height: 1.8;">
                    <?php echo nl2br(htmlspecialchars($row['text'])); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Comments Section -->
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
</div>

<script>
    function <?= $deleteFunction ?>(id) {
        if (confirm('Вы уверены, что хотите удалить это учреждение?')) {
            window.location.href = '/pages/dashboard/<?= $entity_type ?>-dashboard/<?= $entity_type ?>-delete/<?= $entity_type ?>-delete.php?id=' + id;
        }
    }
</script>