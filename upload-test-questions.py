#!/usr/bin/env python3

import ftplib

# FTP credentials
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

print("Connecting to FTP server...")
ftp = ftplib.FTP()
ftp.connect(HOST, 21)
ftp.login(USER, PASS)
ftp.cwd(PATH)

# Upload History test questions
print("\nUploading History test questions...")
with open('/Applications/XAMPP/xamppfiles/htdocs/pages/tests/history-test/questions.php', 'rb') as f:
    ftp.storbinary('STOR pages/tests/history-test/questions.php', f)
print("✓ Uploaded history-test/questions.php")

# Upload Astronomy test questions
print("\nUploading Astronomy test questions...")
with open('/Applications/XAMPP/xamppfiles/htdocs/pages/tests/astronomy-test/questions.php', 'rb') as f:
    ftp.storbinary('STOR pages/tests/astronomy-test/questions.php', f)
print("✓ Uploaded astronomy-test/questions.php")

# Upload Emotional Intelligence test questions
print("\nUploading Emotional Intelligence test questions...")
with open('/Applications/XAMPP/xamppfiles/htdocs/pages/tests/emotional-intelligence-test/questions.php', 'rb') as f:
    ftp.storbinary('STOR pages/tests/emotional-intelligence-test/questions.php', f)
print("✓ Uploaded emotional-intelligence-test/questions.php")

ftp.quit()
print("\n✅ ALL TEST QUESTIONS UPLOADED!")
print("\nNew Tests Available:")
print("📚 История (30 вопросов) - Russian history from ancient times to modern")
print("🌟 Астрономия (20 вопросов) - Planets, stars, galaxies, space phenomena")
print("🧠 Эмоциональный интеллект (20 вопросов) - Social awareness and emotional regulation")
print("\nAll tests are now live and ready for use!")
print("Test them at: https://11klassniki.ru/tests")