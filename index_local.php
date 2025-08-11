<?php
// Homepage - Local development version without database dependencies
$connection = null; // No database needed locally

$page_title = '11-классники - Образовательный портал';

// Section 1: Title
ob_start();
?>
<script>
// Remove hash from URL on homepage
if (window.location.hash) {
    history.replaceState(null, null, window.location.pathname + window.location.search);
}
</script>
<div style="text-align: center; margin: 30px 0;">
    <h1 style="font-size: 36px; font-weight: 700; color: #007bff; margin-bottom: 10px;">11-классники</h1>
    <p style="font-size: 18px; color: #666;">Образовательный портал для школьников, абитуриентов и студентов</p>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: Empty
$greyContent2 = '';

// Section 3: Stats section with mock data
ob_start();
?>
<div style="display: flex; justify-content: space-around; flex-wrap: wrap; gap: 20px; padding: 20px; background: #f8f9fa; border-radius: 10px; margin: 20px 0;">
    <div style="text-align: center;">
        <div style="font-size: 32px; font-weight: 700; color: #007bff;">3,318</div>
        <div style="font-size: 16px; color: #666;">Школ</div>
    </div>
    <div style="text-align: center;">
        <div style="font-size: 32px; font-weight: 700; color: #007bff;">2,520</div>
        <div style="font-size: 16px; color: #666;">ВУЗов</div>
    </div>
    <div style="text-align: center;">
        <div style="font-size: 32px; font-weight: 700; color: #007bff;">1,850</div>
        <div style="font-size: 16px; color: #666;">ССУЗов</div>
    </div>
    <div style="text-align: center;">
        <div style="font-size: 32px; font-weight: 700; color: #007bff;">496</div>
        <div style="font-size: 16px; color: #666;">Статей</div>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();

// Section 4: Featured Posts with mock data
ob_start();
?>
<div style="margin: 40px 0;">
    <h2 style="font-size: 28px; font-weight: 700; margin-bottom: 20px; text-align: center;">Рекомендуемые статьи</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <!-- Mock post 1 -->
        <div style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden; background: white;">
            <div style="padding: 15px;">
                <span style="background: #17a2b8; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px;">11-классники</span>
                <h3 style="margin: 10px 0; font-size: 16px;"><a href="/post/sample-post-1" style="color: #333; text-decoration: none;">Как выбрать ВУЗ: полное руководство</a></h3>
                <p style="color: #666; font-size: 14px; margin: 10px 0;">Подробное руководство по выбору высшего учебного заведения...</p>
                <small style="color: #999;">10 августа 2025</small>
            </div>
        </div>
        
        <!-- Mock post 2 -->
        <div style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden; background: white;">
            <div style="padding: 15px;">
                <span style="background: #fd7e14; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px;">Абитуриентам</span>
                <h3 style="margin: 10px 0; font-size: 16px;"><a href="/post/sample-post-2" style="color: #333; text-decoration: none;">Подготовка к ЕГЭ 2025</a></h3>
                <p style="color: #666; font-size: 14px; margin: 10px 0;">Эффективные стратегии подготовки к единому государственному экзамену...</p>
                <small style="color: #999;">9 августа 2025</small>
            </div>
        </div>
        
        <!-- Mock post 3 -->
        <div style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden; background: white;">
            <div style="padding: 15px;">
                <span style="background: #17a2b8; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px;">11-классники</span>
                <h3 style="margin: 10px 0; font-size: 16px;"><a href="/post/sample-post-3" style="color: #333; text-decoration: none;">Профориентация: найди свое призвание</a></h3>
                <p style="color: #666; font-size: 14px; margin: 10px 0;">Как определиться с будущей профессией и выбрать правильный путь...</p>
                <small style="color: #999;">8 августа 2025</small>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent4 = ob_get_clean();

// Section 5: Quick Links
ob_start();
?>
<div style="background: #007bff; color: white; padding: 30px; border-radius: 10px; text-align: center; margin: 20px 0;">
    <h2 style="margin-bottom: 20px;">Популярные разделы</h2>
    <div style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
        <a href="/vpo-all-regions" style="background: rgba(255,255,255,0.2); color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; transition: all 0.3s;">ВУЗы России</a>
        <a href="/spo-all-regions" style="background: rgba(255,255,255,0.2); color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; transition: all 0.3s;">Колледжи</a>
        <a href="/schools-all-regions" style="background: rgba(255,255,255,0.2); color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; transition: all 0.3s;">Школы</a>
        <a href="/news" style="background: rgba(255,255,255,0.2); color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; transition: all 0.3s;">Новости</a>
    </div>
</div>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Empty
$greyContent6 = '';

// Include the local template (no database dependencies)
include $_SERVER['DOCUMENT_ROOT'] . '/template.php';
?>