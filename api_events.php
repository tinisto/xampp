<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Необходима авторизация']);
    exit;
}

$action = $_GET['action'] ?? '';
$userId = $_SESSION['user_id'];

try {
    switch ($action) {
        case 'subscribe':
            // Subscribe to event reminders
            $input = json_decode(file_get_contents('php://input'), true);
            $eventId = (int)($input['event_id'] ?? 0);
            $reminderMinutes = (int)($input['reminder_minutes'] ?? 60);
            
            if (!$eventId) {
                echo json_encode(['success' => false, 'error' => 'Не указан ID события']);
                exit;
            }
            
            // Check if event exists and is public
            $event = db_fetch_one("SELECT id, start_date, start_time FROM events WHERE id = ? AND is_public = 1", [$eventId]);
            if (!$event) {
                echo json_encode(['success' => false, 'error' => 'Событие не найдено']);
                exit;
            }
            
            // Check if event is in the future
            $eventDateTime = $event['start_date'] . ' ' . ($event['start_time'] ?? '00:00:00');
            if (strtotime($eventDateTime) <= time()) {
                echo json_encode(['success' => false, 'error' => 'Нельзя подписаться на прошедшее событие']);
                exit;
            }
            
            // Check if already subscribed
            $existing = db_fetch_one(
                "SELECT id FROM event_subscriptions WHERE user_id = ? AND event_id = ?", 
                [$userId, $eventId]
            );
            
            if ($existing) {
                echo json_encode(['success' => false, 'error' => 'Вы уже подписаны на это событие']);
                exit;
            }
            
            // Create subscription
            $result = db_query(
                "INSERT INTO event_subscriptions (user_id, event_id, reminder_minutes) VALUES (?, ?, ?)",
                [$userId, $eventId, $reminderMinutes]
            );
            
            if ($result) {
                // Create notification about subscription
                require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/notifications.php';
                NotificationManager::notifyEventSubscription($userId, $event);
                
                echo json_encode(['success' => true, 'message' => 'Подписка оформлена успешно']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Ошибка при создании подписки']);
            }
            break;
            
        case 'unsubscribe':
            // Unsubscribe from event reminders
            $input = json_decode(file_get_contents('php://input'), true);
            $eventId = (int)($input['event_id'] ?? 0);
            
            if (!$eventId) {
                echo json_encode(['success' => false, 'error' => 'Не указан ID события']);
                exit;
            }
            
            // Remove subscription
            $result = db_query(
                "DELETE FROM event_subscriptions WHERE user_id = ? AND event_id = ?",
                [$userId, $eventId]
            );
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Подписка отменена']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Ошибка при отмене подписки']);
            }
            break;
            
        case 'get_subscriptions':
            // Get user's event subscriptions
            $subscriptions = db_fetch_all("
                SELECT es.*, e.title, e.start_date, e.start_time, e.event_type, e.location
                FROM event_subscriptions es
                JOIN events e ON es.event_id = e.id
                WHERE es.user_id = ? AND e.start_date >= CURRENT_DATE
                ORDER BY e.start_date ASC, e.start_time ASC
            ", [$userId]);
            
            echo json_encode(['success' => true, 'subscriptions' => $subscriptions]);
            break;
            
        case 'update_reminder':
            // Update reminder time for subscription
            $input = json_decode(file_get_contents('php://input'), true);
            $eventId = (int)($input['event_id'] ?? 0);
            $reminderMinutes = (int)($input['reminder_minutes'] ?? 60);
            
            if (!$eventId) {
                echo json_encode(['success' => false, 'error' => 'Не указан ID события']);
                exit;
            }
            
            // Update reminder time
            $result = db_query(
                "UPDATE event_subscriptions SET reminder_minutes = ? WHERE user_id = ? AND event_id = ?",
                [$reminderMinutes, $userId, $eventId]
            );
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Время напоминания обновлено']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Ошибка при обновлении']);
            }
            break;
            
        case 'check_upcoming':
            // Check for upcoming events that need reminders
            $now = date('Y-m-d H:i:s');
            
            // Find subscriptions where reminder should be sent
            $upcomingEvents = db_fetch_all("
                SELECT es.*, e.title, e.start_date, e.start_time, e.event_type, e.location,
                       u.email, u.name as user_name
                FROM event_subscriptions es
                JOIN events e ON es.event_id = e.id
                JOIN users u ON es.user_id = u.id
                WHERE es.is_reminded = 0 
                AND DATETIME(e.start_date || ' ' || COALESCE(e.start_time, '00:00:00'), 
                           '-' || es.reminder_minutes || ' minutes') <= ?
                AND DATETIME(e.start_date || ' ' || COALESCE(e.start_time, '00:00:00')) > ?
            ", [$now, $now]);
            
            $reminders = [];
            
            foreach ($upcomingEvents as $event) {
                // Send notification
                require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/notifications.php';
                NotificationManager::notifyEventReminder($event['user_id'], $event);
                
                // Send email reminder
                require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/email.php';
                EmailNotification::sendEventReminder($event);
                
                // Mark as reminded
                db_query(
                    "UPDATE event_subscriptions SET is_reminded = 1 WHERE id = ?",
                    [$event['id']]
                );
                
                $reminders[] = [
                    'event_id' => $event['event_id'],
                    'user_id' => $event['user_id'],
                    'title' => $event['title'],
                    'start_date' => $event['start_date']
                ];
            }
            
            echo json_encode([
                'success' => true, 
                'reminders_sent' => count($reminders),
                'events' => $reminders
            ]);
            break;
            
        case 'get_calendar_events':
            // Get events for calendar view
            $month = $_GET['month'] ?? date('m');
            $year = $_GET['year'] ?? date('Y');
            
            $startDate = "$year-$month-01";
            $endDate = date('Y-m-t', strtotime($startDate));
            
            $events = db_fetch_all("
                SELECT id, title, start_date, start_time, end_date, end_time, 
                       event_type, location, is_featured
                FROM events 
                WHERE is_public = 1 
                AND start_date BETWEEN ? AND ?
                ORDER BY start_date, start_time
            ", [$startDate, $endDate]);
            
            // Group events by date
            $calendar = [];
            foreach ($events as $event) {
                $date = $event['start_date'];
                if (!isset($calendar[$date])) {
                    $calendar[$date] = [];
                }
                $calendar[$date][] = $event;
            }
            
            echo json_encode(['success' => true, 'calendar' => $calendar]);
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Неизвестное действие']);
            break;
    }
    
} catch (Exception $e) {
    error_log("Events API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Внутренняя ошибка сервера']);
}
?>