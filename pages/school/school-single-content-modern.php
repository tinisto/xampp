<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Ensure $additionalData is defined
if (isset($additionalData) && is_array($additionalData)) {
    $row = $additionalData['row'];
} else {
    $row = []; // Default to an empty array if $additionalData is not set
}

if (!include $_SERVER['DOCUMENT_ROOT'] . '/pages/school/school-single-functions.php') {
    header("Location: /error");
    exit();
}

?>

<style>
    /* Modern School Page Styling */
    .school-header {
        background: var(--bg-secondary);
        border-radius: 20px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        border: 1px solid var(--border-color);
    }
    
    .school-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        background: var(--gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        line-height: 1.2;
    }
    
    .school-meta {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
        color: var(--text-secondary);
        font-size: 0.9rem;
    }
    
    .school-meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .school-meta-item i {
        color: var(--accent-primary);
        font-size: 1rem;
    }
    
    .school-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .school-image {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        height: 250px;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        transition: all 0.3s;
    }
    
    .school-image:hover {
        transform: scale(1.02);
        border-color: var(--accent-primary);
    }
    
    .school-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .school-tabs {
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
    
    /* Modern Comment Section */
    .comments-section {
        background: var(--bg-secondary);
        border-radius: 20px;
        padding: 2rem;
        border: 1px solid var(--border-color);
    }
    
    .comments-header {
        margin-bottom: 2rem;
    }
    
    .comments-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }
    
    .comment-form {
        background: var(--bg-primary);
        padding: 1.5rem;
        border-radius: 16px;
        border: 1px solid var(--border-color);
        margin-bottom: 2rem;
    }
    
    .comment-form textarea {
        background: transparent;
        border: 1px solid var(--border-color);
        color: var(--text-primary);
        border-radius: 12px;
        padding: 1rem;
        width: 100%;
        min-height: 120px;
        resize: vertical;
        font-family: inherit;
    }
    
    .comment-form textarea:focus {
        outline: none;
        border-color: var(--accent-primary);
    }
    
    .comment-form button {
        background: var(--gradient);
        color: white;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 1rem;
    }
    
    .comment-form button:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .comment-item {
        background: transparent;
        padding: 1.5rem;
        border-radius: 16px;
        border: 1px solid var(--border-color);
        margin-bottom: 1rem;
    }
    
    .comment-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .comment-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        flex-shrink: 0;
    }
    
    .comment-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .comment-meta {
        flex: 1;
    }
    
    .comment-author {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }
    
    .comment-date {
        font-size: 0.875rem;
        color: var(--text-muted);
    }
    
    .comment-content {
        color: var(--text-primary);
        line-height: 1.6;
    }
    
    @media (max-width: 768px) {
        .school-title {
            font-size: 2rem;
        }
        
        .school-header {
            padding: 1.5rem;
        }
        
        .tab-content {
            padding: 1.5rem;
        }
        
        .comments-section {
            padding: 1.5rem;
        }
    }
</style>

<div class="container mt-4">
    <!-- Header Section -->
    <div class="school-header">
        <h1 class="school-title"><?php echo htmlspecialchars($pageTitle); ?></h1>
        
        <div class="school-meta">
            <?php if (!empty($row['region_name'])): ?>
                <div class="school-meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?php echo htmlspecialchars($row['region_name']); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($row['town_name'])): ?>
                <div class="school-meta-item">
                    <i class="fas fa-city"></i>
                    <span><?php echo htmlspecialchars($row['town_name']); ?></span>
                </div>
            <?php endif; ?>
            
            <div class="school-meta-item">
                <i class="fas fa-eye"></i>
                <span><?php echo number_format($row['view']); ?> просмотров</span>
            </div>
            
            <?php if (!empty($row['updated'])): ?>
                <div class="school-meta-item">
                    <i class="fas fa-clock"></i>
                    <span>Обновлено: <?php displayIfNotEmptyDate($row['updated']); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Admin Panel -->
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
        <div class="admin-panel">
            <div>ID: <?php echo $row['id_school']; ?></div>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <a href="/school-edit-form.php?id_school=<?php echo $row['id_school']; ?>" style="color: var(--accent-primary);">
                    <i class="fas fa-edit"></i>
                </a>
                <i class="fas fa-trash" onclick="deleteSchool('<?php echo $row['id_school']; ?>')"
                   style="color: #ef4444; cursor: pointer;"></i>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Image Gallery -->
    <?php
    $hasImages = false;
    for ($i = 1; $i <= 3; $i++) {
        if (!empty($row["image_school_$i"])) {
            $hasImages = true;
            break;
        }
    }
    
    if ($hasImages): ?>
        <div class="school-gallery">
            <?php for ($i = 1; $i <= 3; $i++) : ?>
                <?php if (!empty($row["image_school_$i"])) : ?>
                    <div class="school-image">
                        <img src="/images/school-images/<?= htmlspecialchars($row["image_school_$i"]); ?>"
                             alt="<?php echo htmlspecialchars($pageTitle); ?> - Фото <?= $i ?>">
                    </div>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
    
    <!-- Tabs Navigation -->
    <div class="school-tabs">
        <ul class="nav nav-tabs" id="schoolTabs" role="tablist">
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
            <?php if (!empty($row['text_school'])): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">
                        <i class="fas fa-file-alt"></i> Описание
                    </button>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    
    <!-- Tab Content -->
    <div class="tab-content" id="schoolTabContent">
        <!-- Info Tab -->
        <div class="tab-pane fade show active" id="info" role="tabpanel">
            <div class="info-grid">
                <?php if (!empty($row['full_name_school'])): ?>
                    <div class="info-card">
                        <h6>Полное название</h6>
                        <p><?php echo htmlspecialchars($row['full_name_school']); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($row['address_school'])): ?>
                    <div class="info-card">
                        <h6>Адрес</h6>
                        <p><?php echo htmlspecialchars($row['address_school']); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($row['type_school'])): ?>
                    <div class="info-card">
                        <h6>Тип школы</h6>
                        <p><?php echo htmlspecialchars($row['type_school']); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($row['number_students'])): ?>
                    <div class="info-card">
                        <h6>Количество учеников</h6>
                        <p><?php echo htmlspecialchars($row['number_students']); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Contacts Tab -->
        <div class="tab-pane fade" id="contacts" role="tabpanel">
            <div class="contact-grid">
                <?php if (!empty($row['phone_school'])): ?>
                    <a href="tel:<?php echo htmlspecialchars($row['phone_school']); ?>" class="contact-item text-decoration-none">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-text">
                            <small>Телефон</small>
                            <p><?php echo htmlspecialchars($row['phone_school']); ?></p>
                        </div>
                    </a>
                <?php endif; ?>
                
                <?php if (!empty($row['email_school'])): ?>
                    <a href="mailto:<?php echo htmlspecialchars($row['email_school']); ?>" class="contact-item text-decoration-none">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-text">
                            <small>Email</small>
                            <p><?php echo htmlspecialchars($row['email_school']); ?></p>
                        </div>
                    </a>
                <?php endif; ?>
                
                <?php if (!empty($row['site_school'])): ?>
                    <a href="<?php echo htmlspecialchars($row['site_school']); ?>" target="_blank" class="contact-item text-decoration-none">
                        <div class="contact-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="contact-text">
                            <small>Веб-сайт</small>
                            <p><?php echo htmlspecialchars($row['site_school']); ?></p>
                        </div>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Leadership Tab -->
        <div class="tab-pane fade" id="leadership" role="tabpanel">
            <div class="info-grid">
                <?php if (!empty($row['director_school'])): ?>
                    <div class="info-card">
                        <h6>Директор</h6>
                        <p><?php echo htmlspecialchars($row['director_school']); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($row['director_email_school'])): ?>
                    <div class="info-card">
                        <h6>Email директора</h6>
                        <p><a href="mailto:<?php echo htmlspecialchars($row['director_email_school']); ?>" style="color: var(--accent-primary);">
                            <?php echo htmlspecialchars($row['director_email_school']); ?>
                        </a></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Description Tab -->
        <?php if (!empty($row['text_school'])): ?>
            <div class="tab-pane fade" id="description" role="tabpanel">
                <div style="color: var(--text-primary); line-height: 1.8;">
                    <?php echo nl2br(htmlspecialchars($row['text_school'])); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Modern Comments Section -->
    <div class="comments-section">
        <div class="comments-header">
            <h3 class="comments-title">Комментарии</h3>
            <p style="color: var(--text-secondary);">Поделитесь своим мнением об этой школе</p>
        </div>
        
        <?php
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/getEntityIdFromURL.php';
        $result = getEntityIdFromURL($connection, 'school');
        $id_entity = $result['id_entity'];
        $entity_type = $result['entity_type'];
        
        // Check if the user is logged in
        if (isset($_SESSION['email']) && isset($_SESSION['avatar'])) {
            $user = $_SESSION['email'];
            $avatar = $_SESSION['avatar'];
        }
        
        require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/get_avatar.php";
        include $_SERVER['DOCUMENT_ROOT'] . '/comments/user_comments_modern.php';
        ?>
    </div>
</div>

<script>
    function deleteSchool(id) {
        if (confirm('Вы уверены, что хотите удалить эту школу?')) {
            window.location.href = '/pages/dashboard/school-dashboard/school-delete/school-delete.php?id=' + id;
        }
    }
</script>