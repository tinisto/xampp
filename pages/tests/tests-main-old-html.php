<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

$pageTitle = 'Онлайн тесты - Проверьте свои знания';
$metaD = 'Пройдите бесплатные онлайн тесты по различным предметам: IQ тест, математика, русский язык, профориентация и многое другое';
$metaK = 'онлайн тесты, IQ тест, тесты знаний, профориентация, математика, русский язык';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - 11-классники</title>
    <meta name="description" content="<?php echo htmlspecialchars($metaD); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($metaK); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .main-content {
            flex: 1;
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        .hero-title {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .hero-subtitle {
            font-size: 20px;
            opacity: 0.9;
            margin-bottom: 30px;
        }
        .test-category {
            margin-bottom: 50px;
        }
        .category-title {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 30px;
            color: #333;
            text-align: center;
        }
        .test-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .test-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }
        .test-icon {
            font-size: 48px;
            margin-bottom: 20px;
            text-align: center;
        }
        .test-title {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }
        .test-description {
            color: #666;
            margin-bottom: 20px;
            flex: 1;
        }
        .test-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 14px;
            color: #888;
        }
        .test-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 500;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
        }
        .test-btn:hover {
            color: white;
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        .stats-section {
            background: #667eea;
            color: white;
            padding: 60px 0;
            text-align: center;
        }
        .stat-item {
            margin-bottom: 30px;
        }
        .stat-number {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .stat-label {
            font-size: 18px;
            opacity: 0.9;
        }
        .features-section {
            padding: 80px 0;
            background: white;
        }
        .feature-item {
            text-align: center;
            margin-bottom: 40px;
        }
        .feature-icon {
            font-size: 64px;
            color: #667eea;
            margin-bottom: 20px;
        }
        .feature-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .feature-description {
            color: #666;
            font-size: 16px;
        }
        @media (max-width: 768px) {
            .hero-title {
                font-size: 36px;
            }
            .hero-subtitle {
                font-size: 18px;
            }
            .test-card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
    
    <main class="main-content">
        <!-- Hero Section -->
        <div class="hero-section">
            <div class="container">
                <h1 class="hero-title">Онлайн тесты</h1>
                <p class="hero-subtitle">Проверьте свои знания и навыки с помощью наших интерактивных тестов</p>
                <a href="#tests" class="test-btn">Начать тестирование</a>
            </div>
        </div>

        <!-- Tests Section -->
        <div class="container py-5" id="tests">
            <!-- IQ and Psychology Tests -->
            <div class="test-category">
                <h2 class="category-title">
                    <i class="fas fa-brain me-3" style="color: #667eea;"></i>
                    Тесты интеллекта и психологии
                </h2>
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="test-card">
                            <div class="test-icon" style="color: #e74c3c;">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                            <h3 class="test-title">IQ Тест</h3>
                            <p class="test-description">Классический тест на определение уровня интеллекта. Включает логические задачи, математические вопросы и задания на пространственное мышление.</p>
                            <div class="test-meta">
                                <span><i class="fas fa-clock me-1"></i>20 минут</span>
                                <span><i class="fas fa-question me-1"></i>30 вопросов</span>
                            </div>
                            <a href="/test/iq-test" class="test-btn">Пройти тест</a>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6">
                        <div class="test-card">
                            <div class="test-icon" style="color: #9b59b6;">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h3 class="test-title">Профориентация</h3>
                            <p class="test-description">Определите свои профессиональные склонности и найдите подходящую сферу деятельности на основе ваших интересов и способностей.</p>
                            <div class="test-meta">
                                <span><i class="fas fa-clock me-1"></i>15 минут</span>
                                <span><i class="fas fa-question me-1"></i>40 вопросов</span>
                            </div>
                            <a href="/test/career-test" class="test-btn">Пройти тест</a>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6">
                        <div class="test-card">
                            <div class="test-icon" style="color: #f39c12;">
                                <i class="fas fa-palette"></i>
                            </div>
                            <h3 class="test-title">Тип личности</h3>
                            <p class="test-description">Узнайте свой психологический тип личности по системе Майерс-Бриггс. Поймите свои сильные стороны и особенности характера.</p>
                            <div class="test-meta">
                                <span><i class="fas fa-clock me-1"></i>12 минут</span>
                                <span><i class="fas fa-question me-1"></i>25 вопросов</span>
                            </div>
                            <a href="/test/personality-test" class="test-btn">Пройти тест</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Tests -->
            <div class="test-category">
                <h2 class="category-title">
                    <i class="fas fa-graduation-cap me-3" style="color: #667eea;"></i>
                    Академические тесты
                </h2>
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="test-card">
                            <div class="test-icon" style="color: #3498db;">
                                <i class="fas fa-calculator"></i>
                            </div>
                            <h3 class="test-title">Математика</h3>
                            <p class="test-description">Проверьте свои знания по математике: алгебра, геометрия, арифметика. Подходит для учеников 9-11 классов.</p>
                            <div class="test-meta">
                                <span><i class="fas fa-clock me-1"></i>25 минут</span>
                                <span><i class="fas fa-question me-1"></i>35 вопросов</span>
                            </div>
                            <a href="/test/math-test" class="test-btn">Пройти тест</a>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6">
                        <div class="test-card">
                            <div class="test-icon" style="color: #e67e22;">
                                <i class="fas fa-spell-check"></i>
                            </div>
                            <h3 class="test-title">Русский язык</h3>
                            <p class="test-description">Тест по русской грамматике, орфографии и пунктуации. Проверьте свою грамотность и знание родного языка.</p>
                            <div class="test-meta">
                                <span><i class="fas fa-clock me-1"></i>20 минут</span>
                                <span><i class="fas fa-question me-1"></i>30 вопросов</span>
                            </div>
                            <a href="/test/russian-test" class="test-btn">Пройти тест</a>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6">
                        <div class="test-card">
                            <div class="test-icon" style="color: #27ae60;">
                                <i class="fas fa-globe"></i>
                            </div>
                            <h3 class="test-title">География</h3>
                            <p class="test-description">Проверьте знания по географии России и мира: столицы, реки, горы, климат и природные зоны.</p>
                            <div class="test-meta">
                                <span><i class="fas fa-clock me-1"></i>18 минут</span>
                                <span><i class="fas fa-question me-1"></i>25 вопросов</span>
                            </div>
                            <a href="/test/geography-test" class="test-btn">Пройти тест</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Science Tests -->
            <div class="test-category">
                <h2 class="category-title">
                    <i class="fas fa-flask me-3" style="color: #667eea;"></i>
                    Естественные науки
                </h2>
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="test-card">
                            <div class="test-icon" style="color: #8e44ad;">
                                <i class="fas fa-atom"></i>
                            </div>
                            <h3 class="test-title">Физика</h3>
                            <p class="test-description">Тест по основам физики: механика, термодинамика, электричество и оптика для старшеклассников.</p>
                            <div class="test-meta">
                                <span><i class="fas fa-clock me-1"></i>30 минут</span>
                                <span><i class="fas fa-question me-1"></i>25 вопросов</span>
                            </div>
                            <a href="/test/physics-test" class="test-btn">Пройти тест</a>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6">
                        <div class="test-card">
                            <div class="test-icon" style="color: #16a085;">
                                <i class="fas fa-flask"></i>
                            </div>
                            <h3 class="test-title">Химия</h3>
                            <p class="test-description">Проверьте знания по химии: органическая и неорганическая химия, периодическая система, реакции.</p>
                            <div class="test-meta">
                                <span><i class="fas fa-clock me-1"></i>25 минут</span>
                                <span><i class="fas fa-question me-1"></i>30 вопросов</span>
                            </div>
                            <a href="/test/chemistry-test" class="test-btn">Пройти тест</a>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6">
                        <div class="test-card">
                            <div class="test-icon" style="color: #2ecc71;">
                                <i class="fas fa-dna"></i>
                            </div>
                            <h3 class="test-title">Биология</h3>
                            <p class="test-description">Тест по биологии: анатомия человека, ботаника, зоология и основы генетики.</p>
                            <div class="test-meta">
                                <span><i class="fas fa-clock me-1"></i>22 минут</span>
                                <span><i class="fas fa-question me-1"></i>28 вопросов</span>
                            </div>
                            <a href="/test/biology-test" class="test-btn">Пройти тест</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="stats-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-number">50,000+</div>
                            <div class="stat-label">Пройденных тестов</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-number">12</div>
                            <div class="stat-label">Видов тестов</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-number">500+</div>
                            <div class="stat-label">Уникальных вопросов</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-number">15,000+</div>
                            <div class="stat-label">Активных пользователей</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="features-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <div class="feature-item">
                            <i class="fas fa-chart-line feature-icon"></i>
                            <h3 class="feature-title">Детальная статистика</h3>
                            <p class="feature-description">Получите подробный анализ ваших результатов с объяснениями правильных ответов</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-item">
                            <i class="fas fa-mobile-alt feature-icon"></i>
                            <h3 class="feature-title">Мобильная версия</h3>
                            <p class="feature-description">Проходите тесты с любого устройства - компьютера, планшета или смартфона</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-item">
                            <i class="fas fa-certificate feature-icon"></i>
                            <h3 class="feature-title">Сертификаты</h3>
                            <p class="feature-description">Получите цифровой сертификат по итогам успешного прохождения теста</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>