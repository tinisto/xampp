<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';

// Check if the environment is under construction
if ($_ENV['APP_ENV'] !== 'under_construction') {
    // Redirect to the index page if not under construction
    header('Location: /index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сайт в разработке</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
        }

        h1 {
            font-size: 3em;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.2em;
        }
    </style>
</head>

<body>
    <h1>Сайт в разработке</h1>
    <p>Мы в настоящее время работаем над обновлениями.</p>
    <p>Пожалуйста, загляните позже.</p>
</body>

</html>