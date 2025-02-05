<div class="py-md-3">
  <hr>
</div>
<?php
$queryCategory = "SELECT * FROM categories";
$resultCategory = mysqli_query($connection, $queryCategory);

if ($resultCategory && mysqli_num_rows($resultCategory) > 0) {
  echo '<div class="container">';
  $count = 0;

  while ($categoryData = mysqli_fetch_assoc($resultCategory)) {
    $title = $categoryData['title_category'];
    $url = $categoryData['url_category'];

    if ($count % 4 === 0) {
      echo '<div class="row mb-3">';
    }

    echo '<div class="col-md-3">';
    echo '<a href="/category/' . $url . '" class="link-white"><small>' . $title . '</small></a>';
    echo '</div>';

    if ($count % 4 === 3) {
      echo '</div>';
    }

    $count++;
  }

  // Close any remaining open row div
  if ($count % 4 !== 0) {
    echo '</div>';
  }

  echo '</div>';
}
?>