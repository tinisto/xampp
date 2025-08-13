<?php
/**
 * Script to update inline styles to CSS classes for dark mode support
 * Run this from command line: php update-dark-mode-styles.php
 */

// Include the dark mode helpers
require_once __DIR__ . '/includes/dark-mode-helpers.php';

// List of files to update
$files_to_update = [
    'contact.php',
    'events.php',
    'search.php',
    'reading-lists.php',
    'tests.php',
    'categories-all.php',
    'cards-grid.php',
    'educational-institutions-all-regions-real.php',
    'index.php'
];

echo "Starting dark mode style updates...\n\n";

foreach ($files_to_update as $file) {
    $filepath = __DIR__ . '/' . $file;
    
    if (!file_exists($filepath)) {
        echo "⚠️  File not found: $file\n";
        continue;
    }
    
    // Read the file
    $content = file_get_contents($filepath);
    
    // Create backup
    $backup_path = $filepath . '.backup.' . date('Y-m-d-H-i-s');
    file_put_contents($backup_path, $content);
    echo "✅ Created backup: " . basename($backup_path) . "\n";
    
    // Apply transformations
    $original_content = $content;
    
    // Add CSS link if not present
    if (strpos($content, 'dark-mode-fix.css') === false) {
        // Look for existing CSS includes or ob_start
        if (preg_match('/(ob_start\(\);[\s]*\?>)/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
            $insert_position = $matches[0][1] + strlen($matches[0][0]);
            $content = substr($content, 0, $insert_position) . 
                      "\n<link rel=\"stylesheet\" href=\"/css/dark-mode-fix.css\">" . 
                      substr($content, $insert_position);
        }
    }
    
    // Replace inline styles with CSS classes
    $replacements = [
        // Text colors
        'style="color: #000"' => 'class="text-primary-adaptive"',
        'style="color: #333"' => 'class="text-primary-adaptive"',
        'style="color: #666"' => 'class="text-secondary-adaptive"',
        'style="color: #999"' => 'class="text-muted-adaptive"',
        'style="color: #555"' => 'class="text-secondary-adaptive"',
        'style="color: #717171"' => 'class="text-secondary-adaptive"',
        'style="color: #222222"' => 'class="text-heading-adaptive"',
        'style="color: #000000"' => 'class="text-heading-adaptive"',
        
        // Complex style replacements - maintain other styles
        'color: #000;' => '',
        'color: #333;' => '',
        'color: #666;' => '',
        'color: #999;' => '',
        'color: #555;' => '',
        'color: #717171;' => '',
        'color: #222222;' => '',
        
        // Background colors
        'background: #f8f9fa' => 'class="bg-secondary-adaptive"',
        'background-color: #f8f9fa' => 'class="bg-secondary-adaptive"',
        'background: white' => 'class="bg-primary-adaptive"',
        'background-color: white' => 'class="bg-primary-adaptive"',
        'background: #ffffff' => 'class="bg-primary-adaptive"',
        'background-color: #ffffff' => 'class="bg-primary-adaptive"',
        'background: #e9ecef' => 'class="bg-tertiary-adaptive"',
        'background-color: #e9ecef' => 'class="bg-tertiary-adaptive"',
    ];
    
    // Apply simple replacements
    foreach ($replacements as $search => $replace) {
        $content = str_replace($search, $replace, $content);
    }
    
    // Handle combined styles with color
    $content = preg_replace_callback(
        '/style="([^"]*)(color:\s*#[0-9a-fA-F]{3,6}|color:\s*#\w+)([^"]*)"/',
        function($matches) {
            $beforeColor = trim($matches[1]);
            $colorStyle = $matches[2];
            $afterColor = trim($matches[3]);
            
            // Map colors to classes
            $colorMap = [
                '#000' => 'text-primary-adaptive',
                '#333' => 'text-primary-adaptive',
                '#666' => 'text-secondary-adaptive',
                '#999' => 'text-muted-adaptive',
                '#555' => 'text-secondary-adaptive',
                '#717171' => 'text-secondary-adaptive',
                '#222222' => 'text-heading-adaptive',
                '#000000' => 'text-heading-adaptive',
            ];
            
            $class = '';
            foreach ($colorMap as $color => $className) {
                if (stripos($colorStyle, $color) !== false) {
                    $class = $className;
                    break;
                }
            }
            
            // Remove color from style
            $remainingStyle = trim($beforeColor . ' ' . $afterColor, '; ');
            
            if ($class && $remainingStyle) {
                return 'class="' . $class . '" style="' . $remainingStyle . '"';
            } elseif ($class) {
                return 'class="' . $class . '"';
            } else {
                return 'style="' . $remainingStyle . '"';
            }
        },
        $content
    );
    
    // Update specific patterns
    $patterns = [
        // Share buttons
        '/<a([^>]*?)style="([^"]*?)background:\s*#4267B2([^"]*?)"([^>]*?)>/' => 
            '<a$1class="share-button share-button-vk" style="$2$3"$4>',
        
        '/<a([^>]*?)style="([^"]*?)background:\s*#0088cc([^"]*?)"([^>]*?)>/' => 
            '<a$1class="share-button share-button-telegram" style="$2$3"$4>',
        
        // Primary buttons
        '/<a([^>]*?)style="([^"]*?)background:\s*#28a745([^"]*?)"([^>]*?)>/' => 
            '<a$1class="nav-button-primary" style="$2background: #28a745$3"$4>',
        
        // Form inputs
        '/<input([^>]*?)style="([^"]*?)border:\s*2px\s+solid\s+#ddd([^"]*?)"([^>]*?)>/' => 
            '<input$1class="form-input-adaptive" style="$2$3"$4>',
        
        '/<textarea([^>]*?)style="([^"]*?)border:\s*2px\s+solid\s+#ddd([^"]*?)"([^>]*?)>/' => 
            '<textarea$1class="form-input-adaptive" style="$2$3"$4>',
        
        '/<select([^>]*?)style="([^"]*?)border:\s*2px\s+solid\s+#ddd([^"]*?)"([^>]*?)>/' => 
            '<select$1class="form-input-adaptive" style="$2$3"$4>',
    ];
    
    foreach ($patterns as $pattern => $replacement) {
        $content = preg_replace($pattern, $replacement, $content);
    }
    
    // Clean up empty style attributes
    $content = str_replace('style=""', '', $content);
    $content = preg_replace('/style="\s+"/', '', $content);
    
    // Check if content changed
    if ($content !== $original_content) {
        // Write updated content
        file_put_contents($filepath, $content);
        echo "✅ Updated: $file\n";
    } else {
        echo "ℹ️  No changes needed: $file\n";
        // Remove backup if no changes
        unlink($backup_path);
    }
}

echo "\n✅ Dark mode style update completed!\n";
echo "\nTo revert changes, use the backup files created with .backup.* extension\n";
?>