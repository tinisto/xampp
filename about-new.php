<?php
/**
 * About page using real_template.php
 */

// Section 1: Title
$greyContent1 = '<div style="padding: 30px; text-align: center;"><h1>О проекте 11-классники</h1></div>';

// Section 2: Empty
$greyContent2 = '';

// Section 3: Empty
$greyContent3 = '';

// Section 4: Empty
$greyContent4 = '';

// Section 5: Main content
$greyContent5 = '<div style="padding: 40px; max-width: 800px; margin: 0 auto; line-height: 1.6;">
    <h2 style="color: #333; margin-bottom: 20px;">Добро пожаловать на портал 11-классники!</h2>
    
    <p style="font-size: 18px; margin-bottom: 20px;">
        Наш образовательный портал создан специально для выпускников школ, студентов и всех, 
        кто стремится к получению качественного образования в России.
    </p>
    
    <h3 style="color: #28a745; margin: 30px 0 15px 0;">Что мы предлагаем:</h3>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin: 30px 0;">
        <div style="padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;">
            <h4 style="color: #333; margin-bottom: 15px;">🎓 Образовательные учреждения</h4>
            <p>Полная база данных ВУЗов, СПО и школ России с подробной информацией о специальностях, условиях поступления и контактных данных.</p>
        </div>
        
        <div style="padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;">
            <h4 style="color: #333; margin-bottom: 15px;">📰 Актуальные новости</h4>
            <p>Последние новости образования, изменения в системе поступления, стипендии и гранты.</p>
        </div>
        
        <div style="padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;">
            <h4 style="color: #333; margin-bottom: 15px;">📝 Онлайн тесты</h4>
            <p>Подготовка к ЕГЭ, ОГЭ и вступительным экзаменам с помощью интерактивных тестов.</p>
        </div>
        
        <div style="padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;">
            <h4 style="color: #333; margin-bottom: 15px;">💡 Полезные статьи</h4>
            <p>Советы по выбору профессии, подготовке документов и успешному поступлению.</p>
        </div>
    </div>
    
    <h3 style="color: #28a745; margin: 30px 0 15px 0;">Наша миссия</h3>
    <p>
        Мы помогаем молодым людям сделать осознанный выбор своего образовательного пути, 
        предоставляя всю необходимую информацию в удобном и доступном формате.
    </p>
    
    <div style="text-align: center; margin: 40px 0; padding: 20px; background: #e3f2fd; border-radius: 8px;">
        <h4 style="color: #1976d2; margin-bottom: 15px;">Присоединяйтесь к нам!</h4>
        <p style="margin-bottom: 20px;">Станьте частью сообщества будущих студентов и профессионалов.</p>
        <a href="/register" style="display: inline-block; padding: 12px 30px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; font-weight: 600;">
            Зарегистрироваться
        </a>
    </div>
</div>';

// Section 6: Empty
$greyContent6 = '';

// Section 7: No comments
$blueContent = '';

// Page title
$pageTitle = 'О проекте - 11-классники';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>