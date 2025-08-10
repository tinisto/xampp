<?php
// Modern SPO (College) single page
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

// Get college slug from URL
$spoSlug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (!$spoSlug) {
    header('Location: /spo');
    exit;
}

// Fetch college data
$spo = db_fetch_one("
    SELECT s.*
    FROM spo s
    WHERE s.url_slug = ?
", [$spoSlug]);

if (!$spo) {
    header('HTTP/1.0 404 Not Found');
    include $_SERVER['DOCUMENT_ROOT'] . '/404_modern.php';
    exit;
}

// Get similar colleges (same region)
$similarColleges = db_fetch_all("
    SELECT id, name, url_slug, full_name
    FROM spo 
    WHERE region_id = ? AND id != ?
    ORDER BY RAND()
    LIMIT 4
", [$spo['region_id'], $spo['id']]);

// Prepare content for template
$pageTitle = $spo['name'];

// Section 1: Title and main info
ob_start();
?>
<div style="padding: 50px 20px; margin: 0; background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%); color: white;">
    <div style="max-width: 1000px; margin: 0 auto;">
        <div style="display: flex; align-items: start; gap: 40px; flex-wrap: wrap;">
            <div style="width: 120px; height: 120px; background: white; border-radius: 20px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                <?php if ($spo['image_1']): ?>
                <img src="<?= htmlspecialchars($spo['image_1']) ?>" alt="<?= htmlspecialchars($spo['name']) ?>" 
                     style="max-width: 100px; max-height: 100px;">
                <?php else: ?>
                <i class="fas fa-school" style="font-size: 48px; color: #00b09b;"></i>
                <?php endif; ?>
            </div>
            
            <div style="flex: 1;">
                <h1 style="font-size: 36px; font-weight: 700; margin-bottom: 20px; line-height: 1.2;">
                    <?= htmlspecialchars($spo['name']) ?>
                </h1>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                <?php 
                $isFavorite = db_fetch_one("
                    SELECT id FROM favorites 
                    WHERE user_id = ? AND item_type = 'spo' AND item_id = ?
                ", [$_SESSION['user_id'], $spo['id_college']]);
                ?>
                <button onclick="toggleFavorite('spo', <?= $spo['id_college'] ?>)" 
                        style="margin-top: 10px; padding: 10px 20px; background: rgba(255,255,255,0.2); 
                               color: white; border: 2px solid white; border-radius: 8px; 
                               cursor: pointer; font-weight: 600; transition: all 0.2s;"
                        onmouseover="this.style.background='rgba(255,255,255,0.3)'"
                        onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                    <i class="<?= $isFavorite ? 'fas' : 'far' ?> fa-heart"></i> 
                    <?= $isFavorite ? 'В избранном' : 'Добавить в избранное' ?>
                </button>
                <?php endif; ?>
                
                <div style="display: flex; gap: 30px; flex-wrap: wrap; font-size: 16px; opacity: 0.9;">
                    <?php if ($spo['site']): ?>
                    <a href="<?= htmlspecialchars($spo['site']) ?>" target="_blank" 
                       style="color: white; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-globe"></i> Официальный сайт
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($spo['tel']): ?>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-phone"></i> <?= htmlspecialchars($spo['tel']) ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($spo['email']): ?>
                    <a href="mailto:<?= htmlspecialchars($spo['email']) ?>" 
                       style="color: white; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-envelope"></i> <?= htmlspecialchars($spo['email']) ?>
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
            <a href="/spo" style="color: #666; text-decoration: none;">Колледжи</a>
            <span style="color: #999; margin: 0 10px;">›</span>
            <span style="color: #333;"><?= htmlspecialchars($spo['name']) ?></span>
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
                <?php if ($spo['full_name'] && $spo['full_name'] != $spo['name']): ?>
                <section style="margin-bottom: 40px;">
                    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #333;">О колледже</h2>
                    <div style="font-size: 16px; line-height: 1.8; color: #666;">
                        <?= nl2br(htmlspecialchars($spo['full_name'])) ?>
                    </div>
                </section>
                <?php endif; ?>
                
                <!-- Programs section -->
                <section style="margin-bottom: 40px;">
                    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #333;">Направления подготовки</h2>
                    <div style="display: grid; gap: 15px;">
                        <?php 
                        $programs = [
                            'Информационные системы и программирование',
                            'Экономика и бухгалтерский учет',
                            'Туризм и гостиничный сервис',
                            'Дизайн и реклама',
                            'Строительство и архитектура'
                        ];
                        foreach ($programs as $program):
                        ?>
                        <div style="background: #f8f9fa; padding: 15px 20px; border-radius: 8px; border-left: 4px solid #00b09b;">
                            <h4 style="margin: 0; font-size: 16px; font-weight: 600; color: #333;"><?= $program ?></h4>
                            <p style="margin: 5px 0 0 0; font-size: 14px; color: #666;">Срок обучения: 2-4 года</p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                
                <!-- Admission info -->
                <section style="margin-bottom: 40px;">
                    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #333;">Поступление</h2>
                    <div style="background: #e8f5e9; padding: 25px; border-radius: 12px;">
                        <h4 style="margin: 0 0 15px 0; color: #2e7d32;">Документы для поступления:</h4>
                        <ul style="margin: 0; padding-left: 20px; color: #666;">
                            <li>Заявление о приеме</li>
                            <li>Аттестат об основном общем или среднем общем образовании</li>
                            <li>Фотографии 3x4 (4 шт.)</li>
                            <li>Медицинская справка (форма 086/у)</li>
                            <li>Паспорт (копия)</li>
                        </ul>
                    </div>
                </section>
            </div>
            
            <!-- Right column - contact info -->
            <div>
                <div style="background: #f8f9fa; padding: 30px; border-radius: 12px; position: sticky; top: 20px;">
                    <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 20px; color: #333;">Контактная информация</h3>
                    
                    <?php if ($spo['street']): ?>
                    <div style="margin-bottom: 20px;">
                        <h4 style="font-size: 16px; font-weight: 600; color: #666; margin-bottom: 8px;">
                            <i class="fas fa-map-marker-alt" style="color: #00b09b;"></i> Адрес
                        </h4>
                        <p style="margin: 0; color: #666;"><?= htmlspecialchars($spo['street']) ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($spo['tel']): ?>
                    <div style="margin-bottom: 20px;">
                        <h4 style="font-size: 16px; font-weight: 600; color: #666; margin-bottom: 8px;">
                            <i class="fas fa-phone" style="color: #00b09b;"></i> Телефон
                        </h4>
                        <p style="margin: 0;"><a href="tel:<?= htmlspecialchars($spo['tel']) ?>" style="color: #00b09b; text-decoration: none;">
                            <?= htmlspecialchars($spo['tel']) ?>
                        </a></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($spo['email']): ?>
                    <div style="margin-bottom: 20px;">
                        <h4 style="font-size: 16px; font-weight: 600; color: #666; margin-bottom: 8px;">
                            <i class="fas fa-envelope" style="color: #00b09b;"></i> Email
                        </h4>
                        <p style="margin: 0;"><a href="mailto:<?= htmlspecialchars($spo['email']) ?>" style="color: #00b09b; text-decoration: none;">
                            <?= htmlspecialchars($spo['email']) ?>
                        </a></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($spo['site']): ?>
                    <a href="<?= htmlspecialchars($spo['site']) ?>" target="_blank"
                       style="display: block; background: #00b09b; color: white; text-align: center; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 500; margin-top: 20px;">
                        <i class="fas fa-external-link-alt"></i> Перейти на сайт
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();

// Section 4: Similar colleges
ob_start();
if (!empty($similarColleges)):
?>
<div style="background: #f8f9fa; padding: 60px 20px; margin: 0;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <h2 style="font-size: 28px; font-weight: 700; margin-bottom: 40px; text-align: center;">Другие колледжи региона</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
            <?php foreach ($similarColleges as $similar): ?>
            <article style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s;">
                <div style="height: 8px; background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%);"></div>
                
                <div style="padding: 25px;">
                    <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 15px; line-height: 1.4;">
                        <a href="/spo/<?= htmlspecialchars($similar['url_slug']) ?>" 
                           style="color: #333; text-decoration: none;">
                            <?= htmlspecialchars($similar['name']) ?>
                        </a>
                    </h3>
                    
                    <?php if ($similar['full_name'] && $similar['full_name'] != $similar['name']): ?>
                    <p style="color: #666; font-size: 14px; line-height: 1.6;">
                        <?= htmlspecialchars(mb_substr($similar['full_name'], 0, 100)) ?>...
                    </p>
                    <?php endif; ?>
                    
                    <a href="/spo/<?= htmlspecialchars($similar['url_slug']) ?>" 
                       style="display: inline-flex; align-items: center; gap: 5px; margin-top: 15px; color: #00b09b; text-decoration: none; font-weight: 500;">
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
        <a href="/spo" 
           style="display: inline-block; background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%); color: white; padding: 15px 40px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 16px; transition: transform 0.3s;"
           onmouseover="this.style.transform='translateY(-2px)'"
           onmouseout="this.style.transform='translateY(0)'">
            <i class="fas fa-arrow-left"></i> Все колледжи
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

<script>
function toggleFavorite(type, id) {
    // Here you would make an AJAX call to toggle the favorite
    // For now, just reload the page after the action
    fetch('/api/favorites/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ type: type, id: id })
    }).then(() => {
        location.reload();
    }).catch(() => {
        alert('Для добавления в избранное необходимо войти в систему');
    });
}
</script>