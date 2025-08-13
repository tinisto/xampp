<?php
/**
 * Dark Mode Configuration
 * Handles dark mode functionality and theme switching
 */

// Prevent direct access
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

class DarkModeManager {
    private static $cookieName = 'theme_preference';
    private static $defaultTheme = 'light';
    
    /**
     * Get current theme preference
     */
    public static function getCurrentTheme() {
        if (isset($_COOKIE[self::$cookieName])) {
            $theme = $_COOKIE[self::$cookieName];
            return in_array($theme, ['light', 'dark', 'auto']) ? $theme : self::$defaultTheme;
        }
        
        return self::$defaultTheme;
    }
    
    /**
     * Set theme preference
     */
    public static function setTheme($theme) {
        if (in_array($theme, ['light', 'dark', 'auto'])) {
            setcookie(self::$cookieName, $theme, time() + (365 * 24 * 60 * 60), '/');
            return true;
        }
        return false;
    }
    
    /**
     * Generate theme toggle button HTML
     */
    public static function getThemeToggleButton() {
        $currentTheme = self::getCurrentTheme();
        
        return <<<HTML
        <div class="theme-toggle-container">
            <button type="button" class="btn btn-outline-secondary btn-sm theme-toggle" 
                    data-current-theme="$currentTheme" 
                    title="Переключить тему">
                <i class="fas fa-moon theme-icon-dark"></i>
                <i class="fas fa-sun theme-icon-light"></i>
                <i class="fas fa-adjust theme-icon-auto"></i>
            </button>
        </div>
HTML;
    }
    
    /**
     * Generate theme selection dropdown
     */
    public static function getThemeSelector() {
        $currentTheme = self::getCurrentTheme();
        
        $options = [
            'light' => 'Светлая',
            'dark' => 'Тёмная',
            'auto' => 'Системная'
        ];
        
        $html = '<div class="dropdown theme-selector">';
        $html .= '<button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">';
        $html .= '<i class="fas fa-palette"></i> Тема';
        $html .= '</button>';
        $html .= '<ul class="dropdown-menu">';
        
        foreach ($options as $value => $label) {
            $active = $value === $currentTheme ? ' active' : '';
            $html .= '<li><a class="dropdown-item theme-option' . $active . '" href="#" data-theme="' . $value . '">';
            $html .= '<i class="fas fa-' . ($value === 'dark' ? 'moon' : ($value === 'light' ? 'sun' : 'adjust')) . '"></i> ';
            $html .= $label;
            $html .= '</a></li>';
        }
        
        $html .= '</ul></div>';
        
        return $html;
    }
    
    /**
     * Get theme detection script
     */
    public static function getThemeScript() {
        $currentTheme = self::getCurrentTheme();
        
        return <<<SCRIPT
        <script>
        (function() {
            // Theme management
            let currentTheme = '$currentTheme';
            
            function applyTheme(theme) {
                const html = document.documentElement;
                
                if (theme === 'auto') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    html.setAttribute('data-bs-theme', prefersDark ? 'dark' : 'light');
                } else {
                    html.setAttribute('data-bs-theme', theme);
                }
                
                updateThemeIcons(theme);
                localStorage.setItem('theme', theme);
            }
            
            function updateThemeIcons(theme) {
                const toggleButton = document.querySelector('.theme-toggle');
                if (toggleButton) {
                    toggleButton.setAttribute('data-current-theme', theme);
                }
                
                // Update dropdown active state
                document.querySelectorAll('.theme-option').forEach(option => {
                    option.classList.remove('active');
                    if (option.dataset.theme === theme) {
                        option.classList.add('active');
                    }
                });
            }
            
            function setTheme(newTheme) {
                currentTheme = newTheme;
                applyTheme(newTheme);
                
                // Save to server
                fetch('/includes/ui/set_theme.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ theme: newTheme })
                });
            }
            
            // Initialize theme on page load
            applyTheme(currentTheme);
            
            // Listen for system theme changes when in auto mode
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                if (currentTheme === 'auto') {
                    applyTheme('auto');
                }
            });
            
            // Handle theme toggle button
            document.addEventListener('click', function(e) {
                if (e.target.closest('.theme-toggle')) {
                    const themes = ['light', 'dark', 'auto'];
                    const currentIndex = themes.indexOf(currentTheme);
                    const nextIndex = (currentIndex + 1) % themes.length;
                    setTheme(themes[nextIndex]);
                }
                
                // Handle dropdown theme selection
                if (e.target.closest('.theme-option')) {
                    e.preventDefault();
                    const theme = e.target.closest('.theme-option').dataset.theme;
                    setTheme(theme);
                }
            });
        })();
        </script>
SCRIPT;
    }
    
    /**
     * Get dark mode CSS
     */
    public static function getDarkModeCSS() {
        return <<<CSS
        <style>
        /* Dark mode styles */
        .theme-toggle-container {
            display: inline-block;
        }
        
        .theme-toggle {
            position: relative;
            border: 1px solid rgba(108, 117, 125, 0.5);
            background: transparent;
            color: var(--bs-body-color);
            width: 38px;
            height: 38px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .theme-toggle:hover {
            background-color: rgba(108, 117, 125, 0.1);
        }
        
        .theme-toggle .theme-icon-dark,
        .theme-toggle .theme-icon-light,
        .theme-toggle .theme-icon-auto {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        .theme-toggle[data-current-theme="light"] .theme-icon-light {
            display: inline-block;
        }
        
        .theme-toggle[data-current-theme="dark"] .theme-icon-dark {
            display: inline-block;
        }
        
        .theme-toggle[data-current-theme="auto"] .theme-icon-auto {
            display: inline-block;
        }
        
        /* Theme selector dropdown */
        .theme-selector .dropdown-item {
            transition: all 0.2s ease;
        }
        
        .theme-selector .dropdown-item.active {
            background-color: var(--bs-primary);
            color: white;
        }
        
        .theme-selector .dropdown-item:hover {
            background-color: var(--bs-primary-bg-subtle);
        }
        
        /* Dark mode fixes for existing content */
        [data-bs-theme="dark"] {
            --bs-body-bg: #1a1a1a;
            --bs-body-color: #f8f9fa;
            --bs-link-color: #66b3ff;
            --bs-link-hover-color: #4da3ff;
        }
        
        [data-bs-theme="dark"] .card {
            background-color: #2d2d2d;
            border-color: #495057;
        }
        
        [data-bs-theme="dark"] .navbar-dark {
            background-color: #000000 !important;
        }
        
        [data-bs-theme="dark"] .btn-outline-secondary {
            border-color: #6c757d;
            color: #6c757d;
        }
        
        [data-bs-theme="dark"] .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: #fff;
        }
        </style>
CSS;
    }
}

/**
 * Theme switching endpoint
 */
if (isset($_POST['theme']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['theme'])) {
        DarkModeManager::setTheme($input['theme']);
        echo json_encode(['success' => true]);
        exit;
    }
}
?>