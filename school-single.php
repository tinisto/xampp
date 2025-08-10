<?php
// Modern School single page
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

// Get school slug from URL
$schoolSlug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (!$schoolSlug) {
    header('Location: /schools');
    exit;
}

// Fetch school data
$school = db_fetch_one("
    SELECT s.*
    FROM schools s
    WHERE s.url_slug = ?
", [$schoolSlug]);

if (!$school) {
    header('HTTP/1.0 404 Not Found');
    include $_SERVER['DOCUMENT_ROOT'] . '/404_modern.php';
    exit;
}

// Get similar schools (same region)
$similarSchools = db_fetch_all("
    SELECT id, name, url_slug, full_name
    FROM schools 
    WHERE region_id = ? AND id != ?
    ORDER BY RAND()
    LIMIT 4
", [$school['region_id'], $school['id']]);

// Prepare content for template
$pageTitle = $school['name'];

// Section 1: Title and main info
ob_start();
?>
<div style="padding: 50px 20px; margin: 0; background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%); color: white;">
    <div style="max-width: 1000px; margin: 0 auto;">
        <div style="display: flex; align-items: start; gap: 40px; flex-wrap: wrap;">
            <div style="width: 120px; height: 120px; background: white; border-radius: 20px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                <?php if ($school['logo']): ?>
                <img src="<?= htmlspecialchars($school['image_1']) ?>" alt="<?= htmlspecialchars($school['name']) ?>" 
                     style="max-width: 100px; max-height: 100px;">
                <?php else: ?>
                <i class="fas fa-graduation-cap" style="font-size: 48px; color: #f5576c;"></i>
                <?php endif; ?>
            </div>
            
            <div style="flex: 1;">
                <h1 style="font-size: 36px; font-weight: 700; margin-bottom: 20px; line-height: 1.2;">
                    <?= htmlspecialchars($school['name']) ?>
                </h1>
                
                <div style="display: flex; gap: 30px; flex-wrap: wrap; font-size: 16px; opacity: 0.9;">
                    <?php if ($school['site']): ?>
                    <a href="<?= htmlspecialchars($school['site']) ?>" target="_blank" 
                       style="color: white; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-globe"></i> Официальный сайт
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($school['tel']): ?>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-phone"></i> <?= htmlspecialchars($school['tel']) ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($school['email']): ?>
                    <a href="mailto:<?= htmlspecialchars($school['email']) ?>" 
                       style="color: white; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-envelope"></i> <?= htmlspecialchars($school['email']) ?>
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
            <a href="/schools" style="color: #666; text-decoration: none;">Школы</a>
            <span style="color: #999; margin: 0 10px;">›</span>
            <span style="color: #333;"><?= htmlspecialchars($school['name']) ?></span>
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
                <?php if ($school['full_name'] && $school['full_name'] != $school['name']): ?>
                <section style="margin-bottom: 40px;">
                    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #333;">О школе</h2>
                    <div style="font-size: 16px; line-height: 1.8; color: #666;">
                        <?= nl2br(htmlspecialchars($school['full_name'])) ?>
                    </div>
                </section>
                <?php endif; ?>
                
                <!-- Educational programs section -->
                <section style="margin-bottom: 40px;">
                    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #333;">Образовательные программы</h2>
                    <div style="display: grid; gap: 20px;">
                        <div style="background: #fff3e0; padding: 25px; border-radius: 12px;">
                            <h4 style="margin: 0 0 15px 0; color: #e65100;"><i class="fas fa-book"></i> Начальное образование (1-4 классы)</h4>
                            <p style="margin: 0; color: #666;">Основы грамотности, математики, окружающего мира. Развитие творческих способностей и социальных навыков.</p>
                        </div>
                        
                        <div style="background: #e8f5e9; padding: 25px; border-radius: 12px;">
                            <h4 style="margin: 0 0 15px 0; color: #2e7d32;"><i class="fas fa-microscope"></i> Основное общее образование (5-9 классы)</h4>
                            <p style="margin: 0; color: #666;">Углубленное изучение предметов, подготовка к ОГЭ, профориентация, проектная деятельность.</p>
                        </div>
                        
                        <div style="background: #e3f2fd; padding: 25px; border-radius: 12px;">
                            <h4 style="margin: 0 0 15px 0; color: #1976d2;"><i class="fas fa-user-graduate"></i> Среднее общее образование (10-11 классы)</h4>
                            <p style="margin: 0; color: #666;">Профильное обучение, подготовка к ЕГЭ, индивидуальные образовательные траектории.</p>
                        </div>
                    </div>
                </section>
                
                <!-- Special features -->
                <section style="margin-bottom: 40px;">
                    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #333;">Особенности школы</h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <?php
                        $features = [
                            ['icon' => 'language', 'title' => 'Углубленное изучение языков', 'color' => '#4caf50'],
                            ['icon' => 'laptop', 'title' => 'IT-классы', 'color' => '#2196f3'],
                            ['icon' => 'flask', 'title' => 'Научные лаборатории', 'color' => '#9c27b0'],
                            ['icon' => 'dumbbell', 'title' => 'Спортивные секции', 'color' => '#ff5722'],
                            ['icon' => 'palette', 'title' => 'Творческие студии', 'color' => '#ff9800'],
                            ['icon' => 'users', 'title' => 'Малые классы', 'color' => '#00bcd4'],
                        ];
                        foreach ($features as $feature):
                        ?>
                        <div style="background: #f8f9fa; padding: 20px; border-radius: 12px; text-align: center;">
                            <i class="fas fa-<?= $feature['icon'] ?>" style="font-size: 32px; color: <?= $feature['color'] ?>; margin-bottom: 10px;"></i>
                            <p style="margin: 0; font-weight: 600; color: #333;"><?= $feature['title'] ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                
                <!-- Admission info -->
                <section style="margin-bottom: 40px;">
                    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #333;">Прием в школу</h2>
                    <div style="background: #fce4ec; padding: 25px; border-radius: 12px;">
                        <h4 style="margin: 0 0 15px 0; color: #c2185b;">Документы для поступления в 1 класс:</h4>
                        <ul style="margin: 0; padding-left: 20px; color: #666;">
                            <li>Заявление родителей (законных представителей)</li>
                            <li>Свидетельство о рождении ребенка</li>
                            <li>Документ о регистрации ребенка по месту жительства</li>
                            <li>Медицинская карта (форма 026/у)</li>
                            <li>СНИЛС ребенка</li>
                            <li>Паспорт родителя (законного представителя)</li>
                        </ul>
                        <p style="margin: 20px 0 0 0; color: #666; font-style: italic;">
                            Прием заявлений в 1 класс начинается с 1 апреля для детей, проживающих на закрепленной территории.
                        </p>
                    </div>
                </section>
            </div>
            
            <!-- Right column - contact info -->
            <div>
                <div style="background: #f8f9fa; padding: 30px; border-radius: 12px; position: sticky; top: 20px;">
                    <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 20px; color: #333;">Контактная информация</h3>
                    
                    <?php if ($school['street']): ?>
                    <div style="margin-bottom: 20px;">
                        <h4 style="font-size: 16px; font-weight: 600; color: #666; margin-bottom: 8px;">
                            <i class="fas fa-map-marker-alt" style="color: #f5576c;"></i> Адрес
                        </h4>
                        <p style="margin: 0; color: #666;"><?= htmlspecialchars($school['street']) ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($school['tel']): ?>
                    <div style="margin-bottom: 20px;">
                        <h4 style="font-size: 16px; font-weight: 600; color: #666; margin-bottom: 8px;">
                            <i class="fas fa-phone" style="color: #f5576c;"></i> Телефон
                        </h4>
                        <p style="margin: 0;"><a href="tel:<?= htmlspecialchars($school['tel']) ?>" style="color: #f5576c; text-decoration: none;">
                            <?= htmlspecialchars($school['tel']) ?>
                        </a></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($school['email']): ?>
                    <div style="margin-bottom: 20px;">
                        <h4 style="font-size: 16px; font-weight: 600; color: #666; margin-bottom: 8px;">
                            <i class="fas fa-envelope" style="color: #f5576c;"></i> Email
                        </h4>
                        <p style="margin: 0;"><a href="mailto:<?= htmlspecialchars($school['email']) ?>" style="color: #f5576c; text-decoration: none;">
                            <?= htmlspecialchars($school['email']) ?>
                        </a></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($school['site']): ?>
                    <a href="<?= htmlspecialchars($school['site']) ?>" target="_blank"
                       style="display: block; background: #f5576c; color: white; text-align: center; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 500; margin-top: 20px;">
                        <i class="fas fa-external-link-alt"></i> Перейти на сайт
                    </a>
                    <?php endif; ?>
                    
                    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6;">
                        <h4 style="font-size: 16px; font-weight: 600; color: #666; margin-bottom: 15px;">
                            <i class="fas fa-clock" style="color: #f5576c;"></i> Режим работы
                        </h4>
                        <p style="margin: 0; color: #666; font-size: 14px;">
                            Пн-Пт: 8:00 - 19:00<br>
                            Сб: 8:00 - 15:00<br>
                            Вс: выходной
                        </p>
                    </div>
                    
                    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #dee2e6;">
                        <h4 style="font-size: 16px; font-weight: 600; color: #666; margin-bottom: 15px;">
                            <i class="fas fa-info-circle" style="color: #f5576c;"></i> Дополнительно
                        </h4>
                        <div style="display: grid; gap: 10px; font-size: 14px; color: #666;">
                            <div><i class="fas fa-check" style="color: #4caf50;"></i> Группа продленного дня</div>
                            <div><i class="fas fa-check" style="color: #4caf50;"></i> Школьная столовая</div>
                            <div><i class="fas fa-check" style="color: #4caf50;"></i> Медицинский кабинет</div>
                            <div><i class="fas fa-check" style="color: #4caf50;"></i> Охрана и видеонаблюдение</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();

// Section 4: Similar schools
ob_start();
if (!empty($similarSchools)):
?>
<div style="background: #f8f9fa; padding: 60px 20px; margin: 0;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <h2 style="font-size: 28px; font-weight: 700; margin-bottom: 40px; text-align: center;">Другие школы района</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
            <?php foreach ($similarSchools as $similar): ?>
            <article style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s;">
                <div style="height: 8px; background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);"></div>
                
                <div style="padding: 25px;">
                    <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 15px; line-height: 1.4;">
                        <a href="/school/<?= htmlspecialchars($similar['url_slug']) ?>" 
                           style="color: #333; text-decoration: none;">
                            <?= htmlspecialchars($similar['name']) ?>
                        </a>
                    </h3>
                    
                    <?php if ($similar['full_name'] && $similar['full_name'] != $similar['name']): ?>
                    <p style="color: #666; font-size: 14px; line-height: 1.6;">
                        <?= htmlspecialchars(mb_substr($similar['full_name'], 0, 100)) ?>...
                    </p>
                    <?php endif; ?>
                    
                    <a href="/school/<?= htmlspecialchars($similar['url_slug']) ?>" 
                       style="display: inline-flex; align-items: center; gap: 5px; margin-top: 15px; color: #f5576c; text-decoration: none; font-weight: 500;">
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
        <a href="/schools" 
           style="display: inline-block; background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%); color: white; padding: 15px 40px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 16px; transition: transform 0.3s;"
           onmouseover="this.style.transform='translateY(-2px)'"
           onmouseout="this.style.transform='translateY(0)'">
            <i class="fas fa-arrow-left"></i> Все школы
        </a>
    </div>
</div>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Empty
$greyContent6 = '';

// Include template
$blueContent = '';
include $_SERVER['DOCUMENT_ROOT'] . '/real_template_local.php';
?>