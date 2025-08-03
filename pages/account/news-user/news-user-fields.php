<?php
// This would show news created by the user (for representatives)
// Check user role
$userRole = $_SESSION['role'] ?? 'user';
?>

<?php if (strpos($userRole, 'Представитель') !== false): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> 
        Здесь будут отображаться новости, которые вы опубликовали.
    </div>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">Мои новости</h5>
        <a href="/account/representative/create-news" class="btn btn-primary">
            <i class="fas fa-plus"></i> Создать новость
        </a>
    </div>
    
    <div class="text-center py-5">
        <i class="fas fa-newspaper" style="font-size: 48px; color: #dee2e6; margin-bottom: 1rem; display: block;"></i>
        <p class="text-muted">У вас пока нет опубликованных новостей</p>
    </div>
<?php else: ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i> 
        Эта страница доступна только для представителей учебных заведений.
    </div>
    
    <p>Если вы являетесь представителем учебного заведения, обновите ваш профиль в разделе 
        <a href="/account/personal-data-change">личных данных</a>.
    </p>
<?php endif; ?>