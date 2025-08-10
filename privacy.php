<?php
// Minimal privacy policy page without header/footer
?>
<!DOCTYPE html>
<html lang="ru" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Политика конфиденциальности - 11klassniki.ru</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8cmVjdCB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHJ4PSI2IiBmaWxsPSJ1cmwoI2dyYWRpZW50KSIvPgogIDx0ZXh0IHg9IjE2IiB5PSIyMiIgZm9udC1mYW1pbHk9InNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMTgiIGZvbnQtd2VpZ2h0PSI3MDAiIGZpbGw9IndoaXRlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj4xMTwvdGV4dD4KICA8ZGVmcz4KICAgIDxsaW5lYXJHcmFkaWVudCBpZD0iZ3JhZGllbnQiIHgxPSIwIiB5MT0iMCIgeDI9IjMyIiB5Mj0iMzIiPgogICAgICA8c3RvcCBzdG9wLWNvbG9yPSIjMDA3YmZmIi8+CiAgICAgIDxzdG9wIG9mZnNldD0iMSIgc3RvcC1jb2xvcj0iIzAwNTNkNCIvPgogICAgPC9saW5lYXJHcmFkaWVudD4KICA8L2RlZnM+Cjwvc3ZnPg==" type="image/svg+xml">
    
    <style>
        :root {
            --bg-primary: #ffffff;
            --text-primary: #333333;
            --text-secondary: #666666;
            --link-color: #007bff;
        }
        
        [data-bs-theme="dark"] {
            --bg-primary: #1a1a1a;
            --text-primary: #f8f9fa;
            --text-secondary: #adb5bd;
            --link-color: #66b3ff;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            line-height: 1.6;
            color: var(--text-primary);
            background: var(--bg-primary);
            transition: background-color 0.3s, color 0.3s;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .back-link {
            position: fixed;
            top: 20px;
            left: 20px;
            background: var(--bg-primary);
            color: var(--text-primary);
            padding: 12px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 500;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
            z-index: 1000;
        }
        
        .back-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            color: var(--link-color);
        }
        
        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--bg-primary);
            border: 2px solid #e9ecef;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .theme-toggle:hover {
            transform: rotate(180deg);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        
        h1 {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: var(--text-secondary);
            font-size: 18px;
        }
        
        h2 {
            font-size: 22px;
            font-weight: 600;
            margin: 40px 0 20px 0;
            color: var(--text-primary);
        }
        
        p {
            margin-bottom: 20px;
            font-size: 16px;
            line-height: 1.7;
        }
        
        ul {
            margin-bottom: 20px;
            padding-left: 30px;
        }
        
        li {
            margin-bottom: 8px;
        }
        
        a {
            color: var(--link-color);
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
        }
        
        .update-date {
            margin-top: 40px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid var(--link-color);
        }
        
        .update-date p {
            margin: 0;
            font-size: 14px;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 20px 15px;
            }
            
            .back-link, .theme-toggle {
                position: relative;
                top: auto;
                left: auto;
                right: auto;
                margin: 10px;
            }
            
            .header {
                margin-top: 20px;
            }
            
            h1 {
                font-size: 28px;
            }
            
            h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <a href="/" class="back-link">
        <i class="fas fa-arrow-left"></i> Вернуться на сайт
    </a>
    
    <button onclick="toggleTheme()" class="theme-toggle" aria-label="Toggle dark mode">
        <i class="fas fa-moon" id="theme-icon"></i>
    </button>
    
    <div class="container">
        <div class="header">
            <h1>Политика конфиденциальности</h1>
            <p class="subtitle">Обработка персональных данных в соответствии с законодательством РФ</p>
        </div>
        
        <div style="font-size: 16px; line-height: 1.7; color: var(--text-primary);">
            
            <h2>1. Общие положения</h2>
            <p>
                Настоящая Политика конфиденциальности (далее — Политика) действует в отношении всей информации, 
                включая персональные данные в понимании применимого законодательства (далее — «Персональные данные»), 
                которую Администрация сайта 11klassniki.ru и/или его аффилированные лица (далее — «Мы») 
                можем получить о Вас в процессе использования Вами любых сайтов, сервисов, служб, 
                программ для ЭВМ, продуктов или услуг сайта 11klassniki.ru (далее — «Сервисы») 
                и в ходе исполнения любых соглашений и договоров, 
                заключенных с Вами.
            </p>
            <p>
                Использование Сервисов означает безоговорочное согласие с настоящей Политикой и указанными в ней 
                условиями обработки Персональных данных; в случае несогласия с этими условиями Вы должны 
                воздержаться от использования Сервисов.
            </p>

            <h2>2. Персональные данные</h2>
            <p>
                В рамках настоящей Политики под «Персональными данными» понимается информация, определенная как 
                «персональные данные» в Федеральном законе Российской Федерации от 27 июля 2006 г. № 152-ФЗ 
                «О персональных данных».
            </p>
            <p>
                Персональные данные разрешается обрабатывать в следующих случаях:
            </p>
            <ul>
                <li>обработка персональных данных осуществляется с согласия субъекта персональных данных на обработку его персональных данных;</li>
                <li>обработка персональных данных необходима для достижения целей, предусмотренных международным договором Российской Федерации или законом, для осуществления и выполнения возложенных законодательством Российской Федерации на оператора функций, полномочий и обязанностей;</li>
                <li>обработка персональных данных необходима для исполнения договора, стороной которого либо выгодоприобретателем или поручителем по которому является субъект персональных данных.</li>
            </ul>

            <h2>3. Цели обработки персональных данных</h2>
            <p>
                Мы собираем и используем Персональные данные в следующих целях:
            </p>
            <ul>
                <li>Идентификация стороны в рамках соглашений и договоров с Администрацией сайта;</li>
                <li>Предоставление Пользователю персонализированных Сервисов;</li>
                <li>Связь с Пользователем, в том числе направление уведомлений, запросов и информации, касающихся использования Сервисов, исполнения соглашений и договоров, а также обработка запросов и заявок от Пользователя;</li>
                <li>Улучшение качества Сервисов, удобства их использования, разработка новых Сервисов и функционала;</li>
                <li>Таргетирование рекламных материалов;</li>
                <li>Проведение статистических и иных исследований на основе обезличенных данных.</li>
            </ul>

            <h2>4. Условия обработки персональных данных</h2>
            <p>
                Обработка Персональных данных осуществляется с соблюдением принципов и правил, предусмотренных 
                Федеральным законом «О персональных данных»:
            </p>
            <ul>
                <li>обработка Персональных данных осуществляется на законной и справедливой основе;</li>
                <li>обработка Персональных данных ограничивается достижением конкретных, заранее определенных и законных целей;</li>
                <li>не допускается обработка Персональных данных, несовместимая с целями сбора Персональных данных;</li>
                <li>не допускается объединение баз данных, содержащих Персональные данные, обработка которых осуществляется в целях, несовместимых между собой;</li>
                <li>обработке подлежат только Персональные данные, которые отвечают целям их обработки;</li>
                <li>содержание и объем обрабатываемых Персональных данных соответствуют заявленным целям обработки;</li>
                <li>при обработке Персональных данных обеспечивается точность Персональных данных, их достаточность, а в необходимых случаях и актуальность по отношению к целям обработки Персональных данных.</li>
            </ul>

            <h2>5. Способы и сроки обработки персональных данных</h2>
            <p>
                Обработка Персональных данных может осуществляться с использованием средств автоматизации или без 
                использования таких средств. Обработка Персональных данных с использованием средств автоматизации 
                осуществляется с применением информационных технологий, позволяющих обеспечить безопасность 
                Персональных данных.
            </p>
            <p>
                Персональные данные обрабатываются в течение срока, необходимого для достижения целей обработки 
                Персональных данных, если иное не предусмотрено договором или применимым законодательством. 
                Персональные данные подлежат уничтожению либо обезличиванию по достижении целей обработки или в 
                случае утраты необходимости в достижении этих целей, если иное не предусмотрено федеральным законом.
            </p>

            <h2>6. Права субъектов персональных данных</h2>
            <p>
                В соответствии с Федеральным законом «О персональных данных» Вы имеете право:
            </p>
            <ul>
                <li>получать информацию, касающуюся обработки Персональных данных;</li>
                <li>требовать уточнения Персональных данных, их блокирования или уничтожения в случае, если Персональные данные являются неполными, устаревшими, неточными, незаконно полученными или не являются необходимыми для заявленной цели обработки;</li>
                <li>отзывать согласие на обработку Персональных данных;</li>
                <li>обжаловать действия или бездействие Оператора в уполномоченный орган по защите прав субъектов персональных данных или в судебном порядке;</li>
                <li>на защиту своих прав и законных интересов, в том числе на возмещение убытков и/или компенсацию морального вреда в судебном порядке.</li>
            </ul>

            <h2>7. Меры по обеспечению безопасности персональных данных</h2>
            <p>
                Мы принимаем необходимые правовые, организационные и технические меры или обеспечиваем их принятие 
                для защиты Персональных данных от неправомерного или случайного доступа к ним, уничтожения, 
                изменения, блокирования, копирования, предоставления, распространения Персональных данных, а также 
                от иных неправомерных действий в отношении Персональных данных.
            </p>
            <p>
                Обеспечение безопасности Персональных данных достигается в том числе:
            </p>
            <ul>
                <li>назначением ответственного за организацию обработки Персональных данных;</li>
                <li>осуществлением внутреннего контроля соответствия обработки Персональных данных Федеральному закону «О персональных данных» и принятым в соответствии с ним нормативным правовым актам;</li>
                <li>оценкой вреда, который может быть причинен субъектам Персональных данных в случае нарушения Федерального закона «О персональных данных»;</li>
                <li>применением организационных и технических мер по обеспечению безопасности Персональных данных при их обработке в информационных системах персональных данных.</li>
            </ul>

            <h2>8. Передача персональных данных</h2>
            <p>
                Мы не раскрываем третьим лицам и не распространяем Персональные данные без согласия субъекта 
                Персональных данных, за исключением случаев, предусмотренных федеральным законом.
            </p>
            <p>
                Условия, при которых Администрация сайта имеет право передать Персональные данные третьим лицам:
            </p>
            <ul>
                <li>субъект Персональных данных выразил свое согласие на такие действия;</li>
                <li>передача предусмотрена российским или иным применимым законодательством в рамках установленной законодательством процедуры;</li>
                <li>передача происходит в рамках продажи или иной передачи бизнеса (полностью или в части), при этом к приобретателю переходят все обязательства по соблюдению условий настоящей Политики применительно к полученным им Персональным данным;</li>
                <li>в целях обеспечения возможности защиты прав и законных интересов Администрации сайта или третьих лиц в случаях, когда субъект Персональных данных нарушает Пользовательское соглашение сервисов сайта.</li>
            </ul>

            <h2>9. Изменение Политики конфиденциальности</h2>
            <p>
                Настоящая Политика конфиденциальности может изменяться нами. При внесении изменений в актуальной 
                редакции указывается дата последнего обновления. Новая редакция Политики вступает в силу с момента 
                ее размещения, если иное не предусмотрено новой редакцией Политики.
            </p>
            <p>
                Действующая редакция всегда находится на странице по адресу 
                <a href="/privacy">https://11klassniki.ru/privacy</a>
            </p>

            <h2>10. Обратная связь. Вопросы и предложения</h2>
            <p>
                Все вопросы и предложения по поводу настоящей Политики Вы можете направить через 
                <a href="/contact.php">форму обратной связи</a> на нашем сайте.
            </p>
            
            <div class="update-date">
                <p>
                    <strong>Дата последнего обновления:</strong> 10 августа 2025 года
                </p>
            </div>

        </div>
    </div>
    
    <script>
        // Theme toggle functionality
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-bs-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            html.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            const themeIcon = document.getElementById('theme-icon');
            if (themeIcon) {
                themeIcon.className = newTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
            }
        }
        
        // Initialize theme
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            const html = document.documentElement;
            const themeIcon = document.getElementById('theme-icon');
            
            html.setAttribute('data-bs-theme', savedTheme);
            
            if (themeIcon) {
                themeIcon.className = savedTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
            }
        });
    </script>
</body>
</html>