<?php
if (isset($_GET['url_post'])) {
  $urlPost = mysqli_real_escape_string($connection, $_GET['url_post']);

  $queryPost = "SELECT * FROM posts WHERE url_post = '$urlPost'";
  $resultPost = mysqli_query($connection, $queryPost);

  if ($resultPost && mysqli_num_rows($resultPost) > 0) {
    $postData = mysqli_fetch_assoc($resultPost);
    $pageTitle = $postData['title_post'];
    $metaD = $postData['meta_d_post'];
    $metaK = $postData['meta_k_post'];

    mysqli_free_result($resultPost);
  } else {
    header("Location: /404");
    exit();
  }
} else {
  header("Location: /404");
  exit();
}
