<?php
/**
 * YouTube-style Loading Placeholders Component
 * 
 * Usage:
 * include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/loading-spinner.php';
 * 
 * Then add this to your page where you want the placeholders:
 * <?php renderLoadingSpinner(); ?>
 * 
 * The placeholders will automatically show when the page is loading
 * and hide when the page is fully loaded.
 */

function renderLoadingSpinner() {
    // Check current path to show appropriate placeholders
    $currentPath = $_SERVER['REQUEST_URI'];
    
    // Skip placeholders for thank-you and write pages
    if (strpos($currentPath, '/thank-you') === 0 || strpos($currentPath, '/write') === 0) {
        return;
    }
    
    // Tests page
    if ($currentPath === '/tests' || strpos($currentPath, '/tests?') === 0) {
        renderTestsPagePlaceholder();
        return;
    }
    
    // News pages
    if (strpos($currentPath, '/news/') === 0) {
        renderNewsPagePlaceholder();
        return;
    }
    
    // Region pages (SPO/VPO in region)
    if (preg_match('/\/(spo|vpo)-in-region\//', $currentPath)) {
        renderRegionPagePlaceholder();
        return;
    }
    
    // All regions pages
    if (preg_match('/\/(spo|vpo|schools)-all-regions/', $currentPath)) {
        renderAllRegionsPlaceholder();
        return;
    }
    
    // Default placeholder
    ?>
    <!-- YouTube-style loading placeholders only (no spinner) -->
    <div id="loading-placeholders" class="loading-placeholders-container">
        <!-- Header placeholder -->
        <div class="placeholder-header">
            <div class="container">
                <div class="placeholder-brand"></div>
                <div class="placeholder-nav">
                    <div class="placeholder-nav-item"></div>
                    <div class="placeholder-nav-item"></div>
                    <div class="placeholder-nav-item"></div>
                    <div class="placeholder-nav-item"></div>
                </div>
            </div>
        </div>
        
        <!-- Main content placeholders -->
        <div class="container">
            <div class="placeholder-content">
                <!-- Title placeholder -->
                <div class="placeholder-title"></div>
                
                <!-- Cards grid placeholder -->
                <div class="placeholder-grid">
                    <?php for ($i = 0; $i < 8; $i++): ?>
                        <div class="placeholder-card">
                            <div class="placeholder-image"></div>
                            <div class="placeholder-card-content">
                                <div class="placeholder-text-line"></div>
                                <div class="placeholder-text-line short"></div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        /* YouTube-style loading placeholders */
        .loading-placeholders-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #f8f9fa;
            z-index: 9998;
            overflow-y: auto;
        }
        
        .placeholder-header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 20px 0;
            margin-bottom: 40px;
        }
        
        .placeholder-brand {
            width: 150px;
            height: 24px;
            background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
            background-size: 400% 100%;
            border-radius: 4px;
            animation: shimmer 2s infinite linear;
            float: left;
        }
        
        .placeholder-nav {
            display: flex;
            gap: 30px;
            justify-content: center;
            flex: 1;
        }
        
        .placeholder-nav-item {
            width: 80px;
            height: 20px;
            background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
            background-size: 400% 100%;
            border-radius: 4px;
            animation: shimmer 2s infinite linear;
        }
        
        .placeholder-title {
            width: 60%;
            height: 40px;
            background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
            background-size: 400% 100%;
            border-radius: 8px;
            margin: 0 auto 40px;
            animation: shimmer 2s infinite linear;
        }
        
        .placeholder-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .placeholder-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        
        .placeholder-image {
            width: 100%;
            height: 180px;
            background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
            background-size: 400% 100%;
            animation: shimmer 2s infinite linear;
        }
        
        .placeholder-card-content {
            padding: 20px;
        }
        
        .placeholder-text-line {
            width: 100%;
            height: 16px;
            background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
            background-size: 400% 100%;
            border-radius: 4px;
            margin-bottom: 12px;
            animation: shimmer 2s infinite linear;
        }
        
        .placeholder-text-line.short {
            width: 70%;
            margin-bottom: 0;
        }
        
        /* Enhanced YouTube-style shimmer animation */
        @keyframes shimmer {
            0% { background-position: -400% 0; }
            100% { background-position: 400% 0; }
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .placeholder-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .placeholder-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
            .placeholder-nav {
                display: none;
            }
        }
        
        @media (max-width: 480px) {
            .placeholder-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Dark mode for placeholders */
        [data-bs-theme="dark"] .loading-placeholders-container {
            background: #1a202c;
        }
        
        [data-bs-theme="dark"] .placeholder-header {
            background: #2d3748;
        }
        
        [data-bs-theme="dark"] .placeholder-card {
            background: #2d3748;
        }
        
        [data-bs-theme="dark"] .placeholder-brand,
        [data-bs-theme="dark"] .placeholder-nav-item,
        [data-bs-theme="dark"] .placeholder-title,
        [data-bs-theme="dark"] .placeholder-image,
        [data-bs-theme="dark"] .placeholder-text-line {
            background: linear-gradient(90deg, #4a5568 25%, #2d3748 50%, #4a5568 75%);
            background-size: 400% 100%;
            animation: shimmer 2s infinite linear;
        }
        
        /* Hide placeholders when page is loaded */
        body.loaded .loading-placeholders-container {
            display: none;
        }
    </style>
    
    <script>
        // Hide placeholders when page is fully loaded
        window.addEventListener('load', function() {
            const placeholders = document.getElementById('loading-placeholders');
            
            if (placeholders) {
                placeholders.style.opacity = '0';
                placeholders.style.transition = 'opacity 0.3s ease-out';
                setTimeout(() => {
                    placeholders.style.display = 'none';
                    document.body.classList.add('loaded');
                }, 300);
            }
        });
        
        // Hide placeholders after maximum wait time (fallback)
        setTimeout(() => {
            const placeholders = document.getElementById('loading-placeholders');
            
            if (placeholders && placeholders.style.display !== 'none') {
                placeholders.style.opacity = '0';
                placeholders.style.transition = 'opacity 0.3s ease-out';
                setTimeout(() => {
                    placeholders.style.display = 'none';
                    document.body.classList.add('loaded');
                }, 300);
            }
        }, 4000); // Hide after 4 seconds max
        
        // Don't show placeholders when navigating away
        // This prevents showing wrong placeholders for the destination page
        window.addEventListener('beforeunload', function() {
            // Disabled to prevent showing incorrect placeholders
            // The destination page will show its own appropriate placeholders
        });
    </script>
    <?php
}

/**
 * Alternative spinner with dots
 */
function renderDotsSpinner() {
    ?>
    <div id="loading-spinner" class="loading-spinner-overlay">
        <div class="spinner-container">
            <div class="spinner-dots">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
            <div class="loading-text">Загрузка...</div>
        </div>
    </div>
    <?php
}

/**
 * Tests page specific loading placeholders
 */
function renderTestsPagePlaceholder() {
    ?>
    <div id="loading-placeholders" class="loading-placeholders-container">
        <!-- Header placeholder -->
        <div class="placeholder-header">
            <div class="container">
                <div class="placeholder-brand"></div>
                <div class="placeholder-nav">
                    <div class="placeholder-nav-item"></div>
                    <div class="placeholder-nav-item"></div>
                    <div class="placeholder-nav-item"></div>
                    <div class="placeholder-nav-item"></div>
                </div>
            </div>
        </div>
        
        <!-- Hero section placeholder -->
        <div class="placeholder-hero" style="background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%); padding: 80px 0;">
            <div class="container text-center">
                <div class="placeholder-text-line" style="width: 300px; height: 40px; margin: 0 auto 20px;"></div>
                <div class="placeholder-text-line" style="width: 500px; height: 20px; margin: 0 auto 30px;"></div>
            </div>
        </div>
        
        <!-- Main content placeholders -->
        <div class="container" style="padding: 40px 0;">
            <!-- Test cards -->
            <div class="row">
                <?php for ($i = 0; $i < 4; $i++): ?>
                    <div class="col-md-6" style="margin-bottom: 30px;">
                        <div class="placeholder-card" style="height: 200px; padding: 30px;">
                            <div class="placeholder-text-line" style="width: 60%; height: 24px; margin-bottom: 15px;"></div>
                            <div class="placeholder-text-line" style="width: 100%; height: 16px; margin-bottom: 10px;"></div>
                            <div class="placeholder-text-line" style="width: 90%; height: 16px; margin-bottom: 20px;"></div>
                            <div class="placeholder-text-line" style="width: 120px; height: 36px; border-radius: 18px;"></div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    
    <script>
        // Check if we're on tests page and replace generic placeholders
        window.addEventListener('DOMContentLoaded', function() {
            if (window.location.pathname === '/tests') {
                // Hide after short delay
                setTimeout(() => {
                    const placeholders = document.getElementById('loading-placeholders');
                    if (placeholders) {
                        placeholders.style.opacity = '0';
                        placeholders.style.transition = 'opacity 0.3s ease-out';
                        setTimeout(() => {
                            placeholders.style.display = 'none';
                            document.body.classList.add('loaded');
                        }, 300);
                    }
                }, 500);
            }
        });
    </script>
    <?php
}

/**
 * News page specific loading placeholders
 */
function renderNewsPagePlaceholder() {
    ?>
    <div id="loading-placeholders" class="loading-placeholders-container">
        <!-- Header placeholder -->
        <div class="placeholder-header">
            <div class="container">
                <div class="placeholder-brand"></div>
                <div class="placeholder-nav">
                    <div class="placeholder-nav-item"></div>
                    <div class="placeholder-nav-item"></div>
                    <div class="placeholder-nav-item"></div>
                    <div class="placeholder-nav-item"></div>
                </div>
            </div>
        </div>
        
        <!-- News content placeholder -->
        <div class="container" style="padding: 40px 0;">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Article placeholder -->
                    <div class="placeholder-card" style="padding: 30px;">
                        <div class="placeholder-text-line" style="width: 70%; height: 36px; margin-bottom: 20px;"></div>
                        <div class="placeholder-text-line" style="width: 100%; height: 16px; margin-bottom: 10px;"></div>
                        <div class="placeholder-text-line" style="width: 95%; height: 16px; margin-bottom: 10px;"></div>
                        <div class="placeholder-text-line" style="width: 90%; height: 16px; margin-bottom: 30px;"></div>
                        <div class="placeholder-image" style="height: 300px; margin-bottom: 30px;"></div>
                        <div class="placeholder-text-line" style="width: 100%; height: 16px; margin-bottom: 10px;"></div>
                        <div class="placeholder-text-line" style="width: 98%; height: 16px; margin-bottom: 10px;"></div>
                        <div class="placeholder-text-line" style="width: 85%; height: 16px;"></div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <!-- Sidebar placeholder -->
                    <div class="placeholder-card" style="padding: 20px; margin-bottom: 20px;">
                        <div class="placeholder-text-line" style="width: 60%; height: 20px; margin-bottom: 15px;"></div>
                        <div class="placeholder-text-line" style="width: 100%; height: 14px; margin-bottom: 10px;"></div>
                        <div class="placeholder-text-line" style="width: 90%; height: 14px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Region page specific loading placeholders
 */
function renderRegionPagePlaceholder() {
    ?>
    <div id="loading-placeholders" class="loading-placeholders-container">
        <!-- Header placeholder -->
        <div class="placeholder-header">
            <div class="container">
                <div class="placeholder-brand"></div>
                <div class="placeholder-nav">
                    <div class="placeholder-nav-item"></div>
                    <div class="placeholder-nav-item"></div>
                    <div class="placeholder-nav-item"></div>
                    <div class="placeholder-nav-item"></div>
                </div>
            </div>
        </div>
        
        <!-- Region page placeholders -->
        <div class="container" style="padding: 40px 0;">
            <!-- Title -->
            <div class="placeholder-text-line" style="width: 40%; height: 32px; margin: 0 auto 30px;"></div>
            
            <!-- Institution list -->
            <?php for ($i = 0; $i < 6; $i++): ?>
                <div class="placeholder-card" style="padding: 20px; margin-bottom: 15px;">
                    <div class="placeholder-text-line" style="width: 60%; height: 18px; margin-bottom: 10px;"></div>
                    <div class="placeholder-text-line" style="width: 40%; height: 14px;"></div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
    <?php
}

/**
 * All regions page specific loading placeholders
 */
function renderAllRegionsPlaceholder() {
    ?>
    <div id="loading-placeholders" class="loading-placeholders-container">
        <!-- Header placeholder -->
        <div class="placeholder-header">
            <div class="container">
                <div class="placeholder-brand"></div>
                <div class="placeholder-nav">
                    <div class="placeholder-nav-item"></div>
                    <div class="placeholder-nav-item"></div>
                    <div class="placeholder-nav-item"></div>
                    <div class="placeholder-nav-item"></div>
                </div>
            </div>
        </div>
        
        <!-- Hero section placeholder -->
        <div class="placeholder-hero" style="background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%); padding: 60px 0;">
            <div class="container text-center">
                <div class="placeholder-text-line" style="width: 400px; height: 36px; margin: 0 auto 20px;"></div>
                <div class="placeholder-text-line" style="width: 300px; height: 18px; margin: 0 auto;"></div>
            </div>
        </div>
        
        <!-- Regions grid placeholder -->
        <div class="container" style="padding: 40px 0;">
            <div class="placeholder-grid" style="grid-template-columns: repeat(4, 1fr);">
                <?php for ($i = 0; $i < 12; $i++): ?>
                    <div class="placeholder-card" style="padding: 25px;">
                        <div class="placeholder-text-line" style="width: 70%; height: 18px; margin-bottom: 0;"></div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Inline spinner for AJAX requests
 */
function renderInlineSpinner($id = 'inline-spinner') {
    ?>
    <div id="<?= htmlspecialchars($id) ?>" class="inline-spinner" style="display: none;">
        <div class="spinner-small">
            <div class="double-bounce1"></div>
            <div class="double-bounce2"></div>
        </div>
    </div>
    
    <style>
        .inline-spinner {
            display: inline-block;
            margin: 10px;
        }
        
        .spinner-small {
            width: 20px;
            height: 20px;
            position: relative;
            display: inline-block;
        }
        
        .spinner-small .double-bounce1,
        .spinner-small .double-bounce2 {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background-color: #28a745;
            opacity: 0.6;
            position: absolute;
            top: 0;
            left: 0;
            animation: sk-bounce 2.0s infinite ease-in-out;
        }
        
        .spinner-small .double-bounce2 {
            animation-delay: -1.0s;
        }
    </style>
    <?php
}
?>