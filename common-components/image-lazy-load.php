<?php
/**
 * Image Lazy Loading Component with YouTube-style Placeholders
 * Updated to use enhanced LazyLoading utility class
 * 
 * Provides lazy loading for images with smooth placeholder animations
 * 
 * Usage:
 * include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/image-lazy-load.php';
 * 
 * renderLazyImage([
 *     'src' => '/images/news-images/123_1.jpg',
 *     'alt' => 'Image description',
 *     'class' => 'news-image',
 *     'aspectRatio' => '16:9'
 * ]);
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/utils/lazy_loading.php';

function renderLazyImage($options = []) {
    $src = $options['src'] ?? '';
    $alt = $options['alt'] ?? '';
    $class = $options['class'] ?? '';
    $aspectRatio = $options['aspectRatio'] ?? '16:9';
    $placeholder = $options['placeholder'] ?? 'default';
    $width = $options['width'] ?? '';
    $height = $options['height'] ?? '';
    
    // Calculate aspect ratio padding
    $ratioMap = [
        '16:9' => '56.25%',
        '4:3' => '75%',
        '1:1' => '100%',
        '3:2' => '66.67%',
        '2:1' => '50%'
    ];
    
    $paddingBottom = $ratioMap[$aspectRatio] ?? '56.25%';
    
    // Generate unique ID for this image
    $imageId = 'lazy-img-' . uniqid();
    
    ?>
    <div class="lazy-image-wrapper <?= htmlspecialchars($class) ?>-wrapper" 
         style="padding-bottom: <?= $paddingBottom ?>;"
         data-image-id="<?= $imageId ?>">
        
        <!-- Placeholder with animated gradient -->
        <div class="lazy-image-placeholder">
            <div class="placeholder-gradient"></div>
            <div class="placeholder-icon">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                    <polyline points="21 15 16 10 5 21"></polyline>
                </svg>
            </div>
        </div>
        
        <!-- Actual image (hidden initially) -->
        <img 
            class="lazy-image <?= htmlspecialchars($class) ?>" 
            data-src="<?= htmlspecialchars($src) ?>"
            alt="<?= htmlspecialchars($alt) ?>"
            <?= $width ? 'width="' . htmlspecialchars($width) . '"' : '' ?>
            <?= $height ? 'height="' . htmlspecialchars($height) . '"' : '' ?>
            loading="lazy"
            id="<?= $imageId ?>"
        >
    </div>
    <?php
}

// Include CSS and JS only once
if (!defined('IMAGE_LAZY_LOAD_INCLUDED')) {
    define('IMAGE_LAZY_LOAD_INCLUDED', true);
    ?>
    <style>
        /* Lazy Image Styles */
        .lazy-image-wrapper {
            position: relative;
            width: 100%;
            height: 0;
            overflow: hidden;
            background-color: var(--placeholder-bg, #f0f0f0);
        }
        
        .lazy-image-placeholder {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--placeholder-bg, #f0f0f0);
        }
        
        .placeholder-gradient {
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.4),
                transparent
            );
            animation: placeholder-shimmer 1.5s infinite;
        }
        
        @keyframes placeholder-shimmer {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(200%);
            }
        }
        
        .placeholder-icon {
            color: var(--placeholder-icon, #d0d0d0);
            opacity: 0.5;
            z-index: 1;
        }
        
        .lazy-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        
        .lazy-image.loaded {
            opacity: 1;
        }
        
        .lazy-image-placeholder.hide {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        
        /* Dark mode support */
        [data-theme="dark"] .lazy-image-wrapper {
            background-color: var(--placeholder-bg, #2a2a2a);
        }
        
        [data-theme="dark"] .lazy-image-placeholder {
            background-color: var(--placeholder-bg, #2a2a2a);
        }
        
        [data-theme="dark"] .placeholder-gradient {
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.1),
                transparent
            );
        }
        
        [data-theme="dark"] .placeholder-icon {
            color: var(--placeholder-icon, #606060);
        }
        
        /* Specific styles for different image types */
        .post-image-wrapper {
            border-radius: 12px 12px 0 0;
            overflow: hidden;
        }
        
        .news-image-wrapper {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .avatar-image-wrapper {
            border-radius: 50%;
            overflow: hidden;
        }
        
        /* Remove padding for fixed height images */
        .lazy-image-wrapper.fixed-height {
            padding-bottom: 0 !important;
            height: auto;
        }
    </style>
    
    <script>
        // Lazy Loading Script
        document.addEventListener('DOMContentLoaded', function() {
            const lazyImages = document.querySelectorAll('.lazy-image');
            
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        const src = img.getAttribute('data-src');
                        
                        // Create a new image to preload
                        const tempImg = new Image();
                        tempImg.onload = function() {
                            // Set the source
                            img.src = src;
                            img.classList.add('loaded');
                            
                            // Hide placeholder after image loads
                            const wrapper = img.closest('.lazy-image-wrapper');
                            const placeholder = wrapper.querySelector('.lazy-image-placeholder');
                            if (placeholder) {
                                placeholder.classList.add('hide');
                            }
                        };
                        
                        tempImg.onerror = function() {
                            // Keep placeholder visible on error
                            console.error('Failed to load image:', src);
                        };
                        
                        tempImg.src = src;
                        observer.unobserve(img);
                    }
                });
            }, {
                rootMargin: '50px 0px',
                threshold: 0.01
            });
            
            lazyImages.forEach(img => {
                imageObserver.observe(img);
            });
        });
    </script>
    <?php
}

/**
 * Render lazy loaded background image
 */
function renderLazyBackground($options = []) {
    $src = $options['src'] ?? '';
    $class = $options['class'] ?? '';
    $height = $options['height'] ?? '400px';
    $content = $options['content'] ?? '';
    
    $bgId = 'lazy-bg-' . uniqid();
    
    ?>
    <div class="lazy-bg-wrapper <?= htmlspecialchars($class) ?>" 
         style="height: <?= htmlspecialchars($height) ?>;"
         data-bg-id="<?= $bgId ?>"
         data-bg-src="<?= htmlspecialchars($src) ?>">
        
        <!-- Placeholder -->
        <div class="lazy-bg-placeholder">
            <div class="placeholder-gradient"></div>
        </div>
        
        <!-- Content overlay -->
        <?php if ($content): ?>
            <div class="lazy-bg-content">
                <?= $content ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if (!defined('LAZY_BG_SCRIPT_INCLUDED')): 
        define('LAZY_BG_SCRIPT_INCLUDED', true);
    ?>
    <style>
        .lazy-bg-wrapper {
            position: relative;
            overflow: hidden;
            background-color: var(--placeholder-bg, #f0f0f0);
        }
        
        .lazy-bg-placeholder {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: var(--placeholder-bg, #f0f0f0);
        }
        
        .lazy-bg-wrapper.loaded .lazy-bg-placeholder {
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }
        
        .lazy-bg-content {
            position: relative;
            z-index: 1;
            height: 100%;
        }
        
        [data-theme="dark"] .lazy-bg-wrapper,
        [data-theme="dark"] .lazy-bg-placeholder {
            background-color: var(--placeholder-bg, #2a2a2a);
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const lazyBgs = document.querySelectorAll('.lazy-bg-wrapper[data-bg-src]');
            
            const bgObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const element = entry.target;
                        const src = element.getAttribute('data-bg-src');
                        
                        const tempImg = new Image();
                        tempImg.onload = function() {
                            element.style.backgroundImage = `url(${src})`;
                            element.style.backgroundSize = 'cover';
                            element.style.backgroundPosition = 'center';
                            element.classList.add('loaded');
                        };
                        
                        tempImg.src = src;
                        observer.unobserve(element);
                    }
                });
            }, {
                rootMargin: '50px 0px',
                threshold: 0.01
            });
            
            lazyBgs.forEach(bg => {
                bgObserver.observe(bg);
            });
        });
    </script>
    <?php endif; ?>
    <?php
}
?>