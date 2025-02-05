<?php
$visiblePages = 5; // Adjust the number of visible pages as needed
?>

<?php if ($totalPages > 1): // Only show pagination if there is more than one page ?>
  <div class="d-flex justify-content-center mt-3">
    <nav aria-label="Search results pagination">
      <ul class="pagination">

        <?php
        // Add << for the first page
        if ($currentPage > 1) {
          echo '<li class="page-item"><a class="page-link" href="?query=' . htmlspecialchars(urlencode($additionalData['searchQuery'])) . '&page=1">&laquo;&laquo;</a></li>';
        }

        // Display page numbers within the visible range
        for ($i = max(1, $currentPage - floor($visiblePages / 2)); $i <= min($totalPages, $currentPage + floor($visiblePages / 2)); $i++) {
          echo '<li class="page-item' . ($currentPage == $i ? ' active' : '') . '"><a class="page-link" href="?query=' . htmlspecialchars(urlencode($additionalData['searchQuery'])) . '&page=' . $i . '">' . $i . '</a></li>';
        }

        // Add >> for the last page
        if ($currentPage < $totalPages) {
          echo '<li class="page-item"><a class="page-link" href="?query=' . htmlspecialchars(urlencode($additionalData['searchQuery'])) . '&page=' . $totalPages . '">&raquo;&raquo;</a></li>';
        }
        ?>
      </ul>
    </nav>
  </div>
<?php endif; ?>