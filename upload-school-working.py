#!/usr/bin/env python3

import ftplib
import os
import sys

# FTP Configuration
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_PATH = '/11klassnikiru/'

def main():
    print("🏫 School Page Working Version")
    print("=" * 30)
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Create working version
        with open('pages/school/school-single-working.php', 'w') as f:
            f.write('''<?php
// Working version without template engine
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Handle slug-based URLs only
$url_slug = $_GET['url_slug'] ?? null;

if (!$url_slug) {
    header("Location: /404");
    exit();
}

$query = "SELECT s.*, r.region_name, r.region_name_en, t.town_name, t.town_name_en 
          FROM schools s
          LEFT JOIN regions r ON s.region_id = r.region_id
          LEFT JOIN towns t ON s.town_id = t.town_id
          WHERE s.url_slug = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $url_slug);

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: /404");
    exit();
}

$row = $result->fetch_assoc();
$pageTitle = $row['name'] ?? 'Школа';

// Build location info
$locationParts = array_filter([
    $row['town_name'] ?? '',
    $row['region_name'] ?? ''
]);
$locationText = implode(', ', $locationParts);

// Build address
$addressParts = array_filter([
    $row['zip_code'] ?? '',
    $row['street'] ?? ''
]);
$address = implode(', ', $addressParts);

// Update view count
$updateQuery = "UPDATE schools SET view = view + 1 WHERE url_slug = ?";
$updateStmt = $connection->prepare($updateQuery);
$updateStmt->bind_param("s", $url_slug);
$updateStmt->execute();
$updateStmt->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - 11-классники</title>
    <link href="/css/unified-styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
    
    <?php 
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/page-section-header.php';
    renderPageSectionHeader([
        'title' => $pageTitle,
        'showSearch' => false,
        'badge' => $locationText
    ]);
    ?>
    
    <main style="padding: 40px 0;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="display: flex; gap: 30px;">
                <!-- Main content -->
                <div style="flex: 1;">
                    <!-- Contact Information -->
                    <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
                        <h2 style="color: #28a745; margin-bottom: 20px;">Контактная информация</h2>
                        
                        <?php if (!empty($address)): ?>
                            <p style="margin: 10px 0;">
                                <i class="fas fa-map-marker-alt" style="width: 20px; color: #28a745;"></i>
                                <strong>Адрес:</strong> <?= htmlspecialchars($address) ?>
                            </p>
                        <?php endif; ?>
                        
                        <?php if (!empty($row['tel'])): ?>
                            <p style="margin: 10px 0;">
                                <i class="fas fa-phone" style="width: 20px; color: #28a745;"></i>
                                <strong>Телефон:</strong> <?= htmlspecialchars($row['tel']) ?>
                            </p>
                        <?php endif; ?>
                        
                        <?php if (!empty($row['email'])): ?>
                            <p style="margin: 10px 0;">
                                <i class="fas fa-envelope" style="width: 20px; color: #28a745;"></i>
                                <strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($row['email']) ?>" style="color: #28a745;"><?= htmlspecialchars($row['email']) ?></a>
                            </p>
                        <?php endif; ?>
                        
                        <?php if (!empty($row['site'])): ?>
                            <p style="margin: 10px 0;">
                                <i class="fas fa-globe" style="width: 20px; color: #28a745;"></i>
                                <strong>Сайт:</strong> <a href="<?= htmlspecialchars($row['site']) ?>" target="_blank" style="color: #28a745;"><?= htmlspecialchars($row['site']) ?></a>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Administration -->
                    <?php if (!empty($row['director_name'])): ?>
                    <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
                        <h2 style="color: #28a745; margin-bottom: 20px;">Администрация</h2>
                        
                        <p style="margin: 10px 0;">
                            <i class="fas fa-user-tie" style="width: 20px; color: #28a745;"></i>
                            <strong><?= htmlspecialchars($row['director_role'] ?? 'Директор') ?>:</strong> <?= htmlspecialchars($row['director_name']) ?>
                        </p>
                        
                        <?php if (!empty($row['director_phone'])): ?>
                            <p style="margin: 10px 0;">
                                <i class="fas fa-phone" style="width: 20px; color: #28a745;"></i>
                                <strong>Телефон директора:</strong> <?= htmlspecialchars($row['director_phone']) ?>
                            </p>
                        <?php endif; ?>
                        
                        <?php if (!empty($row['director_email'])): ?>
                            <p style="margin: 10px 0;">
                                <i class="fas fa-envelope" style="width: 20px; color: #28a745;"></i>
                                <strong>Email директора:</strong> <a href="mailto:<?= htmlspecialchars($row['director_email']) ?>" style="color: #28a745;"><?= htmlspecialchars($row['director_email']) ?></a>
                            </p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($row['full_name']) && $row['full_name'] !== $row['name']): ?>
                        <!-- Full name -->
                        <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <h2 style="color: #28a745; margin-bottom: 20px;">Полное наименование</h2>
                            <p><?= htmlspecialchars($row['full_name']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <div style="width: 300px;">
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                        <h3 style="margin: 0 0 15px 0; font-size: 18px;">Информация</h3>
                        <p style="margin: 10px 0;">
                            <i class="fas fa-graduation-cap" style="width: 20px; color: #28a745;"></i>
                            Тип: Общеобразовательное учреждение
                        </p>
                        <p style="margin: 10px 0;">
                            <i class="fas fa-eye" style="width: 20px; color: #28a745;"></i>
                            Просмотров: <?= number_format($row['view'] ?? 0) ?>
                        </p>
                    </div>

                    <?php if (!empty($row['region_name_en'])): ?>
                        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                            <h3 style="margin: 0 0 15px 0; font-size: 18px;">Другие школы региона</h3>
                            <a href="/schools-in-region/<?= htmlspecialchars($row['region_name_en']) ?>" style="color: #28a745; text-decoration: none;">
                                <i class="fas fa-arrow-right"></i> Все школы в регионе <?= htmlspecialchars($row['region_name']) ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
    
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php'; ?>
</body>
</html>
<?php
$stmt->close();
$connection->close();
?>''')
        
        print("✓ Created working version")
        
        # Upload working version
        with open('pages/school/school-single-working.php', 'rb') as f:
            ftp.storbinary('STOR pages/school/school-single.php', f)
        print("✓ Uploaded working version")
        
        ftp.quit()
        
        print("=" * 30)
        print("✅ Working version active!")
        print("📝 Using direct HTML output")
        print("🔗 Test: https://11klassniki.ru/school/ilezskaya-ileza")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()