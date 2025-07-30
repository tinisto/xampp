<?php
require_once __DIR__ . '/../includes/init.php';
?>
<section class="d-flex justify-content-center align-items-center" style="height: 60vh;">
    <div class="col-lg-4 col-md-6 col-sm-10 mx-auto">
        <div class="border border-dark-subtle rounded-3 mb-2 pb-2 px-2">
            <div class="p-3 shadow-sm">
                <h5 class="text-center mb-3 sign-up-custom">Логин</h5>
                <form method="post" action="/login/login_process.php">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3 mx-2">
                        <label for="exampleInputEmail1" class="form-label">Email address</label>
                        <input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" required>
                    </div>
                    <div class="mx-2">
                        <label for="exampleInputPassword1" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control" id="passwordInput" required>
                            <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                <i class="fa fa-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success d-block mx-auto mt-3">Логин</button>
                </form>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger mt-3">
                        <?= h($_SESSION['error']) ?>
                        <?php unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('passwordInput');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
});
</script>