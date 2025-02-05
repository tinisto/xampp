<?php
$subject = "Ваша новость на обновление была отклонена";
$body = "
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                color: #333;
                line-height: 1.6;
            }
            .greeting {
                font-size: 18px;
                font-weight: bold;
            }
            .message {
                font-size: 16px;
                margin-top: 10px;
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
            }
        </style>
    </head>
    <body>
        <div class='greeting'>Здравствуйте!</div>
        <div class='message'>
            К сожалению, ваши изменения для вашего учебного заведения были отклонены.
            <br><br>
            Если у вас есть вопросы, пожалуйста, свяжитесь с нами.
        </div>
        <div class='signature'>
            С уважением,<br>
            Команда технической поддержки 11klassniki.ru
        </div>
        <div class='footer'>
            Если у вас есть вопросы, не стесняйтесь обращаться к нам.
        </div>
    </body>
    </html>
";
