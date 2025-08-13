<?php
/**
 * Unified Template Configuration
 * Centralizes template settings and provides helper functions
 */

// Prevent direct access
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

/**
 * Page configuration class
 */
class PageConfig {
    public $title = '11-классники';
    public $description = '';
    public $keywords = '';
    public $contentFile = '';
    public $contentData = [];
    public $showHeader = true;
    public $showFooter = true;
    public $showSidebar = false;
    public $breadcrumbs = [];
    public $scripts = [];
    public $styles = [];
    
    public function __construct($options = []) {
        foreach ($options as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}

/**
 * Legacy template function wrapper for backward compatibility
 */
function renderTemplate($pageTitle, $mainContent, $additionalData = [], $metaD = "", $metaK = "") {
    // Include original template engine
    require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/template.php';
    
    // Call unified template function with default layout
    renderUnifiedTemplate($pageTitle, $mainContent, $additionalData, $metaD, $metaK, '', '', '', 'default');
}

/**
 * Simplified page rendering function
 */
function renderPage($config) {
    // Ensure config is PageConfig instance
    if (!($config instanceof PageConfig)) {
        $config = new PageConfig($config);
    }
    
    // Include required files
    require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/template.php';
    
    // Prepare additional data
    $additionalData = $config->contentData;
    
    // Extract content data for use in content file
    if (!empty($additionalData)) {
        extract($additionalData);
    }
    
    // Render using existing template engine
    renderTemplate(
        $config->title,
        $config->contentFile,
        $additionalData,
        $config->description,
        $config->keywords
    );
}

/**
 * Quick page setup function
 */
function setupPage($title, $contentFile, $options = []) {
    $defaults = [
        'title' => $title,
        'contentFile' => $contentFile
    ];
    
    $config = new PageConfig(array_merge($defaults, $options));
    renderPage($config);
}

/**
 * Standard page layouts
 */
class PageLayouts {
    // Standard content page
    public static function contentPage($title, $contentFile, $meta = []) {
        setupPage($title, $contentFile, [
            'description' => $meta['description'] ?? '',
            'keywords' => $meta['keywords'] ?? ''
        ]);
    }
    
    // Full-width page (no sidebar)
    public static function fullWidthPage($title, $contentFile, $meta = []) {
        setupPage($title, $contentFile, array_merge($meta, [
            'showSidebar' => false
        ]));
    }
    
    // Minimal page (no header/footer)
    public static function minimalPage($title, $contentFile, $meta = []) {
        setupPage($title, $contentFile, array_merge($meta, [
            'showHeader' => false,
            'showFooter' => false
        ]));
    }
    
    // Admin page
    public static function adminPage($title, $contentFile, $meta = []) {
        // Check admin authentication
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /unauthorized');
            exit;
        }
        
        setupPage('Админ: ' . $title, $contentFile, array_merge($meta, [
            'keywords' => 'admin, dashboard, управление'
        ]));
    }
}

/**
 * Content sections for modular layouts
 */
class ContentSections {
    // Hero section
    public static function hero($title, $subtitle = '', $backgroundImage = '') {
        $bgStyle = $backgroundImage ? "style='background-image: url($backgroundImage);'" : '';
        return <<<HTML
        <section class="hero-section py-5 text-center" $bgStyle>
            <div class="container">
                <h1 class="display-4 fw-bold">$title</h1>
                <p class="lead">$subtitle</p>
            </div>
        </section>
HTML;
    }
    
    // Breadcrumbs
    public static function breadcrumbs($items) {
        if (empty($items)) return '';
        
        $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
        $html .= '<li class="breadcrumb-item"><a href="/">Главная</a></li>';
        
        foreach ($items as $i => $item) {
            if ($i === count($items) - 1) {
                $html .= '<li class="breadcrumb-item active" aria-current="page">' . 
                         htmlspecialchars($item['name']) . '</li>';
            } else {
                $html .= '<li class="breadcrumb-item"><a href="' . 
                         htmlspecialchars($item['url']) . '">' . 
                         htmlspecialchars($item['name']) . '</a></li>';
            }
        }
        
        $html .= '</ol></nav>';
        return $html;
    }
    
    // Alert message
    public static function alert($message, $type = 'info', $dismissible = true) {
        $dismissClass = $dismissible ? ' alert-dismissible fade show' : '';
        $dismissButton = $dismissible ? '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' : '';
        
        return <<<HTML
        <div class="alert alert-$type$dismissClass" role="alert">
            $message
            $dismissButton
        </div>
HTML;
    }
    
    // Card grid
    public static function cardGrid($items, $columns = 3) {
        $colClass = 'col-md-' . (12 / $columns);
        $html = '<div class="row g-4">';
        
        foreach ($items as $item) {
            $html .= <<<HTML
            <div class="$colClass">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{$item['title']}</h5>
                        <p class="card-text">{$item['content']}</p>
                    </div>
                </div>
            </div>
HTML;
        }
        
        $html .= '</div>';
        return $html;
    }
}
?>