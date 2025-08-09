<?php
// Auto-tuning Configuration Dashboard
session_start();

// Check admin access
if ((!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') && 
    (!isset($_SESSION['occupation']) || $_SESSION['occupation'] !== 'admin')) {
    header('Location: /unauthorized');
    exit();
}

// Get user info
$username = $_SESSION['first_name'] ?? $_SESSION['email'] ?? 'Admin';
$userInitial = strtoupper(mb_substr($username, 0, 1));

// Set dashboard title
$dashboardTitle = 'Автонастройка системы';

// Build dashboard content
ob_start();
?>
<style>
.auto-tune-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 15px;
}

.toggle-switch {
    display: flex;
    align-items: center;
    gap: 10px;
}

.switch {
    position: relative;
    width: 60px;
    height: 30px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 30px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #4caf50;
}

input:checked + .slider:before {
    transform: translateX(30px);
}

.config-section {
    background: var(--surface);
    padding: 20px;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    margin-bottom: 20px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.section-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
}

.config-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.config-item {
    padding: 15px;
    background: var(--bg-light);
    border-radius: 8px;
    border: 1px solid var(--border-color);
}

.config-label {
    font-size: 14px;
    color: var(--text-secondary);
    margin-bottom: 8px;
    display: block;
}

.config-value {
    font-size: 20px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.config-input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 16px;
    background: var(--surface);
    color: var(--text-primary);
}

.recommendation {
    background: #e3f2fd;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    border-left: 4px solid #2196f3;
}

.recommendation-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.recommendation-title {
    font-weight: 600;
    color: #1976d2;
}

.recommendation-action {
    display: flex;
    gap: 10px;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-success:hover {
    background: #218838;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
}

.analysis-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.analysis-card {
    background: var(--bg-light);
    padding: 15px;
    border-radius: 8px;
    text-align: center;
}

.analysis-value {
    font-size: 24px;
    font-weight: bold;
    color: var(--primary-color);
    margin-bottom: 5px;
}

.analysis-label {
    font-size: 13px;
    color: var(--text-secondary);
}

.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
}

.alert-info {
    background: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.alert-warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.keyword-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}

.keyword-tag {
    background: #e9ecef;
    color: #495057;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.keyword-tag .remove {
    cursor: pointer;
    color: #dc3545;
    font-weight: bold;
}

.add-keyword {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.add-keyword input {
    flex: 1;
}

@media (max-width: 768px) {
    .auto-tune-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .config-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="auto-tune-header">
    <h2>Автонастройка системы комментариев</h2>
    <div class="toggle-switch">
        <span>Автонастройка:</span>
        <label class="switch">
            <input type="checkbox" id="auto-tune-toggle" checked>
            <span class="slider"></span>
        </label>
    </div>
</div>

<!-- System Analysis -->
<div class="config-section">
    <div class="section-header">
        <h3 class="section-title">Анализ системы</h3>
        <button class="btn btn-primary" onclick="runAnalysis()">
            <i class="fas fa-sync-alt"></i> Анализировать
        </button>
    </div>
    
    <div class="analysis-grid" id="analysis-grid">
        <div class="analysis-card">
            <div class="analysis-value">-</div>
            <div class="analysis-label">Комментариев за период</div>
        </div>
        <div class="analysis-card">
            <div class="analysis-value">-</div>
            <div class="analysis-label">Уникальных авторов</div>
        </div>
        <div class="analysis-card">
            <div class="analysis-value">-</div>
            <div class="analysis-label">Попыток спама</div>
        </div>
        <div class="analysis-card">
            <div class="analysis-value">-</div>
            <div class="analysis-label">Превышений лимитов</div>
        </div>
    </div>
    
    <div id="recommendations" style="display: none;">
        <h4 style="margin: 20px 0 15px;">Рекомендации:</h4>
        <div id="recommendations-list"></div>
    </div>
</div>

<!-- Rate Limits Configuration -->
<div class="config-section">
    <div class="section-header">
        <h3 class="section-title">Лимиты скорости</h3>
        <button class="btn btn-secondary btn-sm" onclick="saveSection('rate_limits')">
            Сохранить
        </button>
    </div>
    
    <div class="config-grid">
        <div class="config-item">
            <label class="config-label">Комментариев в минуту</label>
            <input type="number" class="config-input" id="comments_per_minute" value="3" min="1" max="20">
        </div>
        <div class="config-item">
            <label class="config-label">Комментариев в час</label>
            <input type="number" class="config-input" id="comments_per_hour" value="20" min="5" max="100">
        </div>
        <div class="config-item">
            <label class="config-label">Комментариев в день</label>
            <input type="number" class="config-input" id="comments_per_day" value="100" min="10" max="1000">
        </div>
    </div>
</div>

<!-- Spam Filters Configuration -->
<div class="config-section">
    <div class="section-header">
        <h3 class="section-title">Спам-фильтры</h3>
        <button class="btn btn-secondary btn-sm" onclick="saveSection('spam_filters')">
            Сохранить
        </button>
    </div>
    
    <div class="config-grid">
        <div class="config-item">
            <label class="config-label">Минимальная длина комментария</label>
            <input type="number" class="config-input" id="min_comment_length" value="3" min="1" max="50">
        </div>
        <div class="config-item">
            <label class="config-label">Максимальная длина комментария</label>
            <input type="number" class="config-input" id="max_comment_length" value="2000" min="100" max="10000">
        </div>
        <div class="config-item">
            <label class="config-label">Максимум ссылок в комментарии</label>
            <input type="number" class="config-input" id="link_limit" value="2" min="0" max="10">
        </div>
    </div>
    
    <div style="margin-top: 20px;">
        <label class="config-label">Ключевые слова спама</label>
        <div class="keyword-list" id="spam-keywords"></div>
        <div class="add-keyword">
            <input type="text" class="config-input" id="new-keyword" placeholder="Добавить ключевое слово">
            <button class="btn btn-primary" onclick="addKeyword()">Добавить</button>
        </div>
    </div>
</div>

<!-- Auto-tune Settings -->
<div class="config-section">
    <div class="section-header">
        <h3 class="section-title">Настройки автонастройки</h3>
        <button class="btn btn-secondary btn-sm" onclick="saveSection('auto_tune')">
            Сохранить
        </button>
    </div>
    
    <div class="config-grid">
        <div class="config-item">
            <label class="config-label">Период обучения (дней)</label>
            <input type="number" class="config-input" id="learning_period_days" value="7" min="1" max="30">
        </div>
        <div class="config-item">
            <label class="config-label">Фактор корректировки (%)</label>
            <input type="number" class="config-input" id="adjustment_factor" value="20" min="5" max="50">
        </div>
        <div class="config-item">
            <label class="config-label">Минимальный размер выборки</label>
            <input type="number" class="config-input" id="min_sample_size" value="100" min="10" max="1000">
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div style="display: flex; gap: 10px; justify-content: center; margin-top: 30px;">
    <button class="btn btn-success" onclick="applyRecommendations()">
        <i class="fas fa-magic"></i> Применить рекомендации
    </button>
    <button class="btn btn-danger" onclick="resetToDefaults()">
        <i class="fas fa-undo"></i> Сбросить настройки
    </button>
</div>

<script>
let currentConfig = {};
let currentAnalysis = {};
let currentRecommendations = {};

// Load configuration on page load
async function loadConfiguration() {
    try {
        const response = await fetch('/api/comments/auto-tune.php?action=analyze');
        const data = await response.json();
        
        if (data.success) {
            currentConfig = data.current_config;
            currentAnalysis = data.analysis;
            currentRecommendations = data.recommendations;
            
            updateUI();
            updateAnalysis();
            updateRecommendations();
        }
    } catch (error) {
        console.error('Error loading configuration:', error);
        showAlert('Ошибка загрузки конфигурации', 'error');
    }
}

// Update UI with current configuration
function updateUI() {
    // Auto-tune toggle
    document.getElementById('auto-tune-toggle').checked = currentConfig.auto_tune.enabled;
    
    // Rate limits
    document.getElementById('comments_per_minute').value = currentConfig.rate_limits.comments_per_minute;
    document.getElementById('comments_per_hour').value = currentConfig.rate_limits.comments_per_hour;
    document.getElementById('comments_per_day').value = currentConfig.rate_limits.comments_per_day;
    
    // Spam filters
    document.getElementById('min_comment_length').value = currentConfig.spam_filters.min_comment_length;
    document.getElementById('max_comment_length').value = currentConfig.spam_filters.max_comment_length;
    document.getElementById('link_limit').value = currentConfig.spam_filters.link_limit;
    
    // Auto-tune settings
    document.getElementById('learning_period_days').value = currentConfig.auto_tune.learning_period_days;
    document.getElementById('adjustment_factor').value = currentConfig.auto_tune.adjustment_factor * 100;
    document.getElementById('min_sample_size').value = currentConfig.auto_tune.min_sample_size;
    
    // Spam keywords
    updateKeywordsList();
}

// Update analysis display
function updateAnalysis() {
    const grid = document.getElementById('analysis-grid');
    
    grid.innerHTML = `
        <div class="analysis-card">
            <div class="analysis-value">${formatNumber(currentAnalysis.velocity?.total_comments || 0)}</div>
            <div class="analysis-label">Комментариев за период</div>
        </div>
        <div class="analysis-card">
            <div class="analysis-value">${formatNumber(currentAnalysis.velocity?.unique_users || 0)}</div>
            <div class="analysis-label">Уникальных авторов</div>
        </div>
        <div class="analysis-card">
            <div class="analysis-value">${formatNumber(currentAnalysis.spam?.total_spam_attempts || 0)}</div>
            <div class="analysis-label">Попыток спама</div>
        </div>
        <div class="analysis-card">
            <div class="analysis-value">${formatNumber(currentAnalysis.rate_violations?.violations || 0)}</div>
            <div class="analysis-label">Превышений лимитов</div>
        </div>
    `;
}

// Update recommendations display
function updateRecommendations() {
    const container = document.getElementById('recommendations');
    const list = document.getElementById('recommendations-list');
    
    if (Object.keys(currentRecommendations).length === 0) {
        container.style.display = 'none';
        return;
    }
    
    container.style.display = 'block';
    list.innerHTML = '';
    
    for (const [section, recommendations] of Object.entries(currentRecommendations)) {
        for (const [key, rec] of Object.entries(recommendations)) {
            if (rec.suggested !== undefined) {
                list.innerHTML += `
                    <div class="recommendation">
                        <div class="recommendation-header">
                            <div class="recommendation-title">${getSectionName(section)} - ${getKeyName(key)}</div>
                            <div class="recommendation-action">
                                <span>Текущее: ${rec.current}</span>
                                <span>→</span>
                                <span>Рекомендуемое: ${rec.suggested}</span>
                            </div>
                        </div>
                        <div style="color: #666; font-size: 14px; margin-top: 5px;">${rec.reason}</div>
                    </div>
                `;
            }
        }
    }
}

// Update spam keywords list
function updateKeywordsList() {
    const container = document.getElementById('spam-keywords');
    const keywords = currentConfig.spam_filters.spam_keywords || [];
    
    container.innerHTML = keywords.map(keyword => `
        <span class="keyword-tag">
            ${keyword}
            <span class="remove" onclick="removeKeyword('${keyword}')">×</span>
        </span>
    `).join('');
}

// Add spam keyword
function addKeyword() {
    const input = document.getElementById('new-keyword');
    const keyword = input.value.trim().toLowerCase();
    
    if (keyword && !currentConfig.spam_filters.spam_keywords.includes(keyword)) {
        currentConfig.spam_filters.spam_keywords.push(keyword);
        updateKeywordsList();
        input.value = '';
    }
}

// Remove spam keyword
function removeKeyword(keyword) {
    const index = currentConfig.spam_filters.spam_keywords.indexOf(keyword);
    if (index > -1) {
        currentConfig.spam_filters.spam_keywords.splice(index, 1);
        updateKeywordsList();
    }
}

// Save configuration section
async function saveSection(section) {
    const updates = { [section]: {} };
    
    switch (section) {
        case 'rate_limits':
            updates.rate_limits = {
                comments_per_minute: parseInt(document.getElementById('comments_per_minute').value),
                comments_per_hour: parseInt(document.getElementById('comments_per_hour').value),
                comments_per_day: parseInt(document.getElementById('comments_per_day').value)
            };
            break;
            
        case 'spam_filters':
            updates.spam_filters = {
                min_comment_length: parseInt(document.getElementById('min_comment_length').value),
                max_comment_length: parseInt(document.getElementById('max_comment_length').value),
                link_limit: parseInt(document.getElementById('link_limit').value),
                spam_keywords: currentConfig.spam_filters.spam_keywords
            };
            break;
            
        case 'auto_tune':
            updates.auto_tune = {
                learning_period_days: parseInt(document.getElementById('learning_period_days').value),
                adjustment_factor: parseInt(document.getElementById('adjustment_factor').value) / 100,
                min_sample_size: parseInt(document.getElementById('min_sample_size').value)
            };
            break;
    }
    
    try {
        const response = await fetch('/api/comments/auto-tune.php?action=manual', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(updates)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Настройки сохранены', 'success');
            currentConfig = result.config;
        } else {
            showAlert('Ошибка сохранения: ' + result.error, 'error');
        }
    } catch (error) {
        showAlert('Ошибка сохранения настроек', 'error');
    }
}

// Run analysis
async function runAnalysis() {
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Анализ...';
    
    try {
        await loadConfiguration();
        showAlert('Анализ завершен', 'success');
    } catch (error) {
        showAlert('Ошибка анализа', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-sync-alt"></i> Анализировать';
    }
}

// Apply recommendations
async function applyRecommendations() {
    if (Object.keys(currentRecommendations).length === 0) {
        showAlert('Нет рекомендаций для применения', 'warning');
        return;
    }
    
    if (!confirm('Применить все рекомендации?')) {
        return;
    }
    
    try {
        const response = await fetch('/api/comments/auto-tune.php?action=apply');
        const result = await response.json();
        
        if (result.success) {
            showAlert('Рекомендации применены', 'success');
            currentConfig = result.new_config;
            updateUI();
            await runAnalysis();
        } else {
            showAlert('Ошибка применения: ' + result.error, 'error');
        }
    } catch (error) {
        showAlert('Ошибка применения рекомендаций', 'error');
    }
}

// Reset to defaults
async function resetToDefaults() {
    if (!confirm('Сбросить все настройки к значениям по умолчанию?')) {
        return;
    }
    
    try {
        const response = await fetch('/api/comments/auto-tune.php?action=reset');
        const result = await response.json();
        
        if (result.success) {
            showAlert('Настройки сброшены', 'success');
            currentConfig = result.config;
            updateUI();
        }
    } catch (error) {
        showAlert('Ошибка сброса настроек', 'error');
    }
}

// Toggle auto-tune
document.getElementById('auto-tune-toggle').addEventListener('change', async function() {
    try {
        const response = await fetch('/api/comments/auto-tune.php?action=toggle');
        const result = await response.json();
        
        if (result.success) {
            showAlert(result.message, 'success');
        }
    } catch (error) {
        showAlert('Ошибка переключения автонастройки', 'error');
        this.checked = !this.checked;
    }
});

// Utility functions
function formatNumber(num) {
    return new Intl.NumberFormat('ru-RU').format(num);
}

function getSectionName(section) {
    const names = {
        'rate_limits': 'Лимиты скорости',
        'spam_filters': 'Спам-фильтры',
        'auto_tune': 'Автонастройка'
    };
    return names[section] || section;
}

function getKeyName(key) {
    const names = {
        'comments_per_minute': 'Комментариев в минуту',
        'comments_per_hour': 'Комментариев в час',
        'comments_per_day': 'Комментариев в день',
        'min_comment_length': 'Минимальная длина',
        'max_comment_length': 'Максимальная длина',
        'link_limit': 'Лимит ссылок'
    };
    return names[key] || key;
}

function showAlert(message, type = 'info') {
    // You can implement a toast notification here
    console.log(`[${type}] ${message}`);
}

// Initialize on load
document.addEventListener('DOMContentLoaded', loadConfiguration);
</script>

<?php
$dashboardContent = ob_get_clean();

// Set active menu
$activeMenu = 'settings';

// Include the dashboard template
include $_SERVER['DOCUMENT_ROOT'] . '/dashboard-template.php';
?>