<?php
session_start();

// Check if coming from registration
if (!isset($_SESSION['registration_success']) || !$_SESSION['registration_success']) {
    header("Location: /");
    exit();
}

$successMessage = $_SESSION['success_message'] ?? 'Регистрация успешна!';
$showActivationLink = $_SESSION['show_activation_link'] ?? false;
$activationLink = $_SESSION['activation_link'] ?? '';

// Clear session variables
unset($_SESSION['registration_success']);
unset($_SESSION['success_message']);
unset($_SESSION['show_activation_link']);
unset($_SESSION['activation_link']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация успешна - 11-классники</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary-color: #28a745;
            --primary-hover: #218838;
            --text-primary: #333;
            --text-secondary: #666;
            --bg-color: #f8f9fa;
            --card-bg: #ffffff;
            --border-color: #dee2e6;
            --shadow: 0 0 20px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .success-container {
            width: 100%;
            max-width: 500px;
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--shadow);
            overflow: hidden;
            animation: slideIn 0.4s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .success-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .success-icon {
            font-size: 60px;
            margin-bottom: 15px;
        }
        
        .success-header h1 {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }
        
        .success-body {
            padding: 30px;
            text-align: center;
        }
        
        .message {
            font-size: 16px;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 25px;
        }
        
        .activation-link-box {
            background: #f0f8ff;
            border: 2px solid #d0e8ff;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        
        .activation-link-box h3 {
            color: #0066cc;
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .activation-link-box p {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .activation-link {
            display: block;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            word-break: break-all;
            font-family: monospace;
            font-size: 12px;
            color: #0066cc;
            margin-bottom: 10px;
        }
        
        .copy-btn {
            background: #0066cc;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: var(--transition);
        }
        
        .copy-btn:hover {
            background: #0052a3;
        }
        
        .copy-btn.copied {
            background: var(--primary-color);
        }
        
        .action-buttons {
            margin-top: 25px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: var(--transition);
            margin: 0 5px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #545b62);
        }
        
        .btn-secondary:hover {
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
        }
        
        .info-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 15px;
            margin-top: 20px;
            font-size: 14px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-header">
            <div class="success-icon">✓</div>
            <h1>Регистрация успешна!</h1>
        </div>
        
        <div class="success-body">
            <p class="message"><?= htmlspecialchars($successMessage) ?></p>
            
            <?php if ($showActivationLink && $activationLink): ?>
                <div class="activation-link-box">
                    <h3>⚠️ Важно: Сохраните ссылку для активации</h3>
                    <p>Email не был отправлен. Используйте эту ссылку для активации аккаунта:</p>
                    <div class="activation-link" id="activationLink"><?= htmlspecialchars($activationLink) ?></div>
                    <button class="copy-btn" onclick="copyLink()">Скопировать ссылку</button>
                </div>
                
                <div class="info-box">
                    <strong>Примечание:</strong> Для автоматической отправки email необходимо настроить SMTP. 
                    См. EMAIL_CONFIGURATION_GUIDE.md для инструкций.
                </div>
            <?php else: ?>
                <div class="info-box">
                    <strong>Что дальше?</strong><br>
                    Проверьте свою электронную почту и перейдите по ссылке активации. 
                    Если письмо не пришло в течение нескольких минут, проверьте папку "Спам".
                </div>
            <?php endif; ?>
            
            <div class="action-buttons">
                <a href="/login" class="btn">Перейти к входу</a>
                <a href="/" class="btn btn-secondary">На главную</a>
            </div>
        </div>
    </div>
    
    <script>
    function copyLink() {
        const linkText = document.getElementById('activationLink').textContent;
        navigator.clipboard.writeText(linkText).then(function() {
            const btn = document.querySelector('.copy-btn');
            btn.textContent = '✓ Скопировано!';
            btn.classList.add('copied');
            setTimeout(() => {
                btn.textContent = 'Скопировать ссылку';
                btn.classList.remove('copied');
            }, 2000);
        }).catch(function(err) {
            // Fallback for older browsers
            const textArea = document.createElement("textarea");
            textArea.value = linkText;
            textArea.style.position = "fixed";
            textArea.style.left = "-999999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                const btn = document.querySelector('.copy-btn');
                btn.textContent = '✓ Скопировано!';
                btn.classList.add('copied');
                setTimeout(() => {
                    btn.textContent = 'Скопировать ссылку';
                    btn.classList.remove('copied');
                }, 2000);
            } catch (err) {
                alert('Не удалось скопировать. Пожалуйста, выделите и скопируйте вручную.');
            }
            document.body.removeChild(textArea);
        });
    }
    </script>
</body>
</html>