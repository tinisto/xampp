<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-modern.php';

// Create enhanced test content
$testContent = '
<div class="container mt-4">
    <h1>Enhanced Theme Test Page</h1>
    
    <!-- Basic Theme Elements -->
    <div class="card mb-4">
        <h2>Surface Colors</h2>
        <div class="demo-grid">
            <div class="demo-item" style="background: var(--color-surface-primary);">Primary Surface</div>
            <div class="demo-item" style="background: var(--color-surface-secondary);">Secondary Surface</div>
            <div class="demo-item" style="background: var(--color-surface-tertiary);">Tertiary Surface</div>
        </div>
    </div>
    
    <div class="card mb-4">
        <h2>Text & Links</h2>
        <p style="color: var(--color-text-primary);">Primary Text Color</p>
        <p style="color: var(--color-text-secondary);">Secondary Text Color</p>
        <p style="color: var(--color-text-tertiary);">Tertiary Text Color</p>
        <p>
            <a href="#">Regular Link</a> | 
            <a href="#" style="color: var(--color-link-visited);">Visited Link</a>
        </p>
    </div>
    
    <!-- Post/News Cards -->
    <div class="card mb-4">
        <h2>Post/News Cards</h2>
        <div class="posts-grid">
            <!-- Post Card 1 -->
            <div class="post-card">
                <div class="post-badge">Новости</div>
                <div class="post-image-placeholder">
                    <i class="fas fa-image"></i>
                </div>
                <div class="post-content">
                    <h3 class="post-title">Изменения в ЕГЭ 2025: что нужно знать</h3>
                    <p class="post-excerpt">Министерство образования анонсировало важные изменения в структуре единого государственного экзамена...</p>
                    <div class="post-meta">
                        <span class="post-author"><i class="fas fa-user"></i> Мария Иванова</span>
                        <span class="post-date"><i class="fas fa-calendar"></i> 2 дня назад</span>
                    </div>
                </div>
            </div>
            
            <!-- Post Card 2 -->
            <div class="post-card">
                <div class="post-badge" style="background: #9333ea;">Мир увлечений</div>
                <div class="post-image-placeholder">
                    <i class="fas fa-image"></i>
                </div>
                <div class="post-content">
                    <h3 class="post-title">Топ-10 хобби для подростков в 2025 году</h3>
                    <p class="post-excerpt">Discover the most popular and engaging hobbies that teenagers are passionate about this year...</p>
                    <div class="post-meta">
                        <span class="post-author"><i class="fas fa-user"></i> Алексей Петров</span>
                        <span class="post-date"><i class="fas fa-calendar"></i> 5 дней назад</span>
                    </div>
                </div>
            </div>
            
            <!-- Post Card 3 -->
            <div class="post-card">
                <div class="post-badge" style="background: #3b82f6;">ЕГЭ</div>
                <div class="post-image-placeholder">
                    <i class="fas fa-image"></i>
                </div>
                <div class="post-content">
                    <h3 class="post-title">Как подготовиться к ЕГЭ по математике</h3>
                    <p class="post-excerpt">Эффективные стратегии и методики подготовки к профильному экзамену по математике...</p>
                    <div class="post-meta">
                        <span class="post-author"><i class="fas fa-user"></i> Елена Смирнова</span>
                        <span class="post-date"><i class="fas fa-calendar"></i> 1 неделю назад</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Test Cards -->
    <div class="card mb-4">
        <h2>Test Cards</h2>
        <div class="tests-grid">
            <!-- Test Card 1 -->
            <div class="test-card">
                <div class="test-icon" style="background: var(--color-primary);">
                    <i class="fas fa-brain"></i>
                </div>
                <h3 class="test-title">Тест на IQ</h3>
                <p class="test-description">Проверьте свой уровень интеллекта с помощью классического теста</p>
                <div class="test-stats">
                    <span><i class="fas fa-clock"></i> 30 мин</span>
                    <span><i class="fas fa-question-circle"></i> 40 вопросов</span>
                </div>
                <button class="btn btn-primary w-100">Начать тест</button>
            </div>
            
            <!-- Test Card 2 -->
            <div class="test-card">
                <div class="test-icon" style="background: #f59e0b;">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3 class="test-title">ЕГЭ по Математике</h3>
                <p class="test-description">Пробный вариант ЕГЭ по математике профильного уровня</p>
                <div class="test-stats">
                    <span><i class="fas fa-clock"></i> 235 мин</span>
                    <span><i class="fas fa-question-circle"></i> 18 заданий</span>
                </div>
                <button class="btn btn-primary w-100">Начать тест</button>
            </div>
            
            <!-- Test Card 3 -->
            <div class="test-card">
                <div class="test-icon" style="background: #8b5cf6;">
                    <i class="fas fa-user"></i>
                </div>
                <h3 class="test-title">Тест личности</h3>
                <p class="test-description">Узнайте свой тип личности по методике MBTI</p>
                <div class="test-stats">
                    <span><i class="fas fa-clock"></i> 15 мин</span>
                    <span><i class="fas fa-question-circle"></i> 60 вопросов</span>
                </div>
                <button class="btn btn-primary w-100">Начать тест</button>
            </div>
        </div>
    </div>
    
    <!-- Interactive Elements -->
    <div class="card mb-4">
        <h2>Interactive Elements</h2>
        <div class="demo-buttons">
            <button class="btn">Default Button</button>
            <button class="btn btn-primary">Primary Button</button>
            <button class="btn btn-success">Success Button</button>
            <a href="#" class="btn btn-link">Link Button</a>
        </div>
    </div>
</div>

<style>
/* Demo Styles */
.demo-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-top: 16px;
}

.demo-item {
    padding: 20px;
    border: 1px solid var(--color-border-primary);
    border-radius: 8px;
    text-align: center;
    transition: all var(--transition-normal);
}

/* Post Cards Grid */
.posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 24px;
    margin-top: 20px;
}

.post-card {
    background: var(--color-card-bg);
    border: 1px solid var(--color-border-primary);
    border-radius: 12px;
    overflow: hidden;
    transition: all var(--transition-normal);
    position: relative;
    cursor: pointer;
}

.post-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px var(--color-shadow-md);
    border-color: var(--color-primary);
}

.post-badge {
    position: absolute;
    top: 16px;
    left: 16px;
    background: var(--color-primary);
    color: white;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    z-index: 1;
}

.post-image-placeholder {
    width: 100%;
    height: 180px;
    background: var(--color-surface-secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-text-tertiary);
    font-size: 48px;
}

.post-content {
    padding: 20px;
}

.post-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 12px 0;
    color: var(--color-text-primary);
    line-height: 1.4;
}

.post-excerpt {
    color: var(--color-text-secondary);
    font-size: 14px;
    line-height: 1.6;
    margin: 0 0 16px 0;
}

.post-meta {
    display: flex;
    gap: 20px;
    font-size: 13px;
    color: var(--color-text-tertiary);
}

.post-meta i {
    margin-right: 4px;
}

/* Test Cards Grid */
.tests-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 24px;
    margin-top: 20px;
}

.test-card {
    background: var(--color-card-bg);
    border: 1px solid var(--color-border-primary);
    border-radius: 12px;
    padding: 24px;
    text-align: center;
    transition: all var(--transition-normal);
}

.test-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px var(--color-shadow-md);
    border-color: var(--color-primary);
}

.test-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    color: white;
}

.test-title {
    font-size: 20px;
    font-weight: 600;
    margin: 0 0 12px 0;
    color: var(--color-text-primary);
}

.test-description {
    color: var(--color-text-secondary);
    font-size: 14px;
    line-height: 1.6;
    margin: 0 0 20px 0;
}

.test-stats {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 20px;
    font-size: 13px;
    color: var(--color-text-tertiary);
}

.test-stats i {
    margin-right: 4px;
}

/* Buttons */
.demo-buttons {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 16px;
}

.btn-success {
    background: var(--color-success);
    color: white;
    border-color: var(--color-success);
}

.btn-success:hover {
    background: #218838;
    border-color: #1e7e34;
}

.btn-link {
    background: transparent;
    color: var(--color-link);
    border: none;
}

.btn-link:hover {
    color: var(--color-link-hover);
    background: var(--color-bg-hover);
}

.w-100 {
    width: 100%;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .posts-grid,
    .tests-grid {
        grid-template-columns: 1fr;
    }
    
    .demo-buttons {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
}
</style>
';

// Save the content file
file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/test-theme-content-enhanced.php', $testContent);

// Set up template config
$templateConfig = [
    'layoutType' => 'default',
    'darkMode' => true,
];

// Render the page
renderTemplate('Enhanced Theme Test', 'test-theme-content-enhanced.php', $templateConfig);
?>