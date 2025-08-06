<?php
/**
 * Reusable Category Navigation Component
 * Used for both news and test category navigation with consistent hover effects
 */

function renderCategoryNavigation($items, $currentPath = '', $activeClass = 'active') {
    ?>
    <div class="category-navigation" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 30px; padding: 0 20px;">
        <?php
        $activeStyle = "padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: 500; transition: all 0.3s ease; background: #28a745; color: white; cursor: pointer;";
        $inactiveStyle = "padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: 400; transition: all 0.3s ease; background: var(--surface, #ffffff); color: var(--text-primary, #333); border: 1px solid var(--border-color, #e2e8f0); cursor: pointer;";
        
        foreach ($items as $item) {
            $isActive = ($currentPath === $item['url'] || (empty($currentPath) && $item['url'] === '/'));
            $class = 'category-btn' . ($isActive ? ' active' : '');
            $style = $isActive ? $activeStyle : $inactiveStyle;
            
            echo '<a href="' . htmlspecialchars($item['url']) . '" class="' . $class . '" style="' . $style . '">';
            echo htmlspecialchars($item['title']);
            echo '</a>';
        }
        ?>
    </div>
    
    <style>
        /* Navigation button hover effects - reusable for all category navigation */
        .category-btn {
            position: relative;
            overflow: hidden;
        }

        .category-btn:not(.active):hover {
            color: #28a745 !important;
            border-color: #28a745 !important;
            background: #f8f9fa !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.15);
            text-decoration: none;
        }

        .category-btn.active:hover {
            background: #218838 !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
            text-decoration: none;
        }

        /* Dark mode support for navigation */
        [data-theme="dark"] .category-btn:not(.active) {
            background: var(--surface-dark, #2d3748) !important;
            border-color: var(--border-dark, #4a5568) !important;
            color: var(--text-primary, #e4e6eb) !important;
        }

        [data-theme="dark"] .category-btn:not(.active):hover {
            background: var(--surface-hover-dark, #374151) !important;
            border-color: #28a745 !important;
            color: #28a745 !important;
        }

        [data-theme="dark"] .category-btn.active {
            background: #28a745 !important;
            color: white !important;
        }

        [data-theme="dark"] .category-btn.active:hover {
            background: #218838 !important;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .category-navigation {
                gap: 8px;
                padding: 0 15px;
            }
            
            .category-btn {
                font-size: 13px;
                padding: 6px 12px;
            }
        }
    </style>
    <?php
}
?>