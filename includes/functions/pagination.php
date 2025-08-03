<?php
// Wrapper function for compatibility
function renderPagination($baseUrl, $currentPage, $totalPages) {
    // Parse URL to get query parameters
    $urlParts = parse_url($baseUrl);
    $queryParams = [];
    if (isset($urlParts['query'])) {
        parse_str($urlParts['query'], $queryParams);
    }
    
    generatePagination($currentPage, $totalPages, 5, $queryParams);
}

function generatePagination($currentPage, $totalPages, $visiblePages = 5, $queryParams = [])
{
  // Don't display pagination if there's only one page
  if ($totalPages <= 1) {
    return;
  }

  // Build query string for pagination links
  $queryString = '';
  if (!empty($queryParams)) {
    $queryParts = [];
    foreach ($queryParams as $key => $value) {
      if ($key !== 'page' && !empty($value)) {
        $queryParts[] = $key . '=' . urlencode($value);
      }
    }
    $queryString = !empty($queryParts) ? '&' . implode('&', $queryParts) : '';
  }

  // Modern pagination styles
  echo '<style>
    .pagination-modern {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin: 20px 0;
        padding: 10px;
        flex-wrap: wrap;
    }
    .pagination-modern a,
    .pagination-modern span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        padding: 0 12px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 1px solid var(--color-border, #e9ecef);
        background: var(--color-surface-primary, #ffffff);
        color: var(--color-text-primary, #333);
    }
    .pagination-modern a:hover {
        background: var(--color-bg-hover, rgba(40, 167, 69, 0.1));
        border-color: #28a745;
        color: #28a745;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .pagination-modern .current {
        background: #28a745;
        color: white;
        border-color: #28a745;
        cursor: default;
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
    }
    .pagination-modern .disabled {
        opacity: 0.4;
        cursor: not-allowed;
        background: var(--color-surface-secondary, #f8f9fa);
        color: var(--color-text-secondary, #6c757d);
    }
    
    @media (max-width: 600px) {
        .pagination-modern {
            gap: 4px;
        }
        .pagination-modern a,
        .pagination-modern span {
            min-width: 32px;
            height: 32px;
            font-size: 12px;
            padding: 0 8px;
        }
    }
  </style>';

  echo '<div class="pagination-modern">';

  // Display the first page button (ONLY if not on Page 1)
  if ($currentPage > 1) {
    echo '<a href="?page=1' . $queryString . '" aria-label="First">';
    echo '<i class="fas fa-angle-double-left"></i></a>';
  } else {
    echo '<span class="disabled"><i class="fas fa-angle-double-left"></i></span>';
  }

  // Display visible page numbers
  $startPage = max(1, $currentPage - floor($visiblePages / 2));
  $endPage = min($totalPages, $startPage + $visiblePages - 1);

  // Show first page if not in range
  if ($startPage > 1) {
    echo '<a href="?page=1' . $queryString . '">1</a>';
    if ($startPage > 2) {
      echo '<span class="disabled">...</span>';
    }
  }

  for ($i = $startPage; $i <= $endPage; $i++) {
    if ($i == $currentPage) {
      echo '<span class="current">' . $i . '</span>';
    } else {
      echo '<a href="?page=' . $i . $queryString . '">' . $i . '</a>';
    }
  }

  // Show last page if not in range
  if ($endPage < $totalPages) {
    if ($endPage < $totalPages - 1) {
      echo '<span class="disabled">...</span>';
    }
    echo '<a href="?page=' . $totalPages . $queryString . '">' . $totalPages . '</a>';
  }

  // Display the last page button (ONLY if not on the last page)
  if ($currentPage < $totalPages) {
    echo '<a href="?page=' . $totalPages . $queryString . '" aria-label="Last">';
    echo '<i class="fas fa-angle-double-right"></i></a>';
  } else {
    echo '<span class="disabled"><i class="fas fa-angle-double-right"></i></span>';
  }

  echo '</div>';
}
?>




<?php
function getTableColumns($connection, $tableName)
{
  // Query to fetch column names from the table
  $sql = "DESCRIBE $tableName";
  $result = $connection->query($sql);

  $columns = [];
  while ($row = $result->fetch_assoc()) {
    $columns[] = $row['Field']; // 'Field' is the column name
  }

  return $columns;
}

// Include this function in your separate file (e.g., pagination_functions.php)
function fetchPaginatedResults($postsPerPage, $currentPage, $connection, $tableName, $sortByTimestamp = false)
{
  // Calculate the OFFSET for pagination
  $pageOffset = max(0, ($currentPage - 1) * $postsPerPage);

  // Construct the basic query
  $sql = "SELECT * FROM $tableName";

  // Optionally add sorting by timestamp
  if ($sortByTimestamp) {
    $sql .= " ORDER BY $sortByTimestamp DESC";
  }

  // Add LIMIT and OFFSET for pagination
  $sql .= " LIMIT $postsPerPage OFFSET $pageOffset";

  // Execute the query
  $result = $connection->query($sql);

  return $result;
}


?>

<?php
function calculateTotalPages($connection, $tableName, $itemsPerPage)
{
  $sqlCount = "SELECT COUNT(*) as total FROM $tableName";
  $resultCount = $connection->query($sqlCount);
  $rowCount = $resultCount->fetch_assoc()['total'];
  $totalPages = ceil($rowCount / $itemsPerPage);

  return $totalPages;
}

?>