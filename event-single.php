<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get event ID
$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$eventId) {
    header('Location: /events');
    exit;
}

// Fetch event
$event = db_fetch_one("SELECT * FROM events WHERE id = ? AND is_public = 1", [$eventId]);

if (!$event) {
    header('HTTP/1.0 404 Not Found');
    include $_SERVER['DOCUMENT_ROOT'] . '/404_modern.php';
    exit;
}

// Update views
db_query("UPDATE events SET views = views + 1 WHERE id = ?", [$eventId]);

// Check if user is subscribed
$isSubscribed = false;
if (isset($_SESSION['user_id'])) {
    $subscription = db_fetch_one(
        "SELECT id FROM event_subscriptions WHERE user_id = ? AND event_id = ?", 
        [$_SESSION['user_id'], $eventId]
    );
    $isSubscribed = !empty($subscription);
}

// Fetch related events
$relatedEvents = db_fetch_all("
    SELECT * FROM events 
    WHERE event_type = ? AND id != ? AND is_public = 1 AND start_date >= CURRENT_DATE
    ORDER BY start_date ASC
    LIMIT 4
", [$event['event_type'], $eventId]);

$pageTitle = $event['title'];
$eventDate = new DateTime($event['start_date']);
$isToday = $eventDate->format('Y-m-d') === date('Y-m-d');
$isTomorrow = $eventDate->format('Y-m-d') === date('Y-m-d', strtotime('+1 day'));
$isPast = $eventDate->format('Y-m-d') < date('Y-m-d');

// Event type styling
$typeColors = [
    'deadline' => '#dc3545',
    'exam' => '#fd7e14', 
    'open_day' => '#20c997',
    'conference' => '#6f42c1',
    'other' => '#6c757d'
];
$typeColor = $typeColors[$event['event_type']] ?? '#6c757d';

$typeLabels = [
    'deadline' => 'Дедлайн',
    'exam' => 'Экзамен',
    'open_day' => 'День открытых дверей',
    'conference' => 'Конференция',
    'other' => 'Событие'
];
$typeLabel = $typeLabels[$event['event_type']] ?? 'Событие';

// Section 1: Header
ob_start();
?>
<div style="background: linear-gradient(135deg, <?= $typeColor ?> 0%, <?= $typeColor ?>AA 100%); 
           padding: 60px 20px; color: white;">
    <div style="max-width: 1000px; margin: 0 auto;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
            <span style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px; 
                         font-size: 14px; font-weight: 600;">
                <?= $typeLabel ?>
            </span>
            
            <?php if ($event['is_featured']): ?>
            <span style="background: #ffc107; color: #212529; padding: 8px 16px; border-radius: 20px; 
                         font-size: 14px; font-weight: 600;">
                <i class="fas fa-star"></i> Рекомендуем
            </span>
            <?php endif; ?>
            
            <?php if ($isToday): ?>
            <span style="background: #fff; color: <?= $typeColor ?>; padding: 8px 16px; border-radius: 20px; 
                         font-size: 14px; font-weight: 600; animation: pulse 2s infinite;">
                Сегодня!
            </span>
            <?php elseif ($isTomorrow): ?>
            <span style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px; 
                         font-size: 14px; font-weight: 600;">
                Завтра
            </span>
            <?php elseif ($isPast): ?>
            <span style="background: rgba(108,117,125,0.8); padding: 8px 16px; border-radius: 20px; 
                         font-size: 14px; font-weight: 600;">
                Завершено
            </span>
            <?php endif; ?>
        </div>
        
        <h1 style="font-size: 42px; font-weight: 700; margin-bottom: 15px; line-height: 1.2;">
            <?= htmlspecialchars($event['title']) ?>
        </h1>
        
        <div style="display: flex; flex-wrap: wrap; gap: 25px; font-size: 16px; opacity: 0.95;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-calendar-alt"></i>
                <strong><?= $eventDate->format('d.m.Y') ?></strong>
                <?php if ($event['end_date'] && $event['end_date'] !== $event['start_date']): ?>
                - <?= date('d.m.Y', strtotime($event['end_date'])) ?>
                <?php endif; ?>
            </div>
            
            <?php if ($event['start_time']): ?>
            <div style="display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-clock"></i>
                <strong><?= date('H:i', strtotime($event['start_time'])) ?></strong>
                <?php if ($event['end_time']): ?>
                - <?= date('H:i', strtotime($event['end_time'])) ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <?php if ($event['location']): ?>
            <div style="display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-map-marker-alt"></i>
                <strong><?= htmlspecialchars($event['location']) ?></strong>
            </div>
            <?php endif; ?>
            
            <div style="display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-eye"></i>
                <span><?= number_format($event['views']) ?> просмотров</span>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: Breadcrumbs
ob_start();
?>
<div style="padding: 15px 20px; background: #f8f9fa; margin: 0;">
    <div style="max-width: 1000px; margin: 0 auto;">
        <?php 
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/breadcrumbs.php';
        $breadcrumbData = [
            'title' => $event['title'],
            'event_type' => $event['event_type']
        ];
        render_breadcrumbs(get_breadcrumbs('event-single', $breadcrumbData));
        ?>
    </div>
</div>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Action buttons
ob_start();
?>
<div style="padding: 25px 20px; background: var(--bg-secondary);">
    <div style="max-width: 1000px; margin: 0 auto; display: flex; justify-content: space-between; 
               align-items: center; flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <?php if (isset($_SESSION['user_id']) && !$isPast): ?>
            <?php if ($isSubscribed): ?>
            <button onclick="unsubscribeFromEvent(<?= $eventId ?>)" id="subscribeBtn"
                    style="background: #6c757d; color: white; border: none; padding: 12px 20px; 
                           border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 14px;">
                <i class="fas fa-bell-slash"></i> Отписаться от напоминаний
            </button>
            <?php else: ?>
            <button onclick="subscribeToEvent(<?= $eventId ?>)" id="subscribeBtn"
                    style="background: <?= $typeColor ?>; color: white; border: none; padding: 12px 20px; 
                           border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 14px;">
                <i class="fas fa-bell"></i> Напомнить мне
            </button>
            <?php endif; ?>
            <?php elseif (!isset($_SESSION['user_id']) && !$isPast): ?>
            <a href="/login?redirect=/event/<?= $eventId ?>" 
               style="background: <?= $typeColor ?>; color: white; text-decoration: none; 
                      padding: 12px 20px; border-radius: 8px; font-weight: 600; font-size: 14px;">
                <i class="fas fa-sign-in-alt"></i> Войти для напоминаний
            </a>
            <?php endif; ?>
            
            <button onclick="addToCalendar()" 
                    style="background: #28a745; color: white; border: none; padding: 12px 20px; 
                           border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 14px;">
                <i class="fas fa-calendar-plus"></i> Добавить в календарь
            </button>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button onclick="shareEvent()" 
                    style="background: #007bff; color: white; border: none; padding: 12px 16px; 
                           border-radius: 8px; cursor: pointer; font-size: 14px;">
                <i class="fas fa-share-alt"></i>
            </button>
            
            <?php if (isset($_SESSION['user_id']) && ($_SESSION['role'] === 'admin' || $_SESSION['user_id'] == $event['created_by'])): ?>
            <a href="/event/<?= $eventId ?>/edit" 
               style="background: #fd7e14; color: white; text-decoration: none; padding: 12px 16px; 
                      border-radius: 8px; font-size: 14px;">
                <i class="fas fa-edit"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();

// Section 4: Event details
ob_start();
?>
<div style="padding: 40px 20px;">
    <div style="max-width: 1000px; margin: 0 auto; display: grid; grid-template-columns: 1fr 300px; gap: 40px;">
        <!-- Main content -->
        <div>
            <?php if ($event['description']): ?>
            <div style="background: var(--bg-primary); border-radius: 12px; padding: 30px; margin-bottom: 30px;">
                <h2 style="margin: 0 0 20px 0; font-size: 24px;">Описание</h2>
                <div style="font-size: 16px; line-height: 1.6; color: var(--text-primary);">
                    <?= nl2br(htmlspecialchars($event['description'])) ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Event info grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; 
                       margin-bottom: 30px;">
                <div style="background: var(--bg-primary); border-radius: 12px; padding: 25px; 
                           border-left: 4px solid <?= $typeColor ?>;">
                    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: <?= $typeColor ?>;">
                        <i class="fas fa-info-circle"></i> Детали события
                    </h3>
                    <div style="display: flex; flex-direction: column; gap: 12px; font-size: 14px;">
                        <div><strong>Тип:</strong> <?= $typeLabel ?></div>
                        <div><strong>Дата:</strong> <?= $eventDate->format('d.m.Y') ?>
                            <?php if ($event['end_date'] && $event['end_date'] !== $event['start_date']): ?>
                            - <?= date('d.m.Y', strtotime($event['end_date'])) ?>
                            <?php endif; ?>
                        </div>
                        <?php if ($event['start_time']): ?>
                        <div><strong>Время:</strong> <?= date('H:i', strtotime($event['start_time'])) ?>
                            <?php if ($event['end_time']): ?>
                            - <?= date('H:i', strtotime($event['end_time'])) ?>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <div><strong>Целевая аудитория:</strong> 
                            <?php 
                            $audiences = [
                                'students' => 'Школьники',
                                'graduates' => 'Выпускники', 
                                'parents' => 'Родители',
                                'all' => 'Все'
                            ];
                            echo $audiences[$event['target_audience']] ?? 'Все';
                            ?>
                        </div>
                    </div>
                </div>
                
                <?php if ($event['location'] || $event['organizer']): ?>
                <div style="background: var(--bg-primary); border-radius: 12px; padding: 25px; 
                           border-left: 4px solid #28a745;">
                    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #28a745;">
                        <i class="fas fa-map-marker-alt"></i> Место проведения
                    </h3>
                    <div style="display: flex; flex-direction: column; gap: 12px; font-size: 14px;">
                        <?php if ($event['location']): ?>
                        <div><strong>Адрес:</strong> <?= htmlspecialchars($event['location']) ?></div>
                        <?php endif; ?>
                        <?php if ($event['organizer']): ?>
                        <div><strong>Организатор:</strong> <?= htmlspecialchars($event['organizer']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Time until event -->
            <?php if (!$isPast): ?>
            <div style="background: linear-gradient(135deg, <?= $typeColor ?>15, <?= $typeColor ?>05); 
                       border: 1px solid <?= $typeColor ?>30; border-radius: 12px; padding: 25px; 
                       text-align: center; margin-bottom: 30px;">
                <h3 style="margin: 0 0 15px 0; color: <?= $typeColor ?>;">
                    <i class="fas fa-hourglass-half"></i> 
                    <?= $isToday ? 'Событие сегодня!' : ($isTomorrow ? 'Событие завтра!' : 'До события осталось') ?>
                </h3>
                <div id="countdown" style="font-size: 24px; font-weight: 700; color: <?= $typeColor ?>;">
                    <!-- Countdown will be inserted here by JavaScript -->
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <div>
            <!-- Quick actions -->
            <div style="background: var(--bg-primary); border-radius: 12px; padding: 25px; margin-bottom: 20px;">
                <h3 style="margin: 0 0 20px 0; font-size: 18px;">Быстрые действия</h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <button onclick="copyEventLink()" 
                            style="background: #17a2b8; color: white; border: none; padding: 10px 16px; 
                                   border-radius: 6px; font-size: 14px; cursor: pointer; width: 100%;">
                        <i class="fas fa-link"></i> Скопировать ссылку
                    </button>
                    
                    <button onclick="exportToGoogleCalendar()" 
                            style="background: #4285f4; color: white; border: none; padding: 10px 16px; 
                                   border-radius: 6px; font-size: 14px; cursor: pointer; width: 100%;">
                        <i class="fab fa-google"></i> Google Calendar
                    </button>
                    
                    <button onclick="exportToOutlook()" 
                            style="background: #0078d4; color: white; border: none; padding: 10px 16px; 
                                   border-radius: 6px; font-size: 14px; cursor: pointer; width: 100%;">
                        <i class="fab fa-microsoft"></i> Outlook
                    </button>
                </div>
            </div>
            
            <!-- Related events -->
            <?php if (!empty($relatedEvents)): ?>
            <div style="background: var(--bg-primary); border-radius: 12px; padding: 25px;">
                <h3 style="margin: 0 0 20px 0; font-size: 18px;">Похожие события</h3>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <?php foreach ($relatedEvents as $related): ?>
                    <div style="padding-bottom: 15px; border-bottom: 1px solid var(--border-color);">
                        <h4 style="margin: 0 0 8px 0; font-size: 14px; line-height: 1.4;">
                            <a href="/event/<?= $related['id'] ?>" 
                               style="color: var(--text-primary); text-decoration: none;">
                                <?= htmlspecialchars($related['title']) ?>
                            </a>
                        </h4>
                        <div style="font-size: 12px; color: var(--text-secondary);">
                            <i class="fas fa-calendar"></i> <?= date('d.m.Y', strtotime($related['start_date'])) ?>
                            <?php if ($related['location']): ?>
                            <br><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($related['location']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Event subscription functions
async function subscribeToEvent(eventId) {
    try {
        const response = await fetch('/api/events/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                event_id: eventId,
                reminder_minutes: 60
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            const btn = document.getElementById('subscribeBtn');
            btn.innerHTML = '<i class="fas fa-bell-slash"></i> Отписаться от напоминаний';
            btn.style.background = '#6c757d';
            btn.onclick = () => unsubscribeFromEvent(eventId);
            
            // Show success message
            showMessage('Напоминание установлено!', 'success');
        } else {
            showMessage(data.error || 'Ошибка при подписке', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showMessage('Произошла ошибка', 'error');
    }
}

async function unsubscribeFromEvent(eventId) {
    try {
        const response = await fetch('/api/events/unsubscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                event_id: eventId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            const btn = document.getElementById('subscribeBtn');
            btn.innerHTML = '<i class="fas fa-bell"></i> Напомнить мне';
            btn.style.background = '<?= $typeColor ?>';
            btn.onclick = () => subscribeToEvent(eventId);
            
            showMessage('Подписка отменена', 'success');
        }
    } catch (error) {
        console.error('Error:', error);
        showMessage('Произошла ошибка', 'error');
    }
}

// Calendar export functions
function exportToGoogleCalendar() {
    const startDate = '<?= $eventDate->format('Ymd') ?>';
    const startTime = '<?= $event['start_time'] ? date('His', strtotime($event['start_time'])) : '120000' ?>';
    const endTime = '<?= $event['end_time'] ? date('His', strtotime($event['end_time'])) : '130000' ?>';
    
    const url = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent('<?= addslashes($event['title']) ?>')}&dates=${startDate}T${startTime}/${startDate}T${endTime}&details=${encodeURIComponent('<?= addslashes($event['description'] ?? '') ?>')}&location=${encodeURIComponent('<?= addslashes($event['location'] ?? '') ?>')}`;
    
    window.open(url, '_blank');
}

function exportToOutlook() {
    const startDate = new Date('<?= $event['start_date'] ?>' + ' ' + '<?= $event['start_time'] ?? '12:00' ?>');
    const endDate = new Date(startDate);
    if ('<?= $event['end_time'] ?>') {
        endDate.setTime(new Date('<?= $event['start_date'] ?>' + ' ' + '<?= $event['end_time'] ?>').getTime());
    } else {
        endDate.setHours(endDate.getHours() + 1);
    }
    
    const url = `https://outlook.live.com/calendar/0/deeplink/compose?subject=${encodeURIComponent('<?= addslashes($event['title']) ?>')}&startdt=${startDate.toISOString()}&enddt=${endDate.toISOString()}&body=${encodeURIComponent('<?= addslashes($event['description'] ?? '') ?>')}&location=${encodeURIComponent('<?= addslashes($event['location'] ?? '') ?>')}`;
    
    window.open(url, '_blank');
}

function addToCalendar() {
    // Show options menu
    const options = ['Google Calendar', 'Outlook', 'Скачать .ics файл'];
    const choice = prompt('Выберите сервис календаря:\n1. Google Calendar\n2. Outlook\n3. Скачать .ics файл\n\nВведите номер (1-3):');
    
    switch (choice) {
        case '1':
            exportToGoogleCalendar();
            break;
        case '2':
            exportToOutlook();
            break;
        case '3':
            downloadICS();
            break;
        default:
            return;
    }
}

function downloadICS() {
    const startDate = new Date('<?= $event['start_date'] ?>' + ' ' + '<?= $event['start_time'] ?? '12:00' ?>');
    const endDate = new Date(startDate);
    if ('<?= $event['end_time'] ?>') {
        endDate.setTime(new Date('<?= $event['start_date'] ?>' + ' ' + '<?= $event['end_time'] ?>').getTime());
    } else {
        endDate.setHours(endDate.getHours() + 1);
    }
    
    const icsContent = `BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//11klassniki.ru//Event//RU
BEGIN:VEVENT
UID:event-<?= $eventId ?>@11klassniki.ru
DTSTAMP:${new Date().toISOString().replace(/[-:]/g, '').split('.')[0]}Z
DTSTART:${startDate.toISOString().replace(/[-:]/g, '').split('.')[0]}Z
DTEND:${endDate.toISOString().replace(/[-:]/g, '').split('.')[0]}Z
SUMMARY:<?= addslashes($event['title']) ?>
DESCRIPTION:<?= addslashes($event['description'] ?? '') ?>
LOCATION:<?= addslashes($event['location'] ?? '') ?>
END:VEVENT
END:VCALENDAR`;
    
    const blob = new Blob([icsContent], { type: 'text/calendar' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'event-<?= $eventId ?>.ics';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function copyEventLink() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        showMessage('Ссылка скопирована!', 'success');
    }).catch(() => {
        // Fallback for older browsers
        const input = document.createElement('input');
        input.value = url;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        showMessage('Ссылка скопирована!', 'success');
    });
}

function shareEvent() {
    if (navigator.share) {
        navigator.share({
            title: '<?= addslashes($event['title']) ?>',
            text: 'Посмотри это событие на 11klassniki.ru',
            url: window.location.href
        });
    } else {
        copyEventLink();
    }
}

function showMessage(message, type) {
    const msgDiv = document.createElement('div');
    msgDiv.textContent = message;
    msgDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 8px;
        font-weight: 600;
        color: white;
        z-index: 1000;
        background: ${type === 'success' ? '#28a745' : '#dc3545'};
    `;
    
    document.body.appendChild(msgDiv);
    
    setTimeout(() => {
        msgDiv.style.opacity = '0';
        msgDiv.style.transition = 'opacity 0.3s';
        setTimeout(() => document.body.removeChild(msgDiv), 300);
    }, 3000);
}

// Countdown timer
<?php if (!$isPast): ?>
function updateCountdown() {
    const eventDate = new Date('<?= $event['start_date'] ?>' + ' ' + '<?= $event['start_time'] ?? '00:00' ?>');
    const now = new Date();
    const diff = eventDate.getTime() - now.getTime();
    
    if (diff <= 0) {
        document.getElementById('countdown').innerHTML = 'Событие началось!';
        return;
    }
    
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    
    let countdownText = '';
    if (days > 0) countdownText += `${days} дн. `;
    if (hours > 0) countdownText += `${hours} ч. `;
    countdownText += `${minutes} мин.`;
    
    document.getElementById('countdown').innerHTML = countdownText;
}

// Update countdown every minute
updateCountdown();
setInterval(updateCountdown, 60000);
<?php endif; ?>

// CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }
`;
document.head.appendChild(style);
</script>
<?php
$greyContent4 = ob_get_clean();

// Include template
$blueContent = '';
include $_SERVER['DOCUMENT_ROOT'] . '/real_template_local.php';
?>