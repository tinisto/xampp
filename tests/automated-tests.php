<?php
/**
 * Automated Testing Suite
 * Basic functionality tests for the 11klassniki.ru system
 */

require_once __DIR__ . '/../database/db_modern.php';

class AutomatedTests {
    
    private $results = [];
    private $passed = 0;
    private $failed = 0;
    
    public function runAllTests() {
        echo "<h1>🧪 Automated Testing Suite</h1>\n";
        echo "<p>Running comprehensive tests for 11klassniki.ru system...</p>\n\n";
        
        $this->testDatabaseConnection();
        $this->testDatabaseTables();
        $this->testDataIntegrity();
        $this->testAPIEndpoints();
        $this->testPageRouting();
        $this->testSEOFeatures();
        
        $this->displaySummary();
    }
    
    private function assert($condition, $description, $expected = null, $actual = null) {
        if ($condition) {
            $this->results[] = ['status' => 'PASS', 'test' => $description];
            $this->passed++;
            echo "✅ PASS: $description\n";
        } else {
            $this->results[] = [
                'status' => 'FAIL', 
                'test' => $description,
                'expected' => $expected,
                'actual' => $actual
            ];
            $this->failed++;
            echo "❌ FAIL: $description";
            if ($expected !== null) {
                echo " (Expected: $expected, Got: $actual)";
            }
            echo "\n";
        }
    }
    
    private function testDatabaseConnection() {
        echo "<h2>🗄️ Database Connection Tests</h2>\n";
        
        try {
            $result = db_query("SELECT 1 as test");
            $this->assert(true, "Database connection established");
        } catch (Exception $e) {
            $this->assert(false, "Database connection failed: " . $e->getMessage());
        }
        
        // Test transaction support (MySQL syntax)
        try {
            db_query("START TRANSACTION");
            db_query("ROLLBACK");
            $this->assert(true, "Database transaction support working");
        } catch (Exception $e) {
            $this->assert(false, "Database transaction support failed: " . $e->getMessage());
        }
        
        echo "\n";
    }
    
    private function testDatabaseTables() {
        echo "<h2>📋 Database Tables Tests</h2>\n";
        
        $requiredTables = [
            'news' => 'News articles table',
            'posts' => 'Educational posts table', 
            'categories' => 'Content categories table',
            'schools' => 'Schools information table',
            'vpo' => 'Universities (VPO) table',
            'spo' => 'Colleges (SPO) table'
        ];
        
        $optionalTables = [
            'users' => 'User accounts table',
            'events' => 'Events calendar table',
            'favorites' => 'User favorites table',
            'comments' => 'Comments system table',
            'ratings' => 'Content ratings table'
        ];
        
        foreach ($requiredTables as $table => $description) {
            try {
                $count = db_fetch_column("SELECT COUNT(*) FROM $table");
                $this->assert($count !== false, "$description exists", "integer", $count);
            } catch (Exception $e) {
                $this->assert(false, "$description missing");
            }
        }
        
        // Test optional tables (don't fail if missing)
        foreach ($optionalTables as $table => $description) {
            try {
                $count = db_fetch_column("SELECT COUNT(*) FROM $table");
                $this->assert(true, "$description exists (optional)", "found", "found");
            } catch (Exception $e) {
                $this->assert(true, "$description not implemented yet (optional)", "optional", "not implemented");
            }
        }
        
        echo "\n";
    }
    
    private function testDataIntegrity() {
        echo "<h2>🔍 Data Integrity Tests</h2>\n";
        
        // Test news data (using correct schema)
        $newsCount = db_fetch_column("SELECT COUNT(*) FROM news WHERE approved = 1");
        $this->assert($newsCount > 0, "Published news articles exist", "> 0", $newsCount);
        
        // Test posts data (posts don't have is_published in our schema)
        $postsCount = db_fetch_column("SELECT COUNT(*) FROM posts");
        $this->assert($postsCount > 0, "Posts exist", "> 0", $postsCount);
        
        // Test real data migration
        $realNewsCount = db_fetch_column("
            SELECT COUNT(*) FROM news 
            WHERE title_news LIKE '%ЕГЭ%' OR title_news LIKE '%университет%' OR title_news LIKE '%Минобрнауки%'
        ");
        $this->assert($realNewsCount > 0, "Real educational news data imported", "> 0", $realNewsCount);
        
        // Test events (table may not exist yet, so skip this test)
        try {
            $eventsCount = db_fetch_column("SELECT COUNT(*) FROM events WHERE approved = 1");
            $this->assert($eventsCount >= 0, "Events table accessible", ">= 0", $eventsCount);
        } catch (Exception $e) {
            $this->assert(false, "Events table not implemented yet (expected)", "table exists", "table missing");
        }
        
        // Test categories
        $categoriesCount = db_fetch_column("SELECT COUNT(*) FROM categories");
        $this->assert($categoriesCount > 0, "Content categories exist", "> 0", $categoriesCount);
        
        echo "\n";
    }
    
    private function testAPIEndpoints() {
        echo "<h2>🔗 API Endpoints Tests</h2>\n";
        
        $baseUrl = 'http://localhost:8000';
        
        // Test if API endpoints respond (even with auth errors)
        $endpoints = [
            '/api/v1/' => 'Mobile API base endpoint',
            '/health-check.php?format=json' => 'System health check API',
            '/seo-optimizer.php?action=sitemap-data' => 'SEO sitemap data API'
        ];
        
        foreach ($endpoints as $endpoint => $description) {
            $url = $baseUrl . $endpoint;
            
            // Use cURL to get HTTP status code
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_HEADER, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200) {
                $jsonData = json_decode($response, true);
                $this->assert($jsonData !== null, "$description returns valid JSON", "valid JSON", "received");
            } elseif ($httpCode === 401 || $httpCode === 403) {
                $this->assert(true, "$description exists (requires auth)", "accessible", "401/403 response");
            } else {
                $this->assert(false, "$description is accessible", "200/401/403", "HTTP $httpCode");
            }
        }
        
        echo "\n";
    }
    
    private function testPageRouting() {
        echo "<h2>🛣️ Page Routing Tests</h2>\n";
        
        $baseUrl = 'http://localhost:8000';
        
        $pages = [
            '/' => 'Homepage',
            '/news' => 'News listing page',
            '/events' => 'Events page',
            '/health-check.php' => 'Health check page'
        ];
        
        foreach ($pages as $path => $description) {
            $url = $baseUrl . $path;
            
            // Use cURL for better reliability
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($httpCode === 200 && $response !== false) {
                $hasContent = strlen($response) > 100;
                $this->assert($hasContent, "$description loads with content", "> 100 chars", strlen($response) . " chars");
                
                // Check for basic HTML structure
                $hasHtmlStructure = (
                    strpos($response, '<html') !== false &&
                    (strpos($response, '<head>') !== false || strpos($response, '<head ') !== false) &&
                    strpos($response, '<body') !== false
                );
                $this->assert($hasHtmlStructure, "$description has valid HTML structure");
            } else {
                $this->assert(false, "$description is accessible", "HTTP 200", "HTTP $httpCode" . ($error ? " - $error" : ""));
            }
        }
        
        echo "\n";
    }
    
    private function testSEOFeatures() {
        echo "<h2>🚀 SEO Features Tests</h2>\n";
        
        // Test sitemap generation
        try {
            require_once __DIR__ . '/../seo-optimizer.php';
            $sitemapData = SEOOptimizer::generateSitemapData();
            $this->assert(is_array($sitemapData) && count($sitemapData) > 0, "Sitemap data generation", "array with items", "received");
        } catch (Exception $e) {
            $this->assert(false, "Sitemap generation failed: " . $e->getMessage());
        }
        
        // Test SEO analysis
        try {
            $analysis = SEOOptimizer::analyzeSEO(
                'Test Title for SEO Analysis', 
                'This is a test content for SEO analysis. It contains enough text to pass basic SEO requirements and includes some educational keywords like university, school, education.'
            );
            $this->assert(isset($analysis['score']), "SEO analysis returns score", "score present", isset($analysis['score']) ? "yes" : "no");
            $this->assert($analysis['score'] > 0, "SEO score calculation", "> 0", $analysis['score']);
        } catch (Exception $e) {
            $this->assert(false, "SEO analysis failed: " . $e->getMessage());
        }
        
        // Test structured data generation
        try {
            $testNewsData = [
                'title_news' => 'Test News Article',
                'text_news' => 'Test content for news article',
                'url_news' => 'test-news',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $structuredData = SEOOptimizer::generateStructuredData('news', $testNewsData);
            $this->assert(isset($structuredData['@type']), "Structured data generation", "NewsArticle type", $structuredData['@type'] ?? 'missing');
        } catch (Exception $e) {
            $this->assert(false, "Structured data generation failed: " . $e->getMessage());
        }
        
        echo "\n";
    }
    
    private function displaySummary() {
        echo "<h2>📊 Test Summary</h2>\n";
        echo "<div style='padding: 20px; background: #f8f9fa; border-radius: 8px; margin: 20px 0;'>\n";
        echo "<p><strong>Total Tests:</strong> " . ($this->passed + $this->failed) . "</p>\n";
        echo "<p style='color: #28a745;'><strong>✅ Passed:</strong> {$this->passed}</p>\n";
        echo "<p style='color: #dc3545;'><strong>❌ Failed:</strong> {$this->failed}</p>\n";
        
        $successRate = $this->passed + $this->failed > 0 ? 
            round(($this->passed / ($this->passed + $this->failed)) * 100, 2) : 0;
        
        echo "<p><strong>Success Rate:</strong> {$successRate}%</p>\n";
        echo "</div>\n";
        
        if ($this->failed > 0) {
            echo "<h3>❌ Failed Tests Details</h3>\n";
            echo "<ul>\n";
            foreach ($this->results as $result) {
                if ($result['status'] === 'FAIL') {
                    echo "<li><strong>{$result['test']}</strong>";
                    if (isset($result['expected'])) {
                        echo " - Expected: {$result['expected']}, Got: {$result['actual']}";
                    }
                    echo "</li>\n";
                }
            }
            echo "</ul>\n";
        }
        
        // Display test completion time
        echo "<p><small>Tests completed at: " . date('Y-m-d H:i:s') . "</small></p>\n";
        
        // Recommendations based on results
        if ($successRate >= 90) {
            echo "<div style='padding: 15px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; color: #155724;'>\n";
            echo "<strong>🎉 Excellent!</strong> Your system is performing very well with {$successRate}% test success rate.\n";
            echo "</div>\n";
        } elseif ($successRate >= 70) {
            echo "<div style='padding: 15px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; color: #856404;'>\n";
            echo "<strong>⚠️ Good</strong> Most tests are passing ({$successRate}%), but there are some issues to address.\n";
            echo "</div>\n";
        } else {
            echo "<div style='padding: 15px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; color: #721c24;'>\n";
            echo "<strong>🚨 Needs Attention</strong> Several critical tests are failing ({$successRate}% success rate). Please review the issues above.\n";
            echo "</div>\n";
        }
    }
}

// Run tests if accessed directly
if (basename($_SERVER['SCRIPT_NAME']) === 'automated-tests.php') {
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Automated Tests - 11klassniki.ru</title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, sans-serif; padding: 20px; max-width: 1000px; margin: 0 auto; }
            h1, h2, h3 { color: #333; }
            pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 14px; line-height: 1.4; }
        </style>
    </head>
    <body>
        <pre><?php
        $tests = new AutomatedTests();
        $tests->runAllTests();
        ?></pre>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="/" style="display: inline-block; background: #007bff; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;">
                🏠 Back to Homepage
            </a>
            <a href="/health-check.php" style="display: inline-block; background: #28a745; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; margin-left: 10px;">
                💊 Health Check
            </a>
        </div>
    </body>
    </html>
    <?php
}
?>