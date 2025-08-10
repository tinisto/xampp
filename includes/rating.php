<?php
// Rating component
function get_rating_stats($itemType, $itemId) {
    $stats = db_fetch_one("
        SELECT 
            COUNT(*) as total_ratings,
            AVG(rating) as average_rating,
            SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_stars,
            SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_stars,
            SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_stars,
            SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_stars,
            SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
        FROM ratings
        WHERE item_type = ? AND item_id = ?
    ", [$itemType, $itemId]);
    
    if (!$stats || $stats['total_ratings'] == 0) {
        return [
            'total_ratings' => 0,
            'average_rating' => 0,
            'distribution' => [
                5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0
            ]
        ];
    }
    
    return [
        'total_ratings' => $stats['total_ratings'],
        'average_rating' => round($stats['average_rating'], 1),
        'distribution' => [
            5 => intval($stats['five_stars']),
            4 => intval($stats['four_stars']),
            3 => intval($stats['three_stars']),
            2 => intval($stats['two_stars']),
            1 => intval($stats['one_star'])
        ]
    ];
}

function get_user_rating($itemType, $itemId, $userId) {
    if (!$userId) return null;
    
    return db_fetch_column("
        SELECT rating FROM ratings 
        WHERE item_type = ? AND item_id = ? AND user_id = ?
    ", [$itemType, $itemId, $userId]);
}

function include_rating($itemType, $itemId) {
    $stats = get_rating_stats($itemType, $itemId);
    $userRating = isset($_SESSION['user_id']) ? get_user_rating($itemType, $itemId, $_SESSION['user_id']) : null;
    
    ?>
    <div class="rating-widget" data-item-type="<?= htmlspecialchars($itemType) ?>" data-item-id="<?= $itemId ?>" 
         style="background: var(--bg-secondary); border-radius: 12px; padding: 20px; margin: 20px 0;">
        <h3 style="margin: 0 0 20px 0; font-size: 20px;">Рейтинг</h3>
        
        <div style="display: grid; grid-template-columns: auto 1fr; gap: 30px; align-items: start;">
            <!-- Average rating -->
            <div style="text-align: center;">
                <div style="font-size: 48px; font-weight: 700; color: #ffc107; margin-bottom: 5px;">
                    <?= $stats['average_rating'] ?>
                </div>
                <div class="rating-stars" style="font-size: 24px; margin-bottom: 5px;">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php if ($i <= round($stats['average_rating'])): ?>
                            <i class="fas fa-star" style="color: #ffc107;"></i>
                        <?php else: ?>
                            <i class="far fa-star" style="color: #ffc107;"></i>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
                <div style="color: var(--text-secondary); font-size: 14px;">
                    <?= $stats['total_ratings'] ?> <?= plural_form($stats['total_ratings'], ['оценка', 'оценки', 'оценок']) ?>
                </div>
            </div>
            
            <!-- Rating distribution -->
            <div>
                <?php for ($star = 5; $star >= 1; $star--): ?>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                    <span style="width: 20px; text-align: right;"><?= $star ?></span>
                    <i class="fas fa-star" style="color: #ffc107; font-size: 14px;"></i>
                    <div style="flex: 1; height: 8px; background: var(--bg-primary); border-radius: 4px; overflow: hidden;">
                        <?php 
                        $percentage = $stats['total_ratings'] > 0 ? 
                            ($stats['distribution'][$star] / $stats['total_ratings'] * 100) : 0;
                        ?>
                        <div style="width: <?= $percentage ?>%; height: 100%; background: #ffc107;"></div>
                    </div>
                    <span style="width: 40px; text-align: right; font-size: 14px; color: var(--text-secondary);">
                        <?= $stats['distribution'][$star] ?>
                    </span>
                </div>
                <?php endfor; ?>
            </div>
        </div>
        
        <?php if (isset($_SESSION['user_id'])): ?>
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--border-color);">
            <h4 style="margin: 0 0 15px 0; font-size: 16px;">Ваша оценка</h4>
            <div class="user-rating" style="display: flex; gap: 5px;">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                <button class="rating-star" data-rating="<?= $i ?>" 
                        style="background: transparent; border: none; cursor: pointer; font-size: 28px; 
                               color: #ffc107; transition: transform 0.2s;"
                        onmouseover="highlightStars(<?= $i ?>)"
                        onmouseout="resetStars()"
                        onclick="submitRating(<?= $i ?>)">
                    <?php if ($userRating && $i <= $userRating): ?>
                        <i class="fas fa-star"></i>
                    <?php else: ?>
                        <i class="far fa-star"></i>
                    <?php endif; ?>
                </button>
                <?php endfor; ?>
            </div>
            <?php if ($userRating): ?>
            <p style="margin-top: 10px; color: var(--text-secondary); font-size: 14px;">
                Вы поставили оценку: <?= $userRating ?> из 5
            </p>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--border-color); text-align: center;">
            <p style="color: var(--text-secondary);">
                <a href="/login" style="color: var(--link-color);">Войдите</a>, чтобы оценить материал
            </p>
        </div>
        <?php endif; ?>
    </div>
    
    <script>
    function highlightStars(rating) {
        const widget = document.querySelector('.rating-widget[data-item-type="<?= $itemType ?>"][data-item-id="<?= $itemId ?>"]');
        const stars = widget.querySelectorAll('.user-rating .rating-star');
        
        stars.forEach((star, index) => {
            const icon = star.querySelector('i');
            if (index < rating) {
                icon.className = 'fas fa-star';
                star.style.transform = 'scale(1.1)';
            } else {
                icon.className = 'far fa-star';
                star.style.transform = 'scale(1)';
            }
        });
    }
    
    function resetStars() {
        const widget = document.querySelector('.rating-widget[data-item-type="<?= $itemType ?>"][data-item-id="<?= $itemId ?>"]');
        const stars = widget.querySelectorAll('.user-rating .rating-star');
        const currentRating = <?= $userRating ?: 0 ?>;
        
        stars.forEach((star, index) => {
            const icon = star.querySelector('i');
            if (index < currentRating) {
                icon.className = 'fas fa-star';
            } else {
                icon.className = 'far fa-star';
            }
            star.style.transform = 'scale(1)';
        });
    }
    
    async function submitRating(rating) {
        try {
            const response = await fetch('/api/rating/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    item_type: '<?= $itemType ?>',
                    item_id: <?= $itemId ?>,
                    rating: rating
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Reload the page to update the rating display
                location.reload();
            } else {
                alert(data.error || 'Ошибка при сохранении оценки');
            }
        } catch (error) {
            console.error('Rating error:', error);
            alert('Произошла ошибка при сохранении оценки');
        }
    }
    </script>
    <?php
}

function plural_form($number, $forms) {
    $cases = [2, 0, 1, 1, 1, 2];
    return $forms[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
}
?>