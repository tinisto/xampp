<?php
$subject = 'Активировать ваш аккаунт на 11klassniki.ru';
$activationLink;

$body = "
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .greeting {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .message {
            font-size: 16px;
            margin-top: 10px;
        }
        .cta-button {
            background-color: #ffd800;
            color: #000;
            padding: 12px 20px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            text-align: center;
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            width: auto; /* Make button width auto */
            max-width: 300px; /* Set a max width for the button */
            margin-left: auto;
            margin-right: auto;
        }

        .cta-button:hover {
            background-color: #f1c40f;
        }
        .signature {
            margin-top: 20px;
            font-size: 16px;
            color: #555;
        }
        .footer {
            font-size: 14px;
            color: #888;
            margin-top: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class='email-container'>
        <div class='greeting'>Здравствуйте!</div>
        <div class='message'>
            Спасибо за регистрацию на сайте 11klassniki.ru!
        </div>
        
        <a href=\"$activationLink\" class='cta-button'>Активировать аккаунт</a>

        <div class='message'>
            Пожалуйста, активируйте ваш аккаунт в течение 24 часов. После этого срока токен активации может устареть.
        </div>

        <div class='signature'>
            С уважением,<br>Команда 11klassniki.ru
        </div>

        <div class='footer'>
            Если у вас есть вопросы, не стесняйтесь обращаться к нам.
        </div>
    </div>
</body>
</html>";
?>
