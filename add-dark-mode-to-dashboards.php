<?php
// Script to add dark mode CSS and JS to dashboard files

$darkModeCSS = '
        /* Dark mode variables */
        [data-theme="dark"] {
            --light: #1e293b;
            --dark: #f1f5f9;
            --white: #0f172a;
            --border: #334155;
            --secondary: #94a3b8;
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.3), 0 1px 2px -1px rgb(0 0 0 / 0.3);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.3), 0 4px 6px -4px rgb(0 0 0 / 0.3);
        }

        /* Theme Toggle */
        .theme-toggle {
            background: var(--light);
            border: 1px solid var(--border);
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.25rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .theme-toggle:hover {
            background: var(--border);
        }';

$darkModeJS = '
        // Dark mode toggle
        const themeToggle = document.getElementById(\'themeToggle\');
        const lightIcon = themeToggle.querySelector(\'.theme-icon-light\');
        const darkIcon = themeToggle.querySelector(\'.theme-icon-dark\');
        
        // Check for saved theme preference or default to \'light\' mode
        const currentTheme = localStorage.getItem(\'theme\') || \'light\';
        document.documentElement.setAttribute(\'data-theme\', currentTheme);
        updateThemeIcon(currentTheme);
        
        // Toggle theme
        themeToggle.addEventListener(\'click\', () => {
            const currentTheme = document.documentElement.getAttribute(\'data-theme\');
            const newTheme = currentTheme === \'dark\' ? \'light\' : \'dark\';
            
            document.documentElement.setAttribute(\'data-theme\', newTheme);
            localStorage.setItem(\'theme\', newTheme);
            updateThemeIcon(newTheme);
        });
        
        function updateThemeIcon(theme) {
            if (theme === \'dark\') {
                lightIcon.style.display = \'none\';
                darkIcon.style.display = \'inline\';
            } else {
                lightIcon.style.display = \'inline\';
                darkIcon.style.display = \'none\';
            }
        }';

$files = [
    'dashboard-news-management.php',
    'dashboard-posts-management.php', 
    'dashboard-create-content-unified.php',
    'dashboard-users-professional.php'
];

echo "🌙 Adding dark mode to all dashboard files...\n\n";

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Add dark mode CSS after :root declaration
        if (!strpos($content, '[data-theme="dark"]')) {
            $content = preg_replace(
                '/(:root\s*{[^}]+})/s',
                "$1\n$darkModeCSS",
                $content
            );
        }
        
        // Add theme toggle button in header
        if (!strpos($content, 'themeToggle')) {
            $content = str_replace(
                '<div class="header-right">',
                '<div class="header-right">
                <button class="theme-toggle" id="themeToggle" title="Переключить тему">
                    <span class="theme-icon-light">🌞</span>
                    <span class="theme-icon-dark" style="display: none;">🌙</span>
                </button>',
                $content
            );
        }
        
        // Add dark mode JS before closing script tag
        if (!strpos($content, 'Dark mode toggle')) {
            $content = str_replace(
                '</script>',
                $darkModeJS . "\n    </script>",
                $content
            );
        }
        
        // Add transition to body
        $content = preg_replace(
            '/body\s*{\s*([^}]+)}/s',
            'body {
            $1
            transition: background-color 0.3s ease, color 0.3s ease;
        }',
            $content,
            1
        );
        
        file_put_contents($file, $content);
        echo "✅ Updated: $file\n";
    } else {
        echo "❌ File not found: $file\n";
    }
}

echo "\n✅ Dark mode added to all dashboard files!\n";
?>