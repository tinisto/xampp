# Comment Timezone Fix Summary

## Issue Description
Comments were showing "4 hours ago" instead of "just now" when posted. This was caused by a timezone mismatch between:
1. How dates are stored in the database (using MySQL's NOW() function)
2. How dates are displayed to users (assuming Moscow timezone)

## Root Cause
The `getElapsedTime()` function was creating both DateTime objects with the same timezone parameter, but the database stores timestamps in the server's default timezone (not necessarily Moscow time). This caused a time offset when calculating elapsed time.

## Files Fixed

### 1. `/comments/comment_functions.php`
**Function:** `getElapsedTime()`
**Change:** Modified to properly handle timezone conversion from server time to user timezone
- Now creates comment DateTime without timezone specification (uses server default)
- Then converts to user's timezone before comparison
- Added total seconds calculation for accurate "just now" detection

### 2. `/comments/load_comments_simple.php`
**Function:** `simpleTimeAgo()`
**Change:** Similar fix to ensure consistency
- Removed UTC timezone assumption
- Now uses server's default timezone for initial DateTime creation

## Technical Details

### Before (Incorrect):
```php
$now = new DateTime('now', new DateTimeZone($timezone));
$commentTimestamp = new DateTime($timestamp, new DateTimeZone($timezone));
```
This assumed the database timestamp was already in Moscow timezone.

### After (Fixed):
```php
$now = new DateTime('now', new DateTimeZone($timezone));
$commentTimestamp = new DateTime($timestamp); // Uses server default timezone
$commentTimestamp->setTimezone(new DateTimeZone($timezone)); // Convert to user timezone
```

## Testing
1. Run `/check-timezone-issue.php` to verify server timezone settings
2. Post a new comment and verify it shows "только что" (just now)
3. Wait a few minutes and verify it shows correct elapsed time

## Additional Notes
- The fix assumes the database stores timestamps in the server's default timezone
- If your MySQL server uses a different timezone configuration, you may need to adjust the code
- Consider setting explicit timezone in MySQL connection for consistency: `SET time_zone = '+00:00'`