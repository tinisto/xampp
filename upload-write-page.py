#!/usr/bin/env python3
"""Upload fixed write page"""

import ftplib

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

write_page = '''<?php
/**
 * Write page using real_template.php
 */

// Section 1: Title
$greyContent1 = '<div style="padding: 30px; text-align: center;"><h1>Написать статью</h1></div>';

// Section 2: Empty
$greyContent2 = '';

// Section 3: Empty  
$greyContent3 = '';

// Section 4: Empty
$greyContent4 = '';

// Section 5: Write form
$greyContent5 = '<div style="padding: 40px; max-width: 800px; margin: 0 auto;">
    <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 30px;">
        <h2 style="margin-bottom: 20px; color: #333;">Создать новую статью</h2>
        
        <form method="post" action="/pages/write/write-process.php" style="display: flex; flex-direction: column; gap: 20px;">
            <div>
                <label for="title" style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Заголовок статьи:</label>
                <input type="text" id="title" name="title" required 
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
            </div>
            
            <div>
                <label for="category" style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Категория:</label>
                <select id="category" name="category" required 
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                    <option value="">Выберите категорию</option>
                    <option value="education">Образование</option>
                    <option value="ege">ЕГЭ</option>
                    <option value="university">ВУЗы</option>
                    <option value="college">СПО</option>
                    <option value="school">Школы</option>
                    <option value="career">Карьера</option>
                </select>
            </div>
            
            <div>
                <label for="content" style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Содержание статьи:</label>
                <textarea id="content" name="content" required rows="15"
                          style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; resize: vertical;"></textarea>
            </div>
            
            <div>
                <label for="tags" style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Теги (через запятую):</label>
                <input type="text" id="tags" name="tags" placeholder="образование, учеба, студенты"
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
            </div>
            
            <div style="display: flex; gap: 15px;">
                <button type="submit" 
                        style="padding: 15px 30px; background: #28a745; color: white; border: none; border-radius: 5px; font-size: 16px; font-weight: 600; cursor: pointer;">
                    Опубликовать статью
                </button>
                <button type="button" onclick="window.history.back()"
                        style="padding: 15px 30px; background: #6c757d; color: white; border: none; border-radius: 5px; font-size: 16px; font-weight: 600; cursor: pointer;">
                    Отмена
                </button>
            </div>
        </form>
        
        <div style="margin-top: 30px; padding: 20px; background: #e3f2fd; border-radius: 8px;">
            <h4 style="color: #1976d2; margin-bottom: 15px;">Правила публикации:</h4>
            <ul style="color: #1976d2; margin: 0; padding-left: 20px;">
                <li>Статья должна быть уникальной и полезной для читателей</li>
                <li>Запрещено размещение рекламного контента без согласования</li>
                <li>Текст должен быть грамотно написан и структурирован</li>
                <li>Все статьи проходят модерацию перед публикацией</li>
            </ul>
        </div>
    </div>
</div>';

// Section 6: Empty
$greyContent6 = '';

// Section 7: No comments
$blueContent = '';

// Page title
$pageTitle = 'Написать статью - 11-классники';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''

try:
    print("Uploading write page...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_PATH)
    
    with open('write-new.php', 'w') as f:
        f.write(write_page)
    
    with open('write-new.php', 'rb') as f:
        ftp.storbinary('STOR write-new.php', f)
    
    print("✓ Fixed write-new.php")
    
    ftp.quit()
    print("\n✅ Write page fixed!")
    print("Test: https://11klassniki.ru/write")
    
except Exception as e:
    print(f"Error: {e}")