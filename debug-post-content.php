<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/check_admin.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$post_id = 451;

// Get the post content
$query = "SELECT text_post, bio_post FROM posts WHERE id_post = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Post Content</title>
    <style>
        body { font-family: monospace; padding: 20px; }
        .section { margin: 20px 0; padding: 20px; border: 1px solid #ccc; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
        .rendered { background: #e8f5e9; padding: 10px; }
    </style>
</head>
<body>
    <h1>Debug Post #<?= $post_id ?> Content</h1>
    
    <div class="section">
        <h2>Raw Database Content (text_post):</h2>
        <pre><?= htmlspecialchars($post['text_post']) ?></pre>
    </div>
    
    <div class="section">
        <h2>First 500 characters:</h2>
        <pre><?= htmlspecialchars(substr($post['text_post'], 0, 500)) ?></pre>
    </div>
    
    <div class="section">
        <h2>Check for double encoding:</h2>
        <?php
        $decoded_once = html_entity_decode($post['text_post'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $decoded_twice = html_entity_decode($decoded_once, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        ?>
        <h3>Decoded once:</h3>
        <pre><?= htmlspecialchars(substr($decoded_once, 0, 500)) ?></pre>
        
        <h3>Decoded twice:</h3>
        <pre><?= htmlspecialchars(substr($decoded_twice, 0, 500)) ?></pre>
    </div>
    
    <div class="section">
        <h2>How it renders (with strip_tags):</h2>
        <div class="rendered">
            <?php 
            $allowed_tags = '<p><br><strong><b><em><i><u><a><ul><ol><li><blockquote><h1><h2><h3><h4><h5><h6><span><div>';
            echo strip_tags($post['text_post'], $allowed_tags);
            ?>
        </div>
    </div>
    
    <div class="section">
        <h2>TinyMCE Test:</h2>
        <textarea id="test-content" style="width: 100%; height: 200px;"><?= $post['text_post'] ?></textarea>
        <div id="editor-test" style="margin-top: 20px;"></div>
    </div>
    
    <script src="https://cdn.tiny.cloud/1/YOUR_API_KEY/tinymce/6/tinymce.min.js"></script>
    <script>
        tinymce.init({
            selector: '#test-content',
            height: 300,
            entity_encoding: 'raw',
            setup: function(editor) {
                editor.on('init', function() {
                    console.log('Editor initialized');
                    console.log('Content:', editor.getContent());
                });
            }
        });
    </script>
</body>
</html>