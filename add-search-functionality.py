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
    print("🔍 ADDING SEARCH FUNCTIONALITY WITH DATABASE CONTENT")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Create search page with database integration
        search_page = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = 'Поиск';
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$search_type = isset($_GET['type']) ? $_GET['type'] : 'all';

$results = [
    'schools' => [],
    'vpo' => [],
    'spo' => [],
    'posts' => []
];

$total_results = 0;

// Perform search if query is provided
if (!empty($search_query) && strlen($search_query) >= 2 && $connection) {
    $search_term = '%' . $connection->real_escape_string($search_query) . '%';
    
    try {
        // Search schools
        if ($search_type === 'all' || $search_type === 'schools') {
            $stmt = $connection->prepare("SELECT id, school_name, city, region FROM schools WHERE school_name LIKE ? OR city LIKE ? OR region LIKE ? LIMIT 10");
            if ($stmt) {
                $stmt->bind_param("sss", $search_term, $search_term, $search_term);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $results['schools'][] = $row;
                }
                $stmt->close();
            }
        }
        
        // Search VPO
        if ($search_type === 'all' || $search_type === 'vpo') {
            $stmt = $connection->prepare("SELECT id, vpo_name, city, type FROM vpo WHERE vpo_name LIKE ? OR city LIKE ? LIMIT 10");
            if ($stmt) {
                $stmt->bind_param("ss", $search_term, $search_term);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $results['vpo'][] = $row;
                }
                $stmt->close();
            }
        }
        
        // Search SPO
        if ($search_type === 'all' || $search_type === 'spo') {
            $stmt = $connection->prepare("SELECT id, spo_name, city, type FROM spo WHERE spo_name LIKE ? OR city LIKE ? LIMIT 10");
            if ($stmt) {
                $stmt->bind_param("ss", $search_term, $search_term);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $results['spo'][] = $row;
                }
                $stmt->close();
            }
        }
        
        // Search posts
        if ($search_type === 'all' || $search_type === 'posts') {
            $stmt = $connection->prepare("SELECT id, title_post as title, text_post as text, url_slug, date_post FROM posts WHERE title_post LIKE ? OR text_post LIKE ? ORDER BY date_post DESC LIMIT 10");
            if ($stmt) {
                $stmt->bind_param("ss", $search_term, $search_term);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $results['posts'][] = $row;
                }
                $stmt->close();
            }
        }
        
        // Count total results
        $total_results = count($results['schools']) + count($results['vpo']) + count($results['spo']) + count($results['posts']);
        
    } catch (Exception $e) {
        error_log('Search error: ' . $e->getMessage());
    }
}

$greyContent1 = '
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">Поиск</li>
                </ol>
            </nav>
        </div>
    </div>
</div>';

$greyContent2 = '
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 mb-4">🔍 Поиск по сайту</h1>
            
            <!-- Search form -->
            <form method="GET" action="/search" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" 
                               name="q" 
                               class="form-control form-control-lg" 
                               placeholder="Введите запрос для поиска..." 
                               value="' . htmlspecialchars($search_query) . '"
                               minlength="2"
                               required>
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-select form-select-lg">
                            <option value="all" ' . ($search_type === 'all' ? 'selected' : '') . '>Везде</option>
                            <option value="schools" ' . ($search_type === 'schools' ? 'selected' : '') . '>Школы</option>
                            <option value="vpo" ' . ($search_type === 'vpo' ? 'selected' : '') . '>ВУЗы</option>
                            <option value="spo" ' . ($search_type === 'spo' ? 'selected' : '') . '>СПО</option>
                            <option value="posts" ' . ($search_type === 'posts' ? 'selected' : '') . '>Новости</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-search me-2"></i>Найти
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>';

$greyContent3 = '';

// Show search results
if (!empty($search_query)) {
    $greyContent3 .= '
    <div class="container">
        <div class="row">
            <div class="col-12">';
    
    if ($total_results > 0) {
        $greyContent3 .= '
                <h3 class="mb-4">Найдено результатов: ' . $total_results . '</h3>';
        
        // Show schools results
        if (!empty($results['schools'])) {
            $greyContent3 .= '
                <h4 class="mt-4 mb-3"><i class="fas fa-school me-2"></i>Школы (' . count($results['schools']) . ')</h4>
                <div class="row">';
            
            foreach ($results['schools'] as $school) {
                $greyContent3 .= '
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="/school/' . htmlspecialchars($school['id']) . '" class="text-decoration-none">
                                        ' . htmlspecialchars($school['school_name']) . '
                                    </a>
                                </h5>
                                <p class="card-text">
                                    <i class="fas fa-map-marker-alt text-primary me-1"></i>
                                    ' . htmlspecialchars($school['city']) . ', ' . htmlspecialchars($school['region']) . '
                                </p>
                            </div>
                        </div>
                    </div>';
            }
            
            $greyContent3 .= '</div>';
        }
        
        // Show VPO results
        if (!empty($results['vpo'])) {
            $greyContent3 .= '
                <h4 class="mt-4 mb-3"><i class="fas fa-university me-2"></i>ВУЗы (' . count($results['vpo']) . ')</h4>
                <div class="row">';
            
            foreach ($results['vpo'] as $vpo) {
                $greyContent3 .= '
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="/vpo/' . htmlspecialchars($vpo['id']) . '" class="text-decoration-none">
                                        ' . htmlspecialchars($vpo['vpo_name']) . '
                                    </a>
                                </h5>
                                <p class="card-text">
                                    <i class="fas fa-map-marker-alt text-success me-1"></i>
                                    ' . htmlspecialchars($vpo['city']) . '
                                    ' . (!empty($vpo['type']) ? '<br><span class="badge bg-info">' . htmlspecialchars($vpo['type']) . '</span>' : '') . '
                                </p>
                            </div>
                        </div>
                    </div>';
            }
            
            $greyContent3 .= '</div>';
        }
        
        // Show SPO results
        if (!empty($results['spo'])) {
            $greyContent3 .= '
                <h4 class="mt-4 mb-3"><i class="fas fa-building me-2"></i>СПО (' . count($results['spo']) . ')</h4>
                <div class="row">';
            
            foreach ($results['spo'] as $spo) {
                $greyContent3 .= '
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="/spo/' . htmlspecialchars($spo['id']) . '" class="text-decoration-none">
                                        ' . htmlspecialchars($spo['spo_name']) . '
                                    </a>
                                </h5>
                                <p class="card-text">
                                    <i class="fas fa-map-marker-alt text-warning me-1"></i>
                                    ' . htmlspecialchars($spo['city']) . '
                                    ' . (!empty($spo['type']) ? '<br><span class="badge bg-warning text-dark">' . htmlspecialchars($spo['type']) . '</span>' : '') . '
                                </p>
                            </div>
                        </div>
                    </div>';
            }
            
            $greyContent3 .= '</div>';
        }
        
        // Show posts results
        if (!empty($results['posts'])) {
            $greyContent3 .= '
                <h4 class="mt-4 mb-3"><i class="fas fa-newspaper me-2"></i>Новости и статьи (' . count($results['posts']) . ')</h4>
                <div class="row">';
            
            foreach ($results['posts'] as $post) {
                $greyContent3 .= '
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="/post/' . htmlspecialchars($post['url_slug']) . '" class="text-decoration-none">
                                        ' . htmlspecialchars($post['title']) . '
                                    </a>
                                </h5>
                                <p class="card-text">
                                    ' . htmlspecialchars(mb_substr(strip_tags($post['text']), 0, 150)) . '...
                                </p>
                                <small class="text-muted">
                                    <i class="far fa-calendar me-1"></i>
                                    ' . date('d.m.Y', strtotime($post['date_post'])) . '
                                </small>
                            </div>
                        </div>
                    </div>';
            }
            
            $greyContent3 .= '</div>';
        }
        
    } else {
        $greyContent3 .= '
                <div class="alert alert-info">
                    <h4><i class="fas fa-info-circle me-2"></i>Ничего не найдено</h4>
                    <p>По вашему запросу "' . htmlspecialchars($search_query) . '" ничего не найдено.</p>
                    <p>Попробуйте:</p>
                    <ul>
                        <li>Проверить правописание</li>
                        <li>Использовать другие ключевые слова</li>
                        <li>Сделать запрос более общим</li>
                    </ul>
                </div>';
    }
    
    $greyContent3 .= '
            </div>
        </div>
    </div>';
} else {
    // Show search tips when no query
    $greyContent3 .= '
    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">💡 Как пользоваться поиском</h4>
                        <p>Вы можете искать:</p>
                        <ul>
                            <li><strong>Учебные заведения</strong> - по названию, городу или региону</li>
                            <li><strong>Новости и статьи</strong> - по заголовку или содержанию</li>
                            <li><strong>Конкретный тип</strong> - выберите категорию в выпадающем списке</li>
                        </ul>
                        <p>Минимальная длина запроса - 2 символа.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>';
}

$greyContent4 = '';
$greyContent5 = '';
$greyContent6 = '';

include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        upload_file(ftp, search_page, 'search-new.php')
        print("   ✅ Created search page with database integration")
        
        # Update .htaccess if needed
        print("\n📝 Checking .htaccess for search routing...")
        
        # Download current .htaccess
        htaccess_content = []
        ftp.retrlines('RETR .htaccess', htaccess_content.append)
        
        # Update search routing if needed
        updated = False
        for i, line in enumerate(htaccess_content):
            if 'search/?$' in line and 'search.php' in line:
                htaccess_content[i] = 'RewriteRule ^search/?$ search-new.php [QSA,NC,L]'
                updated = True
                break
        
        if updated:
            # Upload updated .htaccess
            with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
                tmp.write('\n'.join(htaccess_content))
                tmp_path = tmp.name
            
            with open(tmp_path, 'rb') as file:
                ftp.storbinary('STOR .htaccess', file)
            os.unlink(tmp_path)
            
            print("   ✅ Updated .htaccess to use search-new.php")
        else:
            print("   ⚠️  Could not find search routing in .htaccess")
        
        ftp.quit()
        
        print("\n✅ SEARCH FUNCTIONALITY ADDED!")
        
        print("\n🔍 Search features:")
        print("• Search across schools, VPOs, SPOs, and posts")
        print("• Filter by type (all, schools, vpo, spo, posts)")
        print("• Minimum query length: 2 characters")
        print("• Shows up to 10 results per category")
        print("• Links directly to detail pages")
        
        print("\n🎯 Search capabilities:")
        print("• Schools - by name, city, or region")
        print("• VPOs - by name or city")
        print("• SPOs - by name or city")
        print("• Posts - by title or content")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()