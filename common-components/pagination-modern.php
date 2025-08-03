<?php
/**
 * Modern Pagination Component
 * 
 * Usage:
 * include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/pagination-modern.php';
 * renderPaginationModern($currentPage, $totalPages, $baseUrl);
 */

function renderPaginationModern($currentPage, $totalPages, $baseUrl = '') {
    if ($totalPages <= 1) return;
    
    // Parse base URL to handle existing query parameters
    $urlParts = parse_url($baseUrl ?: $_SERVER['REQUEST_URI']);
    $queryParams = [];
    if (isset($urlParts['query'])) {
        parse_str($urlParts['query'], $queryParams);
    }
    unset($queryParams['page']); // Remove existing page parameter
    
    $basePath = $urlParts['path'] ?? '';
    
    // Function to build URL with page parameter
    $buildUrl = function($page) use ($basePath, $queryParams) {
        $params = array_merge($queryParams, ['page' => $page]);
        $query = http_build_query($params);
        return $basePath . ($query ? '?' . $query : '');
    };
    ?>
    
    <style>
        .pagination-modern {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            margin: 10px 0;
            padding: 10px;
            flex-wrap: wrap;
        }
        .pagination-modern a,
        .pagination-modern span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 10px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid #e0e0e0;
            background: white;
            color: #333;
        }
        .pagination-modern a:hover {
            background: #f5f5f5;
            border-color: #28a745;
            color: #28a745;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .pagination-modern .current {
            background: #28a745;
            color: white;
            border-color: #28a745;
            cursor: default;
        }
        .pagination-modern .disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f8f9fa;
            color: #999;
        }
        .pagination-modern .dots {
            border: none;
            background: none;
            cursor: default;
            color: #999;
        }
        .pagination-info {
            font-size: 14px;
            color: #666;
            margin-right: 20px;
        }
        /* Dark mode styles */
        [data-bs-theme="dark"] .pagination-modern a,
        [data-bs-theme="dark"] .pagination-modern span {
            background: #2d3748;
            color: #e4e6eb;
            border-color: #4a5568;
        }
        [data-bs-theme="dark"] .pagination-modern a:hover {
            background: #4a5568;
            border-color: #28a745;
            color: #28a745;
        }
        [data-bs-theme="dark"] .pagination-modern .current {
            background: #28a745;
            color: white;
            border-color: #28a745;
        }
        [data-bs-theme="dark"] .pagination-modern .disabled {
            background: #1a202c;
            color: #718096;
            border-color: #2d3748;
        }
        
        @media (max-width: 600px) {
            .pagination-modern {
                gap: 4px;
            }
            .pagination-modern a,
            .pagination-modern span {
                min-width: 28px;
                height: 28px;
                font-size: 12px;
                padding: 0 8px;
            }
        }
    </style>
    
    <div class="pagination-modern">
        
        <?php if ($currentPage > 1): ?>
            <a href="<?= htmlspecialchars($buildUrl(1)) ?>" title="Первая страница">
                <i class="fas fa-angle-double-left"></i>
            </a>
            <a href="<?= htmlspecialchars($buildUrl($currentPage - 1)) ?>" title="Предыдущая">
                <i class="fas fa-angle-left"></i>
            </a>
        <?php else: ?>
            <span class="disabled">
                <i class="fas fa-angle-double-left"></i>
            </span>
            <span class="disabled">
                <i class="fas fa-angle-left"></i>
            </span>
        <?php endif; ?>
        
        <?php
        // Calculate page range to display
        $range = 2; // Pages to show on each side of current
        $start = max(1, $currentPage - $range);
        $end = min($totalPages, $currentPage + $range);
        
        // Show first page if not in range
        if ($start > 1) {
            echo '<a href="' . htmlspecialchars($buildUrl(1)) . '">1</a>';
            if ($start > 2) {
                echo '<span class="dots">...</span>';
            }
        }
        
        // Show page numbers
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $currentPage) {
                echo '<span class="current">' . $i . '</span>';
            } else {
                echo '<a href="' . htmlspecialchars($buildUrl($i)) . '">' . $i . '</a>';
            }
        }
        
        // Show last page if not in range
        if ($end < $totalPages) {
            if ($end < $totalPages - 1) {
                echo '<span class="dots">...</span>';
            }
            echo '<a href="' . htmlspecialchars($buildUrl($totalPages)) . '">' . $totalPages . '</a>';
        }
        ?>
        
        <?php if ($currentPage < $totalPages): ?>
            <a href="<?= htmlspecialchars($buildUrl($currentPage + 1)) ?>" title="Следующая">
                <i class="fas fa-angle-right"></i>
            </a>
            <a href="<?= htmlspecialchars($buildUrl($totalPages)) ?>" title="Последняя страница">
                <i class="fas fa-angle-double-right"></i>
            </a>
        <?php else: ?>
            <span class="disabled">
                <i class="fas fa-angle-right"></i>
            </span>
            <span class="disabled">
                <i class="fas fa-angle-double-right"></i>
            </span>
        <?php endif; ?>
    </div>
    <?php
}
?>