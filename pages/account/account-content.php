<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/get_avatar.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/user_functions.php";
$userId = $_SESSION['user_id'];
$userData = getUserData($connection, $userId);
$occupation = $_SESSION["occupation"];

// Fetch the count of comments and news for the user
$sqlCommentsCount = "SELECT COUNT(*) as count FROM comments WHERE user_id = ?";
$stmtCommentsCount = $connection->prepare($sqlCommentsCount);
if (!$stmtCommentsCount) {
    header("Location: /error");
    exit();
}
$stmtCommentsCount->bind_param('i', $userId);
if (!$stmtCommentsCount->execute()) {
    header("Location: /error");
    exit();
}
$commentsCount = $stmtCommentsCount->get_result()->fetch_assoc()['count'];

$sqlNewsCount = "SELECT COUNT(*) as count FROM news WHERE user_id = ?";
$stmtNewsCount = $connection->prepare($sqlNewsCount);
if (!$stmtNewsCount) {
    header("Location: /error");
    exit();
}
$stmtNewsCount->bind_param('i', $userId);
if (!$stmtNewsCount->execute()) {
    header("Location: /error");
    exit();
}
$newsCount = $stmtNewsCount->get_result()->fetch_assoc()['count'];
?>

<div class="container mt-5" style="font-size: 14px;">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="list-group">
                <a href="#" class="list-group-item list-group-item-action active" id="myProfileLink">
                    <i class="fas fa-user"></i> Мой профиль
                </a>
                <a href="#" class="list-group-item list-group-item-action" id="passwordLink">
                    <i class="fas fa-address-book"></i> Пароль
                </a>
                <a href="#" class="list-group-item list-group-item-action" id="contactInfoLink">
                    <i class="fas fa-address-book"></i> Контактная информация
                </a>
                <a href="#" class="list-group-item list-group-item-action" id="myAvatarLink">
                    <i class="fas fa-image"></i> Аватар
                </a>
                <?php
                if (
                    $occupation === "Представитель ВУЗа" ||
                    $occupation === "Представитель ССУЗа" ||
                    $occupation === "Представитель школы"
                ) {
                ?>
                    <a href="#" class="list-group-item list-group-item-action" id="myRepresentativeLink">
                        <i class="fas fa-university"></i> Для представителя учебного заведения
                    </a>
                <?php
                }
                ?>
                <a href="#" class="list-group-item list-group-item-action" id="myCommentsLink">
                    <i class="fas fa-comments"></i> Мои комментарии
                    <span class="badge bg-secondary rounded-pill ms-2"><?= $commentsCount ?></span>
                </a>
                <a href="#" class="list-group-item list-group-item-action" id="myNewsLink">
                    <i class="fas fa-newspaper"></i> Мои новости
                    <span class="badge bg-secondary rounded-pill ms-2"><?= $newsCount ?></span>
                </a>
                <a href="/pages/logout/logout.php" class="list-group-item list-group-item-action" id="signOutLink">
                    <i class="fas fa-sign-out-alt"></i> Выход
                </a>
                <a href="#" class="list-group-item list-group-item-action" id="deleteAccountLink">
                    <i class="fas fa-trash-alt"></i> Удалить аккаунт
                </a>
            </div>
        </div>
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card" id="myProfileContent" style="display: block;">
                <div class="card-body">
                    <h4 class="card-title text-center">Мой профиль</h4>
                    <div class="row">
                        <?php include 'profile-intro.php'; ?>
                    </div>
                </div>
            </div>
            <div class="card" id="passwordContent" style="display: none;">
                <div class="card-body">
                    <h4 class="card-title text-center">Пароль</h4>
                    <div class="row">
                        <?php include 'password-change/password-change.php'; ?>
                    </div>
                </div>
            </div>
            <div class="card" id="contactInfoContent" style="display: none;">
                <div class="card-body">
                    <h4 class="card-title text-center">Контактная информация</h4>
                    <div class="row">
                        <?php include 'personal-data-change/personal-data-change.php'; ?>
                    </div>
                </div>
            </div>
            <div class="card" id="myAvatarContent" style="display: none;">
                <div class="card-body">
                    <h4 class="card-title text-center">Аватар</h4>
                    <div class="row">
                        <?php include 'avatar/avatar.php'; ?>
                    </div>
                </div>
            </div>
            <div class="card" id="myRepresentativeContent" style="display: none;">
                <div class="card-body">
                    <h4 class="card-title text-center mb-5">Для представителя учебного заведения</h4>
                    <div class="row">
                        <?php include 'representative/representative-content.php'; ?>
                    </div>
                </div>
            </div>
            <div class="card" id="myCommentsContent" style="display: none;">
                <div class="card-body">
                    <h4 class="card-title text-center">Мои комментарии</h4>
                    <div class="row">
                        <?php include 'comments-user/comments-user.php'; ?>
                    </div>
                </div>
            </div>
            <div class="card" id="myNewsContent" style="display: none;">
                <div class="card-body">
                    <h4 class="card-title text-center">Мои новости</h4>
                    <div class="row">
                        <?php include 'news-user/news-user.php'; ?>
                    </div>
                </div>
            </div>
            <div class="card" id="deleteAccountContent" style="display: none;">
                <div class="card-body">
                    <h4 class="card-title text-center">Удалить аккаунт</h4>
                    <div class="row">
                        <?php include 'delete-account/delete-account.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('myProfileLink').addEventListener('click', function(event) {
        event.preventDefault();
        showContent('myProfileContent', 'myProfileLink');
    });

    document.getElementById('passwordLink').addEventListener('click', function(event) {
        event.preventDefault();
        showContent('passwordContent', 'passwordLink');
    });

    document.getElementById('contactInfoLink').addEventListener('click', function(event) {
        event.preventDefault();
        showContent('contactInfoContent', 'contactInfoLink');
    });

    document.getElementById('myAvatarLink').addEventListener('click', function(event) {
        event.preventDefault();
        showContent('myAvatarContent', 'myAvatarLink');
    });

    <?php
    if (
        $occupation === "Представитель ВУЗа" ||
        $occupation === "Представитель ССУЗа" ||
        $occupation === "Представитель школы"
    ) {
    ?>
        document.getElementById('myRepresentativeLink').addEventListener('click', function(event) {
            event.preventDefault();
            showContent('myRepresentativeContent', 'myRepresentativeLink');
        });
    <?php
    }
    ?>

    document.getElementById('myCommentsLink').addEventListener('click', function(event) {
        event.preventDefault();
        showContent('myCommentsContent', 'myCommentsLink');
    });

    document.getElementById('myNewsLink').addEventListener('click', function(event) {
        event.preventDefault();
        showContent('myNewsContent', 'myNewsLink');
    });

    document.getElementById('deleteAccountLink').addEventListener('click', function(event) {
        event.preventDefault();
        showContent('deleteAccountContent', 'deleteAccountLink');
    });

    function showContent(contentId, linkId) {
        console.log('Showing content: ' + contentId);
        document.querySelectorAll('.card').forEach(function(card) {
            card.style.display = 'none';
        });
        document.getElementById(contentId).style.display = 'block';

        // Remove active class from all links
        document.querySelectorAll('.list-group-item').forEach(function(link) {
            link.classList.remove('active');
        });

        // Add active class to the clicked link
        document.getElementById(linkId).classList.add('active');
    }
</script>