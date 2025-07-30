<?php
echo "Enter your iPage FTP password: ";
$password = trim(fgets(STDIN));

$ftpServer = 'ftp.ipage.com';
$ftpUsername = 'franko';
$ftpPath = '/public_html/';

echo "\nConnecting to iPage...\n";

$conn = ftp_connect($ftpServer);
if (!$conn) {
    die("Could not connect to $ftpServer\n");
}

if (!ftp_login($conn, $ftpUsername, $password)) {
    die("Login failed. Please check your password.\n");
}

echo "Connected successfully!\n";
ftp_pasv($conn, true);

// Create a simple test file first
$testFile = 'test-upload.txt';
file_put_contents($testFile, 'Test upload from Claude improvements');

if (ftp_put($conn, $ftpPath . 'test-upload.txt', $testFile, FTP_ASCII)) {
    echo "Test file uploaded successfully!\n";
    echo "\nYour FTP connection is working.\n";
    echo "Now you can use FileZilla to upload the folders:\n";
    echo "- /app\n";
    echo "- /includes\n";
    echo "- /scripts\n";
    echo "- /js/lazy-loading.js\n";
    echo "- /css/lazy-loading.css\n";
} else {
    echo "Upload failed. Check your FTP path.\n";
}

unlink($testFile);
ftp_close($conn);