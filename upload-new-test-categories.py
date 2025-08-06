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

# Upload updated tests configuration
print("\nUploading updated tests configuration...")
with open('/Applications/XAMPP/xamppfiles/htdocs/pages/tests/tests-main-content.php', 'rb') as f:
    ftp.storbinary('STOR pages/tests/tests-main-content.php', f)
print("✓ Uploaded tests-main-content.php")

ftp.quit()
print("\n✅ NEW TEST CATEGORIES UPLOADED!")
print("\nAdded 15 new tests in 6 categories:")
print("\n📚 Academic (4 new):")
print("   - Литература")
print("   - Информатика") 
print("   - Обществознание")
print("   - Экономика")
print("\n🌏 Languages (3 new):")
print("   - Китайский язык")
print("   - Итальянский язык")
print("   - Японский язык")
print("\n🛠️ Practical Skills (4 new):")
print("   - Финансовая грамотность")
print("   - Управление временем")
print("   - Критическое мышление")
print("   - Учебные навыки")
print("\n🔬 Specialized (4 new):")
print("   - Логика и рассуждения")
print("   - Тест памяти")
print("   - Тест креативности")
print("   - Лидерские качества")
print("\nTotal tests now: 31!")
print("View them at: https://11klassniki.ru/tests")