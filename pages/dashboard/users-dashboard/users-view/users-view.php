<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';

// Check admin access
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
requireAdmin();

// Get user data for display
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get users with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Count total users
$countQuery = "SELECT COUNT(*) as total FROM users";
$countResult = mysqli_query($connection, $countQuery);
$totalUsers = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalUsers / $perPage);

// Get users for current page
$usersQuery = "SELECT id, username, email, role, created_at, last_login, status 
               FROM users 
               ORDER BY created_at DESC 
               LIMIT $perPage OFFSET $offset";
$usersResult = mysqli_query($connection, $usersQuery);

ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><i class="fas fa-users"></i> Управление пользователями</h4>
                    <div>
                        <a href="/dashboard" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Назад к панели
                        </a>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fas fa-plus"></i> Добавить пользователя
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Stats -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title text-primary"><?php echo $totalUsers; ?></h5>
                                    <p class="card-text">Всего пользователей</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <?php
                                    $activeQuery = "SELECT COUNT(*) as count FROM users WHERE status = 'active'";
                                    $activeResult = mysqli_query($connection, $activeQuery);
                                    $activeCount = mysqli_fetch_assoc($activeResult)['count'];
                                    ?>
                                    <h5 class="card-title text-success"><?php echo $activeCount; ?></h5>
                                    <p class="card-text">Активных</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <?php
                                    $adminQuery = "SELECT COUNT(*) as count FROM users WHERE role = 'admin'";
                                    $adminResult = mysqli_query($connection, $adminQuery);
                                    $adminCount = mysqli_fetch_assoc($adminResult)['count'];
                                    ?>
                                    <h5 class="card-title text-warning"><?php echo $adminCount; ?></h5>
                                    <p class="card-text">Администраторов</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <?php
                                    $recentQuery = "SELECT COUNT(*) as count FROM users WHERE created_at > DATE_SUB(NOW(), INTERVAL 30 DAY)";
                                    $recentResult = mysqli_query($connection, $recentQuery);
                                    $recentCount = mysqli_fetch_assoc($recentResult)['count'];
                                    ?>
                                    <h5 class="card-title text-info"><?php echo $recentCount; ?></h5>
                                    <p class="card-text">Новых за месяц</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Users Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Имя пользователя</th>
                                    <th>Email</th>
                                    <th>Роль</th>
                                    <th>Статус</th>
                                    <th>Регистрация</th>
                                    <th>Последний вход</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = mysqli_fetch_assoc($usersResult)): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                            </div>
                                            <?php echo htmlspecialchars($user['username']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $user['role'] === 'admin' ? 'bg-danger' : 'bg-secondary'; ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $user['status'] === 'active' ? 'bg-success' : 'bg-warning'; ?>">
                                            <?php echo ucfirst($user['status'] ?? 'active'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d.m.Y', strtotime($user['created_at'])); ?></td>
                                    <td><?php echo $user['last_login'] ? date('d.m.Y H:i', strtotime($user['last_login'])) : 'Никогда'; ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" onclick="editUser(<?php echo $user['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if ($user['role'] !== 'admin'): ?>
                                            <button class="btn btn-outline-danger" onclick="deleteUser(<?php echo $user['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <nav aria-label="Users pagination">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>">Предыдущая</a>
                            </li>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>">Следующая</a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Добавить пользователя</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addUserForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Имя пользователя</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Роль</label>
                        <select class="form-select" id="role" name="role">
                            <option value="user">Пользователь</option>
                            <option value="admin">Администратор</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Добавить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editUser(userId) {
    // Implement user editing
    alert('Редактирование пользователя ID: ' + userId);
}

function deleteUser(userId) {
    if (confirm('Вы уверены, что хотите удалить этого пользователя?')) {
        // Implement user deletion
        alert('Удаление пользователя ID: ' + userId);
    }
}

document.getElementById('addUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // Implement user creation
    alert('Создание нового пользователя...');
});
</script>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
}
</style>

<?php
$content = ob_get_clean();

// Output using template
echo renderTemplate([
    'title' => 'Управление пользователями',
    'content' => $content,
    'current_page' => 'dashboard'
]);
?>