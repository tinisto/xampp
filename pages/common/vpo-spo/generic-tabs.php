<?php

// Determine the type (vpo or spo) based on the URL
$requestUri = $_SERVER['REQUEST_URI'];
$type = strpos($requestUri, '/vpo/') !== false ? 'vpo' : 'spo';
$idField = 'id'; // Using 'id' for both universities and colleges
$parentField = $type === 'vpo' ? 'parent_university_id' : 'parent_college_id';
$filialsField = 'branch_ids'; // Using 'branch_ids' for both
$newsTable = $type === 'vpo' ? 'universities' : 'colleges';
$parentLabel = $type === 'vpo' ? 'Головной ВУЗ' : 'Головной ССУЗ';
$filialsLabel = 'Филиалы';
$nameField = $type === 'vpo' ? 'university_name' : 'college_name';
$urlField = 'url_slug';
?>

<style>
    /* Fix contact information text colors */
    .tab-pane {
        color: var(--text-primary, #333) !important;
    }
    
    .tab-pane p {
        color: var(--text-primary, #333) !important;
        margin-bottom: 0.75rem;
    }
    
    .tab-pane strong {
        color: var(--text-primary, #333) !important;
        font-weight: 600;
    }
    
    .tab-pane a {
        color: var(--primary-color, #007bff) !important;
        text-decoration: none;
    }
    
    .tab-pane a:hover {
        color: var(--primary-hover, #0056b3) !important;
        text-decoration: underline;
    }
    
    /* Dark mode support */
    [data-theme="dark"] .tab-pane,
    [data-theme="dark"] .tab-pane p,
    [data-theme="dark"] .tab-pane strong {
        color: var(--text-primary, #f9fafb) !important;
    }
    
    [data-theme="dark"] .tab-pane a {
        color: var(--primary-color, #60a5fa) !important;
    }
    
    [data-theme="dark"] .tab-pane a:hover {
        color: var(--primary-hover, #93bbfc) !important;
    }
    
    /* Fix nav tabs styling */
    .nav-tabs {
        border-bottom: 1px solid var(--border-color, #dee2e6);
    }
    
    .nav-tabs .nav-link {
        color: var(--text-primary, #333) !important;
        background: transparent;
        border: 1px solid transparent;
        padding: 0.5rem 1rem;
        margin-bottom: -1px;
    }
    
    .nav-tabs .nav-link:hover {
        border-color: var(--border-color, #e9ecef) var(--border-color, #e9ecef) transparent;
        background: var(--bg-secondary, #f8f9fa);
    }
    
    .nav-tabs .nav-link.active {
        color: var(--text-primary, #495057) !important;
        background-color: var(--bg-primary, #fff);
        border-color: var(--border-color, #dee2e6) var(--border-color, #dee2e6) var(--bg-primary, #fff);
    }
    
    /* Dark mode nav tabs */
    [data-theme="dark"] .nav-tabs {
        border-bottom-color: var(--border-color, #4a5568);
    }
    
    [data-theme="dark"] .nav-tabs .nav-link {
        color: var(--text-primary, #f9fafb) !important;
    }
    
    [data-theme="dark"] .nav-tabs .nav-link:hover {
        background: var(--bg-secondary, #374151);
        border-color: var(--border-color, #4a5568) var(--border-color, #4a5568) transparent;
    }
    
    [data-theme="dark"] .nav-tabs .nav-link.active {
        background-color: var(--bg-primary, #1f2937);
        border-color: var(--border-color, #4a5568) var(--border-color, #4a5568) var(--bg-primary, #1f2937);
    }
    
    /* Tab content background */
    .tab-content {
        background: var(--bg-primary, #fff);
        padding: 1rem;
        border: 1px solid var(--border-color, #dee2e6);
        border-top: none;
        border-radius: 0 0 0.25rem 0.25rem;
    }
    
    [data-theme="dark"] .tab-content {
        background: var(--bg-primary, #1f2937);
        border-color: var(--border-color, #4a5568);
    }
</style>

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Главная</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">Контакты</button>
    </li>
    <?php if (!empty($row['admission_phone']) || !empty($row['otvetcek']) || !empty($row['admission_website']) || !empty($row['admission_email'])): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="priem-tab" data-bs-toggle="tab" data-bs-target="#priem-tab-pane" type="button" role="tab" aria-controls="priem-tab-pane" aria-selected="false">Приемная комиссия</button>
        </li>
    <?php endif; ?>
    <?php if (!empty($row['director_name'])): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="director-tab" data-bs-toggle="tab" data-bs-target="#director-tab-pane" type="button" role="tab" aria-controls="director-tab-pane" aria-selected="false">Руководство</button>
        </li>
    <?php endif; ?>
    <?php if (!empty($row['history'])): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-tab-pane" type="button" role="tab" aria-controls="history-tab-pane" aria-selected="false">История</button>
        </li>
    <?php endif; ?>
    <?php if (!empty($row[$parentField])): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="parent-tab" data-bs-toggle="tab" data-bs-target="#parent-tab-pane" type="button" role="tab" aria-controls="parent-tab-pane" aria-selected="false"><?php echo $parentLabel; ?></button>
        </li>
    <?php endif; ?>
    <?php if (!empty($row[$filialsField])): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="filials-tab" data-bs-toggle="tab" data-bs-target="#filials-tab-pane" type="button" role="tab" aria-controls="filials-tab-pane" aria-selected="false"><?php echo $filialsLabel; ?></button>
        </li>
    <?php endif; ?>
    <?php
    $queryNewsCount = "SELECT COUNT(*) as news_count FROM news WHERE $idField=?";
    $stmtCountNews = mysqli_prepare($connection, $queryNewsCount);
    if (!$stmtCountNews) {
        header("Location: /error");
        exit();
    }
    mysqli_stmt_bind_param($stmtCountNews, "i", $row[$idField]);
    mysqli_stmt_execute($stmtCountNews);
    $resultCountNews = mysqli_stmt_get_result($stmtCountNews);
    if (!$resultCountNews) {
        header("Location: /error");
        exit();
    }
    $rowCountNews = mysqli_fetch_assoc($resultCountNews);

    // Check if there are news items
    if ($rowCountNews['news_count'] > 0) {
        echo '<li class="nav-item" role="presentation">
            <button class="nav-link" id="news-tab" data-bs-toggle="tab" data-bs-target="#news-tab-pane" type="button" role="tab" aria-controls="news-tab-pane" aria-selected="false">Новости</button>
          </li>';
    }
    mysqli_stmt_close($stmtCountNews);
    ?>
</ul>
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
        <div class="mt-3">
            <p><?php displayIfNotEmpty('Полное наименование', $row['full_name']); ?></p>
            <p><?php displayIfNotEmpty('Сокращенное наименование', $row['short_name']); ?></p>
            <p><?php displayIfNotEmpty('Прежние названия', nl2br($row['former_names'])); ?></p>
        </div>
    </div>
    <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">
        <div class="mt-3">
            <p><strong><i class="fa fa-map-marker-alt" style="color: #2179fd;"></i> Адрес:</strong>
                <?php if (isset($myrow_region['region_name']) && !empty($myrow_region['region_name']) && isset($myrow_town['name']) && isset($myrow_area['name'])): ?>
                    <?php echo getAddress($myrow_region, $myrow_area, $myrow_town, $row['address']); ?>
                <?php else: ?>
                    <?php echo $row['address']; ?>
                <?php endif; ?>
            </p>
            <p><?php displayIfNotEmptyWithIcon('fa fa-phone fa-flip-horizontal', 'Тел.', $row['phone'], '#2179fd'); ?></p>
            <p><?php displayIfNotEmptyWithIcon('fa fa-fax', 'Факс', $row['fax'], '#2179fd'); ?></p>
            <?php if (!empty($row['website'])): ?>
                <p><strong><i class="fa fa-globe" style="color: #2179fd;"></i> Сайт:</strong> <a href="<?php echo (stripos($row['website'], 'http') === false) ? 'http://' . $row['website'] : $row['website']; ?>" target="_blank" rel="nofollow"><?php echo $row['website']; ?></a></p>
            <?php endif; ?>
            <div class="d-flex">
                <?php if (!empty($row['email'])): ?>
                    <p><strong><i class="fa fa-envelope-o" style="color: #2179fd;"></i> Email:</strong> <a href="mailto:<?php echo $row['email']; ?>"><?php echo $row['email']; ?></a></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="priem-tab-pane" role="tabpanel" aria-labelledby="priem-tab" tabindex="0">
        <div class="mt-3">
            <p><?php displayIfNotEmptyWithIcon('fa fa-phone fa-flip-horizontal', 'Тел.', $row['admission_phone'], '#2179fd'); ?></p>
            <?php if (!empty($row['admission_website'])): ?>
                <p><strong><i class="fa fa-globe" style="color: #2179fd;"></i> Сайт:</strong> <a href="<?php echo (stripos($row['admission_website'], 'http') === false) ? 'http://' . $row['admission_website'] : $row['admission_website']; ?>" target="_blank" rel="nofollow"><?php echo $row['admission_website']; ?></a></p>
            <?php endif; ?>
            <?php if (!empty($row['admission_email'])): ?>
                <p><strong><i class="fa fa-envelope-o" style="color: #2179fd;"></i> Email:</strong> <a href="mailto:<?php echo $row['admission_email']; ?>"><?php echo $row['admission_email']; ?></a></p>
            <?php endif; ?>
            <?php if (!empty($row['admission_address'])): ?>
                <p><strong><i class="fa fa-map-marker-alt" style="color: #2179fd;"></i> Приёмная комиссия <?php echo $row['short_name']; ?> расположена по адресу:</strong> <?php echo $row['admission_address']; ?></p>
            <?php endif; ?>
        </div>
    </div>
    <div class="tab-pane fade" id="director-tab-pane" role="tabpanel" aria-labelledby="director-tab" tabindex="0">
        <div class="mt-3">
            <p><strong><?php echo $row['director_role']; ?></strong> <?php echo $row['director_name']; ?>.</p>
            <?php if (!empty($row['director_info'])): ?>
                <p>Научные звания и награды руководителя:<br><?php echo nl2br($row['director_info']); ?></p>
            <?php endif; ?>
            <p><?php displayIfNotEmptyWithIcon('fa fa-phone fa-flip-horizontal', 'Тел.', $row['director_phone'], '#2179fd'); ?></p>
            <p><?php displayIfNotEmptyWithIcon('fa fa-envelope-o', 'Email', $row['director_email'], '#2179fd'); ?></p>
        </div>
    </div>
    <div class="tab-pane fade" id="history-tab-pane" role="tabpanel" aria-labelledby="history-tab" tabindex="0">
        <div class="mt-3">
            <p><?php echo nl2br($row['history']); ?></p>
        </div>
    </div>
    <div class="tab-pane fade" id="parent-tab-pane" role="tabpanel" aria-labelledby="parent-tab" tabindex="0">
        <div class="mt-3">
            <p>
                <?php
                $queryParent = "SELECT * FROM $newsTable WHERE $idField=?";
                $stmtParent = mysqli_prepare($connection, $queryParent);
                if (!$stmtParent) {
                    header("Location: /error");
                    exit();
                }
                mysqli_stmt_bind_param($stmtParent, "i", $row[$parentField]);
                mysqli_stmt_execute($stmtParent);
                if (!$resultParent = mysqli_stmt_get_result($stmtParent)) {
                    header("Location: /error");
                    exit();
                }
                $childRow = mysqli_fetch_assoc($resultParent);
                if ($childRow !== null) {
                    echo '<a href="/' . $type . '/' . $childRow[$urlField] . '" target="_blank" class="link-custom">' . $childRow[$nameField] . '</a>';
                } else {
                    echo 'No data available.';
                }
                ?>
            </p>
        </div>
    </div>
    <div class="tab-pane fade" id="filials-tab-pane" role="tabpanel" aria-labelledby="filials-tab" tabindex="0">
        <div class="mt-3">
            <p>
                <?php
                $filialIDs = explode(',', $row[$filialsField]);
                foreach ($filialIDs as $filialID) {
                    $queryParent = "SELECT * FROM $newsTable WHERE $idField=? ORDER BY " . $nameField;
                    $stmtParent = mysqli_prepare($connection, $queryParent);
                    if (!$stmtParent) {
                        header("Location: /error");
                        exit();
                    }
                    mysqli_stmt_bind_param($stmtParent, "i", $filialID);
                    mysqli_stmt_execute($stmtParent);
                    $resultParent = mysqli_stmt_get_result($stmtParent);
                    $childRow = mysqli_fetch_assoc($resultParent);
                    echo '<p>';
                    if ($childRow !== null) {
                        echo '<a href="/' . $type . '/' . $childRow[$urlField] . '" target="_blank" class="link-custom">' . $childRow[$nameField] . '</a>';
                    } else {
                        echo 'No data available.';
                    }
                    echo '</p>';
                }
                ?>
            </p>
        </div>
    </div>
    <div class="tab-pane fade" id="news-tab-pane" role="tabpanel" aria-labelledby="news-tab" tabindex="0">
        <div class="mt-3">
            <?php


            require_once $_SERVER['DOCUMENT_ROOT'] . '/pages/common/vpo-spo/fetchNewsContent.php';

            // Determine the entity type and ID
            $entityType = $type === 'vpo' ? 'vpo' : 'spo';
            $entityId = $row[$idField];

            // Fetch and display news content
            fetchNewsContent($connection, $entityType, $entityId);
            ?>
        </div>
    </div>
</div>