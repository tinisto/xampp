<?php
/**
 * Single test page - displays and runs individual tests
 */

// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get test ID from URL
$testId = isset($_GET['test_id']) ? intval($_GET['test_id']) : 0;

// Sample test data (in a real application, this would come from database)
$availableTests = [
    1 => [
        'id' => 1,
        'title' => 'ЕГЭ по математике (базовый уровень)',
        'description' => 'Тренировочный тест по математике базового уровня ЕГЭ',
        'category' => 'ege',
        'duration' => 180,
        'questions' => 20,
        'difficulty' => 'Базовый',
        'icon' => 'fas fa-calculator',
        'instructions' => 'Тест состоит из 20 заданий базового уровня. На выполнение теста отводится 3 часа. Разрешается использовать линейку и справочные материалы.'
    ],
    2 => [
        'id' => 2, 
        'title' => 'ЕГЭ по русскому языку',
        'description' => 'Подготовительный тест по русскому языку для ЕГЭ',
        'category' => 'ege',
        'duration' => 210,
        'questions' => 27,
        'difficulty' => 'Средний',
        'icon' => 'fas fa-language',
        'instructions' => 'Тест состоит из двух частей. Первая часть содержит 26 заданий с кратким ответом, вторая часть - сочинение.'
    ],
    3 => [
        'id' => 3,
        'title' => 'Тест профориентации',
        'description' => 'Определите подходящую сферу деятельности и профессию',
        'category' => 'career',
        'duration' => 30,
        'questions' => 50,
        'difficulty' => 'Легкий',
        'icon' => 'fas fa-compass',
        'instructions' => 'Отвечайте на вопросы честно, выбирая наиболее подходящий для вас вариант. Правильных или неправильных ответов здесь нет.'
    ],
    4 => [
        'id' => 4,
        'title' => 'ОГЭ по обществознанию',
        'description' => 'Тренировочный тест по обществознанию для 9 класса',
        'category' => 'oge', 
        'duration' => 180,
        'questions' => 24,
        'difficulty' => 'Средний',
        'icon' => 'fas fa-users',
        'instructions' => 'Внимательно прочитайте каждое задание. Тест содержит задания разного типа: с выбором ответа, с кратким ответом и с развернутым ответом.'
    ],
    5 => [
        'id' => 5,
        'title' => 'Физика 10-11 класс',
        'description' => 'Проверочный тест по физике для старших классов',
        'category' => 'subject',
        'duration' => 90,
        'questions' => 15,
        'difficulty' => 'Сложный',
        'icon' => 'fas fa-atom',
        'instructions' => 'Тест включает задачи по механике, термодинамике, электродинамике и квантовой физике. Калькулятор использовать разрешается.'
    ],
    6 => [
        'id' => 6,
        'title' => 'Английский язык B1-B2',
        'description' => 'Тест на определение уровня английского языка',
        'category' => 'subject',
        'duration' => 60,
        'questions' => 30,
        'difficulty' => 'Средний',
        'icon' => 'fas fa-globe',
        'instructions' => 'Тест состоит из разделов: грамматика, лексика, чтение и аудирование. Убедитесь, что у вас работает звук.'
    ]
];

// Check if test exists
if (!isset($availableTests[$testId])) {
    // Redirect to tests page if test not found
    header('Location: /tests');
    exit;
}

$test = $availableTests[$testId];

// Page title using reusable component
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle($test['title'], [
    'fontSize' => '32px',
    'margin' => '20px 0',
    'subtitle' => $test['description']
]);
$headerContent = ob_get_clean();

// Navigation breadcrumb
ob_start();
?>
<div style="padding: 0 20px; margin-bottom: 20px;">
    <nav style="color: #666; font-size: 14px;">
        <a href="/" style="color: #28a745; text-decoration: none;">Главная</a>
        <span style="margin: 0 10px;">/</span>
        <a href="/tests" style="color: #28a745; text-decoration: none;">Тесты</a>
        <span style="margin: 0 10px;">/</span>
        <span><?= htmlspecialchars($test['title']) ?></span>
    </nav>
</div>
<?php
$navigationContent = ob_get_clean();

// Empty sections
$metadataContent = '';
$filtersContent = '';

// Main content - test information and start button
ob_start();
?>
<div class="container" style="max-width: 800px; margin: 0 auto; padding: 20px;">
    <div style="background: white; border-radius: 12px; padding: 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        
        <!-- Test statistics -->
        <div style="display: flex; justify-content: space-around; margin-bottom: 40px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <div style="text-align: center;">
                <i class="fas fa-clock" style="font-size: 24px; color: #28a745; margin-bottom: 8px;"></i>
                <p style="margin: 0; font-weight: 600; font-size: 18px;"><?= $test['duration'] ?> минут</p>
                <small style="color: #666;">Время на тест</small>
            </div>
            <div style="text-align: center;">
                <i class="fas fa-question-circle" style="font-size: 24px; color: #28a745; margin-bottom: 8px;"></i>
                <p style="margin: 0; font-weight: 600; font-size: 18px;"><?= $test['questions'] ?> вопросов</p>
                <small style="color: #666;">Количество заданий</small>
            </div>
            <div style="text-align: center;">
                <i class="fas fa-chart-line" style="font-size: 24px; color: #28a745; margin-bottom: 8px;"></i>
                <p style="margin: 0; font-weight: 600; font-size: 18px;"><?= htmlspecialchars($test['difficulty']) ?></p>
                <small style="color: #666;">Уровень сложности</small>
            </div>
        </div>
        
        <!-- Instructions -->
        <div style="margin-bottom: 40px;">
            <h3 style="color: #333; margin-bottom: 15px;">
                <i class="fas fa-info-circle" style="color: #28a745; margin-right: 10px;"></i>
                Инструкция
            </h3>
            <p style="color: #666; line-height: 1.6;">
                <?= htmlspecialchars($test['instructions']) ?>
            </p>
        </div>
        
        <!-- Start test button -->
        <div style="text-align: center;">
            <?php 
            include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/button.php';
            renderButton('Начать тест', '#', [
                'type' => 'success',
                'size' => 'large',
                'icon' => 'fas fa-play',
                'onclick' => 'startActualTest()'
            ]);
            ?>
            <p style="margin-top: 20px; color: #666; font-size: 14px;">
                <i class="fas fa-exclamation-triangle" style="color: #ffc107; margin-right: 5px;"></i>
                После начала теста время начнет отсчитываться автоматически
            </p>
        </div>
    </div>
    
    <!-- Additional information -->
    <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
        <h4 style="color: #333; margin-bottom: 15px;">Подготовка к тесту</h4>
        <ul style="color: #666; line-height: 1.8;">
            <li>Убедитесь, что у вас есть стабильное интернет-соединение</li>
            <li>Найдите тихое место, где вас не будут отвлекать</li>
            <li>Подготовьте необходимые материалы (ручку, бумагу, калькулятор если разрешен)</li>
            <li>Внимательно читайте каждое задание перед ответом</li>
        </ul>
    </div>
</div>

<script>
function startActualTest() {
    // Hide the test info and show test interface directly
    document.querySelector('.container').innerHTML = `
        <div style="text-align: center; padding: 40px;">
            <h2 style="color: #333; margin-bottom: 20px;">Тест запущен!</h2>
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <h3 style="color: #28a745;">Вопрос 1 из 20</h3>
                <p style="font-size: 18px; margin: 20px 0;">Сколько будет 2 + 2?</p>
                <div style="text-align: left; max-width: 400px; margin: 0 auto;">
                    <label style="display: block; margin: 10px 0; cursor: pointer;">
                        <input type="radio" name="answer" value="3" style="margin-right: 10px;"> 3
                    </label>
                    <label style="display: block; margin: 10px 0; cursor: pointer;">
                        <input type="radio" name="answer" value="4" style="margin-right: 10px;"> 4
                    </label>
                    <label style="display: block; margin: 10px 0; cursor: pointer;">
                        <input type="radio" name="answer" value="5" style="margin-right: 10px;"> 5
                    </label>
                    <label style="display: block; margin: 10px 0; cursor: pointer;">
                        <input type="radio" name="answer" value="22" style="margin-right: 10px;"> 22
                    </label>
                </div>
                <button onclick="finishTest()" style="background: #28a745; color: white; border: none; padding: 12px 24px; border-radius: 6px; margin-top: 20px; cursor: pointer;">
                    Завершить тест
                </button>
            </div>
            <div style="color: #666;">
                <i class="fas fa-clock"></i> Осталось времени: 02:59:45
            </div>
        </div>
    `;
}

function finishTest() {
    const selected = document.querySelector('input[name="answer"]:checked');
    const answer = selected ? selected.value : null;
    
    if (!answer) {
        ModalManager.alert('Внимание', 'Пожалуйста, выберите ответ', 'warning');
        return;
    }
    
    const isCorrect = answer === '4';
    const resultMessage = isCorrect ? 
        'Правильно! Ответ: 4' : 
        'Неправильно. Правильный ответ: 4';
    
    ModalManager.alert('Результат теста', resultMessage + '<br><br>Тест завершен. Возвращаемся к списку тестов...', isCorrect ? 'success' : 'danger');
    
    setTimeout(() => {
        window.location.href = '/tests';
    }, 3000);
}
</script>
<?php
$mainContent = ob_get_clean();

// Empty pagination section
$paginationContent = '';

// Set page metadata
$pageTitle = htmlspecialchars($test['title']) . ' - Тесты - 11классники';
$metaD = htmlspecialchars($test['description']);
$metaK = 'тест, ' . ($test['category'] === 'ege' ? 'ЕГЭ' : ($test['category'] === 'oge' ? 'ОГЭ' : 'онлайн тест')) . ', подготовка, экзамен';

// Comments configuration (compact format)
$commentsContent = [
    'type' => 'test',
    'id' => $test['id'],
    'options' => [
        'showTitle' => false,
        'showStats' => true,
        'collapsed' => true
    ]
];

// Include modal component
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/modal-modern.php';

// Include unified template
include $_SERVER['DOCUMENT_ROOT'] . '/template-unified.php';

// Render modal after template
renderModalModern();
?>