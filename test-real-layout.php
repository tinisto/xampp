<?php
// Load environment and database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/config/environment.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Real Layout - 11-классники</title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html {
            height: 100%;
            overflow: hidden; /* Prevent scrolling on html */
        }
        
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            background: #212529; /* Dark background for overscroll areas */
            overflow: hidden; /* Prevent all scrolling */
            position: relative;
        }
        
        /* Wrapper for yellow background sections */
        .yellow-bg-wrapper {
            background: yellow;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        /* Header - flex-shrink: 0 so it keeps its size */
        .main-header {
            flex-shrink: 0;
            margin: 0; /* No margins */
            padding: 0; /* No padding */
            background: white; /* Ensure header has white background */
        }
        
        /* Page header (green) - flex-shrink: 0 so it keeps its size */
        .page-header {
            flex-shrink: 0;
            /* Margins handled by component itself */
        }
        
        /* Desktop - larger margins */
        @media (min-width: 769px) {
            .main-header {
                margin: 0; /* No margins on desktop either */
            }
        }
        
        /* Content - flex: 1 so it expands to fill space */
        .content {
            flex: 1 1 auto; /* Grow and shrink to fill available space */
            background: red; /* RED background for main content */
            padding: 20px 10px; /* Reduced left/right padding on mobile */
            margin: 0 10px; /* Reduced left/right margins on mobile */
            box-sizing: border-box;
            min-height: 0; /* Allow shrinking */
            overflow: auto; /* Handle overflow internally */
        }
        
        /* Comments section */
        .comments-section {
            background: blue;
            color: white;
            padding: 20px 10px; /* Reduced left/right padding on mobile */
            margin: 0 10px; /* Reduced left/right margins on mobile */
            box-sizing: border-box;
            flex-shrink: 0; /* Don't shrink */
        }
        
        /* Container - no padding, just for visualization */
        .content .container,
        .comments-section .container {
            max-width: none;
            margin: 0;
            padding: 0; /* No padding on container - it's on the parent */
            width: 100%;
        }
        
        /* Desktop - larger padding on colored divs */
        @media (min-width: 769px) {
            .content {
                padding: 40px; /* 40px padding on RED div */
                margin: 0 40px; /* Larger margins on desktop */
            }
            
            .comments-section {
                padding: 40px; /* 40px padding on BLUE div */
                margin: 0 40px; /* Larger margins on desktop */
            }
        }
        
        /* Footer - flex-shrink: 0 so it keeps its size */
        .main-footer {
            flex-shrink: 0;
            margin: 0; /* No margins */
            padding: 0; /* No padding */
            background: #f8f9fa; /* Ensure footer has its light background */
        }
    </style>
</head>
<body>
    <!-- Website Header -->
    <header class="main-header">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
    </header>
    
    <!-- Yellow background wrapper for middle sections -->
    <div class="yellow-bg-wrapper">
        <!-- Green Page Header -->
        <div class="page-header">
            <?php 
            include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/page-section-header.php';
            renderPageSectionHeader([
                'title' => 'Результаты поиска',
                'showSearch' => false
            ]);
            ?>
        </div>
        
        <!-- Main Content (RED background) -->
        <main class="content" style="background: red;">
            <!-- Empty red section -->
        </main>
        
        <!-- Comments Section (BLUE background) -->
        <div class="comments-section" style="background: blue;">
            <!-- Empty blue section -->
        </div>
    </div>
    
    <!-- Website Footer -->
    <footer class="main-footer">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php'; ?>
    </footer>
</body>
</html>