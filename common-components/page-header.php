<?php
/**
 * Reusable Page Header Component
 * 
 * Usage:
 * include $_SERVER['DOCUMENT_ROOT'] . '/common-components/page-header.php';
 * renderPageHeader($title, $subtitle, $showSearch = false, $searchPlaceholder = 'Поиск...');
 */

function renderPageHeader($title, $subtitle = '', $options = []) {
    // Extract options with defaults
    $showSearch = $options['showSearch'] ?? false;
    $searchPlaceholder = $options['searchPlaceholder'] ?? 'Поиск...';
    $searchId = $options['searchId'] ?? 'pageSearch';
    $stats = $options['stats'] ?? [];
    $centered = $options['centered'] ?? true;
    $background = $options['background'] ?? true;
    $showSubtitle = $options['showSubtitle'] ?? true;
    $compact = $options['compact'] ?? false;
    ?>
    <style>
        .page-hero-section {
            background: <?php echo $compact ? 'transparent' : 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'; ?>;
            color: <?php echo $compact ? 'var(--text-primary, #333)' : 'white'; ?>;
            padding: <?php echo $compact ? '15px 0' : '40px 0'; ?>;
            margin-bottom: <?php echo $compact ? '15px' : '30px'; ?>;
            border-bottom: <?php echo $compact ? '1px solid var(--border-color, #e2e8f0)' : 'none'; ?>;
        }
        .page-hero-title {
            font-size: <?php echo $compact ? '28px' : '40px'; ?>;
            font-weight: <?php echo $compact ? '600' : '700'; ?>;
            margin-bottom: <?php echo $compact ? '8px' : '15px'; ?>;
            text-align: center;
            margin-left: auto;
            margin-right: auto;
        }
        .page-hero-title-with-stats {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }
        .page-hero-subtitle {
            font-size: <?php echo $compact ? '14px' : '16px'; ?>;
            opacity: <?php echo $compact ? '0.7' : '0.9'; ?>;
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
            margin-left: auto;
            margin-right: auto;
        }
        
        /* Dark mode support */
        [data-theme="dark"] .page-hero-section {
            background: <?php echo $compact ? 'transparent' : 'linear-gradient(135deg, #4a5568 0%, #2d3748 100%)'; ?>;
            color: <?php echo $compact ? 'var(--text-primary, #f7fafc)' : 'white'; ?>;
            border-bottom-color: <?php echo $compact ? 'var(--border-color, #4a5568)' : 'none'; ?>;
        }
        .page-search-container {
            max-width: 500px;
            margin: 0 auto;
            position: relative;
        }
        .page-search-input {
            width: 100%;
            padding: 12px 50px 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            outline: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: box-shadow 0.3s ease;
        }
        .page-search-input:focus {
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        }
        .page-search-clear {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            font-size: 18px;
            color: #888;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            transition: all 0.2s ease;
            display: none;
        }
        .page-search-clear:hover {
            background: #f0f0f0;
            color: #333;
        }
        .page-search-clear.show {
            display: block;
        }
        .page-header-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .page-header-stat {
            text-align: center;
            color: white;
        }
        .page-header-stat-number {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 2px;
            opacity: 0.95;
        }
        .page-header-stat-label {
            font-size: 12px;
            opacity: 0.8;
            font-weight: 500;
        }
        .page-header-stats-inline {
            display: flex;
            gap: 25px;
            align-items: center;
        }
        .page-header-stat-inline {
            text-align: center;
            color: white;
        }
        .page-header-stat-inline .page-header-stat-number {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 0px;
            opacity: 0.95;
        }
        .page-header-stat-inline .page-header-stat-label {
            font-size: 11px;
            opacity: 0.8;
            font-weight: 500;
        }
        @media (max-width: 768px) {
            .page-hero-title {
                font-size: <?php echo $compact ? '24px' : '20px'; ?>;
            }
            .page-hero-subtitle {
                font-size: <?php echo $compact ? '12px' : '12px'; ?>;
            }
            .page-hero-section {
                padding: <?php echo $compact ? '10px 0' : '40px 0'; ?>;
                margin-bottom: <?php echo $compact ? '10px' : '30px'; ?>;
            }
            .page-search-input {
                font-size: 14px;
                padding: 10px 16px;
            }
            .page-header-stats {
                gap: 30px;
                margin-top: 15px;
            }
            .page-header-stat-number {
                font-size: 24px;
            }
            .page-header-stat-label {
                font-size: 11px;
            }
            .page-hero-title-with-stats {
                flex-direction: column;
                gap: 15px;
            }
            .page-header-stats-inline {
                gap: 20px;
            }
            .page-header-stat-inline .page-header-stat-number {
                font-size: 18px;
            }
            .page-header-stat-inline .page-header-stat-label {
                font-size: 10px;
            }
        }
    </style>
    
    <section class="page-hero-section">
            <?php if (!empty($stats)): ?>
                <div class="page-hero-title-with-stats">
                    <h1 class="page-hero-title" style="margin-bottom: 0;"><?= htmlspecialchars($title) ?></h1>
                    <div class="page-header-stats-inline">
                        <?php foreach ($stats as $stat): ?>
                            <div class="page-header-stat-inline">
                                <div class="page-header-stat-number"><?= number_format($stat['number']) ?></div>
                                <div class="page-header-stat-label"><?= htmlspecialchars($stat['label']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <h1 class="page-hero-title"><?= htmlspecialchars($title) ?></h1>
            <?php endif; ?>
            
            <?php if ($subtitle): ?>
                <p class="page-hero-subtitle"><?= htmlspecialchars($subtitle) ?></p>
            <?php endif; ?>
            
            <?php if ($showSearch): ?>
                <div class="page-search-container">
                    <input 
                        type="text" 
                        id="<?= htmlspecialchars($searchId) ?>" 
                        class="page-search-input" 
                        placeholder="<?= htmlspecialchars($searchPlaceholder) ?>"
                    >
                    <button type="button" class="page-search-clear" id="<?= htmlspecialchars($searchId) ?>Clear">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            <?php endif; ?>
    </section>
    
    <?php if ($showSearch): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('<?= htmlspecialchars($searchId) ?>');
        const clearButton = document.getElementById('<?= htmlspecialchars($searchId) ?>Clear');
        
        if (searchInput && clearButton) {
            // Show/hide clear button based on input content
            function toggleClearButton() {
                if (searchInput.value.length > 0) {
                    clearButton.classList.add('show');
                } else {
                    clearButton.classList.remove('show');
                }
            }
            
            // Handle input changes
            searchInput.addEventListener('input', toggleClearButton);
            searchInput.addEventListener('keyup', toggleClearButton);
            
            // Handle clear button click
            clearButton.addEventListener('click', function() {
                searchInput.value = '';
                toggleClearButton();
                searchInput.focus();
                
                // Trigger input event to update search results
                const event = new Event('input', { bubbles: true });
                searchInput.dispatchEvent(event);
            });
            
            // Initial check
            toggleClearButton();
        }
    });
    </script>
    <?php endif; ?>
    
    <?php
}
?>