<?php
// Include the new reusable components
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/content-wrapper.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/typography.php';

renderContentWrapper('start');

// Use the reusable page header component (much smaller than the old hero section)
renderPageHeader(
    'Онлайн тесты', 
    'Проверьте свои знания и навыки с помощью наших интерактивных тестов',
    ['centered' => true, 'background' => true, 'showSubtitle' => false]
);

// Test categories array
$testCategories = [
    [
        'title' => 'Академические предметы',
        'tests' => [
            ['name' => 'Математика', 'url' => '/test/math-test', 'icon' => 'fa-calculator'],
            ['name' => 'Русский язык', 'url' => '/test/russian-test', 'icon' => 'fa-book'],
            ['name' => 'Физика', 'url' => '/test/physics-test', 'icon' => 'fa-atom'],
            ['name' => 'Химия', 'url' => '/test/chemistry-test', 'icon' => 'fa-flask'],
            ['name' => 'Биология', 'url' => '/test/biology-test', 'icon' => 'fa-dna'],
            ['name' => 'География', 'url' => '/test/geography-test', 'icon' => 'fa-globe']
        ]
    ],
    [
        'title' => 'Профориентация',
        'tests' => [
            ['name' => 'Тест на профессию', 'url' => '/test/career-test', 'icon' => 'fa-briefcase'],
            ['name' => 'Личностный тест', 'url' => '/test/personality-test', 'icon' => 'fa-user']
        ]
    ]
];
?>

<style>
    .test-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }
    
    .test-card {
        background: var(--surface, white);
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        transition: all 0.3s ease;
        text-decoration: none;
        color: inherit;
        display: block;
    }
    
    .test-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        border-color: var(--primary-color, #28a745);
        text-decoration: none;
        color: inherit;
    }
    
    .test-icon {
        width: 64px;
        height: 64px;
        background: var(--primary-color, #28a745);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        color: white;
        font-size: 24px;
    }
    
    .test-title {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
        color: var(--text-primary, #1a202c);
    }
    
    /* Dark mode */
    [data-theme="dark"] .test-card {
        background: var(--surface, #2d3748);
        border-color: var(--border-color, #4a5568);
    }
    
    [data-theme="dark"] .test-card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    }
    
    [data-theme="dark"] .test-title {
        color: var(--text-primary, #f7fafc);
    }
    
    @media (max-width: 768px) {
        .test-grid {
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
        }
        
        .test-card {
            padding: 20px;
        }
        
        .test-icon {
            width: 56px;
            height: 56px;
            font-size: 20px;
        }
        
        .test-title {
            font-size: 16px;
        }
    }
</style>

<?php foreach ($testCategories as $category): ?>
    <?php 
    renderSectionTitle($category['title'], '', ['spacing' => 'compact', 'level' => 3]); 
    ?>
    
    <div class="test-grid">
        <?php foreach ($category['tests'] as $test): ?>
            <a href="<?= htmlspecialchars($test['url']) ?>" class="test-card">
                <div class="test-icon">
                    <i class="fas <?= htmlspecialchars($test['icon']) ?>"></i>
                </div>
                <h3 class="test-title"><?= htmlspecialchars($test['name']) ?></h3>
            </a>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<?php
renderContentWrapper('end');
?>