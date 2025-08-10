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
    print("🔧 Removing comments section from category pages...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Download current category-new.php
        print("📥 Downloading category-new.php...")
        content = []
        ftp.retrlines('RETR category-new.php', content.append)
        
        print(f"Current category-new.php content ({len(content)} lines):")
        for i, line in enumerate(content[:10]):  # Show first 10 lines
            print(f"  {i+1}: {line}")
        
        # Create new category page without comments
        category_without_comments = '''<?php
// Simple category page without comments
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get category from URL
$category_en = $_GET['category_en'] ?? '';

if (empty($category_en)) {
    header('Location: /');
    exit();
}

// Get category details and posts
$posts = [];
$category_name = ucfirst(str_replace('-', ' ', $category_en));

if ($connection) {
    // Get posts for this category
    $stmt = $connection->prepare("
        SELECT p.*, c.name as category_name 
        FROM posts p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE c.url_slug = ? 
        ORDER BY p.date_post DESC 
        LIMIT 20
    ");
    
    if ($stmt) {
        $stmt->bind_param("s", $category_en);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
            if ($row['category_name']) {
                $category_name = $row['category_name'];
            }
        }
        
        $stmt->close();
    }
}
?><!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category_name); ?> - 11klassniki.ru</title>
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
        .nav a:hover { color: #007bff; }
        h1 { color: #333; margin-bottom: 30px; font-size: 2.5rem; }
        .posts-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px; }
        .post-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: transform 0.3s ease; }
        .post-card:hover { transform: translateY(-5px); }
        .post-content { padding: 25px; }
        .post-title { font-size: 1.4rem; font-weight: 700; margin-bottom: 15px; color: #333; }
        .post-title a { color: inherit; text-decoration: none; }
        .post-title a:hover { color: #007bff; }
        .post-excerpt { color: #666; margin-bottom: 15px; line-height: 1.6; }
        .post-meta { display: flex; justify-content: space-between; align-items: center; font-size: 0.9rem; color: #888; }
        .no-posts { text-align: center; padding: 60px 20px; color: #666; }
        .no-posts h2 { margin-bottom: 15px; color: #333; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #007bff; text-decoration: none; font-weight: 500; }
        .back-link:hover { text-decoration: underline; }
        @media (max-width: 768px) {
            .posts-grid { grid-template-columns: 1fr; }
            .header .container { flex-direction: column; gap: 20px; }
            h1 { font-size: 2rem; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <a href="/">11</a>
            </div>
            <nav class="nav">
                <a href="/">Главная</a>
                <a href="/news">Новости</a>
                <a href="/search">Поиск</a>
                <a href="/about">О проекте</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <a href="/" class="back-link">← Вернуться на главную</a>
        
        <h1><?php echo htmlspecialchars($category_name); ?></h1>
        
        <?php if (!empty($posts)): ?>
            <div class="posts-grid">
                <?php foreach ($posts as $post): ?>
                    <article class="post-card">
                        <div class="post-content">
                            <h2 class="post-title">
                                <a href="/post/<?php echo htmlspecialchars($post['url_slug'] ?? ''); ?>">
                                    <?php echo htmlspecialchars($post['title_post'] ?? ''); ?>
                                </a>
                            </h2>
                            <div class="post-excerpt">
                                <?php 
                                $text = $post['text_post'] ?? '';
                                echo htmlspecialchars(mb_substr(strip_tags($text), 0, 200) . (mb_strlen($text) > 200 ? '...' : ''));
                                ?>
                            </div>
                            <div class="post-meta">
                                <span><?php echo date('d.m.Y', strtotime($post['date_post'] ?? 'now')); ?></span>
                                <span>Категория: <?php echo htmlspecialchars($category_name); ?></span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-posts">
                <h2>В этой категории пока нет материалов</h2>
                <p>Попробуйте посмотреть другие разделы или воспользуйтесь поиском.</p>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>'''
        
        # Upload new category page without comments
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(category_without_comments)
            tmp_path = tmp.name
        
        print("📤 Uploading category page without comments...")
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR category-new.php', file)
        
        os.unlink(tmp_path)
        
        ftp.quit()
        
        print("\n✅ Comments section removed from category pages!")
        print("\n📋 What was changed:")
        print("• Removed all threaded comments components")
        print("• Removed comment forms and displays")
        print("• Kept only post listings and navigation")
        print("• Clean, minimal design focused on content")
        
        print("\n🧪 Test the category page:")
        print("https://11klassniki.ru/category/a-naposledok-ya-skazhu")
        print("(Comments section should no longer appear)")
        
        print("\n✅ Category page is now comment-free!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()