<div class="container">

  <div class="row">

    <p class='lead text-center fw-semibold'>Недавние статьи</p>
    <?php

    // Check if database connection exists
    if ($connection && !$connection->connect_error) {
        // Use prepared statement for security
        $stmt = $connection->prepare("SELECT id_post, url_post, title_post FROM posts ORDER BY date_post DESC LIMIT 6");
        $stmt->execute();
        $resultPosts = $stmt->get_result();
        
        if ($resultPosts) {
            while ($rowPost = mysqli_fetch_assoc($resultPosts)) {
      ?>
      <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="card index-card mb-3">
          <a href="/post/<?= $rowPost['url_post'] ?>" class="text-decoration-none">
            <!-- Rest of your existing content -->
            <div class="card-img-container">
              <?php
              $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/{$rowPost['id_post']}_1.jpg";

              if (file_exists($imagePath)) {
                ?>
                <img src="../images/posts-images/<?php echo $rowPost['id_post']; ?>_1.jpg" alt="Post Image"
                  class="img-fluid">
                <?php
              } else {
                ?>
                <img src="../images/posts-images/default.png" alt="Post Image" class="img-fluid">
                <?php
              }
              ?>
            </div>
            <div class="card-title-overlay">
              <h6 class="card-title mb-0">
                <?= htmlspecialchars($rowPost['title_post'], ENT_QUOTES, 'UTF-8') ?>
              </h6>
            </div>
            <!-- End of existing content -->
          </a>
        </div>

      </div>
      <?php
    }
    ?>
  </div>

  <div class="row">
    <p class='lead text-center fw-semibold'>11-классники</p>
    <?php
    // Use prepared statement with parameter binding
    $stmt11 = $connection->prepare("SELECT id_post, url_post, title_post FROM posts WHERE category = ? ORDER BY date_post DESC LIMIT 6");
    $category_id = 1;
    $stmt11->bind_param("i", $category_id);
    $stmt11->execute();
    $result11 = $stmt11->get_result();

    while ($row11 = mysqli_fetch_assoc($result11)) {
      ?>
      <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="card index-card mb-3">
          <a href="/post/<?= $row11['url_post'] ?>" class="text-decoration-none">
            <!-- Rest of your existing content -->
            <div class="card-img-container">
              <?php
              $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/{$row11['id_post']}_1.jpg";

              if (file_exists($imagePath)) {
                ?>
                <img src="../images/posts-images/<?php echo $row11['id_post']; ?>_1.jpg" alt="Post Image" class="img-fluid">
                <?php
              } else {
                ?>
                <img src="../images/posts-images/default.png" alt="Post Image" class="img-fluid">
                <?php
              }
              ?>
            </div>
            <div class="card-title-overlay">
              <h6 class="card-title mb-0">
                <?= htmlspecialchars($row11['title_post'], ENT_QUOTES, 'UTF-8') ?>
              </h6>
            </div>
            <!-- End of existing content -->
          </a>
        </div>

      </div>
      <?php
            }
        }
    } else {
        echo '<div class="col-12"><p class="text-center text-muted">База данных недоступна</p></div>';
    }
    ?>
  </div>

  <div class="row">
    <p class='lead text-center fw-semibold'>Абитуриентам</p>
    <?php
    // Check if database connection exists
    if ($connection && !$connection->connect_error) {
        // Use prepared statement with parameter binding
        $stmtAbiturient = $connection->prepare("SELECT id_post, url_post, title_post FROM posts WHERE category = ? ORDER BY date_post DESC LIMIT 6");
        $category_abitur = 6;
        $stmtAbiturient->bind_param("i", $category_abitur);
        $stmtAbiturient->execute();
        $resultAbiturient = $stmtAbiturient->get_result();
        
        if ($resultAbiturient) {
            while ($rowAbiturient = mysqli_fetch_assoc($resultAbiturient)) {
      ?>
      <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="card index-card mb-3">
          <a href="/post/<?= $rowAbiturient['url_post'] ?>" class="text-decoration-none">
            <!-- Rest of your existing content -->
            <div class="card-img-container">
              <?php
              $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/{$rowAbiturient['id_post']}_1.jpg";

              if (file_exists($imagePath)) {
                ?>
                <img src="../images/posts-images/<?php echo $rowAbiturient['id_post']; ?>_1.jpg" alt="Post Image"
                  class="img-fluid">
                <?php
              } else {
                ?>
                <img src="../images/posts-images/default.png" alt="Post Image" class="img-fluid">
                <?php
              }
              ?>
            </div>
            <div class="card-title-overlay">
              <h6 class="card-title mb-0">
                <?= htmlspecialchars($rowAbiturient['title_post'], ENT_QUOTES, 'UTF-8') ?>
              </h6>
            </div>
            <!-- End of existing content -->
          </a>
        </div>

      </div>
      <?php
            }
        }
    } else {
        echo '<div class="col-12"><p class="text-center text-muted">База данных недоступна</p></div>';
    }
    ?>
  </div>
</div>