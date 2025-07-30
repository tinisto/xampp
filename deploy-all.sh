#!/bin/bash
# Complete deployment script - GitHub + iPage

echo "🚀 Starting Full Deployment Process"
echo "==================================="

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# 1. Git Status Check
echo -e "\n${GREEN}1. Checking Git Status${NC}"
git status

# 2. Add all changes
echo -e "\n${GREEN}2. Adding all changes to Git${NC}"
git add -A

# 3. Commit changes
echo -e "\n${GREEN}3. Creating commit${NC}"
echo "Enter commit message (or press Enter for default):"
read commit_message
if [ -z "$commit_message" ]; then
    commit_message="Update website with security improvements and optimizations"
fi

git commit -m "$commit_message

🤖 Generated with Claude Code

Co-Authored-By: Claude <noreply@anthropic.com>"

# 4. Push to GitHub
echo -e "\n${GREEN}4. Pushing to GitHub${NC}"
git push origin main

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ GitHub push successful${NC}"
else
    echo -e "${RED}✗ GitHub push failed${NC}"
    exit 1
fi

# 5. Deploy to iPage
echo -e "\n${GREEN}5. Deploying to iPage${NC}"
echo "Enter your FTP password:"
read -s ftp_password

# Create temporary PHP config
cat > temp_deploy_config.php << EOF
<?php
\$ftpPassword = '$ftp_password';
?>
EOF

# Run deployment
php -r '
require_once "temp_deploy_config.php";
$ftpServer = "ftp.ipage.com";
$ftpUsername = "dominos";
$ftpPath = "/public_html/";

$deployItems = [
    "app" => "app",
    "includes" => "includes",
    "scripts" => "scripts",
    "js/lazy-loading.js" => "js/lazy-loading.js",
    "css/lazy-loading.css" => "css/lazy-loading.css",
    "pages/search/search-content.php" => "pages/search/search-content.php",
    "pages/search/search-content-secure.php" => "pages/search/search-content-secure.php"
];

$conn = ftp_connect($ftpServer);
if (!$conn) die("Could not connect to FTP\n");

if (!ftp_login($conn, $ftpUsername, $ftpPassword)) {
    die("Could not login to FTP\n");
}

echo "Connected to iPage FTP\n";
ftp_pasv($conn, true);

if (!ftp_chdir($conn, $ftpPath)) {
    die("Could not change to directory $ftpPath\n");
}

function uploadItem($conn, $local, $remote) {
    if (is_dir($local)) {
        @ftp_mkdir($conn, $remote);
        $files = scandir($local);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                uploadItem($conn, "$local/$file", "$remote/$file");
            }
        }
    } else {
        if (file_exists($local)) {
            if (ftp_put($conn, $remote, $local, FTP_BINARY)) {
                echo "Uploaded: $remote\n";
            } else {
                echo "Failed: $remote\n";
            }
        }
    }
}

foreach ($deployItems as $local => $remote) {
    echo "Uploading $local...\n";
    uploadItem($conn, $local, $remote);
}

@ftp_mkdir($conn, "logs");
@ftp_mkdir($conn, "cache");

ftp_close($conn);
echo "FTP deployment complete!\n";
'

# Clean up
rm -f temp_deploy_config.php

echo -e "\n${GREEN}✓ Deployment Complete!${NC}"
echo -e "\nDeployed to:"
echo -e "  - GitHub: https://github.com/tinisto/xampp"
echo -e "  - iPage: Your website URL"
echo -e "\nNext steps:"
echo -e "  1. Test the search functionality on live site"
echo -e "  2. Check forms for CSRF protection"
echo -e "  3. Monitor error logs"