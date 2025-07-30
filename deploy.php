<?php
// Automated deployment script for iPage

echo "iPage Deployment Script\n";
echo "======================\n\n";

// Configuration
$ftpServer = 'ftp.ipage.com';
$ftpUsername = 'dominos';
$ftpPassword = 'your-ftp-password'; // CHANGE THIS - add your password
$ftpPath = '/public_html/'; // CHANGE THIS if needed

// Files and directories to upload
$deployItems = [
    'app' => 'app',
    'includes' => 'includes',
    'scripts' => 'scripts',
    'js/lazy-loading.js' => 'js/lazy-loading.js',
    'css/lazy-loading.css' => 'css/lazy-loading.css',
    'pages/search/search-content.php' => 'pages/search/search-content.php',
    'pages/search/search-content-secure.php' => 'pages/search/search-content-secure.php',
    'CODING_STANDARDS.md' => 'CODING_STANDARDS.md',
    'IMPROVEMENTS_SUMMARY.md' => 'IMPROVEMENTS_SUMMARY.md'
];

// Connect to FTP
$conn = ftp_connect($ftpServer);
if (!$conn) {
    die("Could not connect to $ftpServer\n");
}

echo "Connected to $ftpServer\n";

// Login
if (!ftp_login($conn, $ftpUsername, $ftpPassword)) {
    die("Could not login\n");
}

echo "Logged in successfully\n";

// Set passive mode
ftp_pasv($conn, true);

// Change to target directory
if (!ftp_chdir($conn, $ftpPath)) {
    die("Could not change to directory $ftpPath\n");
}

// Upload function
function uploadItem($conn, $local, $remote) {
    if (is_dir($local)) {
        // Create directory if it doesn't exist
        @ftp_mkdir($conn, $remote);
        
        // Upload directory contents
        $files = scandir($local);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                uploadItem($conn, "$local/$file", "$remote/$file");
            }
        }
    } else {
        // Upload file
        if (ftp_put($conn, $remote, $local, FTP_BINARY)) {
            echo "Uploaded: $remote\n";
        } else {
            echo "Failed to upload: $remote\n";
        }
    }
}

// Upload all items
foreach ($deployItems as $local => $remote) {
    echo "\nUploading $local...\n";
    uploadItem($conn, $local, $remote);
}

// Create necessary directories
echo "\nCreating directories...\n";
@ftp_mkdir($conn, 'logs');
@ftp_mkdir($conn, 'cache');
@ftp_chmod($conn, 0755, 'logs');
@ftp_chmod($conn, 0755, 'cache');

// Close connection
ftp_close($conn);

echo "\nDeployment complete!\n";
echo "\nNext steps:\n";
echo "1. Test the search functionality\n";
echo "2. Check if forms are working (CSRF protection)\n";
echo "3. Monitor error logs at /logs/errors.log\n";