<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-modern.php';

// Create comprehensive theme reference content
$testContent = '
<div class="container mt-4">
    <h1>Theme System Reference Guide</h1>
    <p class="text-secondary">Complete reference for all theme components and mobile variants</p>
    
    <!-- Quick Navigation -->
    <div class="card mb-4">
        <h2>Quick Navigation</h2>
        <div class="quick-nav">
            <a href="#colors" class="btn">Colors</a>
            <a href="#typography" class="btn">Typography</a>
            <a href="#cards" class="btn">Cards</a>
            <a href="#tests" class="btn">Tests</a>
            <a href="#forms" class="btn">Forms</a>
            <a href="#mobile" class="btn">Mobile Views</a>
        </div>
    </div>
    
    <!-- Color System -->
    <div id="colors" class="card mb-4">
        <h2>Color System</h2>
        <div class="demo-grid">
            <div class="demo-item" style="background: var(--color-surface-primary);">
                <strong>Surface Primary</strong><br>
                <small>--color-surface-primary</small>
            </div>
            <div class="demo-item" style="background: var(--color-surface-secondary);">
                <strong>Surface Secondary</strong><br>
                <small>--color-surface-secondary</small>
            </div>
            <div class="demo-item" style="background: var(--color-surface-tertiary);">
                <strong>Surface Tertiary</strong><br>
                <small>--color-surface-tertiary</small>
            </div>
            <div class="demo-item" style="background: var(--color-primary); color: white;">
                <strong>Primary</strong><br>
                <small>--color-primary</small>
            </div>
        </div>
    </div>
    
    <!-- Typography -->
    <div id="typography" class="card mb-4">
        <h2>Typography</h2>
        <h1>Heading 1</h1>
        <h2>Heading 2</h2>
        <h3>Heading 3</h3>
        <h4>Heading 4</h4>
        <p class="lead">Lead paragraph - larger text for emphasis</p>
        <p>Regular paragraph with <a href="#">inline link</a> and <strong>bold text</strong>.</p>
        <p class="text-secondary">Secondary text color for less important content</p>
        <p class="text-tertiary">Tertiary text color for metadata</p>
    </div>
    
    <!-- Post/News Cards -->
    <div id="cards" class="card mb-4">
        <h2>Post/News Cards</h2>
        
        <!-- Desktop Grid -->
        <h3>Desktop View (4 columns)</h3>
        <div class="posts-grid desktop-only">
            <div class="post-card">
                <div class="post-badge">Новости</div>
                <div class="post-image-placeholder">
                    <i class="fas fa-image"></i>
                </div>
                <div class="post-content">
                    <h3 class="post-title">Изменения в ЕГЭ 2025</h3>
                    <p class="post-excerpt">Краткое описание новости...</p>
                    <div class="post-meta">
                        <span><i class="fas fa-eye"></i> 1.2K</span>
                        <span><i class="fas fa-calendar"></i> 2 дня</span>
                    </div>
                </div>
            </div>
            <div class="post-card">
                <div class="post-badge" style="background: #9333ea;">Мир увлечений</div>
                <div class="post-image-placeholder">
                    <i class="fas fa-image"></i>
                </div>
                <div class="post-content">
                    <h3 class="post-title">Топ-10 хобби для подростков</h3>
                    <p class="post-excerpt">Самые популярные увлечения...</p>
                    <div class="post-meta">
                        <span><i class="fas fa-eye"></i> 856</span>
                        <span><i class="fas fa-calendar"></i> 5 дней</span>
                    </div>
                </div>
            </div>
            <div class="post-card">
                <div class="post-badge" style="background: #3b82f6;">ЕГЭ</div>
                <div class="post-image-placeholder">
                    <i class="fas fa-image"></i>
                </div>
                <div class="post-content">
                    <h3 class="post-title">Подготовка к ЕГЭ по математике</h3>
                    <p class="post-excerpt">Эффективные методики...</p>
                    <div class="post-meta">
                        <span><i class="fas fa-eye"></i> 2.3K</span>
                        <span><i class="fas fa-calendar"></i> 1 неделя</span>
                    </div>
                </div>
            </div>
            <div class="post-card">
                <div class="post-badge" style="background: #ef4444;">Олимпиады</div>
                <div class="post-image-placeholder">
                    <i class="fas fa-image"></i>
                </div>
                <div class="post-content">
                    <h3 class="post-title">Всероссийская олимпиада</h3>
                    <p class="post-excerpt">Регистрация открыта...</p>
                    <div class="post-meta">
                        <span><i class="fas fa-eye"></i> 567</span>
                        <span><i class="fas fa-calendar"></i> 3 дня</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobile View -->
        <h3 class="mt-4">Mobile View (1 column)</h3>
        <div class="mobile-demo">
            <div class="post-card-mobile">
                <div class="post-mobile-header">
                    <div class="post-badge">Новости</div>
                    <div class="post-meta">2 дня назад</div>
                </div>
                <h3 class="post-title">Изменения в ЕГЭ 2025: что нужно знать</h3>
                <p class="post-excerpt">Министерство образования анонсировало важные изменения...</p>
                <div class="post-mobile-footer">
                    <span><i class="fas fa-eye"></i> 1.2K просмотров</span>
                    <a href="#" class="btn btn-sm">Читать</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Test Components -->
    <div id="tests" class="card mb-4">
        <h2>Test Components</h2>
        
        <!-- Test Selection Cards -->
        <h3>Test Selection Cards</h3>
        <div class="tests-grid">
            <div class="test-card">
                <div class="test-icon" style="background: var(--color-primary);">
                    <i class="fas fa-brain"></i>
                </div>
                <h3 class="test-title">Тест на IQ</h3>
                <p class="test-description">Проверьте свой уровень интеллекта</p>
                <div class="test-stats">
                    <span><i class="fas fa-clock"></i> 30 мин</span>
                    <span><i class="fas fa-question-circle"></i> 40 вопросов</span>
                </div>
                <button class="btn btn-primary w-100">Начать тест</button>
            </div>
        </div>
        
        <!-- Test Question Card -->
        <h3 class="mt-4">Test Question Card</h3>
        <div class="question-card">
            <div class="question-header">
                <span class="question-number">Вопрос 5 из 20</span>
                <span class="question-timer"><i class="fas fa-clock"></i> 2:45</span>
            </div>
            <div class="question-progress">
                <div class="progress-bar" style="width: 25%;"></div>
            </div>
            <div class="question-content">
                <h3 class="question-text">Какая столица Франции?</h3>
                <div class="question-image-placeholder">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
            </div>
            <div class="answers-list">
                <label class="answer-option">
                    <input type="radio" name="answer" value="a">
                    <span class="answer-letter">A</span>
                    <span class="answer-text">Лондон</span>
                </label>
                <label class="answer-option">
                    <input type="radio" name="answer" value="b">
                    <span class="answer-letter">B</span>
                    <span class="answer-text">Париж</span>
                </label>
                <label class="answer-option correct">
                    <input type="radio" name="answer" value="c" checked>
                    <span class="answer-letter">C</span>
                    <span class="answer-text">Париж</span>
                    <i class="fas fa-check-circle"></i>
                </label>
                <label class="answer-option incorrect">
                    <input type="radio" name="answer" value="d">
                    <span class="answer-letter">D</span>
                    <span class="answer-text">Рим</span>
                    <i class="fas fa-times-circle"></i>
                </label>
            </div>
            <div class="question-footer">
                <button class="btn">Пропустить</button>
                <button class="btn btn-primary">Следующий вопрос</button>
            </div>
        </div>
        
        <!-- Mobile Test Question -->
        <h3 class="mt-4">Mobile Test Question</h3>
        <div class="mobile-demo">
            <div class="question-card-mobile">
                <div class="question-mobile-header">
                    <span>5/20</span>
                    <div class="progress-circle">
                        <svg width="40" height="40">
                            <circle cx="20" cy="20" r="18" fill="none" stroke="var(--color-surface-tertiary)" stroke-width="4"/>
                            <circle cx="20" cy="20" r="18" fill="none" stroke="var(--color-primary)" stroke-width="4" 
                                stroke-dasharray="113" stroke-dashoffset="85" transform="rotate(-90 20 20)"/>
                        </svg>
                        <span class="timer">2:45</span>
                    </div>
                </div>
                <h3 class="question-text">Какая столица Франции?</h3>
                <div class="answers-mobile">
                    <button class="answer-mobile">A. Лондон</button>
                    <button class="answer-mobile selected">B. Париж</button>
                    <button class="answer-mobile">C. Берлин</button>
                    <button class="answer-mobile">D. Рим</button>
                </div>
                <button class="btn btn-primary w-100">Следующий</button>
            </div>
        </div>
    </div>
    
    <!-- Forms -->
    <div id="forms" class="card mb-4">
        <h2>Form Elements</h2>
        <form>
            <div class="form-group">
                <label>Text Input</label>
                <input type="text" class="form-control" placeholder="Enter text...">
            </div>
            <div class="form-group">
                <label>Select</label>
                <select class="form-control">
                    <option>Choose option...</option>
                    <option>Option 1</option>
                    <option>Option 2</option>
                </select>
            </div>
            <div class="form-group">
                <label>Textarea</label>
                <textarea class="form-control" rows="3" placeholder="Enter description..."></textarea>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox">
                    <span>I agree to terms</span>
                </label>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
    
    <!-- Mobile Variants -->
    <div id="mobile" class="card mb-4">
        <h2>Mobile Component Variants</h2>
        <div class="mobile-variants">
            <div class="mobile-demo">
                <h4>Mobile Navigation</h4>
                <div class="mobile-nav">
                    <a href="#" class="mobile-nav-item active">
                        <i class="fas fa-home"></i>
                        <span>Главная</span>
                    </a>
                    <a href="#" class="mobile-nav-item">
                        <i class="fas fa-newspaper"></i>
                        <span>Новости</span>
                    </a>
                    <a href="#" class="mobile-nav-item">
                        <i class="fas fa-flask"></i>
                        <span>Тесты</span>
                    </a>
                    <a href="#" class="mobile-nav-item">
                        <i class="fas fa-user"></i>
                        <span>Профиль</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Demo Framework */
.quick-nav {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

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
}

.desktop-only {
    display: grid;
}

.mobile-demo {
    max-width: 375px;
    margin: 20px auto;
    border: 1px solid var(--color-border-primary);
    border-radius: 16px;
    padding: 16px;
    background: var(--color-surface-secondary);
}

/* Post Cards */
.posts-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
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
    height: 160px;
    background: var(--color-surface-secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-text-tertiary);
    font-size: 48px;
}

.post-content {
    padding: 16px;
}

.post-title {
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 8px 0;
    color: var(--color-text-primary);
    line-height: 1.4;
}

.post-excerpt {
    color: var(--color-text-secondary);
    font-size: 14px;
    line-height: 1.5;
    margin: 0 0 12px 0;
}

.post-meta {
    display: flex;
    gap: 16px;
    font-size: 12px;
    color: var(--color-text-tertiary);
}

/* Mobile Post Card */
.post-card-mobile {
    background: var(--color-card-bg);
    border: 1px solid var(--color-border-primary);
    border-radius: 12px;
    padding: 16px;
}

.post-mobile-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.post-mobile-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 16px;
}

/* Test Cards */
.tests-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
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

/* Question Card */
.question-card {
    background: var(--color-card-bg);
    border: 1px solid var(--color-border-primary);
    border-radius: 12px;
    overflow: hidden;
    max-width: 800px;
    margin: 20px auto;
}

.question-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: var(--color-surface-secondary);
    border-bottom: 1px solid var(--color-border-primary);
}

.question-number {
    font-weight: 600;
    color: var(--color-text-primary);
}

.question-timer {
    color: var(--color-danger);
    font-weight: 600;
}

.question-progress {
    height: 4px;
    background: var(--color-surface-tertiary);
}

.progress-bar {
    height: 100%;
    background: var(--color-primary);
    transition: width var(--transition-normal);
}

.question-content {
    padding: 30px;
}

.question-text {
    font-size: 24px;
    font-weight: 600;
    margin: 0 0 24px 0;
    color: var(--color-text-primary);
}

.question-image-placeholder {
    width: 100%;
    height: 200px;
    background: var(--color-surface-secondary);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-text-tertiary);
    font-size: 64px;
    margin-bottom: 24px;
}

.answers-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 0 30px 30px;
}

.answer-option {
    display: flex;
    align-items: center;
    padding: 16px 20px;
    background: var(--color-surface-secondary);
    border: 2px solid var(--color-border-primary);
    border-radius: 8px;
    cursor: pointer;
    transition: all var(--transition-fast);
    position: relative;
}

.answer-option:hover {
    background: var(--color-bg-hover);
    border-color: var(--color-primary);
}

.answer-option input[type="radio"] {
    display: none;
}

.answer-letter {
    width: 32px;
    height: 32px;
    background: var(--color-surface-primary);
    border: 2px solid var(--color-border-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin-right: 16px;
    transition: all var(--transition-fast);
}

.answer-option:hover .answer-letter {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
}

.answer-option input:checked + .answer-letter {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
}

.answer-option.correct {
    background: rgba(40, 167, 69, 0.1);
    border-color: var(--color-success);
}

.answer-option.correct .answer-letter {
    background: var(--color-success);
    color: white;
    border-color: var(--color-success);
}

.answer-option.incorrect {
    background: rgba(220, 53, 69, 0.1);
    border-color: var(--color-danger);
}

.answer-option.incorrect .answer-letter {
    background: var(--color-danger);
    color: white;
    border-color: var(--color-danger);
}

.answer-option i {
    position: absolute;
    right: 20px;
    font-size: 20px;
}

.answer-option.correct i {
    color: var(--color-success);
}

.answer-option.incorrect i {
    color: var(--color-danger);
}

.answer-text {
    flex: 1;
    font-size: 16px;
}

.question-footer {
    display: flex;
    justify-content: space-between;
    padding: 20px 30px;
    background: var(--color-surface-secondary);
    border-top: 1px solid var(--color-border-primary);
}

/* Mobile Question Card */
.question-card-mobile {
    background: var(--color-card-bg);
    border-radius: 12px;
    padding: 16px;
}

.question-mobile-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.progress-circle {
    position: relative;
    width: 40px;
    height: 40px;
}

.progress-circle .timer {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 10px;
    font-weight: 600;
}

.answers-mobile {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin: 20px 0;
}

.answer-mobile {
    padding: 16px;
    background: var(--color-surface-secondary);
    border: 2px solid var(--color-border-primary);
    border-radius: 8px;
    text-align: left;
    font-size: 16px;
    transition: all var(--transition-fast);
}

.answer-mobile.selected {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
}

/* Mobile Navigation */
.mobile-nav {
    display: flex;
    justify-content: space-around;
    background: var(--color-surface-primary);
    border-radius: 12px;
    padding: 8px;
}

.mobile-nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    padding: 8px 16px;
    border-radius: 8px;
    color: var(--color-text-secondary);
    text-decoration: none;
    font-size: 12px;
    transition: all var(--transition-fast);
}

.mobile-nav-item:hover,
.mobile-nav-item.active {
    background: var(--color-bg-hover);
    color: var(--color-primary);
}

.mobile-nav-item i {
    font-size: 20px;
}

/* Form Elements */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: var(--color-text-primary);
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    background: var(--color-surface-secondary);
    color: var(--color-text-primary);
    border: 1px solid var(--color-border-primary);
    border-radius: 8px;
    font-size: 16px;
    transition: all var(--transition-fast);
}

.form-control:focus {
    outline: none;
    background: var(--color-surface-primary);
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

/* Utility Classes */
.w-100 { width: 100%; }
.mt-4 { margin-top: 32px; }
.mb-4 { margin-bottom: 32px; }
.btn-sm {
    padding: 4px 12px;
    font-size: 14px;
}

/* Responsive */
@media (max-width: 768px) {
    .posts-grid {
        grid-template-columns: 1fr;
    }
    
    .tests-grid {
        grid-template-columns: 1fr;
    }
    
    .desktop-only {
        display: none;
    }
    
    .question-text {
        font-size: 20px;
    }
    
    .question-footer {
        flex-direction: column;
        gap: 12px;
    }
    
    .question-footer .btn {
        width: 100%;
    }
}
</style>
';

// Save the content file
file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/admin/theme-reference-content.php', $testContent);

// Set up template config
$templateConfig = [
    'layoutType' => 'default',
    'darkMode' => true,
];

// Render the page
renderTemplate('Theme System Reference - Admin', 'admin/theme-reference-content.php', $templateConfig);
?>