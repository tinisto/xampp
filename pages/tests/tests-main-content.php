<?php
// Include the test card component
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/test-card.php';

// Define test data
$tests = [
    'intellectual' => [
        'title' => 'Тесты интеллекта и психологии',
        'icon' => 'fas fa-brain',
        'color' => '#667eea',
        'tests' => [
            [
                'slug' => 'iq-test',
                'title' => 'IQ Тест',
                'description' => 'Классический тест на определение уровня интеллекта. Включает логические задачи, математические вопросы и задания на пространственное мышление.',
                'icon' => 'fas fa-lightbulb',
                'color' => '#e74c3c',
                'duration' => '20 минут',
                'questions' => '30 вопросов',
                'category' => 'Интеллект',
                'image' => null
            ],
            [
                'slug' => 'career-test',
                'title' => 'Профориентация',
                'description' => 'Определите свои профессиональные склонности и найдите подходящую сферу деятельности на основе ваших интересов и способностей.',
                'icon' => 'fas fa-user-tie',
                'color' => '#9b59b6',
                'duration' => '15 минут',
                'questions' => '40 вопросов',
                'category' => 'Карьера',
                'image' => null
            ],
            [
                'slug' => 'personality-test',
                'title' => 'Тип личности',
                'description' => 'Узнайте свой психологический тип личности по системе Майерс-Бриггс. Поймите свои сильные стороны и особенности характера.',
                'icon' => 'fas fa-palette',
                'color' => '#f39c12',
                'duration' => '12 минут',
                'questions' => '25 вопросов',
                'category' => 'Психология',
                'image' => null
            ],
            [
                'slug' => 'emotional-intelligence-test',
                'title' => 'Эмоциональный интеллект',
                'description' => 'Оцените свою способность понимать эмоции, управлять ими и эффективно взаимодействовать с окружающими людьми.',
                'icon' => 'fas fa-heart',
                'color' => '#e91e63',
                'duration' => '10 минут',
                'questions' => '20 вопросов',
                'category' => 'Психология',
                'image' => null
            ]
        ]
    ],
    'academic' => [
        'title' => 'Академические тесты',
        'icon' => 'fas fa-graduation-cap',
        'color' => '#667eea',
        'tests' => [
            [
                'slug' => 'math-test',
                'title' => 'Математика',
                'description' => 'Проверьте свои знания по математике: алгебра, геометрия, арифметика. Подходит для учеников 9-11 классов.',
                'icon' => 'fas fa-calculator',
                'color' => '#3498db',
                'duration' => '25 минут',
                'questions' => '35 вопросов',
                'category' => 'Математика',
                'image' => null
            ],
            [
                'slug' => 'russian-test',
                'title' => 'Русский язык',
                'description' => 'Тест по русской грамматике, орфографии и пунктуации. Проверьте свою грамотность и знание родного языка.',
                'icon' => 'fas fa-spell-check',
                'color' => '#e67e22',
                'duration' => '20 минут',
                'questions' => '30 вопросов',
                'category' => 'Русский язык',
                'image' => null
            ],
            [
                'slug' => 'geography-test',
                'title' => 'География',
                'description' => 'Проверьте знания по географии России и мира: столицы, реки, горы, климат и природные зоны.',
                'icon' => 'fas fa-globe',
                'color' => '#27ae60',
                'duration' => '18 минут',
                'questions' => '25 вопросов',
                'category' => 'География',
                'image' => null
            ],
            [
                'slug' => 'history-test',
                'title' => 'История',
                'description' => 'Проверьте свои знания по истории России и мира. Вопросы охватывают основные исторические периоды и события.',
                'icon' => 'fas fa-landmark',
                'color' => '#c0392b',
                'duration' => '22 минут',
                'questions' => '30 вопросов',
                'category' => 'История',
                'image' => null
            ]
        ]
    ],
    'science' => [
        'title' => 'Естественные науки',
        'icon' => 'fas fa-flask',
        'color' => '#667eea',
        'tests' => [
            [
                'slug' => 'physics-test',
                'title' => 'Физика',
                'description' => 'Тест по основам физики: механика, термодинамика, электричество и оптика для старшеклассников.',
                'icon' => 'fas fa-atom',
                'color' => '#8e44ad',
                'duration' => '30 минут',
                'questions' => '25 вопросов',
                'category' => 'Физика',
                'image' => null
            ],
            [
                'slug' => 'chemistry-test',
                'title' => 'Химия',
                'description' => 'Проверьте знания по химии: органическая и неорганическая химия, периодическая система, реакции.',
                'icon' => 'fas fa-flask',
                'color' => '#16a085',
                'duration' => '25 минут',
                'questions' => '30 вопросов',
                'category' => 'Химия',
                'image' => null
            ],
            [
                'slug' => 'biology-test',
                'title' => 'Биология',
                'description' => 'Тест по биологии: анатомия человека, ботаника, зоология и основы генетики.',
                'icon' => 'fas fa-dna',
                'color' => '#2ecc71',
                'duration' => '22 минут',
                'questions' => '28 вопросов',
                'category' => 'Биология',
                'image' => null
            ],
            [
                'slug' => 'astronomy-test',
                'title' => 'Астрономия',
                'description' => 'Изучите космос: планеты, звезды, галактики. Проверьте свои знания о Вселенной и космических явлениях.',
                'icon' => 'fas fa-meteor',
                'color' => '#34495e',
                'duration' => '15 минут',
                'questions' => '20 вопросов',
                'category' => 'Астрономия',
                'image' => null
            ]
        ]
    ],
    'languages' => [
        'title' => 'Иностранные языки',
        'icon' => 'fas fa-language',
        'color' => '#667eea',
        'tests' => [
            [
                'slug' => 'english-test',
                'title' => 'Английский язык',
                'description' => 'Проверьте свой уровень владения английским языком: грамматика, лексика, понимание текстов.',
                'icon' => 'fas fa-flag-usa',
                'color' => '#3498db',
                'duration' => '25 минут',
                'questions' => '40 вопросов',
                'category' => 'Английский',
                'image' => null
            ],
            [
                'slug' => 'german-test',
                'title' => 'Немецкий язык',
                'description' => 'Оцените знания немецкого языка: базовая грамматика, словарный запас и понимание простых текстов.',
                'icon' => 'fas fa-beer',
                'color' => '#f1c40f',
                'duration' => '20 минут',
                'questions' => '30 вопросов',
                'category' => 'Немецкий',
                'image' => null
            ],
            [
                'slug' => 'french-test',
                'title' => 'Французский язык',
                'description' => 'Тест по французскому языку для начинающих и продолжающих. Проверьте грамматику и лексику.',
                'icon' => 'fas fa-wine-glass',
                'color' => '#e74c3c',
                'duration' => '20 минут',
                'questions' => '30 вопросов',
                'category' => 'Французский',
                'image' => null
            ],
            [
                'slug' => 'spanish-test',
                'title' => 'Испанский язык',
                'description' => 'Проверьте базовые знания испанского языка: времена глаголов, лексика, простые фразы.',
                'icon' => 'fas fa-guitar',
                'color' => '#e67e22',
                'duration' => '18 минут',
                'questions' => '25 вопросов',
                'category' => 'Испанский',
                'image' => null
            ]
        ]
    ]
];

// Get category filter from URL
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : 'all';

// Collect all tests for display
$allTests = [];
foreach ($tests as $categoryKey => $category) {
    foreach ($category['tests'] as $test) {
        $test['category_key'] = $categoryKey;
        $test['category_title'] = $category['title'];
        $allTests[] = $test;
    }
}

// Filter tests if category is selected
if ($categoryFilter !== 'all') {
    $allTests = array_filter($allTests, function($test) use ($categoryFilter) {
        return $test['category_key'] === $categoryFilter;
    });
}
?>

<!-- Test Type Navigation using reusable component -->
<?php 
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/category-navigation.php';

$testNavItems = [
    ['title' => 'Все тесты', 'url' => '#', 'data-category' => 'all'],
    ['title' => 'Интеллект и психология', 'url' => '#', 'data-category' => 'intellectual'],
    ['title' => 'Академические', 'url' => '#', 'data-category' => 'academic'],
    ['title' => 'Естественные науки', 'url' => '#', 'data-category' => 'science'],
    ['title' => 'Иностранные языки', 'url' => '#', 'data-category' => 'languages']
];
?>

<div class="test-navigation" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 30px; padding: 0 20px;">
    <?php
    $activeStyle = "padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: 500; transition: all 0.3s ease; background: #28a745; color: white; cursor: pointer;";
    $inactiveStyle = "padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: 400; transition: all 0.3s ease; background: var(--surface, #ffffff); color: var(--text-primary, #333); border: 1px solid var(--border-color, #e2e8f0); cursor: pointer;";
    
    foreach ($testNavItems as $item) {
        $isActive = ($categoryFilter === $item['data-category']);
        $class = 'category-btn' . ($isActive ? ' active' : '');
        $style = $isActive ? $activeStyle : $inactiveStyle;
        
        echo '<a href="' . $item['url'] . '" data-category="' . $item['data-category'] . '" class="' . $class . '" style="' . $style . '">';
        echo htmlspecialchars($item['title']);
        echo '</a>';
    }
    ?>
</div>

<!-- Tests Grid -->
<div class="tests-grid">
    <?php if (!empty($allTests)): ?>
        <?php 
        // Show all tests, not filtered by PHP
        $allTestsUnfiltered = [];
        foreach ($tests as $categoryKey => $category) {
            foreach ($category['tests'] as $test) {
                $test['category_key'] = $categoryKey;
                $test['category_title'] = $category['title'];
                $allTestsUnfiltered[] = $test;
            }
        }
        ?>
        <?php foreach ($allTestsUnfiltered as $test): ?>
            <div class="test-item" data-category="<?= htmlspecialchars($test['category_key']) ?>">
                <?php renderTestCard($test); ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <h3>Тесты не найдены</h3>
            <p>В выбранной категории пока нет тестов.</p>
        </div>
    <?php endif; ?>
</div>

<style>
/* Ensure proper background during page transitions */
body {
    background-color: var(--background, #ffffff);
    transition: background-color 0.3s ease;
}
[data-theme="dark"] body {
    background-color: var(--background-dark, #1a1a1a);
}

/* Navigation button hover effects */
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
}

.category-btn.active:hover {
    background: #218838 !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
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

/* Prevent flash of unstyled content */
.tests-grid {
    min-height: 200px;
    background-color: transparent;
}

/* Test item wrapper for filtering */
.test-item {
    transition: opacity 0.3s ease, transform 0.3s ease;
}
.test-item.hidden {
    display: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryButtons = document.querySelectorAll('.category-btn');
    const testItems = document.querySelectorAll('.test-item');
    
    // Active button styles
    const activeStyle = "padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: 500; transition: all 0.3s ease; background: #28a745; color: white; cursor: pointer;";
    const inactiveStyle = "padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: 400; transition: all 0.3s ease; background: var(--surface, #ffffff); color: var(--text-primary, #333); border: 1px solid var(--border-color, #e2e8f0); cursor: pointer;";
    
    categoryButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const category = this.dataset.category;
            
            // Update active button
            categoryButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.setAttribute('style', inactiveStyle);
            });
            this.classList.add('active');
            this.setAttribute('style', activeStyle);
            
            // Filter tests
            testItems.forEach(item => {
                if (category === 'all' || item.dataset.category === category) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
            
            // Update URL without reload
            const url = category === 'all' ? '/tests' : `/tests?category=${category}`;
            window.history.pushState({category: category}, '', url);
        });
    });
    
    // Handle back/forward buttons
    window.addEventListener('popstate', function(e) {
        const category = e.state ? e.state.category : 'all';
        const button = document.querySelector(`[data-category="${category}"]`);
        if (button) {
            button.click();
        }
    });
});
</script>