<div class="container">

  <div class="row">

    <p class='lead text-center fw-semibold'>Недавние статьи</p>
    <?php

    $queryPosts = "SELECT * FROM posts ORDER BY date_post DESC LIMIT 6";
    $resultPosts = mysqli_query($connection, $queryPosts);

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
                <?= $rowPost['title_post'] ?>
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
    $query11 = "SELECT * FROM posts WHERE category = 1 ORDER BY date_post DESC LIMIT 6;";
    $result11 = mysqli_query($connection, $query11);

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
                <?= $row11['title_post'] ?>
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
    <p class='lead text-center fw-semibold'>Абитуриентам</p>
    <?php
    $queryAbiturient = "SELECT * FROM posts WHERE category = 6 LIMIT 6;";
    $resultAbiturient = mysqli_query($connection, $queryAbiturient);
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
                <?= $rowAbiturient['title_post'] ?>
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
</div>