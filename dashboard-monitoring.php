<?php
// Comment System Performance Monitoring Dashboard
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
$dashboardTitle = 'Мониторинг производительности';

// Build dashboard content
ob_start();
?>
<style>
.monitoring-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.refresh-btn {
    padding: 8px 16px;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.refresh-btn:hover {
    background: #0056b3;
}

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.metric-card {
    background: var(--surface);
    padding: 20px;
    border-radius: 12px;
    border: 1px solid var(--border-color);
}

.metric-label {
    color: var(--text-secondary);
    font-size: 13px;
    text-transform: uppercase;
    margin-bottom: 8px;
}

.metric-value {
    font-size: 28px;
    font-weight: bold;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.metric-change {
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.change-positive { color: #4caf50; }
.change-negative { color: #f44336; }

.monitoring-section {
    background: var(--surface);
    padding: 20px;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    margin-bottom: 20px;
}

.section-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 20px;
    color: var(--text-primary);
}

.alerts-container {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.alert-item {
    padding: 15px;
    border-radius: 8px;
    display: flex;
    align-items: start;
    gap: 12px;
}

.alert-warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert-info {
    background: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.alert-icon {
    font-size: 20px;
    flex-shrink: 0;
}

.alert-content h4 {
    margin: 0 0 4px 0;
    font-size: 16px;
}

.alert-content p {
    margin: 0;
    font-size: 14px;
}

.performance-chart {
    height: 300px;
    position: relative;
}

.table-container {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th,
.data-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
}

.data-table th {
    font-weight: 600;
    color: var(--text-secondary);
    font-size: 13px;
    text-transform: uppercase;
}

.data-table tr:hover {
    background: var(--bg-light);
}

.status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.status-healthy { background: #c8e6c9; color: #2e7d32; }
.status-warning { background: #fff9c4; color: #f9a825; }
.status-critical { background: #ffcdd2; color: #c62828; }

.loading-spinner {
    display: none;
    text-align: center;
    padding: 40px;
}

.loading-spinner.active {
    display: block;
}

.spinner {
    border: 3px solid #f3f3f3;
    border-top: 3px solid var(--primary-color);
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.trend-chart {
    margin-top: 20px;
}

@media (max-width: 768px) {
    .monitoring-header {
        flex-direction: column;
        gap: 15px;
    }
    
    .metrics-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="monitoring-header">
    <h2>Мониторинг производительности</h2>
    <button class="refresh-btn" onclick="refreshMetrics()">
        <i class="fas fa-sync-alt"></i> Обновить
    </button>
</div>

<!-- System Alerts -->
<div id="alerts-section" class="monitoring-section">
    <h3 class="section-title">Системные оповещения</h3>
    <div class="alerts-container" id="alerts-container">
        <div class="loading-spinner active">
            <div class="spinner"></div>
            <p>Загрузка оповещений...</p>
        </div>
    </div>
</div>

<!-- Key Metrics -->
<div class="metrics-grid" id="metrics-grid">
    <div class="metric-card">
        <div class="metric-label">Всего комментариев</div>
        <div class="metric-value">-</div>
        <div class="metric-change">Загрузка...</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Комментариев за час</div>
        <div class="metric-value">-</div>
        <div class="metric-change">Загрузка...</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Активных пользователей</div>
        <div class="metric-value">-</div>
        <div class="metric-change">Загрузка...</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Среднее время отклика</div>
        <div class="metric-value">-</div>
        <div class="metric-change">Загрузка...</div>
    </div>
</div>

<!-- Performance Charts -->
<div class="monitoring-section">
    <h3 class="section-title">Производительность API</h3>
    <canvas id="performanceChart" class="performance-chart"></canvas>
</div>

<!-- Database Health -->
<div class="monitoring-section">
    <h3 class="section-title">Состояние базы данных</h3>
    <div class="table-container">
        <table class="data-table" id="database-table">
            <thead>
                <tr>
                    <th>Таблица</th>
                    <th>Размер (MB)</th>
                    <th>Строк</th>
                    <th>Статус</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4" style="text-align: center;">Загрузка данных...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Usage Trends -->
<div class="monitoring-section">
    <h3 class="section-title">Тренды использования (24 часа)</h3>
    <canvas id="trendsChart" class="performance-chart"></canvas>
</div>

<!-- Error Tracking -->
<div class="monitoring-section">
    <h3 class="section-title">Отслеживание ошибок</h3>
    <div class="metrics-grid" id="error-metrics">
        <div class="metric-card">
            <div class="metric-label">Неотправленные уведомления</div>
            <div class="metric-value" id="failed-notifications">-</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Попытки спама (24ч)</div>
            <div class="metric-value" id="spam-attempts">-</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Превышения лимитов</div>
            <div class="metric-value" id="rate-violations">-</div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let performanceChart = null;
let trendsChart = null;

async function loadMetrics() {
    try {
        // Load overview metrics
        const overviewResponse = await fetch('/api/comments/monitor.php?type=overview');
        const overview = await overviewResponse.json();
        
        if (overview.success) {
            updateMetricsGrid(overview.metrics);
            updateDatabaseTable(overview.metrics.tables);
        }
        
        // Load alerts
        const alertsResponse = await fetch('/api/comments/monitor.php?type=alerts');
        const alerts = await alertsResponse.json();
        
        if (alerts.success) {
            updateAlerts(alerts.metrics.alerts);
        }
        
        // Load performance data
        const perfResponse = await fetch('/api/comments/monitor.php?type=performance');
        const performance = await perfResponse.json();
        
        if (performance.success) {
            updatePerformanceChart(performance.metrics);
        }
        
        // Load trends
        const trendsResponse = await fetch('/api/comments/monitor.php?type=trends');
        const trends = await trendsResponse.json();
        
        if (trends.success) {
            updateTrendsChart(trends.metrics);
        }
        
        // Load errors
        const errorsResponse = await fetch('/api/comments/monitor.php?type=errors');
        const errors = await errorsResponse.json();
        
        if (errors.success) {
            updateErrorMetrics(errors.metrics.errors);
        }
        
    } catch (error) {
        console.error('Error loading metrics:', error);
        showError('Ошибка загрузки метрик');
    }
}

function updateMetricsGrid(metrics) {
    const grid = document.getElementById('metrics-grid');
    const db = metrics.database;
    
    grid.innerHTML = `
        <div class="metric-card">
            <div class="metric-label">Всего комментариев</div>
            <div class="metric-value">${formatNumber(db.total_comments)}</div>
            <div class="metric-change change-positive">
                <i class="fas fa-arrow-up"></i> ${db.comments_last_day} за день
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Комментариев за час</div>
            <div class="metric-value">${db.comments_last_hour}</div>
            <div class="metric-change">
                ~${Math.round(db.comments_last_hour / 60)} в минуту
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Активных пользователей</div>
            <div class="metric-value">${formatNumber(db.total_users)}</div>
            <div class="metric-change">
                Ср. длина: ${Math.round(db.avg_comment_length)} символов
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Использование памяти</div>
            <div class="metric-value">${metrics.health.memory_usage_mb} MB</div>
            <div class="metric-change">
                Пик: ${metrics.health.memory_peak_mb} MB
            </div>
        </div>
    `;
}

function updateAlerts(alerts) {
    const container = document.getElementById('alerts-container');
    
    if (alerts.length === 0) {
        container.innerHTML = `
            <div class="alert-item alert-info">
                <i class="fas fa-check-circle alert-icon"></i>
                <div class="alert-content">
                    <h4>Система работает нормально</h4>
                    <p>Критических проблем не обнаружено</p>
                </div>
            </div>
        `;
    } else {
        container.innerHTML = alerts.map(alert => `
            <div class="alert-item alert-${alert.type}">
                <i class="fas fa-${alert.type === 'error' ? 'exclamation-circle' : 'exclamation-triangle'} alert-icon"></i>
                <div class="alert-content">
                    <h4>${alert.message}</h4>
                    <p>${alert.details}</p>
                </div>
            </div>
        `).join('');
    }
}

function updateDatabaseTable(tables) {
    const tbody = document.querySelector('#database-table tbody');
    
    tbody.innerHTML = Object.entries(tables).map(([tableName, data]) => {
        const sizeStatus = data.size_mb > 100 ? 'warning' : 'healthy';
        
        return `
            <tr>
                <td>${tableName}</td>
                <td>${data.size_mb || 0}</td>
                <td>${formatNumber(data.rows || 0)}</td>
                <td><span class="status-badge status-${sizeStatus}">
                    ${sizeStatus === 'healthy' ? 'Нормально' : 'Внимание'}
                </span></td>
            </tr>
        `;
    }).join('');
}

function updatePerformanceChart(metrics) {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    
    if (performanceChart) {
        performanceChart.destroy();
    }
    
    const endpoints = Object.keys(metrics.query_performance);
    const avgResponseTimes = endpoints.map(ep => metrics.query_performance[ep].avg_response_ms);
    const requestsPerMin = endpoints.map(ep => metrics.query_performance[ep].requests_per_minute);
    
    performanceChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: endpoints.map(ep => ep.charAt(0).toUpperCase() + ep.slice(1)),
            datasets: [{
                label: 'Среднее время отклика (мс)',
                data: avgResponseTimes,
                backgroundColor: 'rgba(0, 123, 255, 0.6)',
                yAxisID: 'y-response'
            }, {
                label: 'Запросов в минуту',
                data: requestsPerMin,
                type: 'line',
                borderColor: '#ff6384',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                yAxisID: 'y-requests'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                'y-response': {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Время отклика (мс)'
                    }
                },
                'y-requests': {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Запросов/мин'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
}

function updateTrendsChart(metrics) {
    const ctx = document.getElementById('trendsChart').getContext('2d');
    
    if (trendsChart) {
        trendsChart.destroy();
    }
    
    const hourlyData = metrics.hourly_trends || [];
    
    trendsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: hourlyData.map(d => {
                const date = new Date(d.hour);
                return date.getHours() + ':00';
            }),
            datasets: [{
                label: 'Комментарии',
                data: hourlyData.map(d => d.comments),
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.3
            }, {
                label: 'Ответы',
                data: hourlyData.map(d => d.replies),
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function updateErrorMetrics(errors) {
    document.getElementById('failed-notifications').textContent = errors.failed_notifications || 0;
    document.getElementById('spam-attempts').textContent = errors.spam_attempts || 0;
    document.getElementById('rate-violations').textContent = errors.rate_limit_violations || 0;
}

function formatNumber(num) {
    return new Intl.NumberFormat('ru-RU').format(num);
}

function showError(message) {
    const alertsContainer = document.getElementById('alerts-container');
    alertsContainer.innerHTML = `
        <div class="alert-item alert-error">
            <i class="fas fa-exclamation-circle alert-icon"></i>
            <div class="alert-content">
                <h4>Ошибка</h4>
                <p>${message}</p>
            </div>
        </div>
    `;
}

function refreshMetrics() {
    const btn = document.querySelector('.refresh-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Обновление...';
    
    loadMetrics().finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-sync-alt"></i> Обновить';
    });
}

// Auto-refresh every 30 seconds
setInterval(loadMetrics, 30000);

// Initial load
document.addEventListener('DOMContentLoaded', loadMetrics);
</script>

<?php
$dashboardContent = ob_get_clean();

// Set active menu
$activeMenu = 'monitoring';

// Include the dashboard template
include $_SERVER['DOCUMENT_ROOT'] . '/dashboard-template.php';
?>