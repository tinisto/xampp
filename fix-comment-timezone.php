<?php
/**
 * Fix for comment timezone display issue
 * 
 * The issue: Comments are showing "4 hours ago" instead of "just now" because
 * the database stores dates in one timezone (likely UTC or server local time)
 * but the display function assumes they're in Moscow time.
 */

// Fixed version of getElapsedTime function
function getElapsedTimeFixed($timestamp, $userTimezone = 'Europe/Moscow') {
    if ($timestamp === null) {
        return 'Неверная метка времени';
    }

    // Current time in user's timezone (Moscow)
    $now = new DateTime('now', new DateTimeZone($userTimezone));
    
    // Comment time is stored in database as server time (usually UTC or server's local timezone)
    // We need to convert it to the user's timezone for correct comparison
    $commentTime = new DateTime($timestamp, new DateTimeZone('UTC')); // Assuming DB stores in UTC
    $commentTime->setTimezone(new DateTimeZone($userTimezone)); // Convert to user timezone
    
    $diff = $now->diff($commentTime);
    
    // Calculate total difference in seconds for more accurate "just now" detection
    $totalSeconds = ($diff->days * 24 * 60 * 60) + 
                   ($diff->h * 60 * 60) + 
                   ($diff->i * 60) + 
                   $diff->s;
    
    // If less than 60 seconds, return "just now"
    if ($totalSeconds < 60) {
        return 'Только что';
    }
    
    $units = [
        "y" => ['г.', 'год', 'года', 'лет'],
        "m" => ['мес.', 'месяц', 'месяца', 'месяцев'],
        "d" => ['д.', 'день', 'дня', 'дней'],
        "h" => ['ч.', 'час', 'часа', 'часов'],
        "i" => ['мин.', 'минута', 'минуты', 'минут'],
    ];

    foreach ($units as $unit => $formats) {
        if ($diff->$unit > 0) {
            $value = $diff->$unit;
            $formatIndex = ($value % 10 == 1 && $value % 100 != 11)
                ? 1
                : (($value % 10 >= 2 && $value % 10 <= 4 && ($value % 100 < 10 || $value % 100 >= 20))
                    ? 2
                    : 3);
            return $value . ' ' . $formats[$formatIndex] . ' назад';
        }
    }

    return 'Только что';
}

// Alternative fix if database timezone is not UTC but server's local timezone
function getElapsedTimeServerTimezone($timestamp, $userTimezone = 'Europe/Moscow') {
    if ($timestamp === null) {
        return 'Неверная метка времени';
    }

    // Get server's default timezone
    $serverTimezone = date_default_timezone_get();
    
    // Current time in user's timezone
    $now = new DateTime('now', new DateTimeZone($userTimezone));
    
    // Comment time from database (in server's timezone)
    $commentTime = new DateTime($timestamp, new DateTimeZone($serverTimezone));
    $commentTime->setTimezone(new DateTimeZone($userTimezone)); // Convert to user timezone
    
    $diff = $now->diff($commentTime);
    
    // Rest of the function remains the same...
    $totalSeconds = ($diff->days * 24 * 60 * 60) + 
                   ($diff->h * 60 * 60) + 
                   ($diff->i * 60) + 
                   $diff->s;
    
    if ($totalSeconds < 60) {
        return 'Только что';
    }
    
    $units = [
        "y" => ['г.', 'год', 'года', 'лет'],
        "m" => ['мес.', 'месяц', 'месяца', 'месяцев'],
        "d" => ['д.', 'день', 'дня', 'дней'],
        "h" => ['ч.', 'час', 'часа', 'часов'],
        "i" => ['мин.', 'минута', 'минуты', 'минут'],
    ];

    foreach ($units as $unit => $formats) {
        if ($diff->$unit > 0) {
            $value = $diff->$unit;
            $formatIndex = ($value % 10 == 1 && $value % 100 != 11)
                ? 1
                : (($value % 10 >= 2 && $value % 10 <= 4 && ($value % 100 < 10 || $value % 100 >= 20))
                    ? 2
                    : 3);
            return $value . ' ' . $formats[$formatIndex] . ' назад';
        }
    }

    return 'Только что';
}

echo "This file contains the fixed timezone functions. To apply the fix:\n\n";
echo "1. First run check-timezone-issue.php to determine your server's timezone configuration\n";
echo "2. Update the getElapsedTime function in /comments/comment_functions.php with one of the fixed versions above\n";
echo "3. The key change is properly handling the timezone conversion from database time to user's timezone\n";
?>