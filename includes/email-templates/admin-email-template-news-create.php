<?php
$subject = "Создана новость";
$author;
$title;
$description;
$text;

// Create the email body
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
        <div class='email-content'>
            <div class='greeting' style='font-size: 20px; font-weight: bold; color: #2c3e50;'>Здравствуйте!</div>
            <div class='message' style='font-size: 16px; color: #34495e; margin-top: 15px;'>
                <p style='font-size: 18px; font-weight: bold; color: #16a085;'>Пользователь " . htmlspecialchars($author) . " добавил новость:</p>
                <p style='font-size: 18px; font-weight: bold; color: #e67e22;'>" . htmlspecialchars($title) . "</p>
                
                <p style='font-size: 16px; line-height: 1.5;'>" . htmlspecialchars($description) . "</p>
                
                <div style='margin-top: 20px; font-size: 16px; line-height: 1.5; color: #7f8c8d;'>
                    " . nl2br(htmlspecialchars($text)) . "
                </div>
            </div>
        </div>

        <div class='signature'>
            С уважением,<br>
            Команда технической поддержки 11klassniki.ru
        </div>
    </body>
    </html>
";
?>
