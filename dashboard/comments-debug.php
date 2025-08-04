<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

echo "Step 1: Session OK<br>";

try {
    require_once dirname(__DIR__) . '/config/loadEnv.php';
    echo "Step 2: Config loaded<br>";
} catch (Exception $e) {
    die("Error loading config: " . $e->getMessage());
}

try {
    require_once dirname(__DIR__) . '/database/db_connections.php';
    echo "Step 3: Database loaded<br>";
} catch (Exception $e) {
    die("Error loading database: " . $e->getMessage());
}

if (!isset($connection)) {
    die("Error: Database connection not established");
}

echo "Step 4: Connection OK<br>";

$message = '';
$messageType = '';

// Get comments with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

echo "Step 5: Pagination set (page=$page, limit=$limit, offset=$offset)<br>";

try {
    $sql = "SELECT c.*, u.username 
            FROM comments c 
            LEFT JOIN users u ON c.user_id = u.id 
            ORDER BY c.date DESC 
            LIMIT ? OFFSET ?";
    
    echo "Step 6: SQL prepared<br>";
    
    $stmt = $connection->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $connection->error);
    }
    
    echo "Step 7: Statement prepared<br>";
    
    $stmt->bind_param("ii", $limit, $offset);
    echo "Step 8: Parameters bound<br>";
    
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    
    echo "Step 9: Query executed<br>";
    
    $comments = $stmt->get_result();
    echo "Step 10: Results fetched<br>";
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM comments";
    $countResult = $connection->query($countSql);
    if (!$countResult) {
        die("Count query failed: " . $connection->error);
    }
    
    $totalComments = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalComments / $limit);
    
    echo "Step 11: Count complete (total=$totalComments, pages=$totalPages)<br>";
    echo "<br>SUCCESS! The query works. Now showing first few comments:<br><br>";
    
    // Show first few comments
    $count = 0;
    while ($comment = $comments->fetch_assoc()) {
        echo "Comment ID: " . $comment['id'] . "<br>";
        echo "Author: " . ($comment['author_of_comment'] ?? $comment['username'] ?? 'Anonymous') . "<br>";
        echo "Text: " . htmlspecialchars(substr($comment['comment_text'], 0, 50)) . "...<br>";
        echo "Date: " . $comment['date'] . "<br><br>";
        
        $count++;
        if ($count >= 3) break;
    }
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "<br>Trace: " . $e->getTraceAsString());
}
?>