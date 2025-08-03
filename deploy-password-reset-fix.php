<?php
// Deployment script for password reset fixes
$ftpServer = "ftp.ipage.com";
$ftpUsername = "franko";

echo "🚀 Deploying Password Reset Security Fixes\n";
echo "=========================================\n";

// Files to deploy
$deployItems = [
    // Modified files
    "common-components/template-engine-ultimate.php" => "common-components/template-engine-ultimate.php",
    "forgot-password-standalone.php" => "forgot-password-standalone.php", 
    "login-modern.php" => "login-modern.php",
    "pages/account/reset-password/reset-password-confirm-content.php" => "pages/account/reset-password/reset-password-confirm-content.php",
    "pages/account/reset-password/reset-password-confirm-process.php" => "pages/account/reset-password/reset-password-confirm-process.php",
    "reset-password.php" => "reset-password.php",
    
    // New files
    "css/site-logo.css" => "css/site-logo.css",
    "includes/components/site-logo.php" => "includes/components/site-logo.php"
];

echo "Enter your FTP password: ";
$ftpPassword = trim(fgets(STDIN));

// Connect to FTP
$conn = ftp_connect($ftpServer);
if (!$conn) {
    die("❌ Could not connect to FTP server\n");
}

if (!ftp_login($conn, $ftpUsername, $ftpPassword)) {
    die("❌ Could not login to FTP\n");
}

echo "✅ Connected to iPage FTP\n";
ftp_pasv($conn, true);

// Change to 11klassnikiru directory
if (!ftp_chdir($conn, "/11klassnikiru")) {
    die("❌ Could not change to 11klassnikiru directory\n");
}

// Upload function
function uploadFile($conn, $localFile, $remoteFile) {
    // Create directory if needed
    $remoteDir = dirname($remoteFile);
    if ($remoteDir !== '.' && $remoteDir !== '/') {
        // Create directory structure
        $parts = explode('/', $remoteDir);
        $path = '';
        foreach ($parts as $part) {
            if ($part) {
                $path .= '/' . $part;
                @ftp_mkdir($conn, $path);
            }
        }
    }
    
    if (!file_exists($localFile)) {
        echo "⚠️  Local file not found: $localFile\n";
        return false;
    }
    
    if (ftp_put($conn, $remoteFile, $localFile, FTP_BINARY)) {
        echo "✅ Uploaded: $remoteFile\n";
        return true;
    } else {
        echo "❌ Failed to upload: $remoteFile\n";
        return false;
    }
}

// Upload all files
$successCount = 0;
$totalFiles = count($deployItems);

foreach ($deployItems as $local => $remote) {
    if (uploadFile($conn, $local, $remote)) {
        $successCount++;
    }
}

ftp_close($conn);

echo "\n📊 Deployment Summary:\n";
echo "   - Total files: $totalFiles\n";
echo "   - Successfully uploaded: $successCount\n";
echo "   - Failed: " . ($totalFiles - $successCount) . "\n";

if ($successCount === $totalFiles) {
    echo "\n✅ All files deployed successfully!\n";
    echo "\n🔒 Security improvements deployed:\n";
    echo "   - Strong password validation (8+ chars, upper/lower/digit/special)\n";
    echo "   - Consistent site branding with reusable logo component\n";
    echo "   - Modern UI design for password reset\n";
} else {
    echo "\n⚠️  Some files failed to upload. Please check the errors above.\n";
}

echo "\n🌐 Test the changes at:\n";
echo "   - https://11klassniki.ru/login\n";
echo "   - https://11klassniki.ru/forgot-password-standalone.php\n";
echo "   - https://11klassniki.ru/reset-password?token=YOUR_TOKEN\n";
?>