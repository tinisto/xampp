<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link text-dark active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Главная</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link text-dark" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">Контакты</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link text-dark" id="director-tab" data-bs-toggle="tab" data-bs-target="#director-tab-pane" type="button" role="tab" aria-controls="director-tab-pane" aria-selected="false">Руководство</button>
    </li>
    <?php
    $queryNewsCount = "SELECT COUNT(*) as news_count FROM news WHERE id_school=?";
    $stmtCountNews = mysqli_prepare($connection, $queryNewsCount);
    mysqli_stmt_bind_param($stmtCountNews, "i", $row['id_school']);
    mysqli_stmt_execute($stmtCountNews);
    $resultCountNews = mysqli_stmt_get_result($stmtCountNews);
    $rowCountNews = mysqli_fetch_assoc($resultCountNews);

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
            <?php displayIfNotEmpty('Полное наименование', $row['full_name']); ?>
            <?php displayIfNotEmpty('Сокращенное наименование', $row['short_name']); ?>
        </div>
    </div>
    <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">
        <div class="mt-3">
            <p><strong><i class="fa fa-map-marker-alt" style="color: #2179fd;"></i> Адрес:</strong>
                <?php if (isset($myrow_region['region_name']) && !empty($myrow_region['region_name']) && isset($myrow_town['name']) && isset($myrow_area['name'])): ?>
                    <?php echo getAddress($myrow_region, $myrow_area, $myrow_town, $row['street']); ?>
                <?php endif; ?>
            </p>
            <?php displayIfNotEmpty('Тел.', $row['tel'], 'fa fa-phone fa-flip-horizontal', '#2179fd'); ?>
            <?php displayIfNotEmpty('Факс', $row['fax'], 'fa fa-fax', '#2179fd'); ?>
            <?php if (!empty($row['site'])): ?>
                <p><strong><i class="fa fa-globe" style="color: #2179fd;"></i> Сайт:</strong> <a href="<?php echo (stripos($row['site'], 'http') === false) ? 'http://' . $row['site'] : $row['site']; ?>" target="_blank" rel="nofollow"><?php echo $row['site']; ?></a></p>
            <?php endif; ?>
            <div class="d-flex">
                <?php displayIfNotEmpty('Email', $row['email'], 'fa fa-envelope', '#2179fd'); ?>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="director-tab-pane" role="tabpanel" aria-labelledby="director-tab" tabindex="0">
        <div class="mt-3">
            <p><strong><?php echo $row['director_role']; ?></strong> <?php echo $row['director_name']; ?>.</p>
            <?php if (!empty($row['director_info'])): ?>
                <p>Научные звания и награды руководителя:<br><?php echo nl2br($row['director_info']); ?></p>
            <?php endif; ?>
            <?php displayIfNotEmpty('Тел.', $row['director_phone'], 'fa fa-phone fa-flip-horizontal', '#2179fd'); ?>
            <?php displayIfNotEmpty('Email', $row['director_email'], 'fa fa-envelope-o', '#2179fd'); ?>
        </div>
    </div>
    <div class="tab-pane fade" id="history-tab-pane" role="tabpanel" aria-labelledby="history-tab" tabindex="0">
        <div class="mt-3">
            <p><?php echo nl2br($row['history']); ?></p>
        </div>
    </div>
    <div class="tab-pane fade" id="news-tab-pane" role="tabpanel" aria-labelledby="news-tab" tabindex="0">
        <div class="mt-3">
            <?php include 'school_news_content.php'; ?>
        </div>
    </div>
</div>
