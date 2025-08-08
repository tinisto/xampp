<?php
// Site Bug Report and Testing Page
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Bug Report - 11-классники</title>
    <link rel="icon" href="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIiBmaWxsPSIjMDA3YmZmIi8+Cjx0ZXh0IHg9IjE2IiB5PSIyMCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE0IiBmb250LXdlaWdodD0iYm9sZCIgZmlsbD0id2hpdGUiIHRleHQtYW5jaG9yPSJtaWRkbGUiPjExPC90ZXh0Pgo8L3N2Zz4K" type="image/svg+xml">
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .bug-section { background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .critical { border-left: 4px solid #dc3545; background: #f8d7da; }
        .warning { border-left: 4px solid #ffc107; background: #fff3cd; }
        .info { border-left: 4px solid #0dcaf0; background: #cff4fc; }
        .fixed { border-left: 4px solid #198754; background: #d1e7dd; }
        h2 { color: #333; }
        h3 { margin-top: 15px; }
        code { background: #e9ecef; padding: 2px 4px; border-radius: 3px; }
        pre { background: #212529; color: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        table { border-collapse: collapse; width: 100%; margin: 15px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #007bff; color: white; }
        .test-button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        .test-button:hover { background: #0056b3; }
    </style>
</head>
<body>
<h1>Site Bug Report & Testing</h1>
<p>Generated: <?php echo date('Y-m-d H:i:s'); ?></p>

<div class="bug-section critical">
    <h2>🚨 Critical Issue: Dark Mode Toggle Not Working</h2>
    <h3>Problem:</h3>
    <p>The <code>toggleTheme()</code> function is missing from <code>real_template.php</code>, causing the dark mode toggle button to fail.</p>
    
    <h3>Affected Pages:</h3>
    <ul>
        <li>Homepage (index.php)</li>
        <li>All pages using real_template.php</li>
        <li>Post pages via router</li>
    </ul>
    
    <h3>Test:</h3>
    <button class="test-button" onclick="testThemeToggle()">Test Theme Toggle</button>
    <div id="theme-test-result"></div>
    
    <h3>Fix Required:</h3>
    <p>Add the following JavaScript to real_template.php before the closing &lt;/body&gt; tag:</p>
    <pre><code>function toggleTheme() {
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-bs-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    html.setAttribute('data-bs-theme', newTheme);
    html.setAttribute('data-theme', newTheme);
    
    // Save preference
    localStorage.setItem('theme', newTheme);
    
    // Update icon
    const icon = document.querySelector('.theme-toggle i');
    if (icon) {
        icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    }
}

// Load saved theme on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-bs-theme', savedTheme);
    document.documentElement.setAttribute('data-theme', savedTheme);
    
    const icon = document.querySelector('.theme-toggle i');
    if (icon) {
        icon.className = savedTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    }
});</code></pre>
</div>

<div class="bug-section warning">
    <h2>⚠️ Warning: Inconsistent Theme Storage Keys</h2>
    <h3>Problem:</h3>
    <p>Different files use different localStorage keys for theme preference:</p>
    <ul>
        <li><code>news-direct-render.php</code> uses: <code>theme</code></li>
        <li><code>real_header_avatar_fixed.php</code> uses: <code>theme</code></li>
    </ul>
    
    <h3>Impact:</h3>
    <p>Theme preference may not persist correctly across different pages.</p>
    
    <h3>Test:</h3>
    <button class="test-button" onclick="checkLocalStorage()">Check LocalStorage</button>
    <div id="storage-test-result"></div>
</div>

<div class="bug-section info">
    <h2>ℹ️ Dark Mode CSS Coverage</h2>
    <h3>Status: ✅ Comprehensive</h3>
    <p>The CSS implementation in <code>unified-styles.css</code> covers:</p>
    <table>
        <tr><th>Component</th><th>Dark Mode Support</th></tr>
        <tr><td>Body & Background</td><td>✅ Yes</td></tr>
        <tr><td>Navigation</td><td>✅ Yes</td></tr>
        <tr><td>Cards</td><td>✅ Yes</td></tr>
        <tr><td>Forms</td><td>✅ Yes</td></tr>
        <tr><td>Tables</td><td>✅ Yes</td></tr>
        <tr><td>Buttons</td><td>✅ Yes</td></tr>
        <tr><td>Modals</td><td>✅ Yes</td></tr>
        <tr><td>Alerts</td><td>✅ Yes</td></tr>
        <tr><td>Badges</td><td>✅ Yes</td></tr>
        <tr><td>Code blocks</td><td>✅ Yes</td></tr>
    </table>
</div>

<div class="bug-section warning">
    <h2>⚠️ Potential Visual Bugs</h2>
    <h3>1. Green Background Flash</h3>
    <p>Status: <strong>Fixed</strong> with loading class, but should verify</p>
    
    <h3>2. Mobile Responsive Issues</h3>
    <p>The theme toggle button may not be visible on mobile devices.</p>
    
    <h3>3. Missing Font Awesome Icons</h3>
    <p>Some pages may not load Font Awesome, causing moon/sun icons to not display.</p>
</div>

<div class="bug-section info">
    <h2>📊 Database Related Issues</h2>
    <h3>Recent Fixes:</h3>
    <ul>
        <li>✅ Category ID 2 missing - Fixed</li>
        <li>✅ News categories using numeric strings - Migrated</li>
        <li>✅ Empty url_slug constraints - Resolved</li>
    </ul>
    
    <h3>Remaining Tasks:</h3>
    <ul>
        <li>Field name standardization between posts/news tables</li>
        <li>Performance indexing</li>
        <li>Foreign key constraints</li>
    </ul>
</div>

<div class="bug-section">
    <h2>🔧 Quick Test Links</h2>
    <p>Test dark mode on these pages:</p>
    <ul>
        <li><a href="/" target="_blank">Homepage</a></li>
        <li><a href="/post/uvlekayus-angliyskim-yazyikom" target="_blank">Sample Post</a></li>
        <li><a href="/news" target="_blank">News Page</a></li>
        <li><a href="/dashboard.php" target="_blank">Dashboard</a></li>
    </ul>
</div>

<script>
function testThemeToggle() {
    const result = document.getElementById('theme-test-result');
    if (typeof toggleTheme === 'function') {
        result.innerHTML = '<p style="color: green;">✅ toggleTheme() function exists</p>';
        try {
            toggleTheme();
            result.innerHTML += '<p style="color: green;">✅ Theme toggled successfully</p>';
        } catch (e) {
            result.innerHTML += '<p style="color: red;">❌ Error: ' + e.message + '</p>';
        }
    } else {
        result.innerHTML = '<p style="color: red;">❌ toggleTheme() function not found - This confirms the bug!</p>';
    }
}

function checkLocalStorage() {
    const result = document.getElementById('storage-test-result');
    const theme = localStorage.getItem('theme');
    const preferredTheme = localStorage.getItem('theme');
    
    let html = '<h4>LocalStorage Values:</h4>';
    html += '<ul>';
    html += '<li>theme: ' + (theme || 'not set') + '</li>';
    html += '<li>theme: ' + (preferredTheme || 'not set') + '</li>';
    html += '</ul>';
    
    if (theme && preferredTheme && theme !== preferredTheme) {
        html += '<p style="color: orange;">⚠️ Inconsistent theme values detected!</p>';
    }
    
    result.innerHTML = html;
}

// Auto-run tests on load
window.addEventListener('DOMContentLoaded', function() {
    // Check current theme
    const currentTheme = document.documentElement.getAttribute('data-bs-theme');
    console.log('Current theme:', currentTheme);
});
</script>

</body>
</html>