<?php
/**
 * Unified Template - Single Template Using Only Reusable Components
 * 
 * This replaces all previous templates with a single, component-based layout
 */

// Include security headers first
require_once __DIR__ . '/includes/security-headers.php';
require_once __DIR__ . '/session-init.php';

// Include the unified page layout component
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/page-layout.php';

// Set page title
$page_title = $page_title ?? $pageTitle ?? '11klassniki.ru - Российское образование';

// Include header (the only non-component part we keep)
require_once __DIR__ . '/includes/header.php';

// Prepare page sections from legacy variables
$sections = [];

// Convert legacy content variables to new section format
if (!empty($headerContent)) {
    $sections['title'] = $headerContent;
} else if (!empty($greyContent1)) {
    $sections['title'] = $greyContent1;
}

if (!empty($navigationContent)) {
    $sections['navigation'] = $navigationContent;
} else if (!empty($greyContent2)) {
    $sections['navigation'] = $greyContent2;
}

if (!empty($metadataContent)) {
    $sections['metadata'] = $metadataContent;
} else if (!empty($greyContent3)) {
    $sections['metadata'] = $greyContent3;
}

if (!empty($filtersContent)) {
    $sections['filters'] = $filtersContent;
} else if (!empty($greyContent4)) {
    $sections['filters'] = $greyContent4;
}

if (!empty($mainContent)) {
    $sections['content'] = $mainContent;
} else if (!empty($greyContent5)) {
    $sections['content'] = $greyContent5;
}

if (!empty($paginationContent)) {
    $sections['pagination'] = $paginationContent;
} else if (!empty($greyContent6)) {
    $sections['pagination'] = $greyContent6;
}

// Handle comments - convert to compact format
$commentsOptions = [];
if (!empty($commentsContent) || !empty($blueContent)) {
    // Extract entity info from URL or set defaults
    $currentUrl = $_SERVER['REQUEST_URI'];
    
    // Try to determine entity type and ID from URL
    if (preg_match('/\/post\/(.+)/', $currentUrl, $matches)) {
        $sections['comments'] = [
            'type' => 'post',
            'id' => $matches[1],
            'options' => $commentsOptions
        ];
    } elseif (preg_match('/\/news\/(.+)/', $currentUrl, $matches)) {
        $sections['comments'] = [
            'type' => 'news',
            'id' => $matches[1],
            'options' => $commentsOptions
        ];
    } elseif (preg_match('/\/school\/(\d+)/', $currentUrl, $matches)) {
        $sections['comments'] = [
            'type' => 'school',
            'id' => $matches[1],
            'options' => $commentsOptions
        ];
    } else {
        // Fallback: show compact comments but without entity
        $sections['comments'] = [
            'type' => 'page',
            'id' => 'default',
            'options' => array_merge($commentsOptions, ['showStats' => false])
        ];
    }
}

// Layout options
$layoutOptions = [
    'maxWidth' => '1200px',
    'padding' => '20px',
    'spacing' => '20px', // Reduced spacing for more compact layout
    'showComments' => !empty($sections['comments']),
    'containerClass' => 'unified-page-layout'
];

// Render the page using the unified layout
?>
<main class="main-content">
    <?php renderPageLayout($sections, $layoutOptions); ?>
</main>

<?php
// Include footer
require_once __DIR__ . '/includes/footer.php';
?>