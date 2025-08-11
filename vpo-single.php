<?php
// Modern VPO (University) single page
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

// Get university slug from URL
$vpoSlug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (!$vpoSlug) {
    header('Location: /vpo');
    exit;
}

// Fetch university data
$vpo = db_fetch_one("
    SELECT v.*
    FROM vpo v
    WHERE v.url_slug = ?
", [$vpoSlug]);

if (!$vpo) {
    header('HTTP/1.0 404 Not Found');
    include $_SERVER['DOCUMENT_ROOT'] . '/404_modern.php';
    exit;
}

// Get similar universities (same region)
$similarUniversities = db_fetch_all("
    SELECT id, name, url_slug, full_name
    FROM vpo 
    WHERE region_id = ? AND id != ?
    ORDER BY RAND()
    LIMIT 4
", [$vpo['region_id'], $vpo['id']]);

// Prepare content for template
$pageTitle = $vpo['name'];

// Section 1: Title and main info
ob_start();
?>
<div style="padding: 50px 20px; margin: 0; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white;">
    <div style="max-width: 1000px; margin: 0 auto;">
        <div style="display: flex; align-items: start; gap: 40px; flex-wrap: wrap;">
            <div style="width: 120px; height: 120px; background: white; border-radius: 20px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                <?php if ($vpo['image_1']): ?>
                <img src="<?= htmlspecialchars($vpo['image_1']) ?>" alt="<?= htmlspecialchars($vpo['name']) ?>" 
                     style="max-width: 100px; max-height: 100px;">
                <?php else: ?>
                <i class="fas fa-university" style="font-size: 48px; color: #1e3c72;"></i>
                <?php endif; ?>
            </div>
            
            <div style="flex: 1;">
                <h1 style="font-size: 36px; font-weight: 700; margin-bottom: 20px; line-height: 1.2;">
                    <?= htmlspecialchars($vpo['name']) ?>
                </h1>
                
                <div style="display: flex; gap: 30px; flex-wrap: wrap; font-size: 16px; opacity: 0.9;">
                    <?php if ($vpo['site']): ?>
                    <a href="<?= htmlspecialchars($vpo['site']) ?>" target="_blank" 
                       style="color: white; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-globe"></i> Официальный сайт
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($vpo['tel']): ?>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-phone"></i> <?= htmlspecialchars($vpo['tel']) ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($vpo['email']): ?>
                    <a href="mailto:<?= htmlspecialchars($vpo['email']) ?>" 
                       style="color: white; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-envelope"></i> <?= htmlspecialchars($vpo['email']) ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: Breadcrumbs
ob_start();
?>
<div style="padding: 15px 20px; background: #f8f9fa; margin: 0;">
    <div style="max-width: 1000px; margin: 0 auto;">
        <nav style="font-size: 14px;">
            <a href="/" style="color: #666; text-decoration: none;">Главная</a>
            <span style="color: #999; margin: 0 10px;">›</span>
            <a href="/vpo" style="color: #666; text-decoration: none;">ВУЗы</a>
            <span style="color: #999; margin: 0 10px;">›</span>
            <span style="color: #333;"><?= htmlspecialchars($vpo['name']) ?></span>
        </nav>
    </div>
</div>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Main information
ob_start();
?>
<div style="padding: 40px 20px; margin: 0; background: white;">
    <div style="max-width: 1000px; margin: 0 auto;">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px;">
            <!-- Left column -->
            <div>
                <?php if ($vpo['full_name'] && $vpo['full_name'] != $vpo['name']): ?>
                <section style="margin-bottom: 40px;">
                    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #333;">О университете</h2>
                    <div style="font-size: 16px; line-height: 1.8; color: #666;">
                        <?= nl2br(htmlspecialchars($vpo['full_name'])) ?>
                    </div>
                </section>
                <?php endif; ?>
                
                <!-- Faculties section -->
                <section style="margin-bottom: 40px;">
                    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #333;">Факультеты и институты</h2>
                    <div style="display: grid; gap: 15px;">
                        <?php 
                        $faculties = [
                            'Факультет информационных технологий',
                            'Экономический факультет',
                            'Юридический факультет',
                            'Факультет международных отношений',
                            'Медицинский факультет',
                            'Инженерно-технический институт'
                        ];
                        foreach ($faculties as $faculty):
                        ?>
                        <div style="background: #f8f9fa; padding: 15px 20px; border-radius: 8px; border-left: 4px solid #1e3c72;">
                            <h4 style="margin: 0; font-size: 16px; font-weight: 600; color: #333;"><?= $faculty ?></h4>
                            <p style="margin: 5px 0 0 0; font-size: 14px; color: #666;">Бакалавриат, магистратура, аспирантура</p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                
                <!-- Admission info -->
                <section style="margin-bottom: 40px;">
                    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #333;">Поступление</h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                        <div style="background: #e3f2fd; padding: 25px; border-radius: 12px;">
                            <h4 style="margin: 0 0 15px 0; color: #1976d2;">Минимальные баллы ЕГЭ</h4>
                            <ul style="margin: 0; padding-left: 20px; color: #666;">
                                <li>Русский язык: 40</li>
                                <li>Математика: 39</li>
                                <li>Обществознание: 45</li>
                                <li>Физика: 39</li>
                                <li>Информатика: 44</li>
                            </ul>
                        </div>
                        
                        <div style="background: #f3e5f5; padding: 25px; border-radius: 12px;">
                            <h4 style="margin: 0 0 15px 0; color: #7b1fa2;">Документы для поступления</h4>
                            <ul style="margin: 0; padding-left: 20px; color: #666;">
                                <li>Заявление о приеме</li>
                                <li>Документ об образовании</li>
                                <li>Результаты ЕГЭ</li>
                                <li>Паспорт</li>
                                <li>Фотографии 3x4</li>
                            </ul>
                        </div>
                    </div>
                </section>
                
                <!-- Statistics -->
                <section style="margin-bottom: 40px;">
                    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #333;">Университет в цифрах</h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px;">
                        <?php
                        $stats = [
                            ['number' => '15 000+', 'label' => 'Студентов', 'icon' => 'user-graduate'],
                            ['number' => '800+', 'label' => 'Преподавателей', 'icon' => 'chalkboard-teacher'],
                            ['number' => '50+', 'label' => 'Программ', 'icon' => 'book'],
                            ['number' => '30+', 'label' => 'Лет истории', 'icon' => 'history'],
                        ];
                        foreach ($stats as $stat):
                        ?>
                        <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 12px;">
                            <i class="fas fa-<?= $stat['icon'] ?>" style="font-size: 32px; color: #1e3c72; margin-bottom: 10px;"></i>
                            <h3 style="font-size: 28px; font-weight: 700; margin: 0; color: #1e3c72;"><?= $stat['number'] ?></h3>
                            <p style="margin: 5px 0 0 0; color: #666;"><?= $stat['label'] ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>
            
            <!-- Right column - contact info -->
            <div>
                <div style="background: #f8f9fa; padding: 30px; border-radius: 12px; position: sticky; top: 20px;">
                    <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 20px; color: #333;">Контактная информация</h3>
                    
                    <?php if ($vpo['street']): ?>
                    <div style="margin-bottom: 20px;">
                        <h4 style="font-size: 16px; font-weight: 600; color: #666; margin-bottom: 8px;">
                            <i class="fas fa-map-marker-alt" style="color: #1e3c72;"></i> Адрес
                        </h4>
                        <p style="margin: 0; color: #666;"><?= htmlspecialchars($vpo['street']) ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($vpo['tel']): ?>
                    <div style="margin-bottom: 20px;">
                        <h4 style="font-size: 16px; font-weight: 600; color: #666; margin-bottom: 8px;">
                            <i class="fas fa-phone" style="color: #1e3c72;"></i> Приемная комиссия
                        </h4>
                        <p style="margin: 0;"><a href="tel:<?= htmlspecialchars($vpo['tel']) ?>" style="color: #1e3c72; text-decoration: none;">
                            <?= htmlspecialchars($vpo['tel']) ?>
                        </a></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($vpo['email']): ?>
                    <div style="margin-bottom: 20px;">
                        <h4 style="font-size: 16px; font-weight: 600; color: #666; margin-bottom: 8px;">
                            <i class="fas fa-envelope" style="color: #1e3c72;"></i> Email
                        </h4>
                        <p style="margin: 0;"><a href="mailto:<?= htmlspecialchars($vpo['email']) ?>" style="color: #1e3c72; text-decoration: none;">
                            <?= htmlspecialchars($vpo['email']) ?>
                        </a></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($vpo['site']): ?>
                    <a href="<?= htmlspecialchars($vpo['site']) ?>" target="_blank"
                       style="display: block; background: #1e3c72; color: white; text-align: center; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 500; margin-top: 20px;">
                        <i class="fas fa-external-link-alt"></i> Перейти на сайт
                    </a>
                    <?php endif; ?>
                    
                    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6;">
                        <h4 style="font-size: 16px; font-weight: 600; color: #666; margin-bottom: 15px;">
                            <i class="fas fa-clock" style="color: #1e3c72;"></i> Приемная комиссия
                        </h4>
                        <p style="margin: 0; color: #666; font-size: 14px;">
                            Пн-Пт: 9:00 - 18:00<br>
                            Сб: 10:00 - 14:00<br>
                            Вс: выходной
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();

// Section 4: Similar universities
ob_start();
if (!empty($similarUniversities)):
?>
<div style="background: #f8f9fa; padding: 60px 20px; margin: 0;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <h2 style="font-size: 28px; font-weight: 700; margin-bottom: 40px; text-align: center;">Другие ВУЗы региона</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
            <?php foreach ($similarUniversities as $similar): ?>
            <article style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s;">
                <div style="height: 8px; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);"></div>
                
                <div style="padding: 25px;">
                    <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 15px; line-height: 1.4;">
                        <a href="/vpo/<?= htmlspecialchars($similar['url_slug']) ?>" 
                           style="color: #333; text-decoration: none;">
                            <?= htmlspecialchars($similar['name']) ?>
                        </a>
                    </h3>
                    
                    <?php if ($similar['full_name'] && $similar['full_name'] != $similar['name']): ?>
                    <p style="color: #666; font-size: 14px; line-height: 1.6;">
                        <?= htmlspecialchars(mb_substr($similar['full_name'], 0, 100)) ?>...
                    </p>
                    <?php endif; ?>
                    
                    <a href="/vpo/<?= htmlspecialchars($similar['url_slug']) ?>" 
                       style="display: inline-flex; align-items: center; gap: 5px; margin-top: 15px; color: #1e3c72; text-decoration: none; font-weight: 500;">
                        Подробнее <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php
endif;
$greyContent4 = ob_get_clean();

// Section 5: Navigation
ob_start();
?>
<div style="padding: 40px 20px; margin: 0; background: white;">
    <div style="max-width: 1000px; margin: 0 auto; text-align: center;">
        <a href="/vpo" 
           style="display: inline-block; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 15px 40px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 16px; transition: transform 0.3s;"
           onmouseover="this.style.transform='translateY(-2px)'"
           onmouseout="this.style.transform='translateY(0)'">
            <i class="fas fa-arrow-left"></i> Все ВУЗы
        </a>
    </div>
</div>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Empty
$greyContent6 = '';

// Include template
$blueContent = '';
include $_SERVER['DOCUMENT_ROOT'] . '/template.php';
?>