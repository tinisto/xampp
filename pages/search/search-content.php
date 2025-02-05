<?php include 'search-form.php'; ?>
<div class="container mt-4">
  <?php if (isset($additionalData['searchQuery']) && $additionalData['searchQuery'] !== ''): // Check if there's a valid search query 
  ?>
    <?php
    // Display search query for debugging
    echo '<p class="custom-info-message">Поисковый запрос: <span class="fw-semibold">' . htmlspecialchars($additionalData['searchQuery']) . '</span></p>';
    // Display search results
    $itemsPerPage = 10; // Set the desired number of items per page

    // Set a default value for $currentPage
    $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;

    // Perform search in each table

    $schoolsResult = mysqli_query($connection, "SELECT * FROM schools WHERE TRIM(school_name) LIKE '%{$additionalData['searchQuery']}%' OR TRIM(short_name) LIKE '%{$additionalData['searchQuery']}%' OR TRIM(email) LIKE '%{$additionalData['searchQuery']}%'");

    $spoResult = mysqli_query($connection, "SELECT * FROM spo WHERE TRIM(spo_name) LIKE '%{$additionalData['searchQuery']}%' OR TRIM(short_name) LIKE '%{$additionalData['searchQuery']}%' OR TRIM(old_name) LIKE '%{$additionalData['searchQuery']}%' OR TRIM(email) LIKE '%{$additionalData['searchQuery']}%' OR TRIM(email_pk) LIKE '%{$additionalData['searchQuery']}%'");

    $universitiesResult = mysqli_query($connection, "SELECT * FROM vpo WHERE TRIM(vpo_name) LIKE '%{$additionalData['searchQuery']}%' OR TRIM(short_name) LIKE '%{$additionalData['searchQuery']}%' OR TRIM(old_name) LIKE '%{$additionalData['searchQuery']}%' OR TRIM(email) LIKE '%{$additionalData['searchQuery']}%' OR TRIM(email_pk) LIKE '%{$additionalData['searchQuery']}%'");

    $postsResult = mysqli_query($connection, "SELECT * FROM posts WHERE TRIM(title_post) LIKE '%{$additionalData['searchQuery']}%' OR TRIM(text_post) LIKE '%{$additionalData['searchQuery']}%'");

    // Merge the results from different tables into a single array
    $results = [];
    while ($row = mysqli_fetch_assoc($schoolsResult)) {
      $row['type'] = 'school';
      $results[] = $row;
    }

    while ($row = mysqli_fetch_assoc($spoResult)) {
      $row['type'] = 'college';
      $results[] = $row;
    }

    while ($row = mysqli_fetch_assoc($universitiesResult)) {
      $row['type'] = 'university';
      $results[] = $row;
    }

    while ($row = mysqli_fetch_assoc($postsResult)) {
      $row['type'] = 'post';
      $results[] = $row;
    }

    // Display only items within the current page
    echo '<ul class="list-group">';
    $count = 0; // Initialize a counter for the displayed items

    // Set a default value for $startIndex
    // $startIndex = isset($startIndex) ? $startIndex : 0;
    $startIndex = ($currentPage - 1) * $itemsPerPage;


    // Loop through the results array and display items for the current page
    for ($i = $startIndex; $i < min($startIndex + $itemsPerPage, count($results)); $i++) {
      // Display only items within the current page
      if (isset($results[$i]['type'])) {
        switch ($results[$i]['type']) {
          case 'school':
            // Check if 'id_school' key exists, otherwise use a default value (e.g., 0)
            $schoolId = isset($results[$i]['id_school']) ? $results[$i]['id_school'] : 0;
            echo '<li class="list-group-item"><a class="link-custom" href="/school/' . $schoolId . '"><small>' . $results[$i]['school_name'] . '</small></a><span class="badge text-bg-warning ms-2">Школы</span></li>';
            break;
          case 'college':
            echo '<li class="list-group-item"><a class="link-custom" href="/spo/' . ($results[$i]['spo_url']) . '"><small>' . $results[$i]['spo_name'] . '</small></a><span class="badge text-bg-warning ms-2">ССУЗы</span></li>';
            break;
          case 'university':
            echo '<li class="list-group-item"><a class="link-custom" href="/vpo/' . ($results[$i]['vpo_url']) . '"><small>' . $results[$i]['vpo_name'] . '</small></a><span class="badge text-bg-warning ms-2">ВУЗы</span></li>';
            break;
          case 'post':
            echo '<li class="list-group-item"><a class="link-custom" href="/post/' . ($results[$i]['url_post']) . '"><small>' . $results[$i]['title_post'] . '</small></a><span class="badge text-bg-warning ms-2">Статьи</span></li>';
            break;
        }
        $count++;
      }
    }
    echo '</ul>';

    // Calculate total pages
    $totalResults = count($results);
    $totalPages = ceil($totalResults / $itemsPerPage);

    // Include pagination if available
    if ($totalPages > 0 && isset($currentPage)) {
      include 'search-pagination.php';
    }

    // Display a message when no results are found
    if ($count === 0) {
      echo '<p class="custom-alert">Нет результатов.</p>';
    }

    ?>
  <?php
  endif; ?>
  <?php
  if (!isset($count)) {
    include 'search-show-categories-white-links.php';
  }
  if ($count === 0) {
    include 'search-show-categories-black-links.php';
  }
  ?>
</div>