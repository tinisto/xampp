<?php
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/session_util.php";

$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$oldData = isset($_SESSION['oldData']) ? $_SESSION['oldData'] : [];

// Clear session variables after retrieving them
unset($_SESSION['errors']);
unset($_SESSION['oldData']);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>
    <form id="demo-form" action="/pages/registration/registration_process.php" method="post" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <select name="occupation" id="occupation" class="form-select" required>
                <option value="">Выберите род деятельности</option>
                <option value="Представитель ВУЗа" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Представитель ВУЗа' ? 'selected' : '' ?>>Представитель ВУЗа</option>
                <option value="Представитель ССУЗа" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Представитель ССУЗа' ? 'selected' : '' ?>>Представитель ССУЗа</option>
                <option value="Представитель школы" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Представитель школы' ? 'selected' : '' ?>>Представитель школы</option>
                <option value="Родитель" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Родитель' ? 'selected' : '' ?>>Родитель</option>
                <option value="Учащийся/учащаяся" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Учащийся/учащаяся' ? 'selected' : '' ?>>Учащийся</option>
                <option value="Другое" <?= isset($oldData['occupation']) && $oldData['occupation'] === 'Другое' ? 'selected' : '' ?>>Другое</option>
            </select>
        </div>

        <div class="mb-3">
            <input type="email" id="email" name="email" class="form-control"
                value="<?= isset($oldData['email']) ? htmlspecialchars($oldData['email']) : '' ?>"
                placeholder="Введите ваш email" required>
        </div>

        <div class="input-group">
            <input type="password" id="password" name="password" class="form-control" placeholder="Введите ваш пароль"
                required>
            <span class="input-group-text" id="showPassword">
                <i class="fas fa-eye"></i>
            </span>
        </div>
        <span id="passwordCriteria" class="form-text text-muted my-3 d-block">Пароль должен содержать минимум 8 символов и
            включать буквы и цифры.</span>


        <input type="hidden" name="timezone" id="timezone" value="">

        <div class="d-grid">
            <button id="registrationButton" class="g-recaptcha btn btn-danger"
                data-sitekey="6LcBTE4pAAAAALPukMxGPUpqAg0b_nfTxGp9NSO0" data-callback='onSubmit' data-action='submit'
                <?= !empty($errors) ? 'disabled' : '' ?>><span class="fw-bold">Зарегистрироваться</span></button>
        </div>
    </form>

    <script>
        // Function to detect and set the user's timezone
        function detectTimezone() {
            const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            document.getElementById('timezone').value = timezone;
        }

        // Call the function when the page is loaded
        window.addEventListener('load', detectTimezone);

        // Function to validate the form before submitting
        function validateForm(event) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const registrationButton = document.getElementById('registrationButton');
            const occupation = document.getElementById('occupation').value;

            // Check if email and password are empty
            if (email === "" || password === "" || occupation === "") {
                alert("Пожалуйста, заполните все обязательные поля.");
                event.preventDefault(); // Prevent form submission
                return false;
            }

            // Check reCAPTCHA validation, if it's empty or not validated
            const recaptcha = grecaptcha.getResponse();
            if (recaptcha.length == 0) {
                alert("Пожалуйста, пройдите проверку reCAPTCHA.");
                event.preventDefault(); // Prevent form submission
                return false;
            }

            return true; // Allow form submission if all required fields are filled and reCAPTCHA is valid
        }

        // Function to handle the reCAPTCHA submission
        function onSubmit(token) {
            document.getElementById("demo-form").submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            function togglePasswordVisibility(passwordId) {
                var passwordInput = document.getElementById(passwordId);
                var icon = document.getElementById('showPassword');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.innerHTML = '<i class="fas fa-eye-slash"></i>';
                } else {
                    passwordInput.type = 'password';
                    icon.innerHTML = '<i class="fas fa-eye"></i>';
                }
            }

            // Add event listener to toggle password visibility
            document.getElementById('showPassword').addEventListener('click', function() {
                togglePasswordVisibility('password');
            });
        });

        // Listen for the form submission to validate it
        document.getElementById('demo-form').addEventListener('submit', function(event) {
            if (!validateForm(event)) {
                event.preventDefault(); // Prevent form submission if validation fails
            }
        });
    </script>
</body>

</html>