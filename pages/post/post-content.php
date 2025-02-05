<?php
// Check if the URL parameter is set
if (isset($_GET['url_post'])) {
  // Sanitize the input
  $urlPost = mysqli_real_escape_string($connection, $_GET['url_post']);

  // Fetch posts
  $queryPosts = "SELECT * FROM posts WHERE url_post = '$urlPost'";
  $resultPosts = mysqli_query($connection, $queryPosts);

  if ($resultPosts) {
    // Display posts
    while ($rowPost = mysqli_fetch_assoc($resultPosts)) {

      // Check if the user has not visited the page during the current session
      if (!isset($_SESSION['visited'])) {
        // Increase view count
        $updatedViews = $rowPost['view_post'] + 1;
        $queryUpdateViews = "UPDATE posts SET view_post = $updatedViews WHERE url_post = '$urlPost'";
        mysqli_query($connection, $queryUpdateViews);
        // Set the session variable to indicate that the user has visited the page
        $_SESSION['visited'] = true;
      }
      include 'post-header-links.php';
?>
      <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/getEntityIdFromURL.php'; ?>

      <?php $entity_type = 'post';
      $id_entity = getEntityIdFromPostURL($connection);
      ?>

      <div class="d-flex justify-content-between align-items-center">
        <h1>
          <?php echo $rowPost['title_post'];
          ?>
        </h1>
        <div>
          <?php
          // Check if the user is an admin
          if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            echo '<a href="/pages/dashboard/posts-dashboard/posts-view/posts-view-edit-form.php?id_post=' . $rowPost['id_post'] . '" class="edit-icon" style="color: red;"><i class="fas fa-edit"></i></a>';
          }
          ?>
        </div>
      </div>
      

      <p class="lead fw-medium">
        <?php echo $rowPost['description_post']; ?>
      </p>
      <div class="float-md-start me-md-4 mx-auto mx-md-0 col-md-3">
        <?php
        $imageFileName = isset($rowPost['image_post_1']) ? $rowPost['image_post_1'] : null;
        $imageUrl = $imageFileName ? "../images/posts-images/" . $imageFileName : null;
        $imagePath = $imageFileName ? $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/" . $imageFileName : null;

        if (!empty($rowPost['image_post_1']) && file_exists($imagePath)) {
          echo '<div class="mb-2">';
          echo '<img src="' . $imageUrl . '" alt="Post Image 1" style="width: 100%; height: 100%;">';
          echo '</div>';
        }
        ?>
        <?php
        $imageFileName = isset($rowPost['image_post_2']) ? $rowPost['image_post_2'] : null;
        $imageUrl = $imageFileName ? "../images/posts-images/" . $imageFileName : null;
        $imagePath = $imageFileName ? $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/" . $imageFileName : null;

        if (!empty($rowPost['image_post_2']) && file_exists($imagePath)) {
          echo '<div class="mb-2">';
          echo '<img src="' . $imageUrl . '" alt="Post Image 2" style="width: 100%; height: 100%;">';
          echo '</div>';
        }
        ?>
        <?php
        $imageFileName = isset($rowPost['image_post_3']) ? $rowPost['image_post_3'] : null;
        $imageUrl = $imageFileName ? "../images/posts-images/" . $imageFileName : null;
        $imagePath = $imageFileName ? $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/" . $imageFileName : null;

        if (!empty($rowPost['image_post_3']) && file_exists($imagePath)) {
          echo '<div class="mb-2">';
          echo '<img src="' . $imageUrl . '" alt="Post Image 3" style="width: 100%; height: 100%;">';
          echo '</div>';
        }
        ?>

        <?php if (
          !empty($rowPost['bio_post']) &&
          ((isset($rowPost['image_post_1']) && $rowPost['image_post_1']) ||
            (isset($rowPost['image_post_2']) && $rowPost['image_post_2']) ||
            (isset($rowPost['image_post_3']) && $rowPost['image_post_3']))
        ) { ?>


          <div class="bio-post">
            <?= $rowPost['bio_post'] ?>
          </div>
        <?php } ?>
      </div>
      <p>
        <?= $rowPost['text_post'] ?>
      </p>

<?php
    }

    // Free the result set for posts
    mysqli_free_result($resultPosts);
  } else {
    echo '<p class="text-danger">Error fetching posts from the database</p>';
  }
} else {
  echo '<p class="text-danger">Invalid URL</p>';
}
?>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/getEntityIdFromURL.php'; ?>
<?php
$result = getEntityIdFromPostURL($connection);

$id_entity = $result['id_entity'];
$entity_type = $result['entity_type'];

?>
<br clear="both">
<?php
include $_SERVER['DOCUMENT_ROOT'] . '/comments/user_comments.php';
?>