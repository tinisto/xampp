<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$pageTitle = 'Онлайн тесты - Проверьте свои знания';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - 11-классники</title>
    <link href="/css/unified-styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .test-category {
            margin-bottom: 40px;
        }
        .category-title {
            color: #333;
            margin-bottom: 30px;
            font-size: 24px;
            display: flex;
            align-items: center;
        }
        .test-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .test-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .test-icon {
            font-size: 48px;
            margin-bottom: 20px;
            text-align: center;
        }
        .test-title {
            color: #333;
            margin-bottom: 15px;
            font-size: 20px;
            text-align: center;
        }
        .test-description {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
            flex-grow: 1;
        }
        .test-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            color: #888;
            font-size: 14px;
        }
        .test-btn {
            background: #28a745;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            text-align: center;
            transition: background 0.3s ease;
            display: inline-block;
        }
        .test-btn:hover {
            background: #218838;
            color: white;
            text-decoration: none;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -15px;
        }
        .col-lg-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
            padding: 0 15px;
        }
        @media (max-width: 768px) {
            .col-lg-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
        @media (max-width: 992px) and (min-width: 769px) {
            .col-lg-4 {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }
    </style>
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
    
    <?php 
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/page-section-header.php';
    renderPageSectionHeader([
        'title' => 'Онлайн тесты',
        'showSearch' => false
    ]);
    ?>
    
    <main style="padding: 40px 0;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; padding: 20px 0;">
                <p style="font-size: 1.1rem; color: #666; margin-bottom: 40px;">Проверьте свои знания и навыки с помощью наших интерактивных тестов</p>
            </div>

            <!-- IQ and Psychology Tests -->
            <div class="test-category">
                <h2 class="category-title">
                    <i class="fas fa-brain" style="color: #667eea; margin-right: 15px;"></i>
                    Тесты интеллекта и психологии
                </h2>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="test-card">
                            <div class="test-icon" style="color: #e74c3c;">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                            <h3 class="test-title">IQ Тест</h3>
                            <p class="test-description">Классический тест на определение уровня интеллекта. Включает логические задачи, математические вопросы и задания на пространственное мышление.</p>
                            <div class="test-meta">
                                <span><i class="fas fa-clock"></i> 20 минут</span>
                                <span><i class="fas fa-question"></i> 30 вопросов</span>
                            </div>
                            <a href="/test/iq-test" class="test-btn">Пройти тест</a>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="test-card">
                            <div class="test-icon" style="color: #9b59b6;">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h3 class="test-title">Профориентация</h3>
                            <p class="test-description">Определите свои профессиональные склонности и найдите подходящую сферу деятельности на основе ваших интересов и способностей.</p>
                            <div class="test-meta">
                                <span><i class="fas fa-clock"></i> 15 минут</span>
                                <span><i class="fas fa-question"></i> 40 вопросов</span>
                            </div>
                            <a href="/test/career-test" class="test-btn">Пройти тест</a>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="test-card">
                            <div class="test-icon" style="color: #f39c12;">
                                <i class="fas fa-palette"></i>
                            </div>
                            <h3 class="test-title">Тип личности</h3>
                            <p class="test-description">Узнайте свой психологический тип личности по системе Майерс-Бриггс. Поймите свои сильные стороны и особенности характера.</p>
                            <div class="test-meta">
                                <span><i class="fas fa-clock"></i> 12 минут</span>
                                <span><i class="fas fa-question"></i> 25 вопросов</span>
                            </div>
                            <a href="/test/personality-test" class="test-btn">Пройти тест</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Tests -->
            <div class="test-category">
                <h2 class="category-title">
                    <i class="fas fa-graduation-cap" style="color: #667eea; margin-right: 15px;"></i>
                    Академические тесты
                </h2>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="test-card">
                            <div class="test-icon" style="color: #3498db;">
                                <i class="fas fa-calculator"></i>
                            </div>
                            <h3 class="test-title">Математика</h3>
                            <p class="test-description">Проверьте свои знания по математике: алгебра, геометрия, арифметика. Подходит для учеников 9-11 классов.</p>
                            <div class="test-meta">
                                <span><i class="fas fa-clock"></i> 25 минут</span>
                                <span><i class="fas fa-question"></i> 35 вопросов</span>
                            </div>
                            <a href="/test/math-test" class="test-btn">Пройти тест</a>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="test-card">
                            <div class="test-icon" style="color: #e67e22;">
                                <i class="fas fa-spell-check"></i>
                            </div>
                            <h3 class="test-title">Русский язык</h3>
                            <p class="test-description">Тест по русской грамматике, орфографии и пунктуации. Проверьте свою грамотность и знание родного языка.</p>
                            <div class="test-meta">
                                <span><i class="fas fa-clock"></i> 20 минут</span>
                                <span><i class="fas fa-question"></i> 30 вопросов</span>
                            </div>
                            <a href="/test/russian-test" class="test-btn">Пройти тест</a>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="test-card">
                            <div class="test-icon" style="color: #1abc9c;">
                                <i class="fas fa-atom"></i>
                            </div>
                            <h3 class="test-title">Физика</h3>
                            <p class="test-description">Тестирование знаний по физике: механика, электричество, оптика. Для старшеклассников и абитуриентов.</p>
                            <div class="test-meta">
                                <span><i class="fas fa-clock"></i> 30 минут</span>
                                <span><i class="fas fa-question"></i> 25 вопросов</span>
                            </div>
                            <a href="/test/physics-test" class="test-btn">Пройти тест</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php'; ?>
</body>
</html>