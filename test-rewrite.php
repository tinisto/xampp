<?php
echo "<h1>URL Rewrite Test</h1>";
echo "<p>If you can see this page, mod_rewrite is working.</p>";
echo "<p><strong>REQUEST_URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>GET parameters:</strong></p>";
echo "<pre>" . print_r($_GET, true) . "</pre>";
?>