<?php
$occupation = $_SESSION["occupation"];
?>

    <?php
    if ($occupation === "Представитель ВУЗа" || $occupation === "Представитель ССУЗа" || $occupation === "Представитель школы") {
        include 'representative-create-page.php';
    }
    ?>

    <?php
    if ($occupation === "Представитель ВУЗа" || $occupation === "Представитель ССУЗа" || $occupation === "Представитель школы") {
        include 'representative-create-news.php';
    }
    ?>