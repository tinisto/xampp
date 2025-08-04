<?php
/**
 * Query Cache Unit Tests
 */

require_once __DIR__ . '/../../TestCase.php';
require_once __DIR__ . '/../../../includes/performance/query_cache.php';

class QueryCacheTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear cache before each test
        QueryCache::clearAll();
    }
    
    protected function tearDown(): void
    {
        QueryCache::clearAll();
        parent::tearDown();
    }
    
    public function testCacheSetAndGet()
    {
        $query = "SELECT * FROM users WHERE id = ?";
        $params = [1];
        $result = [['id' => 1, 'name' => 'Test User']];
        
        // Set cache
        QueryCache::set($query, $params, $result);
        
        // Get from cache
        $cached = QueryCache::get($query, $params);
        
        $this->assertEquals($result, $cached);
    }
    
    public function testCacheGetMiss()
    {
        $query = "SELECT * FROM users WHERE id = ?";
        $params = [1];
        
        $cached = QueryCache::get($query, $params);
        
        $this->assertNull($cached);
    }
    
    public function testCacheExpiration()
    {
        $query = "SELECT * FROM users WHERE id = ?";
        $params = [1];
        $result = [['id' => 1, 'name' => 'Test User']];
        
        // Set cache with 1 second TTL
        QueryCache::set($query, $params, $result, 1);
        
        // Should be cached initially
        $cached = QueryCache::get($query, $params);
        $this->assertEquals($result, $cached);
        
        // Wait for expiration
        sleep(2);
        
        // Should be expired now
        $cached = QueryCache::get($query, $params);
        $this->assertNull($cached);
    }
    
    public function testCacheDifferentParams()
    {
        $query = "SELECT * FROM users WHERE id = ?";
        $result1 = [['id' => 1, 'name' => 'User 1']];
        $result2 = [['id' => 2, 'name' => 'User 2']];
        
        QueryCache::set($query, [1], $result1);
        QueryCache::set($query, [2], $result2);
        
        $cached1 = QueryCache::get($query, [1]);
        $cached2 = QueryCache::get($query, [2]);
        
        $this->assertEquals($result1, $cached1);
        $this->assertEquals($result2, $cached2);
        $this->assertNotEquals($cached1, $cached2);
    }
    
    public function testShouldCacheSelectQuery()
    {
        $selectQuery = "SELECT * FROM users WHERE active = 1";
        $reflection = new ReflectionClass('QueryCache');
        $method = $reflection->getMethod('shouldCache');
        $method->setAccessible(true);
        
        $result = $method->invoke(null, $selectQuery);
        $this->assertTrue($result);
    }
    
    public function testShouldNotCacheInsertQuery()
    {
        $insertQuery = "INSERT INTO users (name) VALUES ('test')";
        $reflection = new ReflectionClass('QueryCache');
        $method = $reflection->getMethod('shouldCache');
        $method->setAccessible(true);
        
        $result = $method->invoke(null, $insertQuery);
        $this->assertFalse($result);
    }
    
    public function testShouldNotCacheUpdateQuery()
    {
        $updateQuery = "UPDATE users SET name = 'test' WHERE id = 1";
        $reflection = new ReflectionClass('QueryCache');
        $method = $reflection->getMethod('shouldCache');
        $method->setAccessible(true);
        
        $result = $method->invoke(null, $updateQuery);
        $this->assertFalse($result);
    }
    
    public function testShouldNotCacheDeleteQuery()
    {
        $deleteQuery = "DELETE FROM users WHERE id = 1";
        $reflection = new ReflectionClass('QueryCache');
        $method = $reflection->getMethod('shouldCache');
        $method->setAccessible(true);
        
        $result = $method->invoke(null, $deleteQuery);
        $this->assertFalse($result);
    }
    
    public function testShouldNotCacheRandomFunction()
    {
        $randomQuery = "SELECT * FROM users ORDER BY RAND() LIMIT 10";
        $reflection = new ReflectionClass('QueryCache');
        $method = $reflection->getMethod('shouldCache');
        $method->setAccessible(true);
        
        $result = $method->invoke(null, $randomQuery);
        $this->assertFalse($result);
    }
    
    public function testShouldNotCacheNowFunction()
    {
        $nowQuery = "SELECT * FROM users WHERE created_at > NOW()";
        $reflection = new ReflectionClass('QueryCache');
        $method = $reflection->getMethod('shouldCache');
        $method->setAccessible(true);
        
        $result = $method->invoke(null, $nowQuery);
        $this->assertFalse($result);
    }
    
    public function testClearPattern()
    {
        $query1 = "SELECT * FROM users WHERE id = 1";
        $query2 = "SELECT * FROM posts WHERE id = 1";
        $query3 = "SELECT * FROM users WHERE id = 2";
        
        $result = [['test' => 'data']];
        
        QueryCache::set($query1, [], $result);
        QueryCache::set($query2, [], $result);
        QueryCache::set($query3, [], $result);
        
        // Clear all user queries
        QueryCache::clearPattern("FROM users");
        
        // User queries should be cleared
        $this->assertNull(QueryCache::get($query1, []));
        $this->assertNull(QueryCache::get($query3, []));
        
        // Post query should remain
        $this->assertEquals($result, QueryCache::get($query2, []));
    }
    
    public function testClearTable()
    {
        $query1 = "SELECT * FROM users WHERE id = 1";
        $query2 = "SELECT * FROM posts WHERE id = 1";
        
        $result = [['test' => 'data']];
        
        QueryCache::set($query1, [], $result);
        QueryCache::set($query2, [], $result);
        
        // Clear users table cache
        QueryCache::clearTable("users");
        
        // Users query should be cleared
        $this->assertNull(QueryCache::get($query1, []));
        
        // Posts query should remain
        $this->assertEquals($result, QueryCache::get($query2, []));
    }
    
    public function testGetStats()
    {
        $query = "SELECT * FROM users";
        $result = [['id' => 1]];
        
        // Set some cache entries
        QueryCache::set($query, [1], $result);
        QueryCache::set($query, [2], $result);
        
        $stats = QueryCache::getStats();
        
        $this->assertArrayHasKey('total_files', $stats);
        $this->assertArrayHasKey('valid_files', $stats);
        $this->assertArrayHasKey('total_size', $stats);
        $this->assertArrayHasKey('enabled', $stats);
        
        $this->assertGreaterThanOrEqual(2, $stats['total_files']);
        $this->assertTrue($stats['enabled']);
    }
    
    public function testCacheEnabled()
    {
        // Disable cache
        QueryCache::setEnabled(false);
        
        $query = "SELECT * FROM users";
        $result = [['id' => 1]];
        
        QueryCache::set($query, [], $result);
        $cached = QueryCache::get($query, []);
        
        $this->assertNull($cached);
        
        // Re-enable cache
        QueryCache::setEnabled(true);
        
        QueryCache::set($query, [], $result);
        $cached = QueryCache::get($query, []);
        
        $this->assertEquals($result, $cached);
    }
}