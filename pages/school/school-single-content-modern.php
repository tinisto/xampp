<?php $row = $additionalData['row']; ?>
<?php 
// Include functions file with proper path
$functionsFile = __DIR__ . '/school-single-functions.php';
if (!file_exists($functionsFile) || !include $functionsFile) {
    // If functions file missing, just continue without it
    // Don't redirect to error
}
?>

<?php 
// Include location info if it exists
$locationFile = $_SERVER['DOCUMENT_ROOT'] . '/includes/location_info.php';
if (file_exists($locationFile)) {
    require_once $locationFile;
}
?>

<style>
  .school-header {
    background: linear-gradient(135deg, #6f42c1 0%, #495057 100%);
    color: white;
    padding: 60px 0 40px;
    margin-bottom: 40px;
  }
  .school-badge {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    color: white;
    padding: 6px 16px;
    border-radius: 25px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 15px;
    border: 1px solid rgba(255,255,255,0.3);
  }
  .school-title {
    font-size: 42px;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 20px;
  }
  .school-meta {
    display: flex;
    align-items: center;
    gap: 20px;
    opacity: 0.9;
    font-size: 14px;
    flex-wrap: wrap;
  }
  .school-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .content-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
  }
  .admin-controls {
    position: absolute;
    top: 20px;
    right: 20px;
    background: rgba(255,255,255,0.2);
    padding: 8px 12px;
    border-radius: 8px;
    transition: all 0.3s ease;
  }
  .admin-controls:hover {
    background: rgba(255,255,255,0.3);
  }
  .image-gallery {
    margin: 30px 0;
  }
  .gallery-image {
    width: 100%;
    height: 250px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    transition: transform 0.3s ease;
  }
  .gallery-image:hover {
    transform: scale(1.02);
  }
  .info-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 30px;
    overflow: hidden;
    transition: transform 0.3s ease;
  }
  .info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
  }
  .info-card-header {
    background: linear-gradient(45deg, #f8f9fa, #e9ecef);
    padding: 20px 25px;
    border-bottom: 1px solid #dee2e6;
  }
  .info-card-title {
    font-size: 20px;
    font-weight: 600;
    color: #333;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .info-card-body {
    padding: 25px;
  }
  .contact-item {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 15px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
    transition: background 0.3s ease;
  }
  .contact-item:hover {
    background: #e9ecef;
  }
  .contact-icon {
    width: 40px;
    height: 40px;
    background: #6f42c1;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .history-text {
    font-size: 16px;
    line-height: 1.8;
    color: #555;
  }
  .academic-programs {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
  }
  .program-item {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    border-left: 4px solid #6f42c1;
    transition: all 0.3s ease;
  }
  .program-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
  }
  @media (max-width: 768px) {
    .school-title {
      font-size: 28px;
    }
    .school-header {
      padding: 40px 0 30px;
    }
    .school-meta {
      flex-direction: column;
      align-items: flex-start;
      gap: 10px;
    }
    .info-card-body {
      padding: 20px;
    }
  }
</style>

<div class="school-header">
  <div class="container">
    <div class="content-wrapper position-relative">
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <div class="admin-controls">
          <a href="/school-edit-form.php?id_school=<?= $row['id_school'] ?>" class="text-white me-2" title="Edit">
            <i class="fas fa-edit"></i>
          </a>
          <span class="text-white" onclick="deleteSchool('<?= $row['id_school'] ?>')" style="cursor: pointer;" title="Delete">
            <i class="fas fa-trash"></i>
          </span>
        </div>
      <?php endif; ?>

      <div class="school-badge">ШКОЛА</div>
      
      <h1 class="school-title"><?= html_entity_decode($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
      
      <div class="school-meta">
        <?php if (!empty($row['city'])): ?>
          <div class="school-meta-item">
            <i class="fas fa-map-marker-alt"></i>
            <span><?= htmlspecialchars($row['city']) ?></span>
          </div>
        <?php endif; ?>
        
        <?php if (!empty($row['updated'])): ?>
          <div class="school-meta-item">
            <i class="fas fa-calendar"></i>
            <span>Обновлено: <?php displayIfNotEmptyDate($row['updated']); ?></span>
          </div>
        <?php endif; ?>
        
        <div class="school-meta-item">
          <i class="fas fa-eye"></i>
          <span><?= number_format($row['view']) ?> просмотров</span>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container">
  <div class="content-wrapper">
    
    <!-- Image Gallery -->
    <?php 
    $hasImages = false;
    for ($i = 1; $i <= 3; $i++) {
      if (!empty($row["image_school_$i"])) {
        $hasImages = true;
        break;
      }
    }
    ?>
    
    <?php if ($hasImages): ?>
      <div class="image-gallery">
        <div class="row">
          <?php for ($i = 1; $i <= 3; $i++) : ?>
            <?php if (!empty($row["image_school_$i"])) : ?>
              <div class="col-lg-4 col-md-6 mb-4">
                <img src="../images/schools-images/<?= htmlspecialchars($row["image_school_$i"]); ?>"
                     class="gallery-image" alt="<?= html_entity_decode($pageTitle, ENT_QUOTES, 'UTF-8') ?> - Изображение <?= $i ?>">
              </div>
            <?php endif; ?>
          <?php endfor; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Main Information -->
    <?php if (!empty($row['text'])): ?>
      <div class="info-card">
        <div class="info-card-header">
          <h2 class="info-card-title">
            <i class="fas fa-info-circle"></i>
            Основная информация
          </h2>
        </div>
        <div class="info-card-body">
          <div class="history-text">
            <?= nl2br(htmlspecialchars($row['text'])) ?>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Contact Information -->
    <div class="info-card">
      <div class="info-card-header">
        <h2 class="info-card-title">
          <i class="fas fa-phone"></i>
          Контактная информация
        </h2>
      </div>
      <div class="info-card-body">
        <div class="row">
          <div class="col-md-6">
            <?php if (!empty($row['address'])): ?>
              <div class="contact-item">
                <div class="contact-icon">
                  <i class="fas fa-map-marker-alt"></i>
                </div>
                <div>
                  <strong>Адрес:</strong><br>
                  <?= htmlspecialchars($row['address']) ?>
                </div>
              </div>
            <?php endif; ?>

            <?php if (!empty($row['phone'])): ?>
              <div class="contact-item">
                <div class="contact-icon">
                  <i class="fas fa-phone"></i>
                </div>
                <div>
                  <strong>Телефон:</strong><br>
                  <a href="tel:<?= htmlspecialchars($row['phone']) ?>"><?= htmlspecialchars($row['phone']) ?></a>
                </div>
              </div>
            <?php endif; ?>

            <?php if (!empty($row['email'])): ?>
              <div class="contact-item">
                <div class="contact-icon">
                  <i class="fas fa-envelope"></i>
                </div>
                <div>
                  <strong>Email:</strong><br>
                  <a href="mailto:<?= htmlspecialchars($row['email']) ?>"><?= htmlspecialchars($row['email']) ?></a>
                </div>
              </div>
            <?php endif; ?>
          </div>
          
          <div class="col-md-6">
            <?php if (!empty($row['website'])): ?>
              <div class="contact-item">
                <div class="contact-icon">
                  <i class="fas fa-globe"></i>
                </div>
                <div>
                  <strong>Веб-сайт:</strong><br>
                  <a href="<?= htmlspecialchars($row['website']) ?>" target="_blank"><?= htmlspecialchars($row['website']) ?></a>
                </div>
              </div>
            <?php endif; ?>

            <?php if (!empty($row['director'])): ?>
              <div class="contact-item">
                <div class="contact-icon">
                  <i class="fas fa-user-tie"></i>
                </div>
                <div>
                  <strong>Директор:</strong><br>
                  <?= htmlspecialchars($row['director']) ?>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Academic Information -->
    <?php if (!empty($row['history']) || !empty($row['programs'])): ?>
      <div class="info-card">
        <div class="info-card-header">
          <h2 class="info-card-title">
            <i class="fas fa-graduation-cap"></i>
            Образовательная деятельность
          </h2>
        </div>
        <div class="info-card-body">
          <?php if (!empty($row['history'])): ?>
            <div class="history-text mb-4">
              <?= nl2br(htmlspecialchars($row['history'])) ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($row['programs'])): ?>
            <h5 class="mb-3">Образовательные программы:</h5>
            <div class="academic-programs">
              <div class="program-item">
                <h6><i class="fas fa-book"></i> Программы обучения</h6>
                <p><?= nl2br(htmlspecialchars($row['programs'])) ?></p>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>

  </div>
</div>

<?php
// Comments section
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/getEntityIdFromURL.php';
$result = getEntityIdFromURL($connection, 'schools');
$id_entity = $result['id_entity'];
$entity_type = $result['entity_type'];

// Check if the user is logged in
if (isset($_SESSION['email']) && isset($_SESSION['avatar'])) {
    include $_SERVER['DOCUMENT_ROOT'] . '/comments/user_comments.php';
}
?>