<div class="d-flex justify-content-center mb-4 gap-5">
    <?php
    include_once  $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/get_avatar.php";
    include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/getRussianDate.php';
    $avatarUrl = $userData['avatar'];
    $avatarPath = getAvatar($avatarUrl);
    echo '<img src="' . htmlspecialchars($avatarPath) . '" alt="Avatar" style="width: 150px; height: 150px; border-radius: 50%;">';
    ?>
    <div class="text-start">
        <h2 class="mt-3">
            <?= htmlspecialchars($_SESSION['firstname'] . ' ' . $_SESSION['lastname'], ENT_QUOTES, 'UTF-8') ?>
        </h2>
        <p class="mb-1"><strong class="text-secondary">Электронная почта:</strong> <?= htmlspecialchars($userData['email'], ENT_QUOTES, 'UTF-8') ?></p>
        <p class="mb-1"><strong class="text-secondary">Род деятельности:</strong> <?= htmlspecialchars($userData['occupation'], ENT_QUOTES, 'UTF-8') ?></p>
        <p class="mb-1"><strong class="text-secondary">Дата регистрации:</strong> <?= getRussianDate($userData['registration_date'] ?? '') ?></p>
    </div>
</div>