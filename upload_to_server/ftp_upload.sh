#\!/bin/bash
# FTP Upload Script
HOST="ftp.ipage.com"
USER="franko"
PASS="JyvR\!HK2E\!N55Zt"

# Using ftp command directly
ftp -inv $HOST << EOF
user $USER $PASS
cd /11klassnikiru
put pages/registration/registration_form.php pages/registration/registration_form.php
put pages/logout/logout.php pages/logout/logout.php
put pages/account/personal-data-change/personal-data-change.php pages/account/personal-data-change/personal-data-change.php
put pages/account/password-change/password-change.php pages/account/password-change/password-change.php
put pages/account/avatar/avatar.php pages/account/avatar/avatar.php
put pages/account/comments-user/comments-user.php pages/account/comments-user/comments-user.php
put pages/account/news-user/news-user.php pages/account/news-user/news-user.php
bye
EOF

