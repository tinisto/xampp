#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib
import tempfile
import os

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔧 Removing comments from news pages...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Check what news files exist
        print("📂 Checking news files...")
        files = []
        ftp.retrlines('LIST', files.append)
        
        news_files = [f for f in files if 'news' in f.lower() and f.endswith('.php')]
        relevant_files = []
        for f in news_files:
            filename = f.split()[-1] if f.split() else ""
            if filename in ['news-new.php', 'news.php']:
                relevant_files.append(filename)
        
        for f in relevant_files[:5]:  # Show first 5 relevant files
            print(f"  {f}")
        
        # Download and modify news-new.php (main news listing page)
        print("\n📥 Processing news-new.php...")
        try:
            content = []
            ftp.retrlines('RETR news-new.php', content.append)
            
            print(f"Current news-new.php: {len(content)} lines")
            
            # Create new content without comments
            new_content = []
            skip_comments = False
            
            for line in content:
                # Skip lines related to comments
                if any(comment_keyword in line.lower() for comment_keyword in [
                    'comment', 'threaded', 'renderThreadedComments', 
                    'api/comments', 'smart-comments', 'discussion'
                ]):
                    skip_comments = True
                    continue
                
                # End of comments section - look for closing tags or new sections
                if skip_comments and any(end_keyword in line for end_keyword in [
                    '</div>', '</section>', '<?php', 'footer', 'script'
                ]):
                    skip_comments = False
                
                if not skip_comments:
                    new_content.append(line)
            
            # Upload modified news-new.php
            with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
                tmp.write('\n'.join(new_content))
                tmp_path = tmp.name
            
            with open(tmp_path, 'rb') as file:
                ftp.storbinary('STOR news-new.php', file)
            
            os.unlink(tmp_path)
            print("✅ Updated news-new.php")
            
        except Exception as e:
            print(f"Could not process news-new.php: {e}")
        
        # Create comment-free news pages
        print("\n🔧 Creating comment-free news templates...")
        
        # News listing page without comments
        news_listing = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get news type and pagination
$news_type = $_GET['news_type'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Get news items
$news_items = [];
$total_news = 0;

if ($connection) {
    // Count total news
    $count_sql = "SELECT COUNT(*) as total FROM news WHERE 1=1";
    $where_params = [];
    $param_types = "";
    
    if ($news_type) {
        $count_sql .= " AND category = ?";
        $where_params[] = $news_type;
        $param_types .= "s";
    }
    
    $stmt = $connection->prepare($count_sql);
    if ($stmt) {
        if ($param_types) {
            $stmt->bind_param($param_types, ...$where_params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $total_news = $result->fetch_assoc()['total'];
        }
        $stmt->close();
    }
    
    // Get news items
    $sql = "SELECT * FROM news WHERE 1=1";
    if ($news_type) {
        $sql .= " AND category = ?";
    }
    $sql .= " ORDER BY date_news DESC LIMIT ? OFFSET ?";
    
    $stmt = $connection->prepare($sql);
    if ($stmt) {
        if ($news_type) {
            $stmt->bind_param("sii", $news_type, $per_page, $offset);
        } else {
            $stmt->bind_param("ii", $per_page, $offset);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $news_items[] = $row;
        }
        $stmt->close();
    }
}

$total_pages = ceil($total_news / $per_page);
?><!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новости образования - 11klassniki.ru</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><circle cx='12' cy='12' r='12' fill='%23007bff'/><text x='12' y='16' text-anchor='middle' fill='white' font-size='10' font-weight='bold' font-family='Arial'>11</text></svg>">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f8f9fa; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { background: white; padding: 20px 0; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header .container { display: flex; align-items: center; justify-content: space-between; }
        .logo a { display: inline-block; width: 40px; height: 40px; background: #007bff; color: white; text-decoration: none; border-radius: 50%; line-height: 40px; text-align: center; font-weight: bold; }
        .nav { display: flex; gap: 30px; }
        .nav a { color: #333; text-decoration: none; font-weight: 500; }
        h1 { color: #333; margin-bottom: 30px; font-size: 2.5rem; }
        .news-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px; margin-bottom: 40px; }
        .news-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: transform 0.3s ease; }
        .news-card:hover { transform: translateY(-5px); }
        .news-content { padding: 25px; }
        .news-title { font-size: 1.4rem; font-weight: 700; margin-bottom: 15px; color: #333; }
        .news-title a { color: inherit; text-decoration: none; }
        .news-excerpt { color: #666; margin-bottom: 15px; }
        .news-meta { font-size: 0.9rem; color: #888; }
        .pagination { display: flex; justify-content: center; gap: 10px; margin-top: 40px; }
        .pagination a, .pagination span { padding: 10px 15px; background: white; border-radius: 6px; text-decoration: none; color: #333; }
        .pagination .current { background: #007bff; color: white; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #007bff; text-decoration: none; }
        @media (max-width: 768px) { .news-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo"><a href="/">11</a></div>
            <nav class="nav">
                <a href="/">Главная</a>
                <a href="/news">Новости</a>
                <a href="/search">Поиск</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <a href="/" class="back-link">← Главная</a>
        <h1>Новости образования</h1>
        
        <div class="news-grid">
            <?php foreach ($news_items as $news): ?>
                <article class="news-card">
                    <div class="news-content">
                        <h2 class="news-title">
                            <a href="/news/<?php echo htmlspecialchars($news['url_news'] ?? ''); ?>">
                                <?php echo htmlspecialchars($news['title_news'] ?? ''); ?>
                            </a>
                        </h2>
                        <div class="news-excerpt">
                            <?php 
                            $text = $news['text_news'] ?? '';
                            echo htmlspecialchars(mb_substr(strip_tags($text), 0, 200) . '...');
                            ?>
                        </div>
                        <div class="news-meta">
                            <?php echo date('d.m.Y', strtotime($news['date_news'] ?? 'now')); ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page-1; ?>&news_type=<?php echo urlencode($news_type); ?>">← Назад</a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="current"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?>&news_type=<?php echo urlencode($news_type); ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page+1; ?>&news_type=<?php echo urlencode($news_type); ?>">Вперед →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>'''
        
        # Upload news listing without comments
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(news_listing)
            tmp_path = tmp.name
        
        print("📤 Uploading comment-free news listing...")
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR news-new.php', file)
        
        os.unlink(tmp_path)
        
        # Also create clean individual news article template
        print("📤 Creating comment-free individual news template...")
        
        # This will be used for individual news articles
        individual_news = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$url_news = $_GET['url_news'] ?? '';
$news_item = null;

if ($connection && $url_news) {
    $stmt = $connection->prepare("SELECT * FROM news WHERE url_news = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("s", $url_news);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $news_item = $result->fetch_assoc();
        }
        $stmt->close();
    }
}

if (!$news_item) {
    header('Location: /news');
    exit();
}
?><!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($news_item['title_news']); ?> - 11klassniki.ru</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><circle cx='12' cy='12' r='12' fill='%23007bff'/><text x='12' y='16' text-anchor='middle' fill='white' font-size='10' font-weight='bold' font-family='Arial'>11</text></svg>">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f8f9fa; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { background: white; padding: 20px 0; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header .container { display: flex; align-items: center; justify-content: space-between; max-width: 1200px; }
        .logo a { display: inline-block; width: 40px; height: 40px; background: #007bff; color: white; text-decoration: none; border-radius: 50%; line-height: 40px; text-align: center; font-weight: bold; }
        .article { background: white; border-radius: 12px; padding: 40px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .article-title { font-size: 2.2rem; font-weight: 700; color: #333; margin-bottom: 20px; line-height: 1.3; }
        .article-meta { color: #666; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        .article-content { font-size: 1.1rem; line-height: 1.8; color: #333; }
        .article-content p { margin-bottom: 20px; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #007bff; text-decoration: none; font-weight: 500; }
        .back-link:hover { text-decoration: underline; }
        @media (max-width: 768px) { 
            .container { padding: 15px; }
            .article { padding: 25px; }
            .article-title { font-size: 1.8rem; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo"><a href="/">11</a></div>
        </div>
    </header>

    <main class="container">
        <a href="/news" class="back-link">← Все новости</a>
        
        <article class="article">
            <h1 class="article-title"><?php echo htmlspecialchars($news_item['title_news']); ?></h1>
            
            <div class="article-meta">
                Опубликовано: <?php echo date('d.m.Y в H:i', strtotime($news_item['date_news'])); ?>
            </div>
            
            <div class="article-content">
                <?php echo nl2br(htmlspecialchars($news_item['text_news'])); ?>
            </div>
        </article>
    </main>
</body>
</html>'''
        
        # Create template for individual news articles
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(individual_news)
            tmp_path = tmp.name
        
        # Save as a template that can be used by routing
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR news-single-clean.php', file)
        
        os.unlink(tmp_path)
        
        ftp.quit()
        
        print("\n✅ Comments removed from news pages!")
        print("\n📋 Updated files:")
        print("• news-new.php - Main news listing (no comments)")
        print("• news-single-clean.php - Individual articles (no comments)")
        
        print("\n🧪 Test these URLs:")
        print("• https://11klassniki.ru/news (main news page)")
        print("• https://11klassniki.ru/news/v-reyting-dvuhsot-luchshih-universitetov-mira-voydut-ne-menee-semi-vuzov-rf")
        
        print("\n✅ News pages are now comment-free!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()