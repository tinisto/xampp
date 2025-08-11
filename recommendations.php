<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/recommendations.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// Get recommendations
$limit = 20;
$recommendations = RecommendationEngine::getRecommendations($_SESSION['user_id'], $limit);

// Page title
$pageTitle = 'Персональные рекомендации';

// Section 1: Header
ob_start();
?>
<div style="background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%); padding: 60px 20px; color: white; text-align: center;">
    <h1 style="font-size: 36px; font-weight: 700; margin-bottom: 10px;">
        <i class="fas fa-lightbulb"></i> Персональные рекомендации
    </h1>
    <p style="font-size: 18px; opacity: 0.9;">Материалы, подобранные специально для вас</p>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: Recommendations
ob_start();
?>
<div style="padding: 40px 20px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <?php if (empty($recommendations)): ?>
        <div style="text-align: center; padding: 60px 20px; background: var(--bg-secondary); 
                    border-radius: 12px; border: 2px dashed var(--border-color);">
            <i class="fas fa-robot" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
            <h3 style="color: var(--text-secondary); margin-bottom: 15px;">Рекомендации формируются</h3>
            <p style="color: var(--text-secondary); margin-bottom: 25px;">
                Читайте статьи, ставьте оценки и добавляйте материалы в избранное, 
                чтобы мы могли подобрать для вас персональные рекомендации
            </p>
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <a href="/news" 
                   style="display: inline-block; background: #007bff; color: white; 
                          text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 600;">
                    <i class="fas fa-newspaper"></i> Читать новости
                </a>
                <a href="/posts" 
                   style="display: inline-block; background: #28a745; color: white; 
                          text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 600;">
                    <i class="fas fa-book"></i> Читать статьи
                </a>
            </div>
        </div>
        <?php else: ?>
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <p style="color: var(--text-secondary); margin: 0;">
                Найдено <?= count($recommendations) ?> рекомендаций на основе ваших предпочтений
            </p>
            <div style="display: flex; gap: 10px;">
                <span style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 8px; 
                             background: var(--bg-secondary); border-radius: 15px; font-size: 14px; color: var(--text-secondary);">
                    <i class="fas fa-brain" style="color: #ffc107;"></i>
                    Персональные
                </span>
                <span style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 8px; 
                             background: var(--bg-secondary); border-radius: 15px; font-size: 14px; color: var(--text-secondary);">
                    <i class="fas fa-fire" style="color: #dc3545;"></i>
                    Популярные
                </span>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px;">
            <?php foreach ($recommendations as $item): ?>
            <div style="background: var(--bg-primary); border: 1px solid var(--border-color); 
                        border-radius: 12px; padding: 25px; transition: all 0.3s ease;" 
                 onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 25px var(--shadow)'" 
                 onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                
                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex-shrink: 0;">
                        <?php
                        $icon = $item['type'] === 'news' ? 'fa-newspaper' : 'fa-book';
                        $color = $item['type'] === 'news' ? '#007bff' : '#28a745';
                        $link = $item['type'] === 'news' ? "/news/{$item['url']}" : "/post/{$item['url']}";
                        $typeLabel = $item['type'] === 'news' ? 'Новость' : 'Статья';
                        ?>
                        <i class="fas <?= $icon ?>" style="color: <?= $color ?>; font-size: 24px; margin-top: 2px;"></i>
                    </div>
                    
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                            <span style="background: <?= $color ?>; color: white; padding: 2px 8px; 
                                         border-radius: 10px; font-size: 12px; font-weight: 600;">
                                <?= $typeLabel ?>
                            </span>
                            
                            <?php if (isset($item['avg_rating']) && $item['avg_rating'] >= 4): ?>
                            <span style="display: inline-flex; align-items: center; gap: 3px; 
                                         color: #ffc107; font-size: 12px;">
                                <i class="fas fa-star"></i>
                                <?= round($item['avg_rating'], 1) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <h3 style="margin: 0 0 12px 0; font-size: 18px; line-height: 1.4;">
                            <a href="<?= $link ?>" style="color: var(--text-primary); text-decoration: none;">
                                <?= htmlspecialchars($item['title']) ?>
                            </a>
                        </h3>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; 
                                    font-size: 14px; color: var(--text-secondary);">
                            <span>
                                <i class="fas fa-calendar"></i> 
                                <?= date('d.m.Y', strtotime($item['created_at'])) ?>
                            </span>
                            <span>
                                <i class="fas fa-eye"></i> 
                                <?= number_format($item['views'] ?? 0) ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <a href="<?= $link ?>" 
                       style="flex: 1; background: <?= $color ?>; color: white; text-decoration: none; 
                              text-align: center; padding: 10px 16px; border-radius: 6px; 
                              font-size: 14px; font-weight: 600;">
                        <i class="fas fa-arrow-right"></i> Читать
                    </a>
                    
                    <button onclick="quickAddToReadLater('<?= $item['type'] ?>', <?= $item['id'] ?>)" 
                            style="background: transparent; border: 1px solid var(--border-color); 
                                   color: var(--text-primary); padding: 10px 12px; border-radius: 6px; 
                                   font-size: 14px; cursor: pointer;" 
                            title="Добавить в список 'Читать позже'">
                        <i class="fas fa-bookmark"></i>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div style="text-align: center; margin-top: 40px; padding: 30px; 
                    background: var(--bg-secondary); border-radius: 12px;">
            <h3 style="margin-bottom: 15px; color: var(--text-primary);">
                <i class="fas fa-info-circle"></i> Как работают рекомендации?
            </h3>
            <p style="color: var(--text-secondary); line-height: 1.6; max-width: 600px; margin: 0 auto;">
                Мы анализируем ваши предпочтения на основе прочитанных материалов, оценок и добавлений в избранное, 
                чтобы предлагать наиболее интересный для вас контент. Чем больше вы взаимодействуете с сайтом, 
                тем точнее становятся рекомендации.
            </p>
        </div>
        
        <?php endif; ?>
    </div>
</div>

<script>
async function quickAddToReadLater(itemType, itemId) {
    try {
        const response = await fetch('/api/reading-lists/quick-add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                item_type: itemType,
                item_id: itemId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show temporary success message
            const btn = event.target.closest('button');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i>';
            btn.style.background = '#28a745';
            btn.style.color = 'white';
            btn.style.border = '1px solid #28a745';
            
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.style.background = 'transparent';
                btn.style.color = 'var(--text-primary)';
                btn.style.border = '1px solid var(--border-color)';
            }, 2000);
        } else {
            alert(data.error || 'Ошибка при добавлении');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Произошла ошибка');
    }
}
</script>
<?php
$greyContent2 = ob_get_clean();

// Include template
include 'template.php';
?>