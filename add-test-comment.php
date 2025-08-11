<?php
session_start();
require_once 'database/db_modern.php';

// Simple form to add test comments
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Test Comment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        button {
            background: #28a745;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add Test Comment</h1>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $db = Database::getInstance();
                $conn = $db->getConnection();
                
                // Get form data
                $user_id = $_POST['user_id'] ?? 1;
                $item_type = $_POST['item_type'] ?? 'post';
                $item_id = $_POST['item_id'] ?? 1;
                $comment_text = $_POST['comment_text'] ?? '';
                $is_approved = $_POST['is_approved'] ?? 1;
                
                // Insert comment
                $stmt = $conn->prepare("INSERT INTO comments (user_id, item_type, item_id, comment_text, is_approved, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$user_id, $item_type, $item_id, $comment_text, $is_approved]);
                
                echo '<div class="message success">✓ Comment added successfully! Comment ID: ' . $conn->lastInsertId() . '</div>';
                
            } catch (Exception $e) {
                echo '<div class="message error">✗ Error: ' . $e->getMessage() . '</div>';
            }
        }
        
        // Show current comments count
        try {
            $db = Database::getInstance();
            $conn = $db->getConnection();
            
            $total = $conn->query("SELECT COUNT(*) FROM comments")->fetchColumn();
            $approved = $conn->query("SELECT COUNT(*) FROM comments WHERE is_approved = 1")->fetchColumn();
            $pending = $conn->query("SELECT COUNT(*) FROM comments WHERE is_approved = 0")->fetchColumn();
            
            echo '<div class="message info">';
            echo '<strong>Current Comments Status:</strong><br>';
            echo 'Total Comments: ' . $total . '<br>';
            echo 'Approved: ' . $approved . '<br>';
            echo 'Pending Moderation: ' . $pending;
            echo '</div>';
        } catch (Exception $e) {
            // Ignore if table doesn't exist
        }
        ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="user_id">User ID:</label>
                <input type="number" id="user_id" name="user_id" value="<?= $_SESSION['user_id'] ?? 1 ?>" required>
            </div>
            
            <div class="form-group">
                <label for="item_type">Item Type:</label>
                <select id="item_type" name="item_type" required>
                    <option value="post">Post</option>
                    <option value="news">News</option>
                    <option value="school">School</option>
                    <option value="spo">SPO</option>
                    <option value="vpo">VPO</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="item_id">Item ID:</label>
                <input type="number" id="item_id" name="item_id" value="1" required>
            </div>
            
            <div class="form-group">
                <label for="comment_text">Comment Text:</label>
                <textarea id="comment_text" name="comment_text" required>This is a test comment created on <?= date('Y-m-d H:i:s') ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="is_approved">Status:</label>
                <select id="is_approved" name="is_approved" required>
                    <option value="1">Approved</option>
                    <option value="0">Pending Moderation</option>
                </select>
            </div>
            
            <button type="submit">Add Comment</button>
        </form>
        
        <br><br>
        <h2>Quick Links:</h2>
        <ul>
            <li><a href="/dashboard-moderation.php" target="_blank">Go to Comment Moderation Dashboard</a></li>
            <li><a href="/dashboard-analytics.php" target="_blank">View Comment Analytics</a></li>
            <li><a href="/">Back to Homepage</a></li>
        </ul>
    </div>
</body>
</html>