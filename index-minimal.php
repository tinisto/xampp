<?php
// Minimal homepage without database dependency
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content for template sections
$greyContent1 = '<div style="text-align: center; padding: 30px;">
    <h1 style="font-size: 36px; color: #333; margin: 0;">11-классники</h1>
    <p style="color: #666; margin: 10px 0 0 0;">Образовательный портал для школьников, абитуриентов и студентов</p>
</div>';

$greyContent2 = '';

$greyContent3 = '<div style="display: flex; justify-content: space-around; flex-wrap: wrap; gap: 20px; padding: 20px;">
    <div style="text-align: center;">
        <div style="font-size: 32px; font-weight: 700; color: #28a745;">15,000+</div>
        <div style="font-size: 16px; color: #666;">Школ</div>
    </div>
    <div style="text-align: center;">
        <div style="font-size: 32px; font-weight: 700; color: #28a745;">500+</div>
        <div style="font-size: 16px; color: #666;">ВУЗов</div>
    </div>
    <div style="text-align: center;">
        <div style="font-size: 32px; font-weight: 700; color: #28a745;">1,000+</div>
        <div style="font-size: 16px; color: #666;">ССУЗов</div>
    </div>
    <div style="text-align: center;">
        <div style="font-size: 32px; font-weight: 700; color: #28a745;">200+</div>
        <div style="font-size: 16px; color: #666;">Статей</div>
    </div>
</div>';

$greyContent4 = '<div style="text-align: center; padding: 20px;">
    <form action="/search" method="get" style="display: inline-flex; gap: 10px;">
        <input type="text" name="q" placeholder="Поиск по сайту..." style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; width: 400px;">
        <button type="submit" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">Найти</button>
    </form>
</div>';

$greyContent5 = '<div style="padding: 20px;">
    <h2 style="text-align: center; color: #333; margin-bottom: 30px;">Последние статьи</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
        <div style="background: white; border: 1px solid #eee; border-radius: 8px; overflow: hidden;">
            <div style="height: 150px; background: #f0f0f0;"></div>
            <div style="padding: 15px;">
                <h3 style="margin: 0 0 10px 0; font-size: 18px;">Пример статьи 1</h3>
                <p style="color: #666; font-size: 14px; margin: 0;">Краткое описание статьи...</p>
            </div>
        </div>
        <div style="background: white; border: 1px solid #eee; border-radius: 8px; overflow: hidden;">
            <div style="height: 150px; background: #f0f0f0;"></div>
            <div style="padding: 15px;">
                <h3 style="margin: 0 0 10px 0; font-size: 18px;">Пример статьи 2</h3>
                <p style="color: #666; font-size: 14px; margin: 0;">Краткое описание статьи...</p>
            </div>
        </div>
        <div style="background: white; border: 1px solid #eee; border-radius: 8px; overflow: hidden;">
            <div style="height: 150px; background: #f0f0f0;"></div>
            <div style="padding: 15px;">
                <h3 style="margin: 0 0 10px 0; font-size: 18px;">Пример статьи 3</h3>
                <p style="color: #666; font-size: 14px; margin: 0;">Краткое описание статьи...</p>
            </div>
        </div>
        <div style="background: white; border: 1px solid #eee; border-radius: 8px; overflow: hidden;">
            <div style="height: 150px; background: #f0f0f0;"></div>
            <div style="padding: 15px;">
                <h3 style="margin: 0 0 10px 0; font-size: 18px;">Пример статьи 4</h3>
                <p style="color: #666; font-size: 14px; margin: 0;">Краткое описание статьи...</p>
            </div>
        </div>
    </div>
</div>';

$greyContent6 = '';
$blueContent = '';

// Set page metadata
$pageTitle = 'Главная';
$metaD = '11-классники - образовательный портал для школьников, абитуриентов и студентов.';
$metaK = '11-классники, образование, школы, ВУЗы, СПО';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>