<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/check_admin.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Test with post 451
$post_id = 451;

// Get the post content
$query = "SELECT text_post FROM posts WHERE id_post = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

$raw_content = $post['text_post'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix TinyMCE Display</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: monospace; padding: 20px; max-width: 1200px; margin: 0 auto; }
        .section { margin: 20px 0; padding: 20px; border: 1px solid #ccc; background: #f5f5f5; }
        pre { background: white; padding: 10px; overflow-x: auto; border: 1px solid #ddd; }
        .test-editor { margin: 20px 0; }
        h2 { color: #333; }
    </style>
</head>
<body>
    <h1>TinyMCE Content Display Test</h1>
    
    <div class="section">
        <h2>1. Raw Content from Database (first 500 chars):</h2>
        <pre><?= htmlspecialchars(substr($raw_content, 0, 500)) ?></pre>
    </div>
    
    <div class="section">
        <h2>2. Check Character Encoding:</h2>
        <?php
        $encoding = mb_detect_encoding($raw_content, ['UTF-8', 'ISO-8859-1', 'Windows-1251'], true);
        echo "<p>Detected encoding: <strong>$encoding</strong></p>";
        
        // Check for specific characters
        $has_html_entities = (strpos($raw_content, '&lt;') !== false || strpos($raw_content, '&gt;') !== false);
        $has_raw_html = (strpos($raw_content, '<p>') !== false || strpos($raw_content, '<strong>') !== false);
        
        echo "<p>Contains HTML entities (&lt;, &gt;): " . ($has_html_entities ? "YES" : "NO") . "</p>";
        echo "<p>Contains raw HTML tags (<p>, <strong>): " . ($has_raw_html ? "YES" : "NO") . "</p>";
        ?>
    </div>
    
    <div class="section">
        <h2>3. TinyMCE Editor Test (Direct Assignment):</h2>
        <textarea id="test1" style="width: 100%; height: 300px;"><?= $raw_content ?></textarea>
    </div>
    
    <div class="section">
        <h2>4. TinyMCE Editor Test (with htmlspecialchars):</h2>
        <textarea id="test2" style="width: 100%; height: 300px;"><?= htmlspecialchars($raw_content) ?></textarea>
    </div>
    
    <div class="section">
        <h2>5. TinyMCE Editor Test (with html_entity_decode):</h2>
        <textarea id="test3" style="width: 100%; height: 300px;"><?= html_entity_decode($raw_content, ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></textarea>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        // Test 1: Direct content
        tinymce.init({
            selector: '#test1',
            license_key: 'gpl',
            height: 300,
            menubar: false,
            entity_encoding: 'raw',
            plugins: 'code',
            toolbar: 'undo redo | bold italic | code',
            setup: function(editor) {
                editor.on('init', function() {
                    console.log('Test 1 - Direct content loaded');
                });
            }
        });
        
        // Test 2: With htmlspecialchars
        tinymce.init({
            selector: '#test2',
            license_key: 'gpl',
            height: 300,
            menubar: false,
            entity_encoding: 'raw',
            plugins: 'code',
            toolbar: 'undo redo | bold italic | code',
            setup: function(editor) {
                editor.on('init', function() {
                    console.log('Test 2 - htmlspecialchars content loaded');
                });
            }
        });
        
        // Test 3: With html_entity_decode
        tinymce.init({
            selector: '#test3',
            license_key: 'gpl',
            height: 300,
            menubar: false,
            entity_encoding: 'raw',
            plugins: 'code',
            toolbar: 'undo redo | bold italic | code',
            setup: function(editor) {
                editor.on('init', function() {
                    console.log('Test 3 - html_entity_decode content loaded');
                });
            }
        });
    </script>
</body>
</html>