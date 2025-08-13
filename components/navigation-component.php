<?php
/**
 * Reusable Navigation Component
 * 
 * Usage: renderNavigation($title, $items, $activeItem)
 */

function renderNavigation($title, $items = [], $activeItem = '') {
    ?>
    <div class="reusable-navigation" style="padding: 0; margin: 0;">
        <nav class="nav-dark" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <?php foreach ($items as $item): ?>
                <?php 
                $isActive = ($activeItem === $item['id'] || $activeItem === $item['value']);
                ?>
                <a class="nav-item-dark <?php echo $isActive ? 'active' : ''; ?>" 
                   href="<?php echo htmlspecialchars($item['link'] ?? '#'); ?>"
                   style="
                       padding: 0.75rem 1.5rem;
                       background: <?php echo $isActive ? '#667eea' : '#1a1a2e'; ?>;
                       color: <?php echo $isActive ? '#fff' : '#999'; ?>;
                       border-radius: 12px;
                       text-decoration: none;
                       font-weight: 500;
                       transition: all 0.2s;
                       border: 1px solid <?php echo $isActive ? '#667eea' : 'transparent'; ?>;
                       display: inline-flex;
                       align-items: center;
                       gap: 0.5rem;
                   "
                   onmouseover="this.style.background='#252538'; this.style.color='#fff';"
                   onmouseout="this.style.background='<?php echo $isActive ? '#667eea' : '#1a1a2e'; ?>'; this.style.color='<?php echo $isActive ? '#fff' : '#999'; ?>';">
                    <?php echo htmlspecialchars($item['label']); ?>
                    <?php if (isset($item['count'])): ?>
                        <span style="
                            background: rgba(255,255,255,0.1);
                            padding: 0.125rem 0.5rem;
                            border-radius: 10px;
                            font-size: 0.875rem;
                        ">
                            <?php echo $item['count']; ?>
                        </span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>
    <?php
}

/**
 * Build navigation items from categories
 */
function buildCategoryNavigation($categories, $baseUrl = '/news', $showAllOption = true) {
    $items = [];
    
    if ($showAllOption) {
        $items[] = [
            'id' => 0,
            'label' => 'Все новости',
            'link' => $baseUrl
        ];
    }
    
    foreach ($categories as $category) {
        $items[] = [
            'id' => $category['id'],
            'label' => $category['name'],
            'link' => $baseUrl . '?category=' . $category['id'],
            'count' => $category['count'] ?? null
        ];
    }
    
    return $items;
}
?>