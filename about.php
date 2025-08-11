<?php
// About page
session_start();
$page_title = 'О проекте - 11klassniki.ru';

// Section 1: Hero
ob_start();
?>
<div style="padding: 20px 20px 20px; background: white; box-shadow: 0 1px 0 rgba(0,0,0,0.08);">
    <div style="max-width: 600px; margin: 0 auto; text-align: center;">
        <h1 style="font-size: 44px; font-weight: 800; margin-bottom: 16px; color: #222222; letter-spacing: -0.02em;">
            О проекте 11klassniki.ru
        </h1>
        <p style="font-size: 18px; color: #717171; line-height: 1.5;">
            Российская образовательная платформа для учеников, родителей и педагогов
        </p>
    </div>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: Mission
ob_start();
?>
<div style="padding: 30px 20px; background: white;">
    <div style="max-width: 900px; margin: 0 auto;">
        <h2 style="font-size: 32px; font-weight: 600; margin-bottom: 30px; text-align: center; color: #333;">Наша миссия</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px; margin: 40px 0;">
            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-graduation-cap" style="color: white; font-size: 32px;"></i>
                </div>
                <h3 style="font-size: 22px; font-weight: 600; margin-bottom: 15px; color: #333;">Качественное образование</h3>
                <p style="color: #666; line-height: 1.6;">
                    Предоставляем доступ к лучшим образовательным ресурсам России для учеников всех уровней
                </p>
            </div>
            
            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-users" style="color: white; font-size: 32px;"></i>
                </div>
                <h3 style="font-size: 22px; font-weight: 600; margin-bottom: 15px; color: #333;">Сообщество</h3>
                <p style="color: #666; line-height: 1.6;">
                    Объединяем учеников, родителей и педагогов в единую образовательную экосистему
                </p>
            </div>
            
            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-rocket" style="color: white; font-size: 32px;"></i>
                </div>
                <h3 style="font-size: 22px; font-weight: 600; margin-bottom: 15px; color: #333;">Инновации</h3>
                <p style="color: #666; line-height: 1.6;">
                    Используем современные технологии для создания удобной и эффективной образовательной платформы
                </p>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent2 = ob_get_clean();

// Section 3: About platform
ob_start();
?>
<div style="padding: 30px 20px; background: #f8f9fa;">
    <div style="max-width: 900px; margin: 0 auto;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: center;">
            <div>
                <h2 style="font-size: 30px; font-weight: 600; margin-bottom: 25px; color: #333;">Что мы предлагаем</h2>
                <div style="space-y: 20px;">
                    <div style="display: flex; align-items: flex-start; margin-bottom: 20px;">
                        <i class="fas fa-check-circle" style="color: #667eea; font-size: 20px; margin-right: 15px; margin-top: 3px;"></i>
                        <div>
                            <h4 style="font-weight: 600; margin-bottom: 8px; color: #333;">Информация о школах</h4>
                            <p style="color: #666; margin: 0;">Полная база данных школ России с рейтингами и отзывами</p>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: flex-start; margin-bottom: 20px;">
                        <i class="fas fa-check-circle" style="color: #667eea; font-size: 20px; margin-right: 15px; margin-top: 3px;"></i>
                        <div>
                            <h4 style="font-weight: 600; margin-bottom: 8px; color: #333;">СПО и ВПО</h4>
                            <p style="color: #666; margin: 0;">Справочник средних и высших учебных заведений</p>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: flex-start; margin-bottom: 20px;">
                        <i class="fas fa-check-circle" style="color: #667eea; font-size: 20px; margin-right: 15px; margin-top: 3px;"></i>
                        <div>
                            <h4 style="font-weight: 600; margin-bottom: 8px; color: #333;">Образовательные события</h4>
                            <p style="color: #666; margin: 0;">Календарь олимпиад, конкурсов и других мероприятий</p>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: flex-start; margin-bottom: 20px;">
                        <i class="fas fa-check-circle" style="color: #667eea; font-size: 20px; margin-right: 15px; margin-top: 3px;"></i>
                        <div>
                            <h4 style="font-weight: 600; margin-bottom: 8px; color: #333;">Персональные рекомендации</h4>
                            <p style="color: #666; margin: 0;">Индивидуальные советы по выбору образовательного пути</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="text-align: center;">
                <div style="width: 300px; height: 300px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <div style="color: white; text-align: center;">
                        <div style="font-size: 72px; font-weight: 700; margin-bottom: 10px;">11</div>
                        <div style="font-size: 18px; opacity: 0.9;">klassniki.ru</div>
                        <div style="font-size: 14px; opacity: 0.7; margin-top: 10px;">Одиннадцать шагов<br>к большому будущему</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();

// Section 4: Statistics
ob_start();
?>
<div style="padding: 30px 20px; background: white;">
    <div style="max-width: 900px; margin: 0 auto; text-align: center;">
        <h2 style="font-size: 30px; font-weight: 600; margin-bottom: 50px; color: #333;">Платформа в цифрах</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px;">
            <div style="text-align: center;">
                <div style="font-size: 48px; font-weight: 700; color: #667eea; margin-bottom: 10px;">1000+</div>
                <h4 style="font-size: 18px; font-weight: 600; margin-bottom: 10px; color: #333;">Школ в базе</h4>
                <p style="color: #666; margin: 0;">Общеобразовательные учреждения по всей России</p>
            </div>
            
            <div style="text-align: center;">
                <div style="font-size: 48px; font-weight: 700; color: #667eea; margin-bottom: 10px;">500+</div>
                <h4 style="font-size: 18px; font-weight: 600; margin-bottom: 10px; color: #333;">ВУЗов и СПО</h4>
                <p style="color: #666; margin: 0;">Высшие и средние специальные учебные заведения</p>
            </div>
            
            <div style="text-align: center;">
                <div style="font-size: 48px; font-weight: 700; color: #667eea; margin-bottom: 10px;">24/7</div>
                <h4 style="font-size: 18px; font-weight: 600; margin-bottom: 10px; color: #333;">Доступность</h4>
                <p style="color: #666; margin: 0;">Круглосуточный доступ к образовательным ресурсам</p>
            </div>
            
            <div style="text-align: center;">
                <div style="font-size: 48px; font-weight: 700; color: #667eea; margin-bottom: 10px;">100%</div>
                <h4 style="font-size: 18px; font-weight: 600; margin-bottom: 10px; color: #333;">Бесплатно</h4>
                <p style="color: #666; margin: 0;">Все основные функции платформы доступны бесплатно</p>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent4 = ob_get_clean();

// Section 5: Team
ob_start();
?>
<div style="padding: 30px 20px; background: #f8f9fa;">
    <div style="max-width: 900px; margin: 0 auto; text-align: center;">
        <h2 style="font-size: 30px; font-weight: 600; margin-bottom: 20px; color: #333;">Команда проекта</h2>
        <p style="font-size: 18px; color: #666; margin-bottom: 50px;">
            Мы - команда профессионалов, объединенных общей целью улучшения российского образования
        </p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
            <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-code" style="color: white; font-size: 32px;"></i>
                </div>
                <h4 style="font-size: 18px; font-weight: 600; margin-bottom: 10px; color: #333;">Команда разработки</h4>
                <p style="color: #666; margin: 0;">Создание и поддержка технической части платформы</p>
            </div>
            
            <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-chalkboard-teacher" style="color: white; font-size: 32px;"></i>
                </div>
                <h4 style="font-size: 18px; font-weight: 600; margin-bottom: 10px; color: #333;">Педагогические эксперты</h4>
                <p style="color: #666; margin: 0;">Кураторы образовательного контента и методик</p>
            </div>
            
            <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-headset" style="color: white; font-size: 32px;"></i>
                </div>
                <h4 style="font-size: 18px; font-weight: 600; margin-bottom: 10px; color: #333;">Служба поддержки</h4>
                <p style="color: #666; margin: 0;">Помощь пользователям и модерация контента</p>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Contact CTA
ob_start();
?>
<div style="padding: 30px 20px; background: #f8f9fa;">
    <div style="max-width: 700px; margin: 0 auto; text-align: center;">
        <h2 style="font-size: 32px; font-weight: 700; margin-bottom: 20px; color: #222222;">Есть вопросы?</h2>
        <p style="font-size: 18px; color: #717171; margin-bottom: 30px;">
            Мы всегда готовы помочь и ответить на все ваши вопросы о платформе
        </p>
        <a href="/contact.php" 
           style="display: inline-block; background: #0039A6; color: white; padding: 15px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 16px; transition: all 0.3s;"
           onmouseover="this.style.background='#002D87'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0, 57, 166, 0.2)';"
           onmouseout="this.style.background='#0039A6'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
            <i class="fas fa-envelope"></i> Связаться с нами
        </a>
    </div>
</div>
<?php
$greyContent6 = ob_get_clean();

$blueContent = '';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template_local.php';
?>