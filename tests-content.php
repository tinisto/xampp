<?php
// Include reusable components
require_once $_SERVER['DOCUMENT_ROOT'] . '/components/card-component.php';

// Available tests - using modern test interface
$availableTests = [
    [
        'id' => 1,
        'title' => 'Тест на IQ',
        'description' => 'Проверьте свой уровень интеллекта с помощью классического IQ теста.',
        'category' => 'career',
        'category_name' => 'Профориентация',
        'duration' => '30 мин',
        'questions' => '40 вопросов',
        'difficulty' => 'Средний',
        'url' => '/iq-test',
        'image' => '/images/tests/iq-test.jpg'
    ],
    [
        'id' => 2,
        'title' => 'Тест на профпригодность',
        'description' => 'Определите свои профессиональные склонности и найдите подходящую карьеру.',
        'category' => 'career',
        'category_name' => 'Профориентация',
        'duration' => '20 мин',
        'questions' => '30 вопросов',
        'difficulty' => 'Легкий',
        'url' => '/aptitude-test',
        'image' => '/images/tests/aptitude-test.jpg'
    ],
    [
        'id' => 3,
        'title' => 'Математика ЕГЭ',
        'description' => 'Подготовка к единому государственному экзамену по математике.',
        'category' => 'ege',
        'category_name' => 'ЕГЭ',
        'duration' => '45 мин',
        'questions' => '25 вопросов',
        'difficulty' => 'Сложный',
        'url' => '#', // 'url' => '/math-test', // TODO: implement
        'image' => '/images/tests/math-test.jpg'
    ],
    [
        'id' => 4,
        'title' => 'Русский язык ЕГЭ',
        'description' => 'Тренировочный тест по русскому языку для подготовки к ЕГЭ.',
        'category' => 'ege',
        'category_name' => 'ЕГЭ',
        'duration' => '40 мин',
        'questions' => '30 вопросов',
        'difficulty' => 'Средний',
        'url' => '#', // 'url' => '/russian-test', // TODO: implement
        'image' => '/images/tests/russian-test.jpg'
    ]
];
?>

<div class="container">
    <style>
        /* Test card styling */
        .test-card {
            height: 220px;
            background: var(--bg-secondary, #1a1a2e);
            border: 1px solid var(--border-color, #2a2a3e);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }
        .test-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            border-color: var(--accent-primary, #667eea);
        }
        .test-card-img {
            height: 140px;
            overflow: hidden;
            position: relative;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .test-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.8;
        }
        .test-category-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }
        .test-card-body {
            padding: 1.25rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .test-card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-primary, #fff);
        }
        .test-card-description {
            font-size: 0.875rem;
            color: var(--text-secondary, #999);
            margin-bottom: 1rem;
            line-height: 1.5;
            flex-grow: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
        .test-card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            color: var(--text-muted, #666);
        }
        .test-card-difficulty {
            padding: 0.2rem 0.5rem;
            border-radius: 12px;
            background: rgba(102, 126, 234, 0.1);
            color: var(--accent-primary, #667eea);
            font-weight: 500;
        }
    </style>
    
    <div class="row">
        <?php foreach ($availableTests as $test): ?>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <a href="<?php echo htmlspecialchars($test['url']); ?>" class="text-decoration-none d-block">
                <div class="test-card">
                    <div class="test-card-img">
                        <span class="test-category-badge"><?php echo htmlspecialchars($test['category_name']); ?></span>
                        <?php if (!empty($test['image']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $test['image'])): ?>
                            <img src="<?php echo htmlspecialchars($test['image']); ?>" alt="<?php echo htmlspecialchars($test['title']); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="test-card-body">
                        <h5 class="test-card-title"><?php echo htmlspecialchars($test['title']); ?></h5>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>