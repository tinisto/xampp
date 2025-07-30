#!/bin/bash
# Alternative: FileZilla automation script

echo "Creating FileZilla batch file..."

# Create FileZilla batch file
cat > filezilla-deploy.txt << 'EOF'
# FileZilla batch commands
# Update these with your credentials
open ftp://username:password@ftp.yourdomain.com
cd /public_html

# Upload directories
put -r app app
put -r includes includes  
put -r scripts scripts

# Upload individual files
put js/lazy-loading.js js/lazy-loading.js
put css/lazy-loading.css css/lazy-loading.css
put pages/search/search-content.php pages/search/search-content.php
put pages/search/search-content-secure.php pages/search/search-content-secure.php

# Create directories
mkdir logs
mkdir cache

close
quit
EOF

echo "Batch file created: filezilla-deploy.txt"
echo ""
echo "To use this:"
echo "1. Edit filezilla-deploy.txt and add your FTP credentials"
echo "2. Run: filezilla -s filezilla-deploy.txt"
echo ""
echo "Or use the PHP script by running: php deploy.php"