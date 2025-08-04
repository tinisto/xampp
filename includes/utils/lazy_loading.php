<?php
/**
 * Lazy Loading Utilities
 * Provides image and content lazy loading functionality
 */

class LazyLoading {
    
    /**
     * Generate lazy loading image tag
     * @param string $src Original image source
     * @param string $alt Alt text for image
     * @param array $options Additional options (class, width, height, placeholder)
     * @return string HTML img tag with lazy loading
     */
    public static function image($src, $alt = '', $options = []) {
        $class = isset($options['class']) ? $options['class'] . ' lazy' : 'lazy';
        $width = isset($options['width']) ? "width='{$options['width']}'" : '';
        $height = isset($options['height']) ? "height='{$options['height']}'" : '';
        $placeholder = isset($options['placeholder']) ? $options['placeholder'] : 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"%3E%3Crect width="100" height="100" fill="%23f0f0f0"/%3E%3C/svg%3E';
        
        return "<img src='{$placeholder}' data-src='{$src}' alt='{$alt}' class='{$class}' {$width} {$height} loading='lazy'>";
    }
    
    /**
     * Generate responsive lazy loading image with multiple sources
     * @param array $sources Array of image sources with breakpoints
     * @param string $fallback Fallback image source
     * @param string $alt Alt text
     * @param array $options Additional options
     * @return string HTML picture element with lazy loading
     */
    public static function responsiveImage($sources, $fallback, $alt = '', $options = []) {
        $class = isset($options['class']) ? $options['class'] . ' lazy' : 'lazy';
        $placeholder = isset($options['placeholder']) ? $options['placeholder'] : 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"%3E%3Crect width="100" height="100" fill="%23f0f0f0"/%3E%3C/svg%3E';
        
        $html = '<picture>';
        foreach ($sources as $source) {
            $media = isset($source['media']) ? "media='{$source['media']}'" : '';
            $html .= "<source data-srcset='{$source['src']}' {$media}>";
        }
        $html .= "<img src='{$placeholder}' data-src='{$fallback}' alt='{$alt}' class='{$class}' loading='lazy'>";
        $html .= '</picture>';
        
        return $html;
    }
    
    /**
     * Generate JavaScript for intersection observer lazy loading
     * @return string JavaScript code for lazy loading
     */
    public static function getScript() {
        return "
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if browser supports IntersectionObserver
            if ('IntersectionObserver' in window) {
                const lazyImages = document.querySelectorAll('.lazy');
                
                const imageObserver = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            
                            // Handle img elements
                            if (img.tagName === 'IMG') {
                                if (img.dataset.src) {
                                    img.src = img.dataset.src;
                                    img.classList.remove('lazy');
                                    img.classList.add('lazy-loaded');
                                    imageObserver.unobserve(img);
                                }
                            }
                            
                            // Handle source elements in picture tags
                            const sources = img.querySelectorAll('source[data-srcset]');
                            sources.forEach(function(source) {
                                source.srcset = source.dataset.srcset;
                            });
                        }
                    });
                }, {
                    root: null,
                    rootMargin: '50px',
                    threshold: 0.1
                });
                
                lazyImages.forEach(function(img) {
                    imageObserver.observe(img);
                });
            } else {
                // Fallback for older browsers
                const lazyImages = document.querySelectorAll('.lazy');
                lazyImages.forEach(function(img) {
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        img.classList.add('lazy-loaded');
                    }
                    
                    const sources = img.querySelectorAll('source[data-srcset]');
                    sources.forEach(function(source) {
                        source.srcset = source.dataset.srcset;
                    });
                });
            }
        });
        </script>";
    }
    
    /**
     * Generate CSS for lazy loading fade-in effect
     * @return string CSS code for lazy loading styles
     */
    public static function getStyles() {
        return "
        <style>
        .lazy {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        
        .lazy-loaded {
            opacity: 1;
        }
        
        .lazy-placeholder {
            background-color: #f0f0f0;
            background-image: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        
        @keyframes loading {
            0% {
                background-position: 200% 0;
            }
            100% {
                background-position: -200% 0;
            }
        }
        
        @media (prefers-reduced-motion: reduce) {
            .lazy {
                transition: none;
            }
            .lazy-placeholder {
                animation: none;
            }
        }
        </style>";
    }
    
    /**
     * Automatically convert img tags in HTML to lazy loading
     * @param string $html HTML content
     * @param array $options Options for conversion
     * @return string Modified HTML with lazy loading
     */
    public static function convertHtml($html, $options = []) {
        $skipClasses = isset($options['skip_classes']) ? $options['skip_classes'] : ['no-lazy'];
        $placeholder = isset($options['placeholder']) ? $options['placeholder'] : 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"%3E%3Crect width="100" height="100" fill="%23f0f0f0"/%3E%3C/svg%3E';
        
        return preg_replace_callback('/<img([^>]+)>/i', function($matches) use ($skipClasses, $placeholder) {
            $imgTag = $matches[0];
            $attributes = $matches[1];
            
            // Check if image should be skipped
            foreach ($skipClasses as $skipClass) {
                if (strpos($attributes, $skipClass) !== false) {
                    return $imgTag;
                }
            }
            
            // Extract src attribute
            if (preg_match('/src=["\']([^"\']+)["\']/i', $attributes, $srcMatch)) {
                $originalSrc = $srcMatch[1];
                
                // Replace src with placeholder and add data-src
                $newAttributes = preg_replace('/src=["\']([^"\']+)["\']/i', 'src="' . $placeholder . '" data-src="$1"', $attributes);
                
                // Add lazy class
                if (preg_match('/class=["\']([^"\']+)["\']/i', $newAttributes)) {
                    $newAttributes = preg_replace('/class=["\']([^"\']+)["\']/i', 'class="$1 lazy"', $newAttributes);
                } else {
                    $newAttributes .= ' class="lazy"';
                }
                
                // Add loading attribute
                if (strpos($newAttributes, 'loading=') === false) {
                    $newAttributes .= ' loading="lazy"';
                }
                
                return '<img' . $newAttributes . '>';
            }
            
            return $imgTag;
        }, $html);
    }
    
    /**
     * Generate preload links for critical images
     * @param array $images Array of critical image URLs
     * @return string HTML link preload tags
     */
    public static function preloadCritical($images) {
        $html = '';
        foreach ($images as $image) {
            $html .= "<link rel='preload' as='image' href='{$image}'>\n";
        }
        return $html;
    }
    
    /**
     * Enable automatic lazy loading for all images in HTML content
     * @param string $html HTML content to process
     * @return string Modified HTML with lazy loading enabled
     */
    public static function enableAutoLazyLoading($html) {
        // Add the script and styles to the HTML
        $styles = self::getStyles();
        $script = self::getScript();
        
        // Convert all img tags to lazy loading
        $html = self::convertHtml($html);
        
        // Add styles and script to the end of the HTML
        $html .= $styles . $script;
        
        return $html;
    }
}