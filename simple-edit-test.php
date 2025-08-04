<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get post 451
$post_id = 451;
$query = "SELECT * FROM posts WHERE id_post = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple Edit Test</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
        .form-group { margin: 20px 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Edit Post #<?= $post_id ?></h1>
    
    <form method="POST" action="/edit-process.php">
        <input type="hidden" name="id" value="<?= $post_id ?>">
        <input type="hidden" name="content_type" value="post">
        
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($item['title_post']) ?>">
        </div>
        
        <div class="form-group">
            <label>Content</label>
            <textarea id="content" name="content" style="display: none;"><?= $item['text_post'] ?></textarea>
        </div>
        
        <button type="submit">Save</button>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js"></script>
    <script>
        tinymce.init({
            selector: '#content',
            license_key: 'gpl',
            height: 400,
            menubar: false,
            entity_encoding: 'raw',
            plugins: 'lists link image code',
            toolbar: 'undo redo | bold italic | bullist numlist | link image | code'
        });
    </script>
</body>
</html>