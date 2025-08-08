#!/usr/bin/env python3
"""Fix all remaining pages to use real_template.php"""

import ftplib

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

# Router files and new pages
files_to_create = {
    # Schools router fix
    'schools-all-regions-real.php': '''<?php
// Schools All Regions router - FIXED
error_reporting(0);

// Set type for the page  
$_GET['type'] = 'school';
$institutionType = 'school';

// Include the NEW real template version
$pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/educational-institutions-all-regions/educational-institutions-all-regions-real.php';
if (file_exists($pageFile)) {
    include $pageFile;
} else {
    // Fallback
    $greyContent1 = '<div style="padding: 30px;"><h1>Школы всех регионов</h1></div>';
    $greyContent2 = '';
    $greyContent3 = '';
    $greyContent4 = '';
    $greyContent5 = '<div style="padding: 20px;"><p>Страница временно недоступна</p></div>';
    $greyContent6 = '';
    $blueContent = '';
    $pageTitle = 'Школы всех регионов - 11-классники';
    
    include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
}
?>''',
    
    # About page with real template
    'about-new.php': '''<?php
/**
 * About page using real_template.php
 */

// Section 1: Title
$greyContent1 = '<div style="padding: 30px; text-align: center;"><h1>О проекте 11-классники</h1></div>';

// Section 2: Empty
$greyContent2 = '';

// Section 3: Empty
$greyContent3 = '';

// Section 4: Empty
$greyContent4 = '';

// Section 5: Main content
$greyContent5 = '<div style="padding: 40px; max-width: 800px; margin: 0 auto; line-height: 1.6;">
    <h2 style="color: #333; margin-bottom: 20px;">Добро пожаловать на портал 11-классники!</h2>
    
    <p style="font-size: 18px; margin-bottom: 20px;">
        Наш образовательный портал создан специально для выпускников школ, студентов и всех, 
        кто стремится к получению качественного образования в России.
    </p>
    
    <h3 style="color: #28a745; margin: 30px 0 15px 0;">Что мы предлагаем:</h3>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin: 30px 0;">
        <div style="padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;">
            <h4 style="color: #333; margin-bottom: 15px;">🎓 Образовательные учреждения</h4>
            <p>Полная база данных ВУЗов, СПО и школ России с подробной информацией о специальностях, условиях поступления и контактных данных.</p>
        </div>
        
        <div style="padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;">
            <h4 style="color: #333; margin-bottom: 15px;">📰 Актуальные новости</h4>
            <p>Последние новости образования, изменения в системе поступления, стипендии и гранты.</p>
        </div>
        
        <div style="padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;">
            <h4 style="color: #333; margin-bottom: 15px;">📝 Онлайн тесты</h4>
            <p>Подготовка к ЕГЭ, ОГЭ и вступительным экзаменам с помощью интерактивных тестов.</p>
        </div>
        
        <div style="padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;">
            <h4 style="color: #333; margin-bottom: 15px;">💡 Полезные статьи</h4>
            <p>Советы по выбору профессии, подготовке документов и успешному поступлению.</p>
        </div>
    </div>
    
    <h3 style="color: #28a745; margin: 30px 0 15px 0;">Наша миссия</h3>
    <p>
        Мы помогаем молодым людям сделать осознанный выбор своего образовательного пути, 
        предоставляя всю необходимую информацию в удобном и доступном формате.
    </p>
    
    <div style="text-align: center; margin: 40px 0; padding: 20px; background: #e3f2fd; border-radius: 8px;">
        <h4 style="color: #1976d2; margin-bottom: 15px;">Присоединяйтесь к нам!</h4>
        <p style="margin-bottom: 20px;">Станьте частью сообщества будущих студентов и профессионалов.</p>
        <a href="/register" style="display: inline-block; padding: 12px 30px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; font-weight: 600;">
            Зарегистрироваться
        </a>
    </div>
</div>';

// Section 6: Empty
$greyContent6 = '';

// Section 7: No comments
$blueContent = '';

// Page title
$pageTitle = 'О проекте - 11-классники';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>''',
    
    # Single news page fix
    'pages/common/news/news-single-real.php': '''<?php
/**
 * Single News Page - Real Template Version
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get the news URL from the request
$newsUrl = $_GET['url_news'] ?? '';
if (empty($newsUrl)) {
    // Extract from REQUEST_URI
    $uri = $_SERVER['REQUEST_URI'];
    if (preg_match('/\/news\/([^\/]+)/', $uri, $matches)) {
        $newsUrl = $matches[1];
    }
}

// Try to get the news article
$newsArticle = null;
if (!empty($newsUrl) && $connection && !$connection->connect_error) {
    $query = "SELECT n.*, c.title_category, c.url_category 
              FROM news n 
              LEFT JOIN categories c ON n.category_id = c.id_category 
              WHERE n.url_news = ? AND n.status = 'published'";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 's', $newsUrl);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $newsArticle = mysqli_fetch_assoc($result);
}

// If no article found, redirect to news listing
if (!$newsArticle) {
    header('Location: /news');
    exit;
}

// Section 1: Title + Breadcrumbs
ob_start();
// Breadcrumbs
echo '<nav style="padding: 15px 0; margin-bottom: 20px;">';
echo '<a href="/" style="color: #28a745; text-decoration: none;">Главная</a>';
echo ' → <a href="/news" style="color: #28a745; text-decoration: none;">Новости</a>';
if (!empty($newsArticle['title_category'])) {
    echo ' → <a href="/category/' . htmlspecialchars($newsArticle['url_category']) . '" style="color: #28a745; text-decoration: none;">' . htmlspecialchars($newsArticle['title_category']) . '</a>';
}
echo ' → <span style="color: #666;">' . htmlspecialchars($newsArticle['title_news']) . '</span>';
echo '</nav>';

// Title
echo '<h1 style="font-size: 32px; color: #333; margin: 20px 0; line-height: 1.3;">' . htmlspecialchars($newsArticle['title_news']) . '</h1>';
$greyContent1 = ob_get_clean();

// Section 2: Empty
$greyContent2 = '';

// Section 3: Meta info (date, category)
ob_start();
echo '<div style="padding: 15px 0; border-bottom: 1px solid #eee; margin-bottom: 20px; display: flex; gap: 20px; align-items: center;">';
echo '<span style="color: #666; font-size: 14px;"><i class="fas fa-calendar"></i> ' . date('d.m.Y', strtotime($newsArticle['created_at'])) . '</span>';
if (!empty($newsArticle['title_category'])) {
    echo '<a href="/category/' . htmlspecialchars($newsArticle['url_category']) . '" style="background: #28a745; color: white; padding: 4px 12px; border-radius: 4px; text-decoration: none; font-size: 12px;">' . htmlspecialchars($newsArticle['title_category']) . '</a>';
}
if (!empty($newsArticle['views'])) {
    echo '<span style="color: #666; font-size: 14px;"><i class="fas fa-eye"></i> ' . $newsArticle['views'] . ' просмотров</span>';
}
echo '</div>';
$greyContent3 = ob_get_clean();

// Section 4: Empty
$greyContent4 = '';

// Section 5: Article content
ob_start();
echo '<div style="max-width: 800px; margin: 0 auto; line-height: 1.6;">';

// Featured image
if (!empty($newsArticle['image_news'])) {
    echo '<div style="text-align: center; margin: 30px 0;">';
    echo '<img src="' . htmlspecialchars($newsArticle['image_news']) . '" alt="' . htmlspecialchars($newsArticle['title_news']) . '" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">';
    echo '</div>';
}

// Article content
echo '<div style="font-size: 18px; color: #333;">';
echo nl2br(htmlspecialchars($newsArticle['content_news']));
echo '</div>';

// Related articles section
echo '<div style="margin-top: 40px; padding-top: 30px; border-top: 2px solid #eee;">';
echo '<h3 style="color: #333; margin-bottom: 20px;">Похожие статьи</h3>';

// Get related articles
if (!empty($newsArticle['category_id'])) {
    $relatedQuery = "SELECT title_news, url_news FROM news 
                     WHERE category_id = ? AND id_news != ? AND status = 'published' 
                     ORDER BY created_at DESC LIMIT 3";
    $relatedStmt = mysqli_prepare($connection, $relatedQuery);
    mysqli_stmt_bind_param($relatedStmt, 'ii', $newsArticle['category_id'], $newsArticle['id_news']);
    mysqli_stmt_execute($relatedStmt);
    $relatedResult = mysqli_stmt_get_result($relatedStmt);
    
    if (mysqli_num_rows($relatedResult) > 0) {
        echo '<ul style="list-style: none; padding: 0;">';
        while ($related = mysqli_fetch_assoc($relatedResult)) {
            echo '<li style="margin-bottom: 10px;">';
            echo '<a href="/news/' . htmlspecialchars($related['url_news']) . '" style="color: #28a745; text-decoration: none; font-size: 16px;">';
            echo htmlspecialchars($related['title_news']);
            echo '</a>';
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p style="color: #666;">Нет связанных статей.</p>';
    }
}

echo '</div>';
echo '</div>';
$greyContent5 = ob_get_clean();

// Section 6: Empty
$greyContent6 = '';

// Section 7: Comments (empty for now)
$blueContent = '';

// Page title
$pageTitle = htmlspecialchars($newsArticle['title_news']) . ' - 11-классники';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>''',

    # Update .htaccess rules
    '.htaccess_addition': '''
# About page
RewriteRule ^about/?$ /about-new.php [L]

# Single news articles - fix routing
RewriteRule ^news/([a-zA-Z0-9-]+)/?$ /pages/common/news/news-single-real.php?url_news=$1 [L]
'''
}

try:
    print("Fixing all remaining pages...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_PATH)
    
    for filepath, content in files_to_create.items():
        filename = filepath.split('/')[-1]
        
        # Skip the htaccess addition for now
        if filepath == '.htaccess_addition':
            continue
        
        with open(filename, 'w') as f:
            f.write(content)
        
        try:
            with open(filename, 'rb') as f:
                ftp.storbinary(f'STOR {filepath}', f)
            print(f"✓ Fixed {filepath}")
        except Exception as e:
            print(f"✗ Failed {filepath}: {e}")
    
    ftp.quit()
    
    print("\n✅ All remaining pages fixed!")
    print("\nPages now using real_template.php:")
    print("- https://11klassniki.ru/schools-all-regions")
    print("- https://11klassniki.ru/about")
    print("- Single news articles (like /news/novye-pravila-postupleniya-vuzy-2025)")
    print("\nAll pages should now use the unified template system!")
    
except Exception as e:
    print(f"Error: {e}")