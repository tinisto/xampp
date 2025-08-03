<?php
// Additional styles for school page
$additionalStyles = '<style>
        .school-header {
            background: linear-gradient(135deg, #6f42c1 0%, #495057 100%);
            color: white;
            padding: 60px 0 40px;
            margin-bottom: 40px;
        }
        .school-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .info-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .info-label {
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 16px;
            color: #333;
        }
    </style>';
?>

<div class="school-header">
        <div class="container">
            <div class="school-badge">
                <i class="fas fa-school"></i> Школа
            </div>
            <h1 class="display-4 fw-bold mb-3"><?= htmlspecialchars($school['school_name']) ?></h1>
            <p class="lead mb-0">
                <i class="fas fa-map-marker-alt me-2"></i>
                <?= htmlspecialchars($school['address'] ?? 'Адрес не указан') ?>
            </p>
        </div>
    </div>
    
    <div class="container mb-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="info-card">
                    <h2 class="h4 mb-4">Основная информация</h2>
                    
                    <?php if (!empty($school['fio_director'])): ?>
                    <div class="mb-3">
                        <div class="info-label">Директор</div>
                        <div class="info-value"><?= htmlspecialchars($school['fio_director']) ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($school['tel'])): ?>
                    <div class="mb-3">
                        <div class="info-label">Телефон</div>
                        <div class="info-value">
                            <i class="fas fa-phone text-primary me-2"></i>
                            <?= htmlspecialchars($school['tel']) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($school['email'])): ?>
                    <div class="mb-3">
                        <div class="info-label">Email</div>
                        <div class="info-value">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            <a href="mailto:<?= htmlspecialchars($school['email']) ?>">
                                <?= htmlspecialchars($school['email']) ?>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($school['site'])): ?>
                    <div class="mb-3">
                        <div class="info-label">Сайт</div>
                        <div class="info-value">
                            <i class="fas fa-globe text-primary me-2"></i>
                            <a href="<?= htmlspecialchars($school['site']) ?>" target="_blank">
                                <?= htmlspecialchars($school['site']) ?>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="info-card">
                    <h3 class="h5 mb-3">Статистика</h3>
                    <p class="mb-2">
                        <i class="fas fa-eye text-muted me-2"></i>
                        Просмотров: <?= $school['view'] ?? 0 ?>
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-id-card text-muted me-2"></i>
                        ID: <?= $school['id_school'] ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
