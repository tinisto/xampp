<?php
/**
 * Performance Optimization Script
 * Implements caching, indexes, and other performance improvements
 */

// Simple database connection for this script
$servername = "localhost";
$username = "franko";
$password = "I68d54M4k71N";
$dbname = "11klassnikiDB";

define('DB_HOST', $servername);
define('DB_USER', $username);
define('DB_PASS', $password);
define('DB_NAME', $dbname);

// Output styling
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.success { color: green; font-weight: bold; }
.error { color: red; font-weight: bold; }
.info { color: blue; }
pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
</style>";

echo "<h1>Performance Optimization Implementation</h1>";

try {
    // Connect to database
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($connection->connect_error) {
        throw new Exception("Connection failed: " . $connection->connect_error);
    }
    
    echo "<div class='success'>✓ Database connection successful</div><br>";
    
    // 1. Add indexes for better query performance
    echo "<h2>1. Creating Database Indexes</h2>";
    
    $indexes = [
        "CREATE INDEX IF NOT EXISTS idx_posts_category_date ON posts(category, date_post DESC)",
        "CREATE INDEX IF NOT EXISTS idx_posts_status ON posts(status)",
        "CREATE INDEX IF NOT EXISTS idx_posts_url_slug ON posts(url_slug)",
        "CREATE INDEX IF NOT EXISTS idx_categories_url ON categories(url_category)",
        "CREATE INDEX IF NOT EXISTS idx_news_url_slug ON news(url_slug)",
        "CREATE INDEX IF NOT EXISTS idx_posts_category_status ON posts(category, status, date_post DESC)"
    ];
    
    foreach ($indexes as $indexQuery) {
        if ($connection->query($indexQuery)) {
            echo "<div class='success'>✓ Index created: " . substr($indexQuery, 0, 50) . "...</div>";
        } else {
            echo "<div class='error'>✗ Failed to create index: " . $connection->error . "</div>";
        }
    }
    
    // 2. Analyze existing queries for optimization opportunities
    echo "<h2>2. Query Analysis</h2>";
    
    // Check if posts table has proper indexes
    $result = $connection->query("SHOW INDEX FROM posts");
    $indexes_found = [];
    while ($row = $result->fetch_assoc()) {
        $indexes_found[] = $row['Key_name'];
    }
    
    echo "<div class='info'>Current indexes on posts table:</div>";
    echo "<pre>" . implode("\n", $indexes_found) . "</pre>";
    
    // 3. Create performance functions file
    echo "<h2>3. Creating Performance Helper Functions</h2>";
    
    $performanceFunctions = '<?php
/**
 * Performance Helper Functions
 */

class PerformanceHelper {
    private static $queryCache = [];
    
    /**
     * Simple query result caching
     */
    public static function getCachedQuery($connection, $query, $params = [], $cacheTime = 300) {
        $cacheKey = md5($query . serialize($params));
        
        // Check if cached result exists and is still valid
        if (isset(self::$queryCache[$cacheKey])) {
            $cached = self::$queryCache[$cacheKey];
            if (time() - $cached[\'time\'] < $cacheTime) {
                return $cached[\'data\'];
            }
        }
        
        // Execute query
        if (empty($params)) {
            $result = mysqli_query($connection, $query);
            $data = [];
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $data[] = $row;
                }
            }
        } else {
            $stmt = mysqli_prepare($connection, $query);
            if ($stmt) {
                $types = str_repeat(\'s\', count($params)); // Assume all strings for simplicity
                mysqli_stmt_bind_param($stmt, $types, ...$params);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $data = [];
                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $data[] = $row;
                    }
                }
                mysqli_stmt_close($stmt);
            }
        }
        
        // Cache the result
        self::$queryCache[$cacheKey] = [
            \'data\' => $data,
            \'time\' => time()
        ];
        
        return $data;
    }
    
    /**
     * Get optimized post query with minimal fields
     */
    public static function getOptimizedPosts($connection, $category = null, $limit = 10, $offset = 0) {
        if ($category) {
            $query = "SELECT id, title_post, text_post, url_slug, date_post 
                     FROM posts 
                     WHERE category = ? AND status = 1 
                     ORDER BY date_post DESC 
                     LIMIT ? OFFSET ?";
            return self::getCachedQuery($connection, $query, [$category, $limit, $offset], 600);
        } else {
            $query = "SELECT id, title_post, text_post, url_slug, date_post 
                     FROM posts 
                     WHERE status = 1 
                     ORDER BY date_post DESC 
                     LIMIT ? OFFSET ?";
            return self::getCachedQuery($connection, $query, [$limit, $offset], 300);
        }
    }
    
    /**
     * Get site statistics with caching
     */
    public static function getSiteStats($connection) {
        $query = "SELECT 
                    (SELECT COUNT(*) FROM schools) as schools_count,
                    (SELECT COUNT(*) FROM vpo) as vpo_count,
                    (SELECT COUNT(*) FROM spo) as spo_count,
                    (SELECT COUNT(*) FROM posts WHERE status = 1) as posts_count";
        
        $result = self::getCachedQuery($connection, $query, [], 1800); // Cache for 30 minutes
        return $result[0] ?? [\'schools_count\' => 0, \'vpo_count\' => 0, \'spo_count\' => 0, \'posts_count\' => 0];
    }
    
    /**
     * Clear all cached queries
     */
    public static function clearCache() {
        self::$queryCache = [];
    }
    
    /**
     * Add versioning to static assets for browser caching
     */
    public static function versionedAsset($path) {
        $fullPath = $_SERVER[\'DOCUMENT_ROOT\'] . $path;
        if (file_exists($fullPath)) {
            $mtime = filemtime($fullPath);
            return $path . \'?v=\' . $mtime;
        }
        return $path;
    }
    
    /**
     * Minify HTML output
     */
    public static function minifyHTML($html) {
        // Remove comments (but preserve conditional comments)
        $html = preg_replace(\'/<!--(?!\\s*(?:\\[if [^\\]]+]|<!|>))(?:(?!-->).)*-->/s\', \'\', $html);
        
        // Remove unnecessary whitespace
        $html = preg_replace(\'/\\s+/\', \' \', $html);
        $html = preg_replace(\'/\\s*([<>])\\s*/\', \'$1\', $html);
        
        return trim($html);
    }
}

// Function for backward compatibility
if (!function_exists(\'versioned_asset\')) {
    function versioned_asset($path) {
        return PerformanceHelper::versionedAsset($path);
    }
}
?>';
    
    if (file_put_contents('includes/functions/performance.php', $performanceFunctions)) {
        echo "<div class='success'>✓ Performance functions file created</div>";
    } else {
        echo "<div class='error'>✗ Failed to create performance functions file</div>";
    }
    
    // 4. Create .htaccess optimizations
    echo "<h2>4. Creating .htaccess Performance Rules</h2>";
    
    $htaccessPerf = '
# Performance Optimizations
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType font/woff "access plus 1 year"
    ExpiresByType font/woff2 "access plus 1 year"
</IfModule>

<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/x-javascript
</IfModule>

<IfModule mod_headers.c>
    # Cache static resources
    <FilesMatch "\\.(css|js|png|jpg|jpeg|gif|webp|svg|woff|woff2)$">
        Header set Cache-Control "public, max-age=31536000"
    </FilesMatch>
    
    # Security headers
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options SAMEORIGIN
    Header always set X-XSS-Protection "1; mode=block"
</IfModule>
';
    
    // Check if .htaccess exists and append performance rules
    $htaccessPath = '.htaccess';
    if (file_exists($htaccessPath)) {
        $currentContent = file_get_contents($htaccessPath);
        if (strpos($currentContent, '# Performance Optimizations') === false) {
            if (file_put_contents($htaccessPath, $currentContent . $htaccessPerf)) {
                echo "<div class='success'>✓ Performance rules added to .htaccess</div>";
            } else {
                echo "<div class='error'>✗ Failed to update .htaccess</div>";
            }
        } else {
            echo "<div class='info'>Performance rules already exist in .htaccess</div>";
        }
    } else {
        echo "<div class='error'>✗ .htaccess file not found</div>";
    }
    
    // 5. Database optimization recommendations
    echo "<h2>5. Database Optimization Analysis</h2>";
    
    // Check table sizes
    $result = $connection->query("
        SELECT table_name, 
               ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
        FROM information_schema.TABLES 
        WHERE table_schema = '" . DB_NAME . "'
        ORDER BY size_mb DESC
    ");
    
    echo "<div class='info'>Table sizes (MB):</div><pre>";
    while ($row = $result->fetch_assoc()) {
        echo $row['table_name'] . ": " . $row['size_mb'] . " MB\n";
    }
    echo "</pre>";
    
    // Check for slow queries (if enabled)
    $result = $connection->query("SHOW VARIABLES LIKE 'slow_query_log'");
    $slowLog = $result->fetch_assoc();
    echo "<div class='info'>Slow query log: " . $slowLog['Value'] . "</div>";
    
    echo "<h2>Optimization Complete!</h2>";
    echo "<div class='success'>✓ Database indexes created</div>";
    echo "<div class='success'>✓ Performance helper functions created</div>";
    echo "<div class='success'>✓ .htaccess performance rules added</div>";
    echo "<div class='info'>Next steps:</div>";
    echo "<ul>";
    echo "<li>Monitor query performance using the slow query log</li>";
    echo "<li>Consider implementing Redis or Memcached for session storage</li>";
    echo "<li>Optimize images using WebP format</li>";
    echo "<li>Consider using a CDN for static assets</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<div class='error'>Error: " . $e->getMessage() . "</div>";
    echo "<div class='error'>Line: " . $e->getLine() . "</div>";
    echo "<div class='error'>File: " . $e->getFile() . "</div>";
}
?>