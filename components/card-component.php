<?php
/**
 * Reusable Card Component
 * 
 * Usage: renderCard($title, $description, $link, $image, $badge, $date)
 */

function renderCard($title, $description = '', $link = '#', $image = '', $badge = '', $date = '') {
    ?>
    <a href="<?php echo htmlspecialchars($link); ?>" style="text-decoration: none; display: block; height: 100%;">
        <div class="card-dark" style="
            background: transparent;
            border-radius: 16px;
            overflow: hidden;
            height: 220px;
            display: flex;
            flex-direction: column;
            transition: all 0.3s;
            border: 1px solid var(--border-color);
            position: relative;
        "
        onmouseover="this.style.transform='translateY(-4px)'; this.style.borderColor='var(--accent-primary)'; this.style.boxShadow='0 8px 24px rgba(102, 126, 234, 0.2)';"
        onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='var(--border-color)'; this.style.boxShadow='none';">
            <?php if (!empty($image)): ?>
                <div style="height: 140px; overflow: hidden; position: relative;">
                    <img src="<?php echo htmlspecialchars($image); ?>" 
                         alt="<?php echo htmlspecialchars($title); ?>" 
                         style="width: 100%; height: 100%; object-fit: cover;">
                    <div style="
                        position: absolute;
                        inset: 0;
                        background: linear-gradient(to bottom, transparent 0%, rgba(26, 26, 46, 0.8) 100%);
                    "></div>
                </div>
            <?php endif; ?>
            
            <div style="padding: 1.5rem; flex-grow: 1; display: flex; flex-direction: column;">
                <?php if (!empty($badge)): ?>
                    <span style="
                        background: rgba(102, 126, 234, 0.2);
                        color: #667eea;
                        padding: 0.375rem 0.875rem;
                        border-radius: 20px;
                        font-size: 0.875rem;
                        align-self: flex-start;
                        margin-bottom: 0.75rem;
                        font-weight: 500;
                        border: 1px solid rgba(102, 126, 234, 0.3);
                    ">
                        <?php echo htmlspecialchars($badge); ?>
                    </span>
                <?php endif; ?>
                
                <h5 style="
                    font-size: 1.125rem;
                    margin: 0;
                    color: var(--text-primary);
                    font-weight: 600;
                    line-height: 1.4;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                ">
                    <?php echo htmlspecialchars($title); ?>
                </h5>
                
            </div>
        </div>
    </a>
    <?php
}

/**
 * Render a grid of cards
 */
function renderCardGrid($cards, $columns = 4) {
    $colClass = 'col-md-' . (12 / $columns);
    ?>
    <div class="row">
        <?php foreach ($cards as $card): ?>
            <div class="<?php echo $colClass; ?> mb-4">
                <?php renderCard(
                    $card['title'] ?? '',
                    $card['description'] ?? '',
                    $card['link'] ?? '#',
                    $card['image'] ?? '',
                    $card['badge'] ?? '',
                    $card['date'] ?? ''
                ); ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}
?>