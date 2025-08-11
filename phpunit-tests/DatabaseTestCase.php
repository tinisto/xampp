<?php
/**
 * Database Test Case for 11klassniki
 * Provides database testing utilities with transactions
 */

require_once __DIR__ . '/TestCase.php';

abstract class DatabaseTestCase extends TestCase
{
    protected $connection;
    protected $useTransactions = true;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test database connection
        $this->setupTestDatabase();
        
        // Start transaction for rollback after test
        if ($this->useTransactions && $this->connection) {
            $this->connection->autocommit(false);
            $this->connection->begin_transaction();
        }
    }
    
    protected function tearDown(): void
    {
        // Rollback transaction to clean up test data
        if ($this->useTransactions && $this->connection) {
            $this->connection->rollback();
            $this->connection->autocommit(true);
        }
        
        // Close connection
        if ($this->connection) {
            $this->connection->close();
        }
        
        parent::tearDown();
    }
    
    /**
     * Set up test database connection
     */
    protected function setupTestDatabase()
    {
        // Try to load environment variables
        $envFile = $_SERVER['DOCUMENT_ROOT'] . '/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                    list($key, $value) = explode('=', $line, 2);
                    $_ENV[trim($key)] = trim($value);
                }
            }
        }
        
        // Use test database or fallback to development
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $username = $_ENV['DB_USER'] ?? 'root';
        $password = $_ENV['DB_PASS'] ?? '';
        $database = $_ENV['DB_NAME_TEST'] ?? $_ENV['DB_NAME'] ?? 'test_11klassniki';
        
        try {
            $this->connection = new mysqli($host, $username, $password, $database);
            
            if ($this->connection->connect_error) {
                $this->markTestSkipped('Database connection failed: ' . $this->connection->connect_error);
            }
            
            $this->connection->set_charset('utf8mb4');
            
        } catch (Exception $e) {
            $this->markTestSkipped('Database setup failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Insert test data into a table
     * @param string $table Table name
     * @param array $data Data to insert
     * @return int|false Insert ID or false on failure
     */
    protected function insertTestData($table, $data)
    {
        $columns = array_keys($data);
        $values = array_values($data);
        $placeholders = str_repeat('?,', count($values) - 1) . '?';
        
        $sql = "INSERT INTO {$table} (" . implode(',', $columns) . ") VALUES ({$placeholders})";
        
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            return false;
        }
        
        $types = str_repeat('s', count($values)); // Assume all strings for simplicity
        $stmt->bind_param($types, ...$values);
        
        $result = $stmt->execute();
        $insertId = $this->connection->insert_id;
        
        $stmt->close();
        
        return $result ? $insertId : false;
    }
    
    /**
     * Create a test user
     * @param array $userData User data override
     * @return int User ID
     */
    protected function createTestUser($userData = [])
    {
        $defaultUser = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'testuser' . uniqid() . '@example.com',
            'password' => password_hash('testpassword', PASSWORD_DEFAULT),
            'role' => 'user',
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $user = array_merge($defaultUser, $userData);
        
        return $this->insertTestData('users', $user);
    }
    
    /**
     * Create test news article
     * @param array $newsData News data override
     * @return int News ID
     */
    protected function createTestNews($newsData = [])
    {
        $defaultNews = [
            'title_news' => 'Test News ' . uniqid(),
            'content_news' => 'This is test news content.',
            'author_news' => 'Test Author',
            'approved' => 1,
            'date_news' => date('Y-m-d H:i:s'),
            'url_news' => 'test-news-' . uniqid()
        ];
        
        $news = array_merge($defaultNews, $newsData);
        
        return $this->insertTestData('news', $news);
    }
    
    /**
     * Create test post
     * @param array $postData Post data override
     * @return int Post ID
     */
    protected function createTestPost($postData = [])
    {
        $defaultPost = [
            'title_post' => 'Test Post ' . uniqid(),
            'content_post' => 'This is test post content.',
            'author_post' => 'Test Author',
            'date_post' => date('Y-m-d H:i:s'),
            'url_post' => 'test-post-' . uniqid(),
            'view_post' => 0
        ];
        
        $post = array_merge($defaultPost, $postData);
        
        return $this->insertTestData('posts', $post);
    }
    
    /**
     * Assert that a record exists in database
     * @param string $table Table name
     * @param array $conditions WHERE conditions
     */
    protected function assertDatabaseHas($table, $conditions)
    {
        $where = [];
        $params = [];
        $types = '';
        
        foreach ($conditions as $column => $value) {
            $where[] = "{$column} = ?";
            $params[] = $value;
            $types .= 's'; // Assume string
        }
        
        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE " . implode(' AND ', $where);
        
        $stmt = $this->connection->prepare($sql);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        $this->assertGreaterThan(0, $row['count'], "Record not found in {$table}");
    }
    
    /**
     * Assert that a record does not exist in database
     * @param string $table Table name
     * @param array $conditions WHERE conditions
     */
    protected function assertDatabaseMissing($table, $conditions)
    {
        $where = [];
        $params = [];
        $types = '';
        
        foreach ($conditions as $column => $value) {
            $where[] = "{$column} = ?";
            $params[] = $value;
            $types .= 's';
        }
        
        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE " . implode(' AND ', $where);
        
        $stmt = $this->connection->prepare($sql);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        $this->assertEquals(0, $row['count'], "Unexpected record found in {$table}");
    }
    
    /**
     * Get record count from table
     * @param string $table Table name
     * @param array $conditions Optional WHERE conditions
     * @return int Record count
     */
    protected function getRecordCount($table, $conditions = [])
    {
        $sql = "SELECT COUNT(*) as count FROM {$table}";
        
        if (!empty($conditions)) {
            $where = [];
            $params = [];
            $types = '';
            
            foreach ($conditions as $column => $value) {
                $where[] = "{$column} = ?";
                $params[] = $value;
                $types .= 's';
            }
            
            $sql .= " WHERE " . implode(' AND ', $where);
            
            $stmt = $this->connection->prepare($sql);
            $stmt->bind_param($types, ...$params);
        } else {
            $stmt = $this->connection->prepare($sql);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return (int)$row['count'];
    }
    
    /**
     * Execute raw SQL for testing
     * @param string $sql SQL query
     * @param array $params Parameters
     * @return mysqli_result|bool Query result
     */
    protected function executeQuery($sql, $params = [])
    {
        if (empty($params)) {
            return $this->connection->query($sql);
        }
        
        $stmt = $this->connection->prepare($sql);
        if ($params) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        
        $result = $stmt->execute();
        
        if ($result && $stmt->result_metadata()) {
            $result = $stmt->get_result();
        }
        
        $stmt->close();
        
        return $result;
    }
}