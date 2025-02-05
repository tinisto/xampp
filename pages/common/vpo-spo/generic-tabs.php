<?php

// Determine the type (vpo or spo) based on the URL
$requestUri = $_SERVER['REQUEST_URI'];
$type = strpos($requestUri, '/vpo/') !== false ? 'vpo' : 'spo';
$idField = $type === 'vpo' ? 'id_vpo' : 'id_spo';
$parentField = $type === 'vpo' ? 'parent_vpo_id' : 'parent_spo_id';
$filialsField = $type === 'vpo' ? 'filials_vpo' : 'filials_spo';
$newsTable = $type === 'vpo' ? 'vpo' : 'spo';
$parentLabel = $type === 'vpo' ? 'Головной ВУЗ' : 'Головной ССУЗ';
$filialsLabel = 'Филиалы';
?>

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link text-dark active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Главная</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link text-dark" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">Контакты</button>
    </li>
    <?php if (!empty($row['tel_pk']) || !empty($row['otvetcek']) || !empty($row['site_pk']) || !empty($row['email_pk'])): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-dark" id="priem-tab" data-bs-toggle="tab" data-bs-target="#priem-tab-pane" type="button" role="tab" aria-controls="priem-tab-pane" aria-selected="false">Приемная комиссия</button>
        </li>
    <?php endif; ?>
    <?php if (!empty($row['director_name'])): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-dark" id="director-tab" data-bs-toggle="tab" data-bs-target="#director-tab-pane" type="button" role="tab" aria-controls="director-tab-pane" aria-selected="false">Руководство</button>
        </li>
    <?php endif; ?>
    <?php if (!empty($row['history'])): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-dark" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-tab-pane" type="button" role="tab" aria-controls="history-tab-pane" aria-selected="false">История</button>
        </li>
    <?php endif; ?>
    <?php if (!empty($row[$parentField])): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-dark" id="parent-tab" data-bs-toggle="tab" data-bs-target="#parent-tab-pane" type="button" role="tab" aria-controls="parent-tab-pane" aria-selected="false"><?php echo $parentLabel; ?></button>
        </li>
    <?php endif; ?>
    <?php if (!empty($row[$filialsField])): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-dark" id="filials-tab" data-bs-toggle="tab" data-bs-target="#filials-tab-pane" type="button" role="tab" aria-controls="filials-tab-pane" aria-selected="false"><?php echo $filialsLabel; ?></button>
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
            <button class="nav-link text-dark" id="news-tab" data-bs-toggle="tab" data-bs-target="#news-tab-pane" type="button" role="tab" aria-controls="news-tab-pane" aria-selected="false">Новости</button>
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
            <p><?php displayIfNotEmpty('Прежние названия', nl2br($row['old_name'])); ?></p>
        </div>
    </div>
    <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">
        <div class="mt-3">
            <p><strong><i class="fa fa-map-marker-alt" style="color: #2179fd;"></i> Адрес:</strong>
                <?php if (isset($myrow_region['region_name']) && !empty($myrow_region['region_name']) && isset($myrow_town['name']) && isset($myrow_area['name'])): ?>
                    <?php echo getAddress($myrow_region, $myrow_area, $myrow_town, $row['street']); ?>
                <?php else: ?>
                    <?php echo $row['street']; ?>
                <?php endif; ?>
            </p>
            <p><?php displayIfNotEmptyWithIcon('fa fa-phone fa-flip-horizontal', 'Тел.', $row['tel'], '#2179fd'); ?></p>
            <p><?php displayIfNotEmptyWithIcon('fa fa-fax', 'Факс', $row['fax'], '#2179fd'); ?></p>
            <?php if (!empty($row['site'])): ?>
                <p><strong><i class="fa fa-globe" style="color: #2179fd;"></i> Сайт:</strong> <a href="<?php echo (stripos($row['site'], 'http') === false) ? 'http://' . $row['site'] : $row['site']; ?>" target="_blank" rel="nofollow"><?php echo $row['site']; ?></a></p>
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
            <p><?php displayIfNotEmptyWithIcon('fa fa-phone fa-flip-horizontal', 'Тел.', $row['tel_pk'], '#2179fd'); ?></p>
            <?php if (!empty($row['site_pk'])): ?>
                <p><strong><i class="fa fa-globe" style="color: #2179fd;"></i> Сайт:</strong> <a href="<?php echo (stripos($row['site_pk'], 'http') === false) ? 'http://' . $row['site_pk'] : $row['site_pk']; ?>" target="_blank" rel="nofollow"><?php echo $row['site_pk']; ?></a></p>
            <?php endif; ?>
            <?php if (!empty($row['email_pk'])): ?>
                <p><strong><i class="fa fa-envelope-o" style="color: #2179fd;"></i> Email:</strong> <a href="mailto:<?php echo $row['email_pk']; ?>"><?php echo $row['email_pk']; ?></a></p>
            <?php endif; ?>
            <?php if (!empty($row['address_pk'])): ?>
                <p><strong><i class="fa fa-map-marker-alt" style="color: #2179fd;"></i> Приёмная комиссия <?php echo $row['short_name']; ?> расположена по адресу:</strong> <?php echo $row['address_pk']; ?></p>
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
                    echo '<a href="/' . $newsTable . '/' . $childRow[$newsTable . '_url'] . '" target="_blank" class="link-custom">' . $childRow[$newsTable . '_name'] . '</a>';
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
                    $queryParent = "SELECT * FROM $newsTable WHERE $idField=? ORDER BY " . $newsTable . "_name";
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
                        echo '<a href="/' . $newsTable . '/' . $childRow[$newsTable . '_url'] . '" target="_blank" class="link-custom">' . $childRow[$newsTable . '_name'] . '</a>';
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