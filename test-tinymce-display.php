<?php
// Start session but don't require admin check to avoid redirect
session_start();
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
    <title>Test TinyMCE Display</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 1200px; margin: 0 auto; }
        .section { margin: 20px 0; padding: 20px; border: 1px solid #ccc; background: #f5f5f5; }
        pre { background: white; padding: 10px; overflow-x: auto; border: 1px solid #ddd; }
        .info { background: #e3f2fd; padding: 10px; margin: 10px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>TinyMCE Content Test for Post #<?= $post_id ?></h1>
    
    <div class="section">
        <h2>Database Content Analysis:</h2>
        <div class="info">
            <p><strong>First 200 characters:</strong></p>
            <pre><?= htmlspecialchars(substr($raw_content, 0, 200)) ?></pre>
        </div>
        
        <?php
        $has_html_entities = (strpos($raw_content, '&lt;') !== false || strpos($raw_content, '&gt;') !== false);
        $has_raw_html = (strpos($raw_content, '<p>') !== false || strpos($raw_content, '<strong>') !== false);
        ?>
        
        <div class="info">
            <p>✓ Contains HTML entities (&amp;lt;, &amp;gt;): <strong><?= $has_html_entities ? "YES" : "NO" ?></strong></p>
            <p>✓ Contains raw HTML tags (&lt;p&gt;, &lt;strong&gt;): <strong><?= $has_raw_html ? "YES" : "NO" ?></strong></p>
        </div>
    </div>
    
    <div class="section">
        <h2>Solution:</h2>
        <?php if ($has_raw_html && !$has_html_entities): ?>
            <p style="color: green;"><strong>✓ Content is stored correctly as HTML.</strong></p>
            <p>The issue is that TinyMCE is treating the HTML as plain text. The textarea should output raw HTML without encoding.</p>
        <?php elseif ($has_html_entities): ?>
            <p style="color: orange;"><strong>⚠ Content is HTML-encoded in the database.</strong></p>
            <p>Need to use html_entity_decode() before displaying in TinyMCE.</p>
        <?php else: ?>
            <p style="color: red;"><strong>✗ No HTML found in content.</strong></p>
        <?php endif; ?>
    </div>
    
    <div class="section">
        <h2>Test Editor:</h2>
        <textarea id="editor" style="width: 100%; height: 400px;"><?= $raw_content ?></textarea>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#editor',
            license_key: 'gpl',
            height: 400,
            menubar: false,
            entity_encoding: 'raw',
            plugins: 'code lists link image',
            toolbar: 'undo redo | bold italic | bullist numlist | link image | code',
            setup: function(editor) {
                editor.on('init', function() {
                    console.log('Editor initialized');
                    // Log the content to console
                    console.log('Content in editor:', editor.getContent());
                });
            }
        });
    </script>
</body>
</html>