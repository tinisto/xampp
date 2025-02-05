<?php
function generatePagination($currentPage, $totalPages, $visiblePages = 5)
{
  // Don't display pagination if there's only one page
  if ($totalPages <= 1) {
    return;
  }

  echo '<nav aria-label="Pagination">';
  echo '<ul class="pagination pagination-sm pagination-dark justify-content-center rounded mt-3">';

  // Display the first page button (ONLY if not on Page 1)
  if ($currentPage > 1) {
    echo '<li class="page-item">';
    echo '<a class="page-link bg-secondary text-white border-light" href="?page=1" aria-label="First">';
    echo '<span aria-hidden="true">&laquo;&laquo;</span></a>';
    echo '</li>';
  }

  // Display visible page numbers
  $startPage = max(1, $currentPage - floor($visiblePages / 2));
  $endPage = min($totalPages, $startPage + $visiblePages - 1);

  for ($i = $startPage; $i <= $endPage; $i++) {
    echo '<li class="page-item ' . ($i == $currentPage ? 'active' : '') . '">';
    echo '<a class="page-link ' . ($i == $currentPage ? 'bg-white text-dark' : 'bg-secondary text-white border-light') . '" href="?page=' . $i . '">' . $i . '</a>';
    echo '</li>';
  }

  // Display the last page button (ONLY if not on the last page)
  if ($currentPage < $totalPages) {
    echo '<li class="page-item">';
    echo '<a class="page-link bg-secondary text-white border-light" href="?page=' . $totalPages . '" aria-label="Last">';
    echo '<span aria-hidden="true">&raquo;&raquo;</span></a>';
    echo '</li>';
  }

  echo '</ul>';
  echo '</nav>';
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