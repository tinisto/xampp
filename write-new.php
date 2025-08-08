<?php
/**
 * Write page using real_template.php
 */

// Start session to check for errors
if (session_status() === PHP_SESSION_ID_NONE) {
    session_start();
}

// Get any validation errors and form data from session
$errors = isset($_SESSION['write_errors']) ? $_SESSION['write_errors'] : [];
$formData = isset($_SESSION['write_data']) ? $_SESSION['write_data'] : [
    'title' => '',
    'category' => '',
    'content' => '',
    'tags' => ''
];

// Clear session data
unset($_SESSION['write_errors']);
unset($_SESSION['write_data']);

// Section 1: Title
$greyContent1 = '<div style="padding: 30px; text-align: center;"><h1>Написать статью</h1></div>';

// Section 2: Empty
$greyContent2 = '';

// Section 3: Empty  
$greyContent3 = '';

// Section 4: Empty
$greyContent4 = '';

// Section 5: Write form
$greyContent5 = '<div style="padding: 40px; max-width: 800px; margin: 0 auto;">
    <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 30px;">
        <h2 style="margin-bottom: 20px; color: #333;">Создать новую статью</h2>';

// Display errors if any
if (!empty($errors)) {
    $greyContent5 .= '
        <div style="background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; padding: 15px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px; color: #721c24;">';
    foreach ($errors as $error) {
        $greyContent5 .= '<li>' . htmlspecialchars($error) . '</li>';
    }
    $greyContent5 .= '</ul></div>';
}

$greyContent5 .= '        
        <form method="post" action="/pages/write/write-process.php" style="display: flex; flex-direction: column; gap: 20px;">
            <div>
                <label for="title" style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Заголовок статьи:</label>
                <input type="text" id="title" name="title" required 
                       value="' . htmlspecialchars($formData['title']) . '"
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
            </div>
            
            <div>
                <label for="category" style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Категория:</label>
                <select id="category" name="category" required 
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                    <option value="">Выберите категорию</option>
                    <option value="education"' . ($formData['category'] == 'education' ? ' selected' : '') . '>Образование</option>
                    <option value="ege"' . ($formData['category'] == 'ege' ? ' selected' : '') . '>ЕГЭ</option>
                    <option value="university"' . ($formData['category'] == 'university' ? ' selected' : '') . '>ВУЗы</option>
                    <option value="college"' . ($formData['category'] == 'college' ? ' selected' : '') . '>СПО</option>
                    <option value="school"' . ($formData['category'] == 'school' ? ' selected' : '') . '>Школы</option>
                    <option value="career"' . ($formData['category'] == 'career' ? ' selected' : '') . '>Карьера</option>
                </select>
            </div>
            
            <div>
                <label for="content" style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Содержание статьи:</label>
                <textarea id="content" name="content" required rows="15"
                          style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; resize: vertical;">' . htmlspecialchars($formData['content']) . '</textarea>
            </div>
            
            <div>
                <label for="tags" style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Теги (через запятую):</label>
                <input type="text" id="tags" name="tags" placeholder="образование, учеба, студенты"
                       value="' . htmlspecialchars($formData['tags']) . '"
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
            </div>
            
            <div style="display: flex; gap: 15px;">
                <button type="submit" 
                        style="padding: 15px 30px; background: #28a745; color: white; border: none; border-radius: 5px; font-size: 16px; font-weight: 600; cursor: pointer;">
                    Опубликовать статью
                </button>
                <button type="button" onclick="window.history.back()"
                        style="padding: 15px 30px; background: #6c757d; color: white; border: none; border-radius: 5px; font-size: 16px; font-weight: 600; cursor: pointer;">
                    Отмена
                </button>
            </div>
        </form>
        
        <div style="margin-top: 30px; padding: 20px; background: #e3f2fd; border-radius: 8px;">
            <h4 style="color: #1976d2; margin-bottom: 15px;">Правила публикации:</h4>
            <ul style="color: #1976d2; margin: 0; padding-left: 20px;">
                <li>Статья должна быть уникальной и полезной для читателей</li>
                <li>Запрещено размещение рекламного контента без согласования</li>
                <li>Текст должен быть грамотно написан и структурирован</li>
                <li>Все статьи проходят модерацию перед публикацией</li>
            </ul>
        </div>
    </div>
</div>';

// Section 6: Empty
$greyContent6 = '';

// Section 7: No comments
$blueContent = '';

// Page title
$pageTitle = 'Написать статью - 11-классники';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>