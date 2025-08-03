<?php
/**
 * Compact Page Header Component for Category Pages
 * 
 * Usage:
 * include $_SERVER['DOCUMENT_ROOT'] . '/common-components/page-header-compact.php';
 * renderPageHeaderCompact($title, $subtitle);
 */

function renderPageHeaderCompact($title, $subtitle = '', $options = []) {
    $showSubtitle = $options['showSubtitle'] ?? true;
    ?>
    <style>
        .page-header-compact {
            padding: 20px 0;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border-color, #e2e8f0);
        }
        .page-header-compact-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }
        .page-title-compact {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
            margin: 0;
            color: var(--text-primary, #1a202c);
            display: inline-flex;
            align-items: center;
        }
        .page-subtitle-compact {
            font-size: 1.125rem;
            line-height: 1;
            margin: 0;
            color: var(--text-secondary, #64748b);
            font-weight: 400;
            display: inline-flex;
            align-items: center;
        }
        .page-subtitle-compact::before {
            content: "•";
            margin: 0 10px;
            color: var(--text-secondary, #94a3b8);
            font-size: 0.875rem;
        }
        
        /* Mobile responsive */
        @media (max-width: 768px) {
            .page-header-compact {
                padding: 15px 0;
                margin-bottom: 20px;
            }
            .page-title-compact {
                font-size: 2rem;
            }
            .page-subtitle-compact {
                font-size: 1rem;
            }
        }
        
        @media (max-width: 480px) {
            .page-title-compact {
                font-size: 1.75rem;
            }
            .page-subtitle-compact {
                font-size: 0.875rem;
            }
        }
        
        /* Dark mode support */
        [data-theme="dark"] .page-header-compact {
            border-bottom-color: var(--border-color, #374151);
        }
        
        [data-theme="dark"] .page-title-compact {
            color: var(--text-primary, #f9fafb);
        }
        
        [data-theme="dark"] .page-subtitle-compact {
            color: var(--text-secondary, #d1d5db);
        }
    </style>
    
    <div class="page-header-compact">
        <div class="page-header-compact-inner">
            <h1 class="page-title-compact"><?= htmlspecialchars($title) ?></h1>
            <?php if (!empty($subtitle) && $showSubtitle): ?>
                <span class="page-subtitle-compact"><?= htmlspecialchars($subtitle) ?></span>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
?>