<?php
// Terms of use page
session_start();
$page_title = 'Условия использования - 11klassniki.ru';

// Section 1: Hero
ob_start();
?>
<div style="padding: 20px 20px 20px; background: white; box-shadow: 0 1px 0 rgba(0,0,0,0.08);">
    <div style="max-width: 800px; margin: 0 auto; text-align: center;">
        <h1 style="font-size: 44px; font-weight: 800; margin-bottom: 16px; color: #222222; letter-spacing: -0.02em;">
            Условия использования
        </h1>
        <p style="font-size: 18px; color: #717171; line-height: 1.5;">
            Правила пользования платформой 11klassniki.ru
        </p>
        <div style="font-size: 14px; color: #717171; margin-top: 15px;">
            Дата последнего обновления: <?php echo date('d.m.Y'); ?>
        </div>
    </div>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: General terms
ob_start();
?>
<div style="padding: 30px 20px; background: white;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h2 style="font-size: 28px; font-weight: 600; margin-bottom: 30px; color: #333;">1. Общие положения</h2>
        
        <div style="margin-bottom: 30px;">
            <p style="color: #555; line-height: 1.7; margin-bottom: 15px;">
                Данные Условия использования регулируют порядок пользования веб-сайтом 11klassniki.ru (далее — «Сайт») 
                и предоставляемыми на нем услугами (далее — «Услуги»).
            </p>
            <p style="color: #555; line-height: 1.7; margin-bottom: 15px;">
                Используя Сайт, вы подтверждаете, что:
            </p>
            <ul style="color: #555; line-height: 1.7; margin-left: 20px;">
                <li style="margin-bottom: 8px;">Ознакомились с настоящими Условиями и согласны с ними</li>
                <li style="margin-bottom: 8px;">Достигли возраста 14 лет или используете Сайт с разрешения родителей/опекунов</li>
                <li style="margin-bottom: 8px;">Обладаете правоспособностью заключать соглашения</li>
            </ul>
        </div>
    </div>
</div>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Services
ob_start();
?>
<div style="padding: 30px 20px; background: #f8f9fa;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h2 style="font-size: 28px; font-weight: 600; margin-bottom: 30px; color: #333;">2. Описание услуг</h2>
        
        <div style="margin-bottom: 25px;">
            <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 15px; color: #333;">2.1 Образовательная платформа</h3>
            <p style="color: #555; line-height: 1.7; margin-bottom: 15px;">
                11klassniki.ru предоставляет информационные услуги в сфере образования, включающие:
            </p>
            <ul style="color: #555; line-height: 1.7; margin-left: 20px;">
                <li style="margin-bottom: 8px;">Справочную информацию об образовательных учреждениях России</li>
                <li style="margin-bottom: 8px;">Календарь образовательных событий и мероприятий</li>
                <li style="margin-bottom: 8px;">Персональные рекомендации по выбору образовательного пути</li>
                <li style="margin-bottom: 8px;">Возможность оставлять отзывы и оценки учебных заведений</li>
            </ul>
        </div>

        <div style="margin-bottom: 25px;">
            <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 15px; color: #333;">2.2 Ограничения</h3>
            <p style="color: #555; line-height: 1.7; margin-bottom: 15px;">
                Мы оставляем за собой право изменять, приостанавливать или прекращать предоставление 
                любых услуг без предварительного уведомления.
            </p>
        </div>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();

// Section 4: User obligations
ob_start();
?>
<div style="padding: 30px 20px; background: white;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h2 style="font-size: 28px; font-weight: 600; margin-bottom: 30px; color: #333;">3. Обязательства пользователя</h2>
        
        <div style="margin-bottom: 25px;">
            <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 15px; color: #333;">3.1 Запрещенные действия</h3>
            <p style="color: #555; line-height: 1.7; margin-bottom: 15px;">
                При использовании Сайта запрещается:
            </p>
            <ul style="color: #555; line-height: 1.7; margin-left: 20px;">
                <li style="margin-bottom: 8px;">Размещать недостоверную, клеветническую или оскорбительную информацию</li>
                <li style="margin-bottom: 8px;">Нарушать права интеллектуальной собственности третьих лиц</li>
                <li style="margin-bottom: 8px;">Использовать автоматизированные средства для сбора информации</li>
                <li style="margin-bottom: 8px;">Пытаться получить несанкционированный доступ к системам Сайта</li>
                <li style="margin-bottom: 8px;">Распространять вредоносное программное обеспечение</li>
            </ul>
        </div>

        <div style="margin-bottom: 25px;">
            <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 15px; color: #333;">3.2 Ответственность за контент</h3>
            <p style="color: #555; line-height: 1.7; margin-bottom: 15px;">
                Пользователь несет полную ответственность за размещаемый им контент и гарантирует, 
                что обладает всеми необходимыми правами для его публикации.
            </p>
        </div>
    </div>
</div>
<?php
$greyContent4 = ob_get_clean();

// Section 5: Privacy and data
ob_start();
?>
<div style="padding: 30px 20px; background: #f8f9fa;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h2 style="font-size: 28px; font-weight: 600; margin-bottom: 30px; color: #333;">4. Конфиденциальность и защита данных</h2>
        
        <div style="margin-bottom: 25px;">
            <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 15px; color: #333;">4.1 Сбор данных</h3>
            <p style="color: #555; line-height: 1.7; margin-bottom: 15px;">
                Мы собираем и обрабатываем персональные данные в соответствии с Федеральным законом 
                «О персональных данных» № 152-ФЗ и нашей Политикой конфиденциальности.
            </p>
        </div>

        <div style="margin-bottom: 25px;">
            <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 15px; color: #333;">4.2 Использование cookies</h3>
            <p style="color: #555; line-height: 1.7; margin-bottom: 15px;">
                Сайт использует файлы cookies для улучшения пользовательского опыта. 
                Продолжая использование Сайта, вы соглашаетесь с использованием cookies.
            </p>
        </div>

        <div style="margin-bottom: 25px;">
            <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 15px; color: #333;">4.3 Безопасность</h3>
            <p style="color: #555; line-height: 1.7; margin-bottom: 15px;">
                Мы принимаем технические и организационные меры для защиты ваших персональных данных 
                от несанкционированного доступа, изменения, раскрытия или уничтожения.
            </p>
        </div>
    </div>
</div>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Liability and changes
ob_start();
?>
<div style="padding: 30px 20px; background: white;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h2 style="font-size: 28px; font-weight: 600; margin-bottom: 30px; color: #333;">5. Ответственность и изменения</h2>
        
        <div style="margin-bottom: 25px;">
            <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 15px; color: #333;">5.1 Ограничение ответственности</h3>
            <p style="color: #555; line-height: 1.7; margin-bottom: 15px;">
                Сайт предоставляется «как есть». Мы не гарантируем бесперебойную работу Сайта 
                и не несем ответственности за любые убытки, возникшие в результате его использования.
            </p>
        </div>

        <div style="margin-bottom: 25px;">
            <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 15px; color: #333;">5.2 Изменения условий</h3>
            <p style="color: #555; line-height: 1.7; margin-bottom: 15px;">
                Мы можем изменять данные Условия в любое время. Актуальная версия всегда доступна 
                по адресу 11klassniki.ru/terms.php. Существенные изменения вступают в силу через 
                30 дней после публикации.
            </p>
        </div>

        <div style="margin-bottom: 25px;">
            <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 15px; color: #333;">5.3 Контактная информация</h3>
            <p style="color: #555; line-height: 1.7; margin-bottom: 15px;">
                По всем вопросам, связанным с данными Условиями, обращайтесь к нам через 
                <a href="/contact.php" style="color: #667eea; text-decoration: none;">страницу обратной связи</a>.
            </p>
        </div>

        <div style="background: #f0f4ff; padding: 20px; border-radius: 8px; border-left: 4px solid #667eea; margin-top: 30px;">
            <p style="color: #333; margin: 0; font-weight: 500;">
                <i class="fas fa-info-circle" style="color: #667eea; margin-right: 8px;"></i>
                Используя наш Сайт, вы подтверждаете, что ознакомились с данными Условиями 
                и согласны соблюдать их.
            </p>
        </div>
    </div>
</div>
<?php
$greyContent6 = ob_get_clean();

$blueContent = '';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/template.php';
?>