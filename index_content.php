<div class="container">
  <style>
    /* Fix for dark theme card layout */
    .index-card {
      height: 220px;
      background: transparent;
      border: 1px solid var(--border-color);
      border-radius: 12px;
      overflow: hidden;
      transition: all 0.3s;
      display: flex;
      flex-direction: column;
    }
    .index-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      border-color: var(--accent-primary, #667eea);
    }
    /* Remove conflicting styles */
    .card-img-container {
      height: 150px;
      overflow: hidden;
      position: relative;
    }
    .card-img-container img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .card-category-badge {
      position: absolute;
      top: 10px;
      left: 10px;
      background: rgba(102, 126, 234, 0.9);
      color: white;
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 500;
      backdrop-filter: blur(10px);
    }
    .card-title-overlay {
      padding: 0.75rem;
      background: rgba(0,0,0,0.8) !important;
      flex-grow: 1;
      display: flex;
      align-items: center;
    }
    .card-title {
      color: white !important;
      font-size: 0.9rem;
      margin: 0;
      overflow: hidden;
      text-overflow: ellipsis;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      line-height: 1.4;
    }
    
    /* Ensure Bootstrap grid works properly */
    .row {
      display: flex !important;
      flex-wrap: wrap !important;
      margin-right: -0.75rem !important;
      margin-left: -0.75rem !important;
    }
    
    .row > * {
      flex-shrink: 0 !important;
      width: 100% !important;
      max-width: 100% !important;
      padding-right: 0.75rem !important;
      padding-left: 0.75rem !important;
    }
    
    @media (min-width: 576px) {
      .col-sm-6 {
        flex: 0 0 auto !important;
        width: 50% !important;
      }
    }
    
    @media (min-width: 768px) {
      .col-md-6 {
        flex: 0 0 auto !important;
        width: 50% !important;
      }
    }
    
    @media (min-width: 992px) {
      .col-lg-3 {
        flex: 0 0 auto !important;
        width: 25% !important;
      }
    }
  </style>

  <div class="row">
    <?php
    // Check if database connection exists
    if ($connection && !$connection->connect_error) {
        // Get all posts with category names
        $stmt = $connection->prepare("
            SELECT p.id_post, p.url_post, p.title_post, c.title_category 
            FROM posts p
            LEFT JOIN categories c ON p.category = c.id_category
            ORDER BY p.date_post DESC 
            LIMIT 24
        ");
        $stmt->execute();
        $resultPosts = $stmt->get_result();
        
        if ($resultPosts) {
            while ($rowPost = mysqli_fetch_assoc($resultPosts)) {
      ?>
      <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <a href="/post/<?php echo htmlspecialchars($rowPost['url_post']); ?>" class="text-decoration-none d-block">
          <div class="card index-card h-100">
            <div class="card-img-container">
              <?php if (!empty($rowPost['title_category'])): ?>
                <span class="card-category-badge"><?php echo htmlspecialchars($rowPost['title_category'], ENT_QUOTES, 'UTF-8'); ?></span>
              <?php endif; ?>
              <?php
              $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/{$rowPost['id_post']}_1.jpg";

              if (file_exists($imagePath)) {
                ?>
                <img src="images/posts-images/<?php echo $rowPost['id_post']; ?>_1.jpg" alt="<?php echo htmlspecialchars($rowPost['title_post']); ?>" class="img-fluid">
                <?php
              } else {
                ?>
                <img src="images/posts-images/default.png" alt="Default Image" class="img-fluid">
                <?php
              }
              ?>
            </div>
            <div class="card-title-overlay">
              <h6 class="card-title mb-0">
                <?php echo htmlspecialchars($rowPost['title_post'], ENT_QUOTES, 'UTF-8'); ?>
              </h6>
            </div>
          </div>
        </a>
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