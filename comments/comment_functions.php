<?php
if (!function_exists('hasReplies')) {
    // Function to check if there are replies for a given comment
    // Check if a comment has replies
    function hasReplies($commentId, $connection)
    {
        $query = "SELECT COUNT(*) FROM comments WHERE parent_id=?";

        $stmt = mysqli_prepare($connection, $query);

        if (!$stmt) {
            header("Location: /error");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "i", $commentId);

        mysqli_stmt_execute($stmt);

        if ($stmt->errno) {
            header("Location: /error");
            exit();
        }

        $result = mysqli_stmt_get_result($stmt);
        $count = mysqli_fetch_array($result)[0];

        mysqli_stmt_close($stmt);

        return $count > 0;
    }
}

if (!function_exists('getChildComments')) {
    // Function to fetch child comments for a given parent comment
    function getChildComments($parentCommentId, $connection, $entityType, $entityId)
    {
        $entityField = getEntityField($entityType);

        // $query = "SELECT comments.id, comments.$entityField, comments.user_id, users.avatar_url, comments.comment_text,
        //     comments.date, users.timezone AS user_timezone
        //     FROM comments
        //     JOIN users ON comments.user_id = users.id
        //     WHERE comments.parent_id = ? ORDER BY comments.date DESC";

        $query = "SELECT comments.id, comments.$entityField, comments.user_id, users.avatar_url AS avatar, comments.comment_text,
    comments.date, users.timezone AS user_timezone, comments.author_of_comment
    FROM comments
    LEFT JOIN users ON comments.user_id = users.id
    WHERE (comments.parent_id = ? AND comments.user_id IS NOT NULL)
        OR (comments.parent_id = ? AND comments.user_id = 0)
    ORDER BY comments.date DESC";


        $stmt = mysqli_prepare($connection, $query);

        if (!$stmt) {
            header("Location: /error");
            exit();
        }

        // mysqli_stmt_bind_param($stmt, "i", $parentCommentId);
        mysqli_stmt_bind_param($stmt, "ii", $parentCommentId, $parentCommentId);

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $childComments = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $childComments[] = $row;
        }

        mysqli_stmt_close($stmt);

        return $childComments;
    }

    // Helper function to get the appropriate entity field based on entity type
    function getEntityField($entityType)
    {
        switch ($entityType) {
            case 'school':
                return 'entity_id'; // Updated field name for school
            case 'vpo':
                return 'entity_id'; // Updated field name for university
            case 'spo':
                return 'entity_id'; // Updated field name for college
            case 'post':
                return 'entity_id'; // Updated field name for post
                // Add more cases for other entity types if needed
            default:
                header("Location: /error");
                exit();
        }
    }
}


if (!function_exists('getUserNames')) {
    // Function to get user names based on user_id
    function getUserNames($userId, $connection)
    {
        $query = "SELECT first_name, last_name FROM users WHERE id=?";
        $stmt = mysqli_prepare($connection, $query);

        if (!$stmt) {
            header("Location: /error");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $userNames = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return $userNames;
    }
}

if (!function_exists('getFormattedDate')) {
    // Function to get formatted date
    function getFormattedDate($timestamp)
    {
        // Check if $timestamp is numeric or convert it to a timestamp
        if (!is_numeric($timestamp)) {
            $timestamp = strtotime($timestamp);
        }

        // Verify if the conversion was successful
        if ($timestamp === false) {
            return ''; // Unable to parse timestamp, return an empty string or handle it as needed
        }

        // Define an array to map English month names to Russian
        $monthTranslations = [
            'January' => 'января',
            'February' => 'февраля',
            'March' => 'марта',
            'April' => 'апреля',
            'May' => 'мая',
            'June' => 'июня',
            'July' => 'июля',
            'August' => 'августа',
            'September' => 'сентября',
            'October' => 'октября',
            'November' => 'ноября',
            'December' => 'декабря',
        ];

        // Format the date using the array for month translation and 24-hour time format
        $formattedDate = date('d F, Y H:i', $timestamp);
        $formattedDate = str_replace(array_keys($monthTranslations), $monthTranslations, $formattedDate);

        return $formattedDate;
    }
}


if (!function_exists('getElapsedTime')) {
    function getElapsedTime($timestamp, $timezone = null)
    {
        if ($timestamp === null) {
            return 'Неверная метка времени';
        }

        // Include timezone handler if not already included
        if (!function_exists('getUserTimezone')) {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/comments/timezone-handler.php';
        }
        
        // Use user's timezone from session if not provided
        if ($timezone === null) {
            $timezone = getUserTimezone();
        }

        // Use the timezone handler function for consistency
        return formatTimeAgoUserTZ($timestamp, $timezone);
    }
}
