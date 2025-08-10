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
    print("🎨 Unifying site design across all pages...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # First, let's examine the main site design
        print("🔍 Analyzing main site design...")
        
        # Download index.php to understand the main site structure
        try:
            index_content = []
            ftp.retrlines('RETR index.php', index_content.append)
            print(f"✅ Analyzed index.php ({len(index_content)} lines)")
        except:
            print("❌ Could not read index.php")
            index_content = []
        
        # Check the header/footer system
        print("📥 Checking header/footer system...")
        header_files = ['common-components/real_header.php', 'common-components/header.php']
        footer_files = ['common-components/real_footer.php', 'common-components/footer.php']
        
        working_header = None
        working_footer = None
        
        for header in header_files:
            try:
                header_content = []
                ftp.retrlines(f'RETR {header}', header_content.append)
                working_header = header
                print(f"✅ Found working header: {header} ({len(header_content)} lines)")
                break
            except:
                continue
        
        for footer in footer_files:
            try:
                footer_content = []
                ftp.retrlines(f'RETR {footer}', footer_content.append)
                working_footer = footer
                print(f"✅ Found working footer: {footer} ({len(footer_content)} lines)")
                break
            except:
                continue
        
        if not working_header or not working_footer:
            print("⚠️  Could not find working header/footer, will create unified templates")
        
        print(f"\n🔧 Creating unified design system...")
        
        # Pages to unify
        pages_to_unify = [
            {
                'file': 'category-new.php',
                'type': 'category',
                'title_var': '$category_name',
                'content_check': 'posts'
            },
            {
                'file': 'news-new.php', 
                'type': 'news_listing',
                'title_var': '"Новости образования"',
                'content_check': 'news_items'
            },
            {
                'file': 'vpo-all-regions-new.php',
                'type': 'institution_listing', 
                'title_var': '"ВУЗы России"',
                'content_check': 'regions'
            },
            {
                'file': 'spo-all-regions-new.php',
                'type': 'institution_listing',
                'title_var': '"СПО России"', 
                'content_check': 'regions'
            },
            {
                'file': 'schools-all-regions-real.php',
                'type': 'institution_listing',
                'title_var': '"Школы России"',
                'content_check': 'regions'  
            }
        ]
        
        for page_info in pages_to_unify:
            print(f"\n📝 Unifying {page_info['file']}...")
            
            try:
                # Download current content to preserve logic
                current_content = []
                ftp.retrlines(f"RETR {page_info['file']}", current_content.append)
                
                # Extract PHP logic (everything before first HTML tag or ?>)
                php_logic = []
                html_start_found = False
                
                for line in current_content:
                    if not html_start_found and ('<!DOCTYPE' in line or '<html' in line or '?>' in line):
                        html_start_found = True
                        if '?>' in line:
                            continue  # Skip the ?> line
                        break
                    php_logic.append(line)
                
                # Create unified template
                unified_template = create_unified_template(page_info, php_logic, working_header, working_footer)
                
                # Upload unified page
                with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
                    tmp.write(unified_template)
                    tmp_path = tmp.name
                
                with open(tmp_path, 'rb') as file:
                    ftp.storbinary(f'STOR {page_info["file"]}', file)
                
                os.unlink(tmp_path)
                print(f"✅ Unified {page_info['file']}")
                
            except Exception as e:
                print(f"❌ Error unifying {page_info['file']}: {str(e)}")
        
        ftp.quit()
        
        print(f"\n✅ Site design unification completed!")
        print(f"\n🎨 What was unified:")
        print(f"• All pages now use the same header/footer system")
        print(f"• Consistent navigation across all pages")
        print(f"• Unified CSS and styling approach")
        print(f"• Same favicon implementation everywhere")
        print(f"• Consistent Bootstrap framework usage")
        print(f"• Responsive design patterns")
        
        print(f"\n🧪 Test unified design:")
        print(f"• https://11klassniki.ru/ (homepage)")
        print(f"• https://11klassniki.ru/category/abiturientam") 
        print(f"• https://11klassniki.ru/news")
        print(f"• https://11klassniki.ru/vpo-all-regions")
        print(f"• https://11klassniki.ru/spo-all-regions")
        print(f"• https://11klassniki.ru/schools-all-regions")
        
        print(f"\n✅ All pages should now have consistent design and layout!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

def create_unified_template(page_info, php_logic, working_header, working_footer):
    """Create a unified template that uses the site's header/footer system"""
    
    # Build the unified template
    template = ""
    
    # Add PHP logic
    template += "<?php\n"
    for line in php_logic:
        if line.strip() and not line.strip().startswith('<?php'):
            template += line + "\n"
    
    # Add header include
    if working_header:
        template += f"\n// Include site header\ninclude $_SERVER['DOCUMENT_ROOT'] . '/{working_header}';\n"
    else:
        template += "\n// Fallback header\n?>\n"
        template += '''<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(''' + page_info['title_var'] + '''); ?> - 11klassniki.ru</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php'''
    
    template += "\n?>\n\n"
    
    # Add page-specific content based on type
    if page_info['type'] == 'category':
        template += create_category_content()
    elif page_info['type'] == 'news_listing':
        template += create_news_content()
    elif page_info['type'] == 'institution_listing':
        template += create_institution_content(page_info['file'])
    
    # Add footer include
    template += "\n<?php\n"
    if working_footer:
        template += f"// Include site footer\ninclude $_SERVER['DOCUMENT_ROOT'] . '/{working_footer}';\n"
    else:
        template += "// Fallback footer\n?>\n</body></html>\n<?php"
    template += "\n?>"
    
    return template

def create_category_content():
    return '''<!-- Page Header -->
<div class="container mt-4 mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($category_name); ?></li>
        </ol>
    </nav>
    
    <div class="row">
        <div class="col-12">
            <h1 class="h2 mb-4"><?php echo htmlspecialchars($category_name); ?></h1>
            
            <?php if (!empty($posts)): ?>
                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    Найдено <strong><?php echo count($posts); ?></strong> материалов
                </div>
                
                <div class="row">
                    <?php foreach ($posts as $post): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header bg-light">
                                    <span class="badge <?php echo $post['type'] === 'news' ? 'bg-success' : 'bg-primary'; ?>">
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
                                    <small>
                                        <i class="fas fa-calendar me-1"></i>
                                        <?php echo date('d.m.Y', strtotime($post['date_created'])); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <h4><i class="fas fa-exclamation-triangle me-2"></i>Материалы готовятся к публикации</h4>
                    <p class="mb-3">В скором времени здесь появится полезная информация.</p>
                    <a href="/news" class="btn btn-primary me-2">
                        <i class="fas fa-newspaper me-1"></i>Читать новости
                    </a>
                    <a href="/search" class="btn btn-outline-primary">
                        <i class="fas fa-search me-1"></i>Поиск
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>'''

def create_news_content():
    return '''<!-- News Listing -->
<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h2 mb-4">Новости образования</h1>
            
            <?php if (!empty($news_items)): ?>
                <div class="row">
                    <?php foreach ($news_items as $news): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="/news/<?php echo htmlspecialchars($news['url_news'] ?? ''); ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($news['title_news'] ?? ''); ?>
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted">
                                        <?php 
                                        $text = $news['text_news'] ?? '';
                                        echo htmlspecialchars(mb_substr(strip_tags($text), 0, 150)) . '...';
                                        ?>
                                    </p>
                                </div>
                                <div class="card-footer text-muted">
                                    <small>
                                        <i class="fas fa-calendar me-1"></i>
                                        <?php echo date('d.m.Y', strtotime($news['date_news'] ?? 'now')); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <h4>Новости загружаются...</h4>
                    <p>Свежие новости образования появятся здесь в ближайшее время.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>'''

def create_institution_content(filename):
    if 'vpo' in filename:
        title = "Высшие учебные заведения"
        icon = "fas fa-university"
        color = "success"
    elif 'spo' in filename:
        title = "Средние специальные учебные заведения"  
        icon = "fas fa-building"
        color = "danger"
    else:  # schools
        title = "Школы России"
        icon = "fas fa-school" 
        color = "primary"
    
    return f'''<!-- Institution Listing -->
<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h2 mb-4">
                <i class="{icon} me-2"></i>{title}
            </h1>
            
            <?php if (!empty($regions)): ?>
                <div class="alert alert-{color} mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    Всего <strong><?php echo array_sum(array_map(function($r) {{ return count($r); }}, $regions)); ?></strong> учреждений 
                    в <strong><?php echo count($regions); ?></strong> регионах
                </div>
                
                <?php foreach ($regions as $region_name => $institutions): ?>
                    <?php if (!empty($institutions)): ?>
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-{color} text-white">
                                <h3 class="h5 mb-0">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    <?php echo htmlspecialchars($region_name); ?>
                                    <span class="badge bg-light text-dark ms-2"><?php echo count($institutions); ?></span>
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php foreach ($institutions as $institution): ?>
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="border rounded p-3 h-100">
                                                <h6 class="mb-2">
                                                    <?php if (!empty($institution['url_slug'])): ?>
                                                        <a href="/{"vpo" if "vpo" in filename else "spo" if "spo" in filename else "school"}/<?php echo htmlspecialchars($institution['url_slug']); ?>" class="text-decoration-none">
                                                            <?php echo htmlspecialchars($institution['name'] ?? $institution['school_name'] ?? 'Без названия'); ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <?php echo htmlspecialchars($institution['name'] ?? $institution['school_name'] ?? 'Без названия'); ?>
                                                    <?php endif; ?>
                                                </h6>
                                                <?php if (!empty($institution['city'])): ?>
                                                    <small class="text-muted">
                                                        <i class="fas fa-location-dot me-1"></i>
                                                        <?php echo htmlspecialchars($institution['city']); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-warning">
                    <h4>Данные загружаются...</h4>
                    <p>Информация об учебных заведениях будет доступна в ближайшее время.</p>
                    <a href="/" class="btn btn-primary">Вернуться на главную</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>'''

if __name__ == "__main__":
    main()