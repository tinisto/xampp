<?php
/**
 * Mobile API Documentation
 */

$pageTitle = '11klassniki.ru Mobile API v1 Documentation';

ob_start();
?>
<div style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); padding: 60px 20px; color: white; text-align: center;">
    <h1 style="font-size: 36px; font-weight: 700; margin-bottom: 10px;">
        <i class="fas fa-mobile-alt"></i> Mobile API v1
    </h1>
    <p style="font-size: 18px; opacity: 0.9;">RESTful API для мобильного приложения 11klassniki.ru</p>
</div>
<?php
$greyContent1 = ob_get_clean();

ob_start();
?>
<div style="padding: 40px 20px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display: grid; grid-template-columns: 1fr 300px; gap: 40px;">
            <!-- Main content -->
            <div>
                <h2>Обзор API</h2>
                <p>Mobile API v1 предоставляет полный доступ к функциональности портала 11klassniki.ru через RESTful интерфейс.</p>
                
                <h3>Базовый URL</h3>
                <code style="background: #f8f9fa; padding: 10px; border-radius: 4px; display: block;">
                    https://11klassniki.ru/api/v1/
                </code>
                
                <h3>Аутентификация</h3>
                <p>API использует JWT-подобные токены для аутентификации. Все запросы (кроме регистрации и входа) требуют заголовок:</p>
                <code style="background: #f8f9fa; padding: 10px; border-radius: 4px; display: block;">
                    Authorization: Bearer {token}
                </code>
                
                <h3>Формат ответов</h3>
                <p>Все ответы возвращаются в формате JSON с полями:</p>
                <pre style="background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto;">
{
    "success": true|false,
    "data": {...},
    "error": "Error message if success is false",
    "pagination": {
        "current_page": 1,
        "per_page": 20,
        "total": 100,
        "total_pages": 5
    }
}
                </pre>
                
                <h2>Эндпоинты</h2>
                
                <h3>🔐 Аутентификация</h3>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>POST /auth/login</h4>
                    <p><strong>Описание:</strong> Вход в систему</p>
                    <p><strong>Параметры:</strong></p>
                    <ul>
                        <li><code>email</code> (string, required) - Email пользователя</li>
                        <li><code>password</code> (string, required) - Пароль</li>
                    </ul>
                    <p><strong>Ответ:</strong></p>
                    <pre style="background: white; padding: 10px; border-radius: 4px; overflow-x: auto;">
{
    "success": true,
    "token": "jwt_token_here",
    "user": {
        "id": 1,
        "name": "Иван Иванов",
        "email": "user@example.com",
        "role": "user",
        "avatar": "/uploads/avatars/1.jpg"
    }
}
                    </pre>
                </div>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>POST /auth/register</h4>
                    <p><strong>Описание:</strong> Регистрация нового пользователя</p>
                    <p><strong>Параметры:</strong></p>
                    <ul>
                        <li><code>name</code> (string, required) - Имя пользователя</li>
                        <li><code>email</code> (string, required) - Email</li>
                        <li><code>password</code> (string, required) - Пароль (минимум 6 символов)</li>
                    </ul>
                </div>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>POST /auth/refresh</h4>
                    <p><strong>Описание:</strong> Обновление токена</p>
                    <p><strong>Требует авторизации:</strong> Да</p>
                </div>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>POST /auth/logout</h4>
                    <p><strong>Описание:</strong> Выход из системы</p>
                    <p><strong>Требует авторизации:</strong> Да</p>
                </div>
                
                <h3>👤 Пользователь</h3>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>GET /user/profile</h4>
                    <p><strong>Описание:</strong> Получить профиль пользователя</p>
                    <p><strong>Требует авторизации:</strong> Да</p>
                </div>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>PUT /user/profile</h4>
                    <p><strong>Описание:</strong> Обновить профиль</p>
                    <p><strong>Параметры:</strong> name, bio, location, website</p>
                </div>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>GET/PUT /user/settings</h4>
                    <p><strong>Описание:</strong> Настройки пользователя</p>
                </div>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>POST/DELETE /user/avatar</h4>
                    <p><strong>Описание:</strong> Загрузка/удаление аватара</p>
                </div>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>GET /user/stats</h4>
                    <p><strong>Описание:</strong> Статистика пользователя</p>
                </div>
                
                <h3>📰 Контент</h3>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>GET /news</h4>
                    <p><strong>Описание:</strong> Список новостей</p>
                    <p><strong>Параметры:</strong></p>
                    <ul>
                        <li><code>page</code> (int) - Страница</li>
                        <li><code>limit</code> (int) - Количество элементов (максимум 100)</li>
                        <li><code>category</code> (string) - Фильтр по категории</li>
                        <li><code>search</code> (string) - Поиск по тексту</li>
                    </ul>
                </div>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>GET /news/{id}</h4>
                    <p><strong>Описание:</strong> Получить новость по ID</p>
                    <p><strong>Ответ включает:</strong> данные новости, статистику рейтинга, статус избранного</p>
                </div>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>GET /posts</h4>
                    <p><strong>Описание:</strong> Список статей</p>
                    <p><strong>Параметры:</strong> аналогично новостям</p>
                </div>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>GET /posts/{id}</h4>
                    <p><strong>Описание:</strong> Получить статью по ID</p>
                </div>
                
                <h3>📅 События</h3>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>GET /events</h4>
                    <p><strong>Описание:</strong> Список событий</p>
                    <p><strong>Параметры:</strong></p>
                    <ul>
                        <li><code>type</code> (string) - Тип события (deadline, exam, open_day, conference, other)</li>
                        <li><code>audience</code> (string) - Целевая аудитория</li>
                        <li><code>date</code> (string) - Фильтр по дате (today, this_week, upcoming)</li>
                    </ul>
                </div>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>GET /events/{id}</h4>
                    <p><strong>Описание:</strong> Получить событие по ID</p>
                </div>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>GET /events/subscriptions</h4>
                    <p><strong>Описание:</strong> Подписки пользователя на события</p>
                </div>
                
                <h3>🏫 Учебные заведения</h3>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>GET /schools, /vpo, /spo</h4>
                    <p><strong>Описание:</strong> Списки школ, ВУЗов, ССУЗов</p>
                    <p><strong>Параметры:</strong></p>
                    <ul>
                        <li><code>region</code> (int) - ID региона</li>
                        <li><code>search</code> (string) - Поиск по названию</li>
                        <li><code>page</code>, <code>limit</code> - Пагинация</li>
                    </ul>
                </div>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>GET /schools/{id}, /vpo/{id}, /spo/{id}</h4>
                    <p><strong>Описание:</strong> Получить учебное заведение по ID</p>
                </div>
                
                <h3>🔍 Поиск</h3>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>GET /search</h4>
                    <p><strong>Описание:</strong> Глобальный поиск</p>
                    <p><strong>Параметры:</strong></p>
                    <ul>
                        <li><code>q</code> (string, required) - Поисковый запрос</li>
                        <li><code>type</code> (string) - Тип контента (all, news, posts, events, schools, vpo, spo)</li>
                        <li><code>limit</code> (int) - Лимит результатов</li>
                    </ul>
                </div>
                
                <h3>⭐ Взаимодействия</h3>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>GET/POST/DELETE /favorites</h4>
                    <p><strong>Описание:</strong> Управление избранным</p>
                    <p><strong>POST параметры:</strong> item_type, item_id</p>
                </div>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>POST /ratings</h4>
                    <p><strong>Описание:</strong> Поставить оценку</p>
                    <p><strong>Параметры:</strong> item_type, item_id, rating (1-5)</p>
                </div>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>GET/POST /comments</h4>
                    <p><strong>Описание:</strong> Комментарии</p>
                    <p><strong>GET параметры:</strong> item_type, item_id</p>
                    <p><strong>POST параметры:</strong> item_type, item_id, content</p>
                </div>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>GET/POST /reading-lists</h4>
                    <p><strong>Описание:</strong> Списки для чтения</p>
                </div>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>GET/POST /notifications</h4>
                    <p><strong>Описание:</strong> Уведомления</p>
                </div>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>GET /recommendations</h4>
                    <p><strong>Описание:</strong> Персональные рекомендации</p>
                </div>
                
                <h3>ℹ️ Информация</h3>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>GET /info/stats</h4>
                    <p><strong>Описание:</strong> Статистика портала</p>
                    <p><strong>Авторизация не требуется</strong></p>
                </div>
                
                <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4>GET /info/version</h4>
                    <p><strong>Описание:</strong> Информация о версии API</p>
                    <p><strong>Авторизация не требуется</strong></p>
                </div>
                
                <h2>Коды ошибок</h2>
                
                <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                    <tr style="background: #f8f9fa;">
                        <th style="border: 1px solid #dee2e6; padding: 10px; text-align: left;">Код</th>
                        <th style="border: 1px solid #dee2e6; padding: 10px; text-align: left;">Описание</th>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #dee2e6; padding: 10px;">200</td>
                        <td style="border: 1px solid #dee2e6; padding: 10px;">Успешный запрос</td>
                    </tr>
                    <tr style="background: #f8f9fa;">
                        <td style="border: 1px solid #dee2e6; padding: 10px;">201</td>
                        <td style="border: 1px solid #dee2e6; padding: 10px;">Ресурс создан</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #dee2e6; padding: 10px;">400</td>
                        <td style="border: 1px solid #dee2e6; padding: 10px;">Неверные параметры запроса</td>
                    </tr>
                    <tr style="background: #f8f9fa;">
                        <td style="border: 1px solid #dee2e6; padding: 10px;">401</td>
                        <td style="border: 1px solid #dee2e6; padding: 10px;">Требуется авторизация</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #dee2e6; padding: 10px;">403</td>
                        <td style="border: 1px solid #dee2e6; padding: 10px;">Доступ запрещен</td>
                    </tr>
                    <tr style="background: #f8f9fa;">
                        <td style="border: 1px solid #dee2e6; padding: 10px;">404</td>
                        <td style="border: 1px solid #dee2e6; padding: 10px;">Ресурс не найден</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #dee2e6; padding: 10px;">405</td>
                        <td style="border: 1px solid #dee2e6; padding: 10px;">Метод не поддерживается</td>
                    </tr>
                    <tr style="background: #f8f9fa;">
                        <td style="border: 1px solid #dee2e6; padding: 10px;">500</td>
                        <td style="border: 1px solid #dee2e6; padding: 10px;">Внутренняя ошибка сервера</td>
                    </tr>
                </table>
                
            </div>
            
            <!-- Sidebar -->
            <div>
                <div style="background: #f8f9fa; border-radius: 12px; padding: 25px; margin-bottom: 20px;">
                    <h3 style="margin: 0 0 20px 0; font-size: 18px;">Быстрые ссылки</h3>
                    <nav style="display: flex; flex-direction: column; gap: 10px;">
                        <a href="#auth" style="color: #007bff; text-decoration: none;">🔐 Аутентификация</a>
                        <a href="#user" style="color: #007bff; text-decoration: none;">👤 Пользователь</a>
                        <a href="#content" style="color: #007bff; text-decoration: none;">📰 Контент</a>
                        <a href="#events" style="color: #007bff; text-decoration: none;">📅 События</a>
                        <a href="#institutions" style="color: #007bff; text-decoration: none;">🏫 Учебные заведения</a>
                        <a href="#search" style="color: #007bff; text-decoration: none;">🔍 Поиск</a>
                        <a href="#interactions" style="color: #007bff; text-decoration: none;">⭐ Взаимодействия</a>
                        <a href="#info" style="color: #007bff; text-decoration: none;">ℹ️ Информация</a>
                    </nav>
                </div>
                
                <div style="background: #e3f2fd; border-radius: 12px; padding: 25px;">
                    <h3 style="margin: 0 0 15px 0; color: #1976d2;">💡 Совет разработчикам</h3>
                    <p style="margin: 0; color: #1976d2; font-size: 14px;">
                        Используйте параметр <code>limit</code> для оптимизации производительности. 
                        Максимальное значение: 100 элементов за запрос.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent2 = ob_get_clean();

// Include template
$blueContent = '';
include $_SERVER['DOCUMENT_ROOT'] . '/template.php';
?>