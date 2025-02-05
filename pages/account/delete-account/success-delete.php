<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config/constants.php';

?>
<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Аккаунт успешно удален</title>
</head>

<body>
  <div style="text-align: center; margin-top: 50px;">
    <h1>Ваш аккаунт успешно удален</h1>
    <p>Спасибо, что были с нами. Если у вас есть отзывы, не стесняйтесь сообщить нам по адресу 
      <a href="mailto:<?php echo ADMIN_EMAIL; ?>"><?php echo ADMIN_EMAIL; ?></a></p>
    <p class="mt-3 text-center"><a href="/" class="text-decoration-none">Вернуться на сайт</a>.</p>
  </div>
</body>


</html>