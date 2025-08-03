<?php
// Initialize region_id and type from additionalData or set to null
$region_id = isset($additionalData['region_id']) ? (int) $additionalData['region_id'] : null;
$type = isset($additionalData['type']) ? $additionalData['type'] : 'spo';

// Include necessary files
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/pagination.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/redirectToErrorPage.php";
include 'outputEducationalInstitutions.php';
include 'function-query.php';
include 'outputTowns.php';

// Constants
$institutionsPerPage = 24; // Institutions per page
$currentPage = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1; // Current page
$pageOffset = max(0, ($currentPage - 1) * $institutionsPerPage); // Pagination offset

// Fetch institutions data
$institutions_result = getInstitutions($connection, $region_id, $type, $pageOffset, $institutionsPerPage);

// Fetch region data
$query_regions = "SELECT * FROM regions WHERE id_region = ?";
$stmt_regions = $connection->prepare($query_regions);
$stmt_regions->bind_param("i", $region_id);
$stmt_regions->execute();
$result_regions = $stmt_regions->get_result();
$myrow_region = $result_regions->fetch_assoc();
$stmt_regions->close();

if ($myrow_region) {
  // Set the region_name_en variable
  $region_name_en = $myrow_region['region_name_en'];

  // Fetch total institutions count for pagination
  $totalInstitutions_sql = "SELECT COUNT(*) AS total FROM $type WHERE id_region = ?";
  $stmt_total = $connection->prepare($totalInstitutions_sql);
  $stmt_total->bind_param("i", $region_id);
  $stmt_total->execute();
  $totalInstitutions_result = $stmt_total->get_result();
  $totalInstitutions = $totalInstitutions_result->fetch_assoc()['total'];
  $stmt_total->close();

  // Get type titles
  $typeTitle = '';
  $typeBadge = '';
  switch ($type) {
      case 'schools':
          $typeTitle = 'Школы';
          $typeBadge = 'Школы';
          break;
      case 'spo':
          $typeTitle = 'Колледжи / Техникумы';
          $typeBadge = 'ССУЗы';
          break;
      case 'vpo':
          $typeTitle = 'Высшие учебные заведения';
          $typeBadge = 'ВУЗы';
          break;
  }
?>

<style>
    .institutions-hero {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 40px 0;
        margin-bottom: 40px;
        text-align: center;
    }
    .institutions-hero h1 {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    .institutions-stats {
        font-size: 16px;
        opacity: 0.9;
        margin-top: 10px;
    }
    .institutions-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    .institution-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .institution-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }
    .institution-image-container {
        position: relative;
        width: 100%;
        height: 160px;
        background: linear-gradient(135deg, #f0f0f0, #e0e0e0);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .institution-image-placeholder {
        color: #999;
        font-size: 16px;
        text-align: center;
    }
    .institution-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: rgba(40, 167, 69, 0.9);
        color: white;
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        z-index: 2;
    }
    .institution-content {
        padding: 25px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .institution-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
        line-height: 1.4;
        flex: 1;
    }
    .institution-title a {
        color: #333;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    .institution-title a:hover {
        color: #28a745;
    }
    .institution-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        color: #666;
        margin-top: auto;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }
    .institution-location {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .institutions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
        margin-bottom: 50px;
    }
    .sidebar {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .sidebar h5 {
        color: #28a745;
        font-weight: 600;
        margin-bottom: 20px;
        font-size: 18px;
    }
    .sidebar ul {
        list-style: none;
        padding: 0;
    }
    .sidebar li {
        margin-bottom: 10px;
    }
    .sidebar a {
        color: #666;
        text-decoration: none;
        transition: all 0.3s ease;
        padding: 5px 0;
        display: block;
    }
    .sidebar a:hover {
        color: #28a745;
        padding-left: 10px;
    }
    .pagination-container {
        display: flex;
        justify-content: center;
        margin: 50px 0;
    }
    .no-institutions {
        text-align: center;
        padding: 100px 20px;
        color: #666;
    }
    .no-institutions i {
        font-size: 80px;
        color: #ddd;
        margin-bottom: 20px;
    }
    @media (max-width: 768px) {
        .institutions-hero {
            padding: 30px 0;
        }
        .institutions-hero h1 {
            font-size: 26px;
        }
        .institutions-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        .institution-content {
            padding: 20px;
        }
        .sidebar {
            padding: 20px;
        }
    }
</style>

<div class="institutions-hero">
    <div class="container">
        <h1><?= $typeTitle ?> <?= htmlspecialchars($myrow_region['region_name_rod']) ?></h1>
        <div class="institutions-stats"><?= $totalInstitutions ?> <?php 
            $lastDigit = $totalInstitutions % 10;
            $lastTwoDigits = $totalInstitutions % 100;
            if ($type === 'schools') {
                if ($lastTwoDigits >= 11 && $lastTwoDigits <= 14) {
                    echo 'школ';
                } elseif ($lastDigit == 1) {
                    echo 'школа';
                } elseif ($lastDigit >= 2 && $lastDigit <= 4) {
                    echo 'школы';
                } else {
                    echo 'школ';
                }
            } else {
                if ($lastTwoDigits >= 11 && $lastTwoDigits <= 14) {
                    echo 'учреждений';
                } elseif ($lastDigit == 1) {
                    echo 'учреждение';
                } elseif ($lastDigit >= 2 && $lastDigit <= 4) {
                    echo 'учреждения';
                } else {
                    echo 'учреждений';
                }
            }
        ?></div>
    </div>
</div>

<div class="institutions-container">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-9">
            <?php if ($institutions_result && mysqli_num_rows($institutions_result) > 0): ?>
                <div class="institutions-grid">
                    <?php while ($institution = mysqli_fetch_assoc($institutions_result)): 
                        // Determine institution data based on type
                        switch ($type) {
                            case 'schools':
                                $name = $institution['school_name'];
                                $url = '/school/' . $institution['id_school'];
                                $location = $institution['town_name'] ?? 'Не указан';
                                break;
                            case 'spo':
                                $name = $institution['spo_name'];
                                $url = '/spo/' . $institution['spo_url'];
                                $location = $institution['town_name'] ?? 'Не указан';
                                break;
                            case 'vpo':
                                $name = $institution['vpo_name'];
                                $url = '/vpo/' . $institution['vpo_url'];
                                $location = $institution['town_name'] ?? 'Не указан';
                                break;
                        }
                    ?>
                    <article class="institution-card">
                        <div class="institution-image-container">
                            <div class="institution-image-placeholder">
                                <i class="fas fa-graduation-cap"></i><br>
                                <?= $typeBadge ?>
                            </div>
                            <div class="institution-badge"><?= $typeBadge ?></div>
                        </div>
                        <div class="institution-content">
                            <h3 class="institution-title">
                                <a href="<?= $url ?>"><?= html_entity_decode($name, ENT_QUOTES, 'UTF-8') ?></a>
                            </h3>
                            <div class="institution-meta">
                                <div class="institution-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?= htmlspecialchars($location) ?></span>
                                </div>
                            </div>
                        </div>
                    </article>
                    <?php endwhile; ?>
                </div>

                <?php if ($totalInstitutions > $institutionsPerPage): ?>
                    <?php $totalPages = ceil($totalInstitutions / $institutionsPerPage); ?>
                    <div class="pagination-container">
                        <?php generatePagination($currentPage, $totalPages, $region_id); ?>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="no-institutions">
                    <i class="fas fa-graduation-cap"></i>
                    <h3>Учреждений пока нет</h3>
                    <p>В этом регионе пока нет добавленных учреждений данного типа</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="sidebar">
                <h5>Города региона</h5>
                <?php
                // Fetch towns with institutions in the region
                $query_towns = "
                SELECT DISTINCT t.*
                FROM towns t
                JOIN $type s ON t.id_town = s.id_town
                WHERE t.id_region = ?
                ORDER BY t.town_name
                ";
                $stmt_towns = $connection->prepare($query_towns);
                $stmt_towns->bind_param("i", $region_id);
                $stmt_towns->execute();
                $result_towns = $stmt_towns->get_result();
                $stmt_towns->close();

                if (mysqli_num_rows($result_towns) > 0): ?>
                    <ul>
                        <?php while ($town = mysqli_fetch_assoc($result_towns)): ?>
                            <li>
                                <a href="/<?= $type ?>-in-town/<?= $region_name_en ?>/<?= $town['url_slug_town'] ?>">
                                    <?= htmlspecialchars($town['town_name']) ?>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p style="color: #666;">Нет доступных городов</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
} else {
    header("Location: /404");
    exit();
}
?>