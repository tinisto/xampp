<?php
/**
 * Migration script to update components to use CSS variables
 * Run this to update all hardcoded colors to CSS variables
 */

$replacements = [
    // Background colors
    '#ffffff' => 'var(--color-surface-primary)',
    '#fff' => 'var(--color-surface-primary)',
    'white' => 'var(--color-surface-primary)',
    '#f8f9fa' => 'var(--color-surface-secondary)',
    '#e9ecef' => 'var(--color-surface-tertiary)',
    
    // Dark mode backgrounds
    '#1e293b' => 'var(--color-surface-primary)',
    '#0f0f0f' => 'var(--color-surface-primary)',
    '#1a1a1a' => 'var(--color-surface-secondary)',
    '#2d3748' => 'var(--color-surface-secondary)',
    '#334155' => 'var(--color-card-bg)',
    
    // Text colors
    '#333' => 'var(--color-text-primary)',
    '#333333' => 'var(--color-text-primary)',
    '#212529' => 'var(--color-text-primary)',
    '#1a202c' => 'var(--color-text-primary)',
    '#64748b' => 'var(--color-text-secondary)',
    '#6b7280' => 'var(--color-text-secondary)',
    '#6c757d' => 'var(--color-text-secondary)',
    
    // Dark mode text
    '#f7fafc' => 'var(--color-text-primary)',
    '#e4e6eb' => 'var(--color-text-primary)',
    '#f1f1f1' => 'var(--color-text-primary)',
    '#e2e8f0' => 'var(--color-text-primary)',
    '#d1d5db' => 'var(--color-text-secondary)',
    '#9ca3af' => 'var(--color-text-secondary)',
    
    // Borders
    '#dee2e6' => 'var(--color-border-primary)',
    '#e2e8f0' => 'var(--color-border-primary)',
    '#374151' => 'var(--color-border-primary)',
    '#4a5568' => 'var(--color-border-primary)',
    '#475569' => 'var(--color-border-primary)',
    
    // Primary colors
    '#28a745' => 'var(--color-primary)',
    '#218838' => 'var(--color-primary-hover)',
    '#22c55e' => 'var(--color-primary)',
    '#4ade80' => 'var(--color-primary)',
    
    // Links
    '#007bff' => 'var(--color-link)',
    '#0056b3' => 'var(--color-link-hover)',
    '#3ea6ff' => 'var(--color-link)',
    
    // Shadows
    'rgba(0,0,0,0.1)' => 'var(--color-shadow-sm)',
    'rgba(0,0,0,0.15)' => 'var(--color-shadow-md)',
    'rgba(0,0,0,0.25)' => 'var(--color-shadow-lg)',
    'rgba(0,0,0,0.05)' => 'var(--color-bg-hover)',
    'rgba(0,0,0,0.08)' => 'var(--color-bg-active)',
    'rgba(255,255,255,0.08)' => 'var(--color-bg-hover)',
    'rgba(255,255,255,0.1)' => 'var(--color-bg-active)',
];

// Files to update
$filesToUpdate = [
    '/common-components/header.php',
    '/common-components/footer.php',
    '/common-components/page-header.php',
    '/common-components/page-header-compact.php',
    '/common-components/content-wrapper.php',
    '/common-components/typography.php',
    '/common-components/card-badge.php',
    '/pages/category/category-content-unified.php',
    '/pages/post/post-content.php',
    '/index_content.php',
];

$updatedFiles = 0;
$totalReplacements = 0;

foreach ($filesToUpdate as $file) {
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . $file;
    
    if (!file_exists($fullPath)) {
        echo "❌ File not found: $file\n";
        continue;
    }
    
    $content = file_get_contents($fullPath);
    $originalContent = $content;
    $fileReplacements = 0;
    
    // Replace hardcoded colors with CSS variables
    foreach ($replacements as $oldColor => $newVar) {
        // Replace in CSS properties
        $patterns = [
            // color: #fff;
            '/color:\s*' . preg_quote($oldColor, '/') . '\s*;/i',
            // background-color: #fff;
            '/background-color:\s*' . preg_quote($oldColor, '/') . '\s*;/i',
            // background: #fff;
            '/background:\s*' . preg_quote($oldColor, '/') . '\s*;/i',
            // border-color: #fff;
            '/border-color:\s*' . preg_quote($oldColor, '/') . '\s*;/i',
            // border: 1px solid #fff;
            '/border:\s*([^;]*)\s+' . preg_quote($oldColor, '/') . '\s*;/i',
            // box-shadow with colors
            '/box-shadow:\s*([^;]*)\s+' . preg_quote($oldColor, '/') . '\s*;/i',
        ];
        
        foreach ($patterns as $pattern) {
            $count = 0;
            if (strpos($pattern, 'border:') !== false) {
                $content = preg_replace($pattern, 'border: $1 ' . $newVar . ';', $content, -1, $count);
            } elseif (strpos($pattern, 'box-shadow:') !== false) {
                $content = preg_replace($pattern, 'box-shadow: $1 ' . $newVar . ';', $content, -1, $count);
            } else {
                $content = preg_replace($pattern, str_replace($oldColor, $newVar, '$0'), $content, -1, $count);
            }
            $fileReplacements += $count;
        }
    }
    
    // Save updated file if changes were made
    if ($content !== $originalContent) {
        // Backup original
        file_put_contents($fullPath . '.backup', $originalContent);
        
        // Save updated content
        file_put_contents($fullPath, $content);
        
        echo "✅ Updated $file ($fileReplacements replacements)\n";
        $updatedFiles++;
        $totalReplacements += $fileReplacements;
    } else {
        echo "ℹ️  No changes needed for $file\n";
    }
}

echo "\n📊 Summary:\n";
echo "- Files updated: $updatedFiles\n";
echo "- Total replacements: $totalReplacements\n";
echo "- Backups created with .backup extension\n";

// Create a simple test page
$testPage = '<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/common-components/template-engine-ultimate.php";

$mainContent = "test-theme-content.php";
$templateConfig = [
    "layoutType" => "default",
    "darkMode" => true,
];

// Create test content file
$testContent = \'
<div class="container mt-4">
    <h1>Theme Variables Test Page</h1>
    
    <div class="card mb-3">
        <h2>Surface Colors</h2>
        <div style="padding: 20px; background: var(--color-surface-primary); border: 1px solid var(--color-border-primary);">Primary Surface</div>
        <div style="padding: 20px; background: var(--color-surface-secondary); border: 1px solid var(--color-border-primary);">Secondary Surface</div>
        <div style="padding: 20px; background: var(--color-surface-tertiary); border: 1px solid var(--color-border-primary);">Tertiary Surface</div>
    </div>
    
    <div class="card mb-3">
        <h2>Text Colors</h2>
        <p style="color: var(--color-text-primary);">Primary Text</p>
        <p style="color: var(--color-text-secondary);">Secondary Text</p>
        <p style="color: var(--color-text-tertiary);">Tertiary Text</p>
    </div>
    
    <div class="card mb-3">
        <h2>Interactive Elements</h2>
        <button class="btn">Default Button</button>
        <button class="btn btn-primary">Primary Button</button>
        <a href="#" style="margin-left: 20px;">Link Example</a>
    </div>
    
    <div class="card mb-3">
        <h2>Hover Effects</h2>
        <div class="card" style="padding: 20px; margin: 10px 0;">
            Hover over this card to see the effect
        </div>
    </div>
</div>
\';

file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/test-theme-content.php", $testContent);

renderTemplate("Theme Test", $mainContent, $templateConfig);
?>';

file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/test-theme.php', $testPage);
echo "\n✅ Created test page: /test-theme.php\n";
?>