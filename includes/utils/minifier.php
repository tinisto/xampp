<?php
/**
 * CSS and JS Minification Utilities
 * Provides minification for CSS and JavaScript files
 */

class Minifier {
    
    /**
     * Minify CSS content
     * @param string $css CSS content to minify
     * @return string Minified CSS
     */
    public static function minifyCSS($css) {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        
        // Remove whitespace and line breaks
        $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
        
        // Remove extra spaces around CSS syntax
        $css = preg_replace('/\s*{\s*/', '{', $css);
        $css = preg_replace('/;\s*}/', '}', $css);
        $css = preg_replace('/\s*;\s*/', ';', $css);
        $css = preg_replace('/\s*:\s*/', ':', $css);
        $css = preg_replace('/\s*,\s*/', ',', $css);
        $css = preg_replace('/\s*>\s*/', '>', $css);
        $css = preg_replace('/\s*\+\s*/', '+', $css);
        $css = preg_replace('/\s*~\s*/', '~', $css);
        
        // Remove unnecessary semicolons before closing braces
        $css = str_replace(';}', '}', $css);
        
        // Remove extra whitespace
        $css = trim($css);
        
        return $css;
    }
    
    /**
     * Minify JavaScript content
     * @param string $js JavaScript content to minify
     * @return string Minified JavaScript
     */
    public static function minifyJS($js) {
        // Remove single line comments (but preserve URLs)
        $js = preg_replace('/(?<!:)\/\/.*$/m', '', $js);
        
        // Remove multi-line comments
        $js = preg_replace('/\/\*.*?\*\//s', '', $js);
        
        // Remove extra whitespace while preserving string literals
        $js = preg_replace('/\s+/', ' ', $js);
        
        // Remove spaces around operators and brackets
        $js = preg_replace('/\s*([{}();,=+\-*\/&|!<>?:])\s*/', '$1', $js);
        
        // Remove trailing semicolons before closing braces
        $js = str_replace(';}', '}', $js);
        
        // Trim extra whitespace
        $js = trim($js);
        
        return $js;
    }
    
    /**
     * Minify CSS file and save result
     * @param string $inputPath Path to CSS file to minify
     * @param string $outputPath Path to save minified CSS (optional)
     * @return bool Success status
     */
    public static function minifyCSS_File($inputPath, $outputPath = null) {
        if (!file_exists($inputPath)) {
            return false;
        }
        
        $css = file_get_contents($inputPath);
        if ($css === false) {
            return false;
        }
        
        $minifiedCSS = self::minifyCSS($css);
        
        // If no output path specified, add .min before extension
        if ($outputPath === null) {
            $pathInfo = pathinfo($inputPath);
            $outputPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.min.' . $pathInfo['extension'];
        }
        
        // Create directory if it doesn't exist
        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        return file_put_contents($outputPath, $minifiedCSS) !== false;
    }
    
    /**
     * Minify JavaScript file and save result
     * @param string $inputPath Path to JS file to minify
     * @param string $outputPath Path to save minified JS (optional)
     * @return bool Success status
     */
    public static function minifyJS_File($inputPath, $outputPath = null) {
        if (!file_exists($inputPath)) {
            return false;
        }
        
        $js = file_get_contents($inputPath);
        if ($js === false) {
            return false;
        }
        
        $minifiedJS = self::minifyJS($js);
        
        // If no output path specified, add .min before extension
        if ($outputPath === null) {
            $pathInfo = pathinfo($inputPath);
            $outputPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.min.' . $pathInfo['extension'];
        }
        
        // Create directory if it doesn't exist
        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        return file_put_contents($outputPath, $minifiedJS) !== false;
    }
    
    /**
     * Combine and minify multiple CSS files
     * @param array $files Array of CSS file paths
     * @param string $outputPath Path to save combined file
     * @return bool Success status
     */
    public static function combineCSS($files, $outputPath) {
        $combinedCSS = '';
        
        foreach ($files as $file) {
            if (file_exists($file)) {
                $css = file_get_contents($file);
                if ($css !== false) {
                    $combinedCSS .= "/* File: " . basename($file) . " */\n";
                    $combinedCSS .= $css . "\n\n";
                }
            }
        }
        
        $minifiedCSS = self::minifyCSS($combinedCSS);
        
        // Create directory if it doesn't exist
        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        return file_put_contents($outputPath, $minifiedCSS) !== false;
    }
    
    /**
     * Combine and minify multiple JavaScript files
     * @param array $files Array of JS file paths
     * @param string $outputPath Path to save combined file
     * @return bool Success status
     */
    public static function combineJS($files, $outputPath) {
        $combinedJS = '';
        
        foreach ($files as $file) {
            if (file_exists($file)) {
                $js = file_get_contents($file);
                if ($js !== false) {
                    $combinedJS .= "/* File: " . basename($file) . " */\n";
                    $combinedJS .= $js . "\n;\n\n"; // Add semicolon to prevent issues
                }
            }
        }
        
        $minifiedJS = self::minifyJS($combinedJS);
        
        // Create directory if it doesn't exist
        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        return file_put_contents($outputPath, $minifiedJS) !== false;
    }
    
    /**
     * Get file size reduction information
     * @param string $originalPath Original file path
     * @param string $minifiedPath Minified file path
     * @return array Size reduction stats
     */
    public static function getSizeReduction($originalPath, $minifiedPath) {
        if (!file_exists($originalPath) || !file_exists($minifiedPath)) {
            return false;
        }
        
        $originalSize = filesize($originalPath);
        $minifiedSize = filesize($minifiedPath);
        $reduction = $originalSize - $minifiedSize;
        $percentage = $originalSize > 0 ? round(($reduction / $originalSize) * 100, 2) : 0;
        
        return [
            'original_size' => $originalSize,
            'minified_size' => $minifiedSize,
            'reduction_bytes' => $reduction,
            'reduction_percentage' => $percentage,
            'original_size_formatted' => self::formatBytes($originalSize),
            'minified_size_formatted' => self::formatBytes($minifiedSize)
        ];
    }
    
    /**
     * Minify all CSS files in a directory
     * @param string $directory Directory containing CSS files
     * @param string $outputDirectory Output directory for minified files (optional)
     * @return array Results of minification process
     */
    public static function minifyDirectoryCSS($directory, $outputDirectory = null) {
        $results = [];
        $cssFiles = glob($directory . '/*.css');
        
        foreach ($cssFiles as $cssFile) {
            // Skip already minified files
            if (strpos(basename($cssFile), '.min.') !== false) {
                continue;
            }
            
            $outputPath = null;
            if ($outputDirectory) {
                $filename = pathinfo($cssFile, PATHINFO_FILENAME);
                $outputPath = $outputDirectory . '/' . $filename . '.min.css';
            }
            
            $success = self::minifyCSS_File($cssFile, $outputPath);
            $results[] = [
                'file' => $cssFile,
                'success' => $success,
                'output' => $outputPath ?: str_replace('.css', '.min.css', $cssFile)
            ];
        }
        
        return $results;
    }
    
    /**
     * Minify all JavaScript files in a directory
     * @param string $directory Directory containing JS files
     * @param string $outputDirectory Output directory for minified files (optional)
     * @return array Results of minification process
     */
    public static function minifyDirectoryJS($directory, $outputDirectory = null) {
        $results = [];
        $jsFiles = glob($directory . '/*.js');
        
        foreach ($jsFiles as $jsFile) {
            // Skip already minified files
            if (strpos(basename($jsFile), '.min.') !== false) {
                continue;
            }
            
            $outputPath = null;
            if ($outputDirectory) {
                $filename = pathinfo($jsFile, PATHINFO_FILENAME);
                $outputPath = $outputDirectory . '/' . $filename . '.min.js';
            }
            
            $success = self::minifyJS_File($jsFile, $outputPath);
            $results[] = [
                'file' => $jsFile,
                'success' => $success,
                'output' => $outputPath ?: str_replace('.js', '.min.js', $jsFile)
            ];
        }
        
        return $results;
    }
    
    /**
     * Format bytes to human readable format
     */
    public static function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Generate HTML tags for minified assets with fallback
     * @param string $originalPath Original asset path
     * @param string $type Asset type ('css' or 'js')
     * @return string HTML tag
     */
    public static function generateAssetTag($originalPath, $type) {
        $minifiedPath = str_replace('.' . $type, '.min.' . $type, $originalPath);
        $assetPath = file_exists($_SERVER['DOCUMENT_ROOT'] . $minifiedPath) ? $minifiedPath : $originalPath;
        
        if ($type === 'css') {
            return "<link rel='stylesheet' href='{$assetPath}'>";
        } elseif ($type === 'js') {
            return "<script src='{$assetPath}'></script>";
        }
        
        return '';
    }
}