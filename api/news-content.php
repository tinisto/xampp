<?php
/**
 * Simple API endpoint for demonstrating lazy loading
 */

// Simulate network delay if requested
if (isset($_GET['delay'])) {
    sleep(intval($_GET['delay']));
}

$newsType = $_GET['type'] ?? '';

// Generate sample news content
$news = [
    ['title' => 'Изменения в ЕГЭ 2024: что нужно знать', 'date' => '02.08.2024', 'views' => 1543, 'desc' => 'Рособрнадзор опубликовал список изменений в контрольно-измерительных материалах ЕГЭ 2024 года.'],
    ['title' => 'Топ-10 вузов России по версии QS', 'date' => '01.08.2024', 'views' => 2891, 'desc' => 'Международный рейтинг QS World University Rankings опубликовал список лучших российских университетов.'],
    ['title' => 'Новые правила приема в колледжи', 'date' => '31.07.2024', 'views' => 967, 'desc' => 'Министерство просвещения утвердило новый порядок приема на обучение по программам СПО.'],
    ['title' => 'Олимпиады для школьников: календарь на год', 'date' => '30.07.2024', 'views' => 1234, 'desc' => 'Опубликован полный перечень олимпиад школьников и их уровней на 2024/25 учебный год.'],
];

// Output HTML content
header('Content-Type: text/html; charset=utf-8');
?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
    <?php foreach ($news as $item): ?>
        <div style="border: 1px solid var(--border-color, #e2e8f0); border-radius: 8px; padding: 20px; background: var(--surface, #ffffff); position: relative;">
            <?php if (empty($newsType)): ?>
            <div style="position: absolute; top: 15px; right: 15px; background: #28a745; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 500;">
                Новости
            </div>
            <?php endif; ?>
            
            <h2 style="margin: 0 0 10px 0; font-size: 18px; line-height: 1.3; <?= empty($newsType) ? 'padding-right: 80px;' : '' ?>">
                <a href="#" style="color: var(--text-primary, #333); text-decoration: none;">
                    <?= htmlspecialchars($item['title']) ?>
                </a>
            </h2>
            <div style="color: var(--text-secondary, #666); font-size: 12px; margin-bottom: 15px;">
                <i class="fas fa-calendar-alt" style="margin-right: 5px;"></i>
                <?= $item['date'] ?>
                <span style="margin-left: 10px;">
                    <i class="fas fa-eye" style="margin-right: 5px;"></i>
                    <?= number_format($item['views']) ?>
                </span>
            </div>
            <p style="color: var(--text-secondary, #666); margin: 0; font-size: 14px; line-height: 1.4;">
                <?= htmlspecialchars($item['desc']) ?>
            </p>
        </div>
    <?php endforeach; ?>
</div>

<div style="margin-top: 40px; display: flex; justify-content: center; align-items: center; gap: 10px;">
    <a href="#" style="padding: 8px 12px; border: 1px solid var(--border-color, #e2e8f0); border-radius: 4px; color: var(--text-primary, #333); text-decoration: none;">‹ Назад</a>
    <span style="padding: 8px 12px; background: var(--primary-color, #28a745); color: white; border-radius: 4px; font-weight: 500;">1</span>
    <a href="#" style="padding: 8px 12px; border: 1px solid var(--border-color, #e2e8f0); border-radius: 4px; color: var(--text-primary, #333); text-decoration: none;">2</a>
    <a href="#" style="padding: 8px 12px; border: 1px solid var(--border-color, #e2e8f0); border-radius: 4px; color: var(--text-primary, #333); text-decoration: none;">3</a>
    <a href="#" style="padding: 8px 12px; border: 1px solid var(--border-color, #e2e8f0); border-radius: 4px; color: var(--text-primary, #333); text-decoration: none;">Далее ›</a>
</div>