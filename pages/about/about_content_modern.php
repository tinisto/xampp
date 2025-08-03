<?php 
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/page-header.php';
renderPageHeader('О проекте 11-классники', 'Помогаем выпускникам сделать правильный выбор');
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            
            <!-- Mission Section -->
            <section class="about-section">
                <div class="section-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h2 class="section-title">Наша миссия</h2>
                <p class="lead">
                    <strong>11klassniki.ru</strong> — это платформа поддержки выпускников школ, 
                    которая помогает сделать осознанный выбор будущей профессии и учебного заведения.
                </p>
            </section>

            <!-- What We Do Section -->
            <section class="about-section">
                <div class="section-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h2 class="section-title">Что мы делаем</h2>
                <div class="feature-grid">
                    <div class="feature-card">
                        <i class="fas fa-microphone feature-icon"></i>
                        <h3>Интервью с выпускниками</h3>
                        <p>Реальные истории одиннадцатиклассников из разных городов о выборе профессии и подготовке к ЕГЭ</p>
                        <a href="/category/11-klassniki" class="feature-link">Читать интервью →</a>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-university feature-icon"></i>
                        <h3>Советы абитуриентам</h3>
                        <p>Первокурсники делятся опытом поступления и первыми впечатлениями от учебы в вузах</p>
                        <a href="/category/abiturientam" class="feature-link">Полезные советы →</a>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-book-reader feature-icon"></i>
                        <h3>База учебных заведений</h3>
                        <p>Подробная информация о школах, колледжах и университетах по всей России</p>
                        <a href="/schools-all-regions" class="feature-link">Найти учебное заведение →</a>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-clipboard-check feature-icon"></i>
                        <h3>Онлайн тесты</h3>
                        <p>Проверьте свои знания и определите профессиональные склонности</p>
                        <a href="/tests" class="feature-link">Пройти тесты →</a>
                    </div>
                </div>
            </section>

            <!-- Values Section -->
            <section class="about-section">
                <div class="section-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <h2 class="section-title">Наши ценности</h2>
                <div class="values-list">
                    <div class="value-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4>Достоверность</h4>
                            <p>Только проверенная информация и реальные истории</p>
                        </div>
                    </div>
                    <div class="value-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4>Поддержка</h4>
                            <p>Создаем позитивное пространство для выпускников</p>
                        </div>
                    </div>
                    <div class="value-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4>Польза</h4>
                            <p>Фокусируемся на практической информации для принятия решений</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Quote Section -->
            <section class="about-section quote-section">
                <blockquote class="modern-quote">
                    <p>"Нам не дано предугадать, как слово наше отзовется"</p>
                    <cite>— Федор Тютчев</cite>
                </blockquote>
                <p class="text-center">
                    Мы верим, что качественный контент может вдохновлять и поддерживать, 
                    помогая выпускникам сосредоточиться на собственном будущем.
                </p>
            </section>

        </div>
    </div>
</div>

<style>
    .about-section {
        margin-bottom: 60px;
        text-align: center;
    }
    .section-icon {
        font-size: 48px;
        color: #667eea;
        margin-bottom: 20px;
    }
    .section-title {
        font-size: 32px;
        font-weight: 600;
        margin-bottom: 30px;
        color: var(--text-primary);
    }
    .feature-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        margin-top: 40px;
    }
    .feature-card {
        background: var(--card-bg);
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        text-align: center;
    }
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }
    .feature-icon {
        font-size: 36px;
        color: #667eea;
        margin-bottom: 20px;
    }
    .feature-card h3 {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 15px;
        color: var(--text-primary);
    }
    .feature-card p {
        color: var(--text-secondary);
        margin-bottom: 20px;
    }
    .feature-link {
        color: #667eea;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
    }
    .feature-link:hover {
        color: #764ba2;
    }
    .values-list {
        text-align: left;
        max-width: 600px;
        margin: 0 auto;
    }
    .value-item {
        display: flex;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 25px;
    }
    .value-item i {
        font-size: 24px;
        color: #28a745;
        flex-shrink: 0;
    }
    .value-item h4 {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 5px;
        color: var(--text-primary);
    }
    .value-item p {
        margin: 0;
        color: var(--text-secondary);
    }
    .quote-section {
        background: var(--bg-secondary);
        padding: 40px;
        border-radius: 12px;
        margin-top: 60px;
    }
    .modern-quote {
        font-size: 24px;
        font-style: italic;
        text-align: center;
        margin-bottom: 30px;
        color: var(--text-primary);
    }
    .modern-quote cite {
        display: block;
        font-size: 16px;
        font-style: normal;
        margin-top: 15px;
        color: var(--text-secondary);
    }
    
    /* Dark mode specific */
    [data-bs-theme="dark"] .feature-card {
        background: var(--card-bg);
    }
    [data-bs-theme="dark"] .quote-section {
        background: var(--card-bg);
    }
    
    /* Mobile styles */
    @media (max-width: 768px) {
        .about-hero {
            padding: 50px 0;
        }
        .about-title {
            font-size: 32px;
        }
        .about-subtitle {
            font-size: 18px;
        }
        .section-title {
            font-size: 24px;
        }
        .feature-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        .feature-card {
            box-shadow: none;
            border-radius: 0;
            border-bottom: 1px solid var(--border-color);
        }
        .quote-section {
            padding: 20px;
            border-radius: 0;
        }
        .modern-quote {
            font-size: 20px;
        }
    }
</style>