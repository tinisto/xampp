<?php
// Test VPO/SPO Pages
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';

$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$connection->set_charset("utf8mb4");

// Get sample VPO and SPO URLs
$vpoQuery = "SELECT id_vpo, vpo_name, vpo_url FROM vpo WHERE vpo_url IS NOT NULL AND vpo_url != '' LIMIT 5";
$vpoResult = $connection->query($vpoQuery);

$spoQuery = "SELECT id_spo, spo_name, spo_url FROM spo WHERE spo_url IS NOT NULL AND spo_url != '' LIMIT 5";
$spoResult = $connection->query($spoQuery);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Test VPO/SPO Pages</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .section { margin-bottom: 30px; }
        .links { list-style: none; padding: 0; }
        .links li { margin: 10px 0; }
        .links a { color: #007bff; text-decoration: none; }
        .links a:hover { text-decoration: underline; }
        .test-link { background: #f8f9fa; padding: 10px; border-radius: 5px; margin: 5px 0; }
    </style>
</head>
<body>
    <h1>🧪 VPO/SPO Pages Test</h1>
    
    <div class="section">
        <h2>📚 Test Links</h2>
        <div class="test-link">
            <a href="/pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php?type=vpo" target="_blank">
                📍 All VPO (Universities) by Regions
            </a>
        </div>
        <div class="test-link">
            <a href="/pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php?type=spo" target="_blank">
                📍 All SPO (Colleges) by Regions
            </a>
        </div>
        <div class="test-link">
            <a href="/final_vpo_spo_fix.php" target="_blank">
                🔧 VPO/SPO Fix Status Page
            </a>
        </div>
    </div>
    
    <div class="section">
        <h2>🎓 Sample VPO (Universities) Pages</h2>
        <?php if ($vpoResult && $vpoResult->num_rows > 0): ?>
            <ul class="links">
                <?php while ($vpo = $vpoResult->fetch_assoc()): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($vpo['vpo_name']); ?></strong><br>
                        <a href="/vpo/<?php echo htmlspecialchars($vpo['vpo_url']); ?>" target="_blank">
                            /vpo/<?php echo htmlspecialchars($vpo['vpo_url']); ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No VPO entries found with URLs.</p>
        <?php endif; ?>
    </div>
    
    <div class="section">
        <h2>🏫 Sample SPO (Colleges) Pages</h2>
        <?php if ($spoResult && $spoResult->num_rows > 0): ?>
            <ul class="links">
                <?php while ($spo = $spoResult->fetch_assoc()): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($spo['spo_name']); ?></strong><br>
                        <a href="/spo/<?php echo htmlspecialchars($spo['spo_url']); ?>" target="_blank">
                            /spo/<?php echo htmlspecialchars($spo['spo_url']); ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No SPO entries found with URLs.</p>
        <?php endif; ?>
    </div>
    
    <div class="section">
        <h2>📊 Database Status</h2>
        <?php
        $tables = ['vpo', 'spo', 'universities', 'colleges', 'schools'];
        foreach ($tables as $table) {
            $count = $connection->query("SELECT COUNT(*) as count FROM $table")->fetch_assoc()['count'];
            echo "<p><strong>$table:</strong> $count records</p>";
        }
        ?>
    </div>
    
    <p><a href="/">← Back to Home</a></p>
</body>
</html>
<?php
$connection->close();
?>