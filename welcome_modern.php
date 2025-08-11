<?php
// Welcome page after registration
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// Page title
$pageTitle = 'Добро пожаловать!';

// Section 1: Welcome message
ob_start();
?>
<div style="padding: 60px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <div style="max-width: 800px; margin: 0 auto; text-align: center;">
        <div style="width: 120px; height: 120px; background: white; border-radius: 50%; display: inline-flex; 
                    align-items: center; justify-content: center; margin-bottom: 30px; box-shadow: 0 8px 30px rgba(0,0,0,0.2);">
            <i class="fas fa-check" style="font-size: 60px; color: #667eea;"></i>
        </div>
        
        <h1 style="font-size: 48px; font-weight: 700; margin-bottom: 20px;">
            Добро пожаловать, <?= htmlspecialchars($_SESSION['user_name']) ?>!
        </h1>
        
        <p style="font-size: 20px; opacity: 0.9; max-width: 600px; margin: 0 auto 40px;">
            Ваш аккаунт успешно создан. Теперь вы можете пользоваться всеми возможностями портала.
        </p>
        
        <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
            <a href="/" 
               style="padding: 15px 40px; background: white; color: #667eea; border-radius: 8px; 
                      text-decoration: none; font-weight: 600; font-size: 16px; transition: transform 0.2s;"
               onmouseover="this.style.transform='translateY(-2px)'"
               onmouseout="this.style.transform='translateY(0)'">
                <i class="fas fa-home"></i> На главную
            </a>
            <a href="/profile" 
               style="padding: 15px 40px; background: rgba(255,255,255,0.2); color: white; 
                      border: 2px solid white; border-radius: 8px; text-decoration: none; 
                      font-weight: 600; font-size: 16px; transition: all 0.2s;"
               onmouseover="this.style.background='rgba(255,255,255,0.3)'"
               onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                <i class="fas fa-user"></i> Мой профиль
            </a>
        </div>
    </div>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: Quick start guide
ob_start();
?>
<div style="padding: 60px 20px; background: white;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <h2 style="font-size: 32px; font-weight: 700; text-align: center; margin-bottom: 50px;">
            С чего начать?
        </h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
            <!-- Browse institutions -->
            <div style="background: #f8f9fa; border-radius: 12px; padding: 30px; text-align: center; 
                        transition: transform 0.3s;"
                 onmouseover="this.style.transform='translateY(-5px)'"
                 onmouseout="this.style.transform='translateY(0)'">
                <div style="width: 80px; height: 80px; background: #e3f2fd; border-radius: 50%; 
                            display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                    <i class="fas fa-university" style="font-size: 36px; color: #1976d2;"></i>
                </div>
                <h3 style="font-size: 24px; font-weight: 600; margin-bottom: 15px;">Найдите учебное заведение</h3>
                <p style="color: #666; margin-bottom: 20px;">
                    Изучите каталог ВУЗов, колледжей и школ. Сохраняйте понравившиеся в избранное.
                </p>
                <a href="/vpo" style="color: #1976d2; text-decoration: none; font-weight: 600;">
                    Перейти к каталогу <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <!-- Read news -->
            <div style="background: #f8f9fa; border-radius: 12px; padding: 30px; text-align: center; 
                        transition: transform 0.3s;"
                 onmouseover="this.style.transform='translateY(-5px)'"
                 onmouseout="this.style.transform='translateY(0)'">
                <div style="width: 80px; height: 80px; background: #e8f5e9; border-radius: 50%; 
                            display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                    <i class="fas fa-newspaper" style="font-size: 36px; color: #43a047;"></i>
                </div>
                <h3 style="font-size: 24px; font-weight: 600; margin-bottom: 15px;">Читайте новости</h3>
                <p style="color: #666; margin-bottom: 20px;">
                    Будьте в курсе последних событий в сфере образования и изменений в правилах приема.
                </p>
                <a href="/news" style="color: #43a047; text-decoration: none; font-weight: 600;">
                    К новостям <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <!-- Study articles -->
            <div style="background: #f8f9fa; border-radius: 12px; padding: 30px; text-align: center; 
                        transition: transform 0.3s;"
                 onmouseover="this.style.transform='translateY(-5px)'"
                 onmouseout="this.style.transform='translateY(0)'">
                <div style="width: 80px; height: 80px; background: #f3e5f5; border-radius: 50%; 
                            display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                    <i class="fas fa-book-open" style="font-size: 36px; color: #7b1fa2;"></i>
                </div>
                <h3 style="font-size: 24px; font-weight: 600; margin-bottom: 15px;">Изучайте статьи</h3>
                <p style="color: #666; margin-bottom: 20px;">
                    Полезные руководства и советы по поступлению, подготовке к экзаменам и выбору профессии.
                </p>
                <a href="/posts" style="color: #7b1fa2; text-decoration: none; font-weight: 600;">
                    Читать статьи <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Personalization tips
ob_start();
?>
<div style="padding: 60px 20px; background: #f8f9fa;">
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="background: white; border-radius: 12px; padding: 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <h2 style="font-size: 28px; font-weight: 600; margin-bottom: 30px; text-align: center;">
                <i class="fas fa-lightbulb" style="color: #ffc107;"></i> Совет
            </h2>
            
            <p style="font-size: 18px; line-height: 1.8; color: #555; text-align: center;">
                Чтобы получать персональные рекомендации, заполните свой профиль: 
                укажите интересующие направления обучения, регион и уровень образования.
            </p>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="/profile/edit" 
                   style="display: inline-block; padding: 12px 30px; background: #ffc107; color: #333; 
                          border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.2s;"
                   onmouseover="this.style.background='#ffb300'"
                   onmouseout="this.style.background='#ffc107'">
                    <i class="fas fa-edit"></i> Заполнить профиль
                </a>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();

// Other sections empty
$greyContent4 = '';
$greyContent5 = '';
$greyContent6 = '';
$blueContent = '';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/template.php';
?>