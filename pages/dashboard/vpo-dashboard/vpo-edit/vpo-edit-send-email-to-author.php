<?php
$topic = "Ваш запрос на обновление информации об учебном заведении";

$body = "
            <html>
            <head>
            <style>
            body {
                font-family: 'Arial', sans-serif;
                background-color: #f4f4f4;
                padding: 20px;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                background-color: #fff;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                padding: 20px;
            }
            h1 {
                color: #333;
            }
            p {
                color: #555;
            }
            .cta-button {
                display: inline-block;
                padding: 10px 20px;
                background-color: #4caf50;
                color: #fff;
                text-decoration: none;
                border-radius: 5px;
            }
        </style>
            </head>
            <body>
            <div class='container'>
            <h1>Здравствуйте!</h1>
            <p style='font-size: 16px;'>К сожалению, Ваш запрос на обновление информации об учебном заведении на сайте 11klassniki.ru отклонен.</p>
            <p style='font-size: 16px;'>С наилучшими пожеланиями,<br>команда 11klassniki.ru</p>
        </div>
            </body>
            </html>
            ";

commonEmail($user_id, $topic, $body);
