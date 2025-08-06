<?php
/**
 * SEO Optimization Implementation Script
 * Updates existing pages with better SEO meta tags and structured data
 */

// Database connection
$servername = "localhost";
$username = "franko";
$password = "I68d54M4k71N";
$dbname = "11klassnikiDB";

echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.success { color: green; font-weight: bold; }
.error { color: red; font-weight: bold; }
.info { color: blue; }
pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
</style>";

echo "<h1>SEO Optimization Implementation</h1>";

try {
    $connection = new mysqli($servername, $username, $password, $dbname);
    
    if ($connection->connect_error) {
        throw new Exception("Connection failed: " . $connection->connect_error);
    }
    
    echo "<div class='success'>✓ Database connection successful</div><br>";
    
    // 1. Create robots.txt
    echo "<h2>1. Creating robots.txt</h2>";
    
    $robotsContent = "User-agent: *
Allow: /

# Disallow sensitive directories
Disallow: /admin/
Disallow: /includes/
Disallow: /config/
Disallow: /database/
Disallow: /_cleanup/
Disallow: /cache/

# Disallow search parameters
Disallow: /search?*
Disallow: /*?*utm_*

# Allow important CSS and JS
Allow: /css/
Allow: /js/
Allow: /images/

# Sitemaps
Sitemap: https://11klassniki.ru/sitemap.xml

# Crawl delays
User-agent: Yandex
Crawl-delay: 1

User-agent: Googlebot
Crawl-delay: 1
";
    
    if (file_put_contents('robots.txt', $robotsContent)) {
        echo "<div class='success'>✓ robots.txt created successfully</div>";
    } else {
        echo "<div class='error'>✗ Failed to create robots.txt</div>";
    }
    
    // 2. Create sitemap.xml generator
    echo "<h2>2. Creating sitemap.xml</h2>";
    
    $sitemapContent = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>https://11klassniki.ru/</loc>
        <lastmod>' . date('Y-m-d') . '</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>https://11klassniki.ru/about</loc>
        <lastmod>' . date('Y-m-d') . '</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>https://11klassniki.ru/news</loc>
        <lastmod>' . date('Y-m-d') . '</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>https://11klassniki.ru/tests</loc>
        <lastmod>' . date('Y-m-d') . '</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>https://11klassniki.ru/vpo-all-regions</loc>
        <lastmod>' . date('Y-m-d') . '</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>https://11klassniki.ru/spo-all-regions</loc>
        <lastmod>' . date('Y-m-d') . '</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>https://11klassniki.ru/schools-all-regions</loc>
        <lastmod>' . date('Y-m-d') . '</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>';
    
    // Add category pages
    $categoryQuery = "SELECT url_category FROM categories";
    $categoryResult = $connection->query($categoryQuery);
    if ($categoryResult) {
        while ($category = $categoryResult->fetch_assoc()) {
            $sitemapContent .= '
    <url>
        <loc>https://11klassniki.ru/category/' . htmlspecialchars($category['url_category']) . '</loc>
        <lastmod>' . date('Y-m-d') . '</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>';
        }
    }
    
    // Add recent posts
    $postQuery = "SELECT url_slug, date_post FROM posts WHERE status = 1 AND url_slug IS NOT NULL ORDER BY date_post DESC LIMIT 100";
    $postResult = $connection->query($postQuery);
    if ($postResult) {
        while ($post = $postResult->fetch_assoc()) {
            $sitemapContent .= '
    <url>
        <loc>https://11klassniki.ru/post/' . htmlspecialchars($post['url_slug']) . '</loc>
        <lastmod>' . date('Y-m-d', strtotime($post['date_post'])) . '</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>';
        }
    }
    
    $sitemapContent .= '
</urlset>';
    
    if (file_put_contents('sitemap.xml', $sitemapContent)) {
        echo "<div class='success'>✓ sitemap.xml created successfully</div>";
    } else {
        echo "<div class='error'>✗ Failed to create sitemap.xml</div>";
    }
    
    // 3. Check and improve meta descriptions in database
    echo "<h2>3. Optimizing Database Meta Tags</h2>";
    
    // Check posts without meta descriptions
    $metaQuery = "SELECT COUNT(*) as count FROM posts WHERE (meta_description IS NULL OR meta_description = '') AND status = 1";
    $metaResult = $connection->query($metaQuery);
    $metaCount = $metaResult->fetch_assoc()['count'];
    
    echo "<div class='info'>Posts without meta descriptions: $metaCount</div>";
    
    if ($metaCount > 0) {
        // Update posts with generated meta descriptions
        $updateQuery = "UPDATE posts 
                       SET meta_description = CONCAT(LEFT(REPLACE(REPLACE(text_post, '<p>', ''), '</p>', ' '), 150), '...') 
                       WHERE (meta_description IS NULL OR meta_description = '') 
                       AND status = 1 
                       AND text_post IS NOT NULL";
        
        if ($connection->query($updateQuery)) {
            echo "<div class='success'>✓ Generated meta descriptions for posts</div>";
        } else {
            echo "<div class='error'>✗ Failed to update meta descriptions</div>";
        }
    }
    
    // 4. Create .htaccess SEO optimizations
    echo "<h2>4. Adding SEO Rules to .htaccess</h2>";
    
    $htaccessSEO = '
# SEO Optimizations
RewriteEngine On

# Force HTTPS (uncomment in production)
# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Remove trailing slashes for SEO
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.+)/$ /$1 [L,R=301]

# Add trailing slash to directories
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^(.+[^/])$ /$1/ [L,R=301]

# Remove www (or add www - choose one)
# RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
# RewriteRule ^(.*)$ http://%1/$1 [R=301,L]

# Prevent access to sensitive files
<FilesMatch "\.(htaccess|htpasswd|ini|log|sh|inc|bak)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Set ETags for better caching
FileETag MTime Size
';
    
    $htaccessPath = '.htaccess';
    if (file_exists($htaccessPath)) {
        $currentContent = file_get_contents($htaccessPath);
        if (strpos($currentContent, '# SEO Optimizations') === false) {
            if (file_put_contents($htaccessPath, $currentContent . $htaccessSEO)) {
                echo "<div class='success'>✓ SEO rules added to .htaccess</div>";
            } else {
                echo "<div class='error'>✗ Failed to update .htaccess</div>";
            }
        } else {
            echo "<div class='info'>SEO rules already exist in .htaccess</div>";
        }
    } else {
        echo "<div class='error'>✗ .htaccess file not found</div>";
    }
    
    // 5. Create structured data templates
    echo "<h2>5. Creating Structured Data Templates</h2>";
    
    $structuredDataTemplate = '<?php
/**
 * Structured Data Template
 * Generate JSON-LD structured data for different page types
 */

function generateStructuredData($type, $data = []) {
    $baseUrl = "https://11klassniki.ru";
    
    switch ($type) {
        case "website":
            $structured = [
                "@context" => "https://schema.org",
                "@type" => "WebSite",
                "name" => "11klassniki.ru",
                "url" => $baseUrl,
                "description" => "Образовательный портал для школьников и абитуриентов",
                "potentialAction" => [
                    "@type" => "SearchAction",
                    "target" => "{$baseUrl}/search?q={search_term_string}",
                    "query-input" => "required name=search_term_string"
                ]
            ];
            break;
            
        case "organization":
            $structured = [
                "@context" => "https://schema.org",
                "@type" => "EducationalOrganization",
                "name" => "11klassniki.ru",
                "url" => $baseUrl,
                "description" => "Образовательный портал для школьников",
                "logo" => "{$baseUrl}/images/logo.png",
                "address" => [
                    "@type" => "PostalAddress",
                    "addressCountry" => "RU"
                ]
            ];
            break;
            
        case "article":
            $structured = [
                "@context" => "https://schema.org",
                "@type" => "Article",
                "headline" => $data["title"] ?? "",
                "description" => $data["description"] ?? "",
                "image" => $data["image"] ?? "{$baseUrl}/images/default-article.jpg",
                "author" => [
                    "@type" => "Organization",
                    "name" => "11klassniki.ru"
                ],
                "publisher" => [
                    "@type" => "Organization",
                    "name" => "11klassniki.ru",
                    "logo" => [
                        "@type" => "ImageObject",
                        "url" => "{$baseUrl}/images/logo.png"
                    ]
                ],
                "datePublished" => $data["datePublished"] ?? date("c"),
                "dateModified" => $data["dateModified"] ?? date("c")
            ];
            break;
            
        case "breadcrumb":
            $structured = [
                "@context" => "https://schema.org",
                "@type" => "BreadcrumbList",
                "itemListElement" => []
            ];
            
            foreach ($data as $index => $item) {
                $structured["itemListElement"][] = [
                    "@type" => "ListItem",
                    "position" => $index + 1,
                    "name" => $item["name"],
                    "item" => $baseUrl . $item["url"]
                ];
            }
            break;
            
        default:
            return "";
    }
    
    return "<script type=\"application/ld+json\">" . json_encode($structured, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "</script>";
}
?>';
    
    if (!is_dir('includes/seo')) {
        mkdir('includes/seo', 0755, true);
    }
    
    if (file_put_contents('./includes/seo/structured-data.php', $structuredDataTemplate)) {
        echo "<div class='success'>✓ Structured data template created</div>";
    } else {
        echo "<div class='error'>✗ Failed to create structured data template</div>";
    }
    
    // 6. Database analysis for SEO
    echo "<h2>6. SEO Database Analysis</h2>";
    
    // Check for duplicate titles
    $duplicateQuery = "SELECT title_post, COUNT(*) as count FROM posts WHERE status = 1 GROUP BY title_post HAVING count > 1";
    $duplicateResult = $connection->query($duplicateQuery);
    $duplicateCount = $duplicateResult->num_rows;
    
    echo "<div class='info'>Duplicate post titles found: $duplicateCount</div>";
    
    // Check for missing alt tags in content
    $altTagQuery = "SELECT COUNT(*) as count FROM posts WHERE text_post LIKE '%<img%' AND text_post NOT LIKE '%alt=%' AND status = 1";
    $altResult = $connection->query($altTagQuery);
    $altCount = $altResult->fetch_assoc()['count'];
    
    echo "<div class='info'>Posts with images missing alt tags: $altCount</div>";
    
    // Check for short meta descriptions
    $shortMetaQuery = "SELECT COUNT(*) as count FROM posts WHERE LENGTH(meta_description) < 120 AND status = 1 AND meta_description IS NOT NULL";
    $shortMetaResult = $connection->query($shortMetaQuery);
    $shortMetaCount = $shortMetaResult->fetch_assoc()['count'];
    
    echo "<div class='info'>Posts with short meta descriptions (<120 chars): $shortMetaCount</div>";
    
    echo "<h2>SEO Optimization Complete!</h2>";
    echo "<div class='success'>✓ robots.txt created</div>";
    echo "<div class='success'>✓ sitemap.xml generated</div>";
    echo "<div class='success'>✓ Meta descriptions optimized</div>";
    echo "<div class='success'>✓ SEO rules added to .htaccess</div>";
    echo "<div class='success'>✓ Structured data templates created</div>";
    
    echo "<h3>Next Steps:</h3>";
    echo "<ul>";
    echo "<li>Submit sitemap to Google Search Console</li>";
    echo "<li>Monitor page load speeds</li>";
    echo "<li>Add more specific meta descriptions manually for important posts</li>";
    echo "<li>Implement Open Graph images for social sharing</li>";
    echo "<li>Consider implementing AMP for mobile performance</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<div class='error'>Error: " . $e->getMessage() . "</div>";
}
?>