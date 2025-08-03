<?php
/**
 * Reusable Stats Section Component
 * 
 * Usage:
 * include $_SERVER['DOCUMENT_ROOT'] . '/common-components/stats-section.php';
 * renderStatsSection([
 *     ['number' => 495, 'label' => 'Новостей'],
 *     ['number' => 12, 'label' => 'Категорий']
 * ]);
 */

function renderStatsSection($stats) {
    if (empty($stats)) return;
    
    $statsCount = count($stats);
    $colClass = $statsCount <= 2 ? 'col-6' : ($statsCount <= 3 ? 'col-4' : 'col-3');
    ?>
    <style>
        .stats-section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 40px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
        }
        .stats-section h3 {
            font-size: 20px;
            font-weight: 700;
            color: #333;
            margin-bottom: 25px;
            text-align: center;
        }
        .stats-number {
            font-size: 36px;
            font-weight: 700;
            color: #28a745;
            margin-bottom: 5px;
            text-align: center;
        }
        .stats-label {
            font-size: 14px;
            color: #666;
            text-align: center;
            font-weight: 500;
        }
        .stats-item {
            text-align: center;
        }
        @media (max-width: 768px) {
            .stats-section {
                padding: 20px;
                margin-bottom: 30px;
            }
            .stats-number {
                font-size: 28px;
            }
            .stats-section h3 {
                font-size: 18px;
                margin-bottom: 20px;
            }
        }
    </style>
    
    <div class="stats-section">
        <h3>Статистика</h3>
        <div class="row">
            <?php foreach ($stats as $stat): ?>
                <div class="<?= $colClass ?>">
                    <div class="stats-item">
                        <div class="stats-number"><?= number_format($stat['number']) ?></div>
                        <div class="stats-label"><?= htmlspecialchars($stat['label']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}
?>