<?php
/**
 * Context-aware loading placeholders that match actual content structure
 */

function renderContextAwarePlaceholder($type = 'card', $options = []) {
    $defaults = [
        'count' => 1,
        'columns' => 4,
        'showImage' => true,
        'showBadge' => true,
        'showMeta' => true,
        'animated' => true
    ];
    
    $options = array_merge($defaults, $options);
    $animationClass = $options['animated'] ? 'skeleton-animated' : '';
    
    switch($type) {
        case 'news-card':
            renderNewsCardPlaceholder($options, $animationClass);
            break;
            
        case 'post-card':
            renderPostCardPlaceholder($options, $animationClass);
            break;
            
        case 'school-card':
            renderSchoolCardPlaceholder($options, $animationClass);
            break;
            
        case 'test-card':
            renderTestCardPlaceholder($options, $animationClass);
            break;
            
        case 'category-header':
            renderCategoryHeaderPlaceholder($options, $animationClass);
            break;
            
        case 'article-content':
            renderArticleContentPlaceholder($options, $animationClass);
            break;
            
        case 'comment':
            renderCommentPlaceholder($options, $animationClass);
            break;
            
        case 'table-row':
            renderTableRowPlaceholder($options, $animationClass);
            break;
            
        default:
            renderGenericCardPlaceholder($options, $animationClass);
    }
}

function renderNewsCardPlaceholder($options, $animationClass) {
    ?>
    <div class="placeholder-news-card" style="border: 1px solid var(--border-color, #e2e8f0); border-radius: 8px; padding: 20px; background: var(--surface, #ffffff); position: relative;">
        <?php if ($options['showBadge']): ?>
        <!-- Category Badge Placeholder -->
        <div class="skeleton <?= $animationClass ?>" style="position: absolute; top: 15px; right: 15px; width: 80px; height: 24px; border-radius: 12px;"></div>
        <?php endif; ?>
        
        <!-- Title Placeholder -->
        <div class="skeleton <?= $animationClass ?>" style="height: 24px; width: 85%; margin-bottom: 8px; border-radius: 4px;"></div>
        <div class="skeleton <?= $animationClass ?>" style="height: 24px; width: 60%; margin-bottom: 15px; border-radius: 4px;"></div>
        
        <!-- Meta Info Placeholder -->
        <div style="display: flex; gap: 20px; margin-bottom: 15px;">
            <div class="skeleton <?= $animationClass ?>" style="height: 16px; width: 100px; border-radius: 4px;"></div>
            <div class="skeleton <?= $animationClass ?>" style="height: 16px; width: 80px; border-radius: 4px;"></div>
        </div>
        
        <!-- Description Placeholder -->
        <div class="skeleton <?= $animationClass ?>" style="height: 16px; width: 100%; margin-bottom: 8px; border-radius: 4px;"></div>
        <div class="skeleton <?= $animationClass ?>" style="height: 16px; width: 90%; margin-bottom: 8px; border-radius: 4px;"></div>
        <div class="skeleton <?= $animationClass ?>" style="height: 16px; width: 70%; border-radius: 4px;"></div>
    </div>
    <?php
}

function renderPostCardPlaceholder($options, $animationClass) {
    ?>
    <div class="placeholder-post-card" style="border: 1px solid var(--border-color, #e2e8f0); border-radius: 12px; overflow: hidden; background: var(--surface, #ffffff); position: relative;">
        <?php if ($options['showBadge']): ?>
        <!-- Category Badge Placeholder -->
        <div class="skeleton <?= $animationClass ?>" style="position: absolute; top: 12px; right: 12px; width: 100px; height: 28px; border-radius: 14px; z-index: 1;"></div>
        <?php endif; ?>
        
        <?php if ($options['showImage']): ?>
        <!-- Image Placeholder -->
        <div class="skeleton <?= $animationClass ?>" style="width: 100%; height: 180px;"></div>
        <?php endif; ?>
        
        <div style="padding: 20px;">
            <!-- Title Placeholder -->
            <div class="skeleton <?= $animationClass ?>" style="height: 20px; width: 90%; margin-bottom: 8px; border-radius: 4px;"></div>
            <div class="skeleton <?= $animationClass ?>" style="height: 20px; width: 70%; margin-bottom: 12px; border-radius: 4px;"></div>
            
            <!-- Excerpt Placeholder -->
            <div class="skeleton <?= $animationClass ?>" style="height: 14px; width: 100%; margin-bottom: 6px; border-radius: 4px;"></div>
            <div class="skeleton <?= $animationClass ?>" style="height: 14px; width: 95%; margin-bottom: 6px; border-radius: 4px;"></div>
            <div class="skeleton <?= $animationClass ?>" style="height: 14px; width: 80%; border-radius: 4px;"></div>
        </div>
    </div>
    <?php
}

function renderSchoolCardPlaceholder($options, $animationClass) {
    ?>
    <div class="placeholder-school-card" style="padding: 20px; background: var(--surface, #ffffff); border: 1px solid var(--border-color, #e2e8f0); border-radius: 8px;">
        <!-- School Name -->
        <div class="skeleton <?= $animationClass ?>" style="height: 24px; width: 70%; margin-bottom: 12px; border-radius: 4px;"></div>
        
        <!-- Location -->
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
            <div class="skeleton <?= $animationClass ?>" style="width: 16px; height: 16px; border-radius: 50%;"></div>
            <div class="skeleton <?= $animationClass ?>" style="height: 16px; width: 200px; border-radius: 4px;"></div>
        </div>
        
        <!-- Phone -->
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
            <div class="skeleton <?= $animationClass ?>" style="width: 16px; height: 16px; border-radius: 50%;"></div>
            <div class="skeleton <?= $animationClass ?>" style="height: 16px; width: 150px; border-radius: 4px;"></div>
        </div>
        
        <!-- Email -->
        <div style="display: flex; align-items: center; gap: 8px;">
            <div class="skeleton <?= $animationClass ?>" style="width: 16px; height: 16px; border-radius: 50%;"></div>
            <div class="skeleton <?= $animationClass ?>" style="height: 16px; width: 180px; border-radius: 4px;"></div>
        </div>
    </div>
    <?php
}

function renderTestCardPlaceholder($options, $animationClass) {
    ?>
    <div class="placeholder-test-card" style="background: var(--surface, white); border: 1px solid var(--border-color, #e2e8f0); border-radius: 12px; padding: 24px; text-align: center;">
        <!-- Icon Placeholder -->
        <div class="skeleton <?= $animationClass ?>" style="width: 64px; height: 64px; border-radius: 16px; margin: 0 auto 16px;"></div>
        
        <!-- Title Placeholder -->
        <div class="skeleton <?= $animationClass ?>" style="height: 20px; width: 120px; margin: 0 auto; border-radius: 4px;"></div>
    </div>
    <?php
}

function renderCategoryHeaderPlaceholder($options, $animationClass) {
    ?>
    <div class="placeholder-category-header" style="text-align: center; padding: 20px 0; margin-bottom: 30px; border-bottom: 1px solid var(--border-color, #e2e8f0);">
        <div style="display: flex; align-items: center; justify-content: center;">
            <!-- Title -->
            <div class="skeleton <?= $animationClass ?>" style="height: 40px; width: 200px; border-radius: 4px;"></div>
            <!-- Post Count -->
            <div class="skeleton <?= $animationClass ?>" style="height: 20px; width: 100px; margin-left: 20px; border-radius: 4px;"></div>
        </div>
    </div>
    <?php
}

function renderArticleContentPlaceholder($options, $animationClass) {
    ?>
    <div class="placeholder-article-content" style="max-width: 800px; margin: 0 auto;">
        <!-- Title -->
        <div class="skeleton <?= $animationClass ?>" style="height: 36px; width: 80%; margin-bottom: 20px; border-radius: 4px;"></div>
        
        <!-- Meta Info -->
        <div style="display: flex; justify-content: space-between; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--border-color, #e2e8f0);">
            <div class="skeleton <?= $animationClass ?>" style="height: 16px; width: 150px; border-radius: 4px;"></div>
            <div class="skeleton <?= $animationClass ?>" style="height: 16px; width: 120px; border-radius: 4px;"></div>
        </div>
        
        <!-- Lead Paragraph -->
        <div class="skeleton <?= $animationClass ?>" style="height: 20px; width: 100%; margin-bottom: 10px; border-radius: 4px;"></div>
        <div class="skeleton <?= $animationClass ?>" style="height: 20px; width: 95%; margin-bottom: 30px; border-radius: 4px;"></div>
        
        <!-- Content Paragraphs -->
        <?php for ($i = 0; $i < 3; $i++): ?>
        <div style="margin-bottom: 20px;">
            <div class="skeleton <?= $animationClass ?>" style="height: 16px; width: 100%; margin-bottom: 8px; border-radius: 4px;"></div>
            <div class="skeleton <?= $animationClass ?>" style="height: 16px; width: 98%; margin-bottom: 8px; border-radius: 4px;"></div>
            <div class="skeleton <?= $animationClass ?>" style="height: 16px; width: 95%; margin-bottom: 8px; border-radius: 4px;"></div>
            <div class="skeleton <?= $animationClass ?>" style="height: 16px; width: <?= rand(70, 90) ?>%; border-radius: 4px;"></div>
        </div>
        <?php endfor; ?>
    </div>
    <?php
}

function renderCommentPlaceholder($options, $animationClass) {
    ?>
    <div class="placeholder-comment" style="padding: 16px 0; border-bottom: 1px solid var(--border-color, #e2e8f0);">
        <div style="display: flex; gap: 12px;">
            <!-- Avatar -->
            <div class="skeleton <?= $animationClass ?>" style="width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0;"></div>
            
            <div style="flex: 1;">
                <!-- User Info -->
                <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 8px;">
                    <div class="skeleton <?= $animationClass ?>" style="height: 16px; width: 120px; border-radius: 4px;"></div>
                    <div class="skeleton <?= $animationClass ?>" style="height: 14px; width: 80px; border-radius: 4px;"></div>
                </div>
                
                <!-- Comment Text -->
                <div class="skeleton <?= $animationClass ?>" style="height: 16px; width: 100%; margin-bottom: 6px; border-radius: 4px;"></div>
                <div class="skeleton <?= $animationClass ?>" style="height: 16px; width: 85%; border-radius: 4px;"></div>
            </div>
        </div>
    </div>
    <?php
}

function renderTableRowPlaceholder($options, $animationClass) {
    $columns = $options['columns'] ?? 4;
    ?>
    <tr class="placeholder-table-row">
        <?php for ($i = 0; $i < $columns; $i++): ?>
        <td style="padding: 12px;">
            <div class="skeleton <?= $animationClass ?>" style="height: 16px; width: <?= $i === 0 ? '60%' : '80%' ?>; border-radius: 4px;"></div>
        </td>
        <?php endfor; ?>
    </tr>
    <?php
}

function renderGenericCardPlaceholder($options, $animationClass) {
    ?>
    <div class="placeholder-generic-card" style="padding: 20px; background: var(--surface, #ffffff); border: 1px solid var(--border-color, #e2e8f0); border-radius: 8px;">
        <div class="skeleton <?= $animationClass ?>" style="height: 24px; width: 70%; margin-bottom: 12px; border-radius: 4px;"></div>
        <div class="skeleton <?= $animationClass ?>" style="height: 16px; width: 100%; margin-bottom: 8px; border-radius: 4px;"></div>
        <div class="skeleton <?= $animationClass ?>" style="height: 16px; width: 90%; margin-bottom: 8px; border-radius: 4px;"></div>
        <div class="skeleton <?= $animationClass ?>" style="height: 16px; width: 75%; border-radius: 4px;"></div>
    </div>
    <?php
}

// Grid wrapper for multiple placeholders
function renderPlaceholderGrid($type, $count, $columns = 4, $options = []) {
    $gridClass = "placeholder-grid-{$columns}";
    ?>
    <div class="<?= $gridClass ?>" style="display: grid; grid-template-columns: repeat(<?= $columns ?>, 1fr); gap: 20px;">
        <?php for ($i = 0; $i < $count; $i++): ?>
            <?php renderContextAwarePlaceholder($type, $options); ?>
        <?php endfor; ?>
    </div>
    <?php
}

// Add CSS for skeleton animation
if (!defined('PLACEHOLDER_CSS_LOADED')) {
    define('PLACEHOLDER_CSS_LOADED', true);
    ?>
    <style>
        .skeleton {
            background: linear-gradient(90deg, 
                var(--skeleton-base, #e2e8f0) 25%, 
                var(--skeleton-highlight, #edf2f7) 50%, 
                var(--skeleton-base, #e2e8f0) 75%
            );
            background-size: 200% 100%;
        }
        
        .skeleton-animated {
            animation: skeleton-loading 1.4s ease-in-out infinite;
        }
        
        @keyframes skeleton-loading {
            0% {
                background-position: 200% 0;
            }
            100% {
                background-position: -200% 0;
            }
        }
        
        /* Dark mode skeleton colors */
        [data-theme="dark"] .skeleton {
            background: linear-gradient(90deg, 
                var(--skeleton-base, #374151) 25%, 
                var(--skeleton-highlight, #4b5563) 50%, 
                var(--skeleton-base, #374151) 75%
            );
            background-size: 200% 100%;
        }
        
        /* Responsive grids */
        @media (max-width: 1200px) {
            .placeholder-grid-4 {
                grid-template-columns: repeat(3, 1fr) !important;
            }
        }
        
        @media (max-width: 900px) {
            .placeholder-grid-4,
            .placeholder-grid-3 {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }
        
        @media (max-width: 600px) {
            .placeholder-grid-4,
            .placeholder-grid-3,
            .placeholder-grid-2 {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
    <?php
}
?>