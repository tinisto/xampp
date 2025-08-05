<?php
/**
 * Timezone Handler for Comments
 * Manages user timezone detection and conversion
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear timezone if requested
if (isset($_GET['clear'])) {
    unset($_SESSION['user_timezone']);
    echo json_encode(['success' => true]);
    exit;
}

// Set user timezone in session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['timezone'])) {
    $timezone = $_POST['timezone'];
    
    // Validate timezone
    $valid_timezones = timezone_identifiers_list();
    if (in_array($timezone, $valid_timezones)) {
        $_SESSION['user_timezone'] = $timezone;
        echo json_encode(['success' => true, 'timezone' => $timezone]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid timezone']);
    }
    exit;
}

// Get user timezone from session or default
function getUserTimezone() {
    if (isset($_SESSION['user_timezone'])) {
        return $_SESSION['user_timezone'];
    }
    
    // Default to UTC if no timezone is set
    return 'UTC';
}

// Get manual timezone selection dropdown
function getTimezoneSelector($currentTimezone = null) {
    if ($currentTimezone === null) {
        $currentTimezone = getUserTimezone();
    }
    
    $common_timezones = [
        'Australia/Sydney' => 'Sydney (UTC+11)',
        'Australia/Melbourne' => 'Melbourne (UTC+11)',
        'Australia/Brisbane' => 'Brisbane (UTC+10)',
        'Australia/Perth' => 'Perth (UTC+8)',
        'Australia/Adelaide' => 'Adelaide (UTC+10:30)',
        'Europe/Moscow' => 'Moscow (UTC+3)',
        'Europe/London' => 'London (UTC+0)',
        'Europe/Berlin' => 'Berlin (UTC+1)',
        'America/New_York' => 'New York (UTC-5)',
        'America/Los_Angeles' => 'Los Angeles (UTC-8)',
        'America/Chicago' => 'Chicago (UTC-6)',
        'Asia/Tokyo' => 'Tokyo (UTC+9)',
        'Asia/Shanghai' => 'Shanghai (UTC+8)',
        'Asia/Dubai' => 'Dubai (UTC+4)',
        'UTC' => 'UTC (UTC+0)'
    ];
    
    $html = '<select id="timezone-selector" onchange="updateTimezone(this.value)" style="padding: 5px; border-radius: 4px;">';
    $html .= '<option value="">-- Select Timezone --</option>';
    
    foreach ($common_timezones as $tz => $label) {
        $selected = ($tz === $currentTimezone) ? 'selected' : '';
        $html .= "<option value=\"$tz\" $selected>$label</option>";
    }
    
    $html .= '</select>';
    
    return $html;
}

// Convert timestamp to user's timezone
function convertToUserTimezone($timestamp, $userTimezone = null) {
    if ($userTimezone === null) {
        $userTimezone = getUserTimezone();
    }
    
    try {
        // IMPORTANT: Database timestamps are stored in UTC-4 (EDT)
        // Based on MySQL timezone check showing -04:00:00 offset
        $databaseTimezone = 'America/New_York'; // UTC-4 in summer (EDT)
        
        // Create DateTime object from timestamp in database timezone
        if (is_numeric($timestamp)) {
            $date = new DateTime();
            $date->setTimestamp($timestamp);
        } else {
            // Database timestamps are in EDT (UTC-4)
            $date = new DateTime($timestamp, new DateTimeZone($databaseTimezone));
        }
        
        // Convert to user's timezone
        $date->setTimezone(new DateTimeZone($userTimezone));
        
        return $date;
    } catch (Exception $e) {
        // Return original timestamp if conversion fails
        return new DateTime($timestamp);
    }
}

// Format time ago in user's timezone
function formatTimeAgoUserTZ($timestamp, $userTimezone = null) {
    if ($userTimezone === null) {
        $userTimezone = getUserTimezone();
    }
    
    try {
        // Current time in user's timezone
        $now = new DateTime('now', new DateTimeZone($userTimezone));
        
        // Convert timestamp to user's timezone
        $commentTime = convertToUserTimezone($timestamp, $userTimezone);
        
        // Calculate difference
        $diff = $now->diff($commentTime);
        
        // Calculate total seconds
        $totalSeconds = ($diff->days * 24 * 60 * 60) + 
                       ($diff->h * 60 * 60) + 
                       ($diff->i * 60) + 
                       $diff->s;
        
        // Format time ago
        if ($totalSeconds < 60) {
            return 'только что';
        } elseif ($totalSeconds < 3600) {
            $minutes = floor($totalSeconds / 60);
            return $minutes . ' ' . getPlural($minutes, 'минуту', 'минуты', 'минут') . ' назад';
        } elseif ($totalSeconds < 86400) {
            $hours = floor($totalSeconds / 3600);
            return $hours . ' ' . getPlural($hours, 'час', 'часа', 'часов') . ' назад';
        } elseif ($totalSeconds < 2592000) {
            $days = floor($totalSeconds / 86400);
            return $days . ' ' . getPlural($days, 'день', 'дня', 'дней') . ' назад';
        } else {
            // Return formatted date for older comments
            return $commentTime->format('d.m.Y');
        }
    } catch (Exception $e) {
        // Fallback to simple format
        return date('d.m.Y', strtotime($timestamp));
    }
}

// Helper function for Russian plurals
function getPlural($number, $one, $two, $five) {
    $number = abs($number) % 100;
    $n1 = $number % 10;
    
    if ($number > 10 && $number < 20) {
        return $five;
    }
    if ($n1 > 1 && $n1 < 5) {
        return $two;
    }
    if ($n1 == 1) {
        return $one;
    }
    
    return $five;
}

// JavaScript for timezone detection
function getTimezoneDetectionScript() {
    return <<<'SCRIPT'
<script>
(function() {
    // Check if timezone is already set
    var timezoneSet = document.cookie.includes('user_timezone_set=true');
    if (timezoneSet) {
        return;
    }
    
    // Get user's timezone
    var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    
    // Send timezone to server
    fetch('/comments/timezone-handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'timezone=' + encodeURIComponent(timezone)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Set cookie to prevent repeated detection
            document.cookie = 'user_timezone_set=true; path=/; max-age=31536000'; // 1 year
            
            // Reload comments to show correct times
            var commentsContainer = document.querySelector('.comments-list');
            if (commentsContainer && window.location.search.includes('comment_success') === false) {
                location.reload();
            }
        }
    })
    .catch(error => {
        console.error('Timezone detection error:', error);
    });
})();
</script>
SCRIPT;
}
?>