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
    print("🔧 Fixing category page header and footer...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Download the original category-working.php to see proper structure
        print("📥 Checking existing category template structure...")
        try:
            working_content = []
            ftp.retrlines('RETR category-working.php', working_content.append)
            print(f"Found category-working.php with {len(working_content)} lines")
        except:
            working_content = []
        
        # Create category page that uses proper template structure
        print("🔧 Creating category page with proper template structure...")
        
        proper_category = '''<?php
// Enhanced category page with proper template structure
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$category_en = $_GET['category_en'] ?? '';
if (empty($category_en)) {
    header('Location: /');
    exit();
}

$posts = [];
$category_name = '';
$category_id = null;

if ($connection) {
    // Get category details
    $stmt = $connection->prepare("SELECT id, name FROM categories WHERE url_slug = ? OR name_en = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("ss", $category_en, $category_en);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $category = $result->fetch_assoc();
            $category_id = $category['id'];
            $category_name = $category['name'];
        }
        $stmt->close();
    }
    
    // Set proper name
    if ($category_en === 'abiturientam' || $category_en === 'abiturientam') {
        $category_name = 'Абитуриентам';
    } elseif (empty($category_name)) {
        $category_name = ucfirst(str_replace('-', ' ', $category_en));
    }
    
    // Get posts for this category
    $all_posts = [];
    
    // Search posts table
    if ($category_id) {
        $stmt = $connection->prepare("SELECT 'post' as type, id, title_post as title, text_post as text, url_slug, date_post as date_created FROM posts WHERE category_id = ? ORDER BY date_post DESC LIMIT 15");
        if ($stmt) {
            $stmt->bind_param("i", $category_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $all_posts[] = $row;
            }
            $stmt->close();
        }
    }
    
    // Search news table for relevant content
    if ($category_en === 'abiturientam') {
        $keywords = ['абитуриент', 'поступление', 'вуз', 'университет', 'колледж', 'егэ', 'экзамен'];
        foreach ($keywords as $keyword) {
            $stmt = $connection->prepare("SELECT 'news' as type, id, title_news as title, text_news as text, url_news as url_slug, date_news as date_created FROM news WHERE title_news LIKE ? OR text_news LIKE ? ORDER BY date_news DESC LIMIT 3");
            if ($stmt) {
                $search = '%' . $keyword . '%';
                $stmt->bind_param("ss", $search, $search);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $all_posts[] = $row;
                }
                $stmt->close();
            }
        }
    }
    
    // Remove duplicates and sort
    $seen = [];
    foreach ($all_posts as $post) {
        $key = $post['type'] . '_' . $post['id'];
        if (!isset($seen[$key])) {
            $seen[$key] = true;
            $posts[] = $post;
        }
    }
    
    // Sort by date
    usort($posts, function($a, $b) {
        return strtotime($b['date_created']) - strtotime($a['date_created']);
    });
    
    $posts = array_slice($posts, 0, 12);
}

// Include header
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_header.php';
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($category_name); ?></li>
        </ol>
    </nav>
    
    <div class="row">
        <div class="col-lg-12">
            <h1 class="h2 mb-4"><?php echo htmlspecialchars($category_name); ?></h1>
            
            <?php if (!empty($posts)): ?>
                <div class="alert alert-info">
                    <strong><?php echo count($posts); ?> материалов</strong> найдено в категории "<?php echo htmlspecialchars($category_name); ?>"
                </div>
                
                <div class="row">
                    <?php foreach ($posts as $post): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header">
                                    <span class="badge <?php echo $post['type'] === 'news' ? 'badge-success' : 'badge-primary'; ?>">
                                        <?php echo $post['type'] === 'news' ? 'Новость' : 'Статья'; ?>
                                    </span>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <?php
                                        $url = $post['type'] === 'news' ? '/news/' . $post['url_slug'] : '/post/' . $post['url_slug'];
                                        ?>
                                        <a href="<?php echo $url; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($post['title']); ?>
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted">
                                        <?php 
                                        $text = $post['text'] ?? '';
                                        echo htmlspecialchars(mb_substr(strip_tags($text), 0, 150));
                                        if (mb_strlen($text) > 150) echo '...';
                                        ?>
                                    </p>
                                </div>
                                <div class="card-footer text-muted">
                                    <small><?php echo date('d.m.Y', strtotime($post['date_created'])); ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <h4>Материалы не найдены</h4>
                    <p>В данной категории пока нет опубликованных материалов. Попробуйте:</p>
                    <ul>
                        <li><a href="/news">Посмотреть последние новости</a></li>
                        <li><a href="/search">Воспользоваться поиском</a></li>
                        <li><a href="/">Вернуться на главную страницу</a></li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Include footer
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_footer.php';
?>'''
        
        # Upload the properly structured category page
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(proper_category)
            tmp_path = tmp.name
        
        print("📤 Uploading category page with proper template structure...")
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR category-new.php', file)
        
        os.unlink(tmp_path)
        
        ftp.quit()
        
        print("\n✅ Category page template fixed!")
        print("\n📋 Improvements:")
        print("• Uses proper site header (real_header.php)")
        print("• Uses proper site footer (real_footer.php)")
        print("• Bootstrap-based responsive design")
        print("• Breadcrumb navigation")
        print("• Card-based post layout")
        print("• Enhanced content detection for 'abiturientam'")
        print("• Proper error handling and fallbacks")
        
        print("\n🧪 Test the fixed category:")
        print("https://11klassniki.ru/category/abiturientam")
        print("(Should now have proper header, footer, and site styling)")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()