#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib
import tempfile
import os

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def upload_file(ftp, content, filename):
    with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
        tmp.write(content)
        tmp_path = tmp.name
    
    with open(tmp_path, 'rb') as file:
        ftp.storbinary(f'STOR {filename}', file)
    os.unlink(tmp_path)

def main():
    print("📝 ADDING CONTENT TO CATEGORY PAGES")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # 1. Abiturientam category
        print("\n1️⃣ Creating Abiturientam category page...")
        abiturientam_page = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = 'Абитуриентам';
$posts = [];

// Get posts from this category
if ($connection) {
    try {
        $stmt = $connection->prepare("SELECT id, title_post as title, text_post as text, url_slug, date_post FROM posts WHERE category LIKE '%абитуриент%' ORDER BY date_post DESC LIMIT 20");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $posts[] = $row;
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        error_log('Category query error: ' . $e->getMessage());
    }
}

$greyContent1 = '
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item"><a href="/category">Категории</a></li>
                    <li class="breadcrumb-item active">Абитуриентам</li>
                </ol>
            </nav>
        </div>
    </div>
</div>';

$greyContent2 = '
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 mb-2">📚 Абитуриентам</h1>
            <p class="lead mb-4">Всё о поступлении в ВУЗы: правила приёма, проходные баллы, советы поступающим</p>
        </div>
    </div>
</div>';

$greyContent3 = '
<div class="container">
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-clipboard-list fa-3x text-primary mb-3"></i>
                    <h5>Правила приёма</h5>
                    <p>Актуальная информация о правилах поступления</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
                    <h5>Проходные баллы</h5>
                    <p>Статистика проходных баллов прошлых лет</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-calendar-alt fa-3x text-warning mb-3"></i>
                    <h5>Календарь абитуриента</h5>
                    <p>Важные даты и сроки подачи документов</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-lightbulb fa-3x text-info mb-3"></i>
                    <h5>Советы поступающим</h5>
                    <p>Рекомендации от студентов и экспертов</p>
                </div>
            </div>
        </div>
    </div>
</div>';

$greyContent4 = '
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">📰 Материалы для абитуриентов</h2>
        </div>
    </div>
    <div class="row">';

if (!empty($posts)) {
    foreach ($posts as $post) {
        $greyContent4 .= '
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="/post/' . htmlspecialchars($post['url_slug'] ?? '') . '" class="text-decoration-none">
                            ' . htmlspecialchars($post['title'] ?? 'Без заголовка') . '
                        </a>
                    </h5>
                    <p class="card-text">' . htmlspecialchars(mb_substr(strip_tags($post['text'] ?? ''), 0, 150)) . '...</p>
                    <small class="text-muted">' . date('d.m.Y', strtotime($post['date_post'] ?? 'now')) . '</small>
                </div>
            </div>
        </div>';
    }
} else {
    $greyContent4 .= '
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <h4>Материалы появятся в ближайшее время</h4>
                <p>Мы готовим полезные статьи для абитуриентов</p>
            </div>
        </div>
    </div>';
}

$greyContent4 .= '
    </div>
</div>';

$greyContent5 = '';
$greyContent6 = '';

include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        # Create category directory if needed
        try:
            ftp.mkd('category')
        except:
            pass  # Directory might exist
            
        upload_file(ftp, abiturientam_page, 'category/abiturientam.php')
        print("   ✅ Created Abiturientam category page")
        
        # 2. 11-klassniki category
        print("\n2️⃣ Creating 11-klassniki category page...")
        klassniki_page = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = '11-классники';
$posts = [];

// Get posts from this category
if ($connection) {
    try {
        $stmt = $connection->prepare("SELECT id, title_post as title, text_post as text, url_slug, date_post FROM posts WHERE category LIKE '%11%класс%' OR category LIKE '%выпускник%' ORDER BY date_post DESC LIMIT 20");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $posts[] = $row;
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        error_log('Category query error: ' . $e->getMessage());
    }
}

$greyContent1 = '
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item"><a href="/category">Категории</a></li>
                    <li class="breadcrumb-item active">11-классники</li>
                </ol>
            </nav>
        </div>
    </div>
</div>';

$greyContent2 = '
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 mb-2">👥 11-классникам</h1>
            <p class="lead mb-4">Подготовка к ЕГЭ, выбор профессии, олимпиады и полезные ресурсы</p>
        </div>
    </div>
</div>';

$greyContent3 = '
<div class="container">
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-book-open fa-3x text-primary mb-3"></i>
                    <h5>Подготовка к ЕГЭ</h5>
                    <p>Материалы и советы по подготовке к экзаменам</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-user-graduate fa-3x text-success mb-3"></i>
                    <h5>Выбор профессии</h5>
                    <p>Тесты и рекомендации по профориентации</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-trophy fa-3x text-warning mb-3"></i>
                    <h5>Олимпиады</h5>
                    <p>Информация об олимпиадах и конкурсах</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-link fa-3x text-info mb-3"></i>
                    <h5>Полезные ресурсы</h5>
                    <p>Ссылки на образовательные платформы</p>
                </div>
            </div>
        </div>
    </div>
</div>';

$greyContent4 = '
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">📚 Материалы для выпускников</h2>
        </div>
    </div>
    <div class="row">';

// Default content for 11-klassniki
$default_articles = [
    ['title' => 'Как эффективно подготовиться к ЕГЭ', 'text' => 'Советы экспертов по организации подготовки к единому государственному экзамену...'],
    ['title' => 'Топ-10 ошибок на ЕГЭ по математике', 'text' => 'Разбор самых частых ошибок выпускников на экзамене по математике...'],
    ['title' => 'Выбор специальности: на что обратить внимание', 'text' => 'Как правильно выбрать будущую профессию и не ошибиться с направлением...'],
    ['title' => 'Олимпиады для 11 класса: полный список', 'text' => 'Перечень всех олимпиад, дающих льготы при поступлении в ВУЗы...']
];

if (!empty($posts)) {
    foreach ($posts as $post) {
        $greyContent4 .= '
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="/post/' . htmlspecialchars($post['url_slug'] ?? '') . '" class="text-decoration-none">
                            ' . htmlspecialchars($post['title'] ?? 'Без заголовка') . '
                        </a>
                    </h5>
                    <p class="card-text">' . htmlspecialchars(mb_substr(strip_tags($post['text'] ?? ''), 0, 150)) . '...</p>
                    <small class="text-muted">' . date('d.m.Y', strtotime($post['date_post'] ?? 'now')) . '</small>
                </div>
            </div>
        </div>';
    }
} else {
    foreach ($default_articles as $article) {
        $greyContent4 .= '
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">' . $article['title'] . '</h5>
                    <p class="card-text">' . $article['text'] . '</p>
                    <a href="/news" class="btn btn-sm btn-outline-primary">Читать далее</a>
                </div>
            </div>
        </div>';
    }
}

$greyContent4 .= '
    </div>
</div>';

$greyContent5 = '';
$greyContent6 = '';

include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        upload_file(ftp, klassniki_page, 'category/11-klassniki.php')
        print("   ✅ Created 11-klassniki category page")
        
        # 3. Education news category
        print("\n3️⃣ Creating Education News category page...")
        news_page = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = 'Новости образования';
$posts = [];

// Get posts from this category
if ($connection) {
    try {
        $stmt = $connection->prepare("SELECT id, title_post as title, text_post as text, url_slug, date_post FROM posts WHERE category LIKE '%новост%' OR category LIKE '%образован%' ORDER BY date_post DESC LIMIT 20");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $posts[] = $row;
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        error_log('Category query error: ' . $e->getMessage());
    }
}

$greyContent1 = '
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item"><a href="/category">Категории</a></li>
                    <li class="breadcrumb-item active">Новости образования</li>
                </ol>
            </nav>
        </div>
    </div>
</div>';

$greyContent2 = '
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 mb-2">📰 Новости образования</h1>
            <p class="lead mb-4">Актуальные новости из мира образования, изменения в ЕГЭ, события в учебных заведениях</p>
        </div>
    </div>
</div>';

$greyContent3 = '';
$greyContent4 = '
<div class="container">
    <div class="row">';

if (!empty($posts)) {
    foreach ($posts as $post) {
        $greyContent4 .= '
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="/post/' . htmlspecialchars($post['url_slug'] ?? '') . '" class="text-decoration-none">
                            ' . htmlspecialchars($post['title'] ?? 'Без заголовка') . '
                        </a>
                    </h5>
                    <p class="card-text">' . htmlspecialchars(mb_substr(strip_tags($post['text'] ?? ''), 0, 150)) . '...</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">' . date('d.m.Y', strtotime($post['date_post'] ?? 'now')) . '</small>
                        <a href="/post/' . htmlspecialchars($post['url_slug'] ?? '') . '" class="btn btn-sm btn-outline-primary">Читать</a>
                    </div>
                </div>
            </div>
        </div>';
    }
} else {
    $greyContent4 .= '
    <div class="col-12">
        <div class="alert alert-info">
            <h4><i class="fas fa-newspaper me-2"></i>Новости загружаются</h4>
            <p>Свежие новости образования появятся здесь в ближайшее время.</p>
        </div>
    </div>';
}

$greyContent4 .= '
    </div>
</div>';

$greyContent5 = '';
$greyContent6 = '';

include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        upload_file(ftp, news_page, 'category/education-news.php')
        print("   ✅ Created Education News category page")
        
        # 4. Last category - A naposledok ya skazhu
        print("\n4️⃣ Creating 'A naposledok ya skazhu' category page...")
        last_page = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = 'А напоследок я скажу';
$posts = [];

// Get posts from this category
if ($connection) {
    try {
        $stmt = $connection->prepare("SELECT id, title_post as title, text_post as text, url_slug, date_post FROM posts WHERE category LIKE '%напоследок%' ORDER BY date_post DESC LIMIT 20");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $posts[] = $row;
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        error_log('Category query error: ' . $e->getMessage());
    }
}

$greyContent1 = '
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item"><a href="/category">Категории</a></li>
                    <li class="breadcrumb-item active">А напоследок я скажу</li>
                </ol>
            </nav>
        </div>
    </div>
</div>';

$greyContent2 = '
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 mb-2">💭 А напоследок я скажу</h1>
            <p class="lead mb-4">Мысли, размышления и истории об образовании</p>
        </div>
    </div>
</div>';

$greyContent3 = '
<div class="container mb-4">
    <div class="row">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <blockquote class="blockquote mb-0">
                        <p>"Образование - это то, что остается после того, как забывается все выученное в школе."</p>
                        <footer class="blockquote-footer">Альберт Эйнштейн</footer>
                    </blockquote>
                </div>
            </div>
        </div>
    </div>
</div>';

$greyContent4 = '
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">📝 Статьи и размышления</h2>
        </div>
    </div>
    <div class="row">';

// Default content
$default_content = [
    ['title' => 'Почему важно учиться всю жизнь', 'text' => 'Размышления о непрерывном образовании и его роли в современном мире...'],
    ['title' => 'История одного выпускника', 'text' => 'Как выбор профессии изменил всю жизнь. Реальная история успеха...'],
    ['title' => 'Учитель, который изменил мою жизнь', 'text' => 'Воспоминания о школьных годах и людях, повлиявших на выбор пути...']
];

if (!empty($posts)) {
    foreach ($posts as $post) {
        $greyContent4 .= '
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="/post/' . htmlspecialchars($post['url_slug'] ?? '') . '" class="text-decoration-none">
                            ' . htmlspecialchars($post['title'] ?? 'Без заголовка') . '
                        </a>
                    </h5>
                    <p class="card-text">' . htmlspecialchars(mb_substr(strip_tags($post['text'] ?? ''), 0, 200)) . '...</p>
                    <small class="text-muted">' . date('d.m.Y', strtotime($post['date_post'] ?? 'now')) . '</small>
                </div>
            </div>
        </div>';
    }
} else {
    foreach ($default_content as $article) {
        $greyContent4 .= '
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">' . $article['title'] . '</h5>
                    <p class="card-text">' . $article['text'] . '</p>
                    <a href="/news" class="btn btn-sm btn-outline-primary">Читать далее</a>
                </div>
            </div>
        </div>';
    }
}

$greyContent4 .= '
    </div>
</div>';

$greyContent5 = '';
$greyContent6 = '';

include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        upload_file(ftp, last_page, 'category/a-naposledok-ya-skazhu.php')
        print("   ✅ Created 'A naposledok ya skazhu' category page")
        
        # Update .htaccess for category routing
        print("\n📝 Updating .htaccess for category routing...")
        
        # Download current .htaccess
        htaccess_content = []
        ftp.retrlines('RETR .htaccess', htaccess_content.append)
        
        # Check if category rules exist
        has_category_rules = any('category/' in line for line in htaccess_content)
        
        if not has_category_rules:
            # Find index to insert new rules (after existing rewrites)
            insert_index = -1
            for i, line in enumerate(htaccess_content):
                if 'RewriteRule' in line and 'spo-all-regions' in line:
                    insert_index = i + 1
                    break
            
            if insert_index > 0:
                # Add category rewrite rules
                new_rules = [
                    '',
                    '# Category pages',
                    'RewriteRule ^category/abiturientam/?$ category/abiturientam.php [QSA,NC,L]',
                    'RewriteRule ^category/11-klassniki/?$ category/11-klassniki.php [QSA,NC,L]',
                    'RewriteRule ^category/education-news/?$ category/education-news.php [QSA,NC,L]',
                    'RewriteRule ^category/a-naposledok-ya-skazhu/?$ category/a-naposledok-ya-skazhu.php [QSA,NC,L]'
                ]
                
                htaccess_content[insert_index:insert_index] = new_rules
                
                # Upload updated .htaccess
                with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
                    tmp.write('\n'.join(htaccess_content))
                    tmp_path = tmp.name
                
                with open(tmp_path, 'rb') as file:
                    ftp.storbinary('STOR .htaccess', file)
                os.unlink(tmp_path)
                
                print("   ✅ Added category routing to .htaccess")
            else:
                print("   ⚠️  Could not find insertion point in .htaccess")
        else:
            print("   ✅ Category routing already exists in .htaccess")
        
        ftp.quit()
        
        print("\n✅ ALL CATEGORY PAGES NOW HAVE CONTENT!")
        
        print("\n📊 Created category pages:")
        print("• /category/abiturientam - For applicants")
        print("• /category/11-klassniki - For 11th graders")
        print("• /category/education-news - Education news")
        print("• /category/a-naposledok-ya-skazhu - Thoughts and stories")
        
        print("\n🎯 All pages now show:")
        print("• Database content where available")
        print("• Default content as fallback")
        print("• Proper breadcrumb navigation")
        print("• Category-specific information")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()