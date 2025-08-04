<?php
/**
 * Base Test Case for 11klassniki
 * Provides common testing utilities
 */

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear any existing session data
        $_SESSION = [];
        
        // Reset global state
        $_POST = [];
        $_GET = [];
        $_FILES = [];
        $_COOKIE = [];
        
        // Set default server variables
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/test';
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_USER_AGENT'] = 'PHPUnit Test';
    }
    
    protected function tearDown(): void
    {
        // Clean up after each test
        $this->clearTestFiles();
        
        parent::tearDown();
    }
    
    /**
     * Create a mock user session
     * @param array $userData User data to set in session
     */
    protected function createUserSession($userData = [])
    {
        $defaultUser = [
            'user_id' => 1,
            'username' => 'testuser',
            'email' => 'test@example.com',
            'role' => 'user',
            'is_authenticated' => true
        ];
        
        $_SESSION = array_merge($defaultUser, $userData);
    }
    
    /**
     * Create admin session
     */
    protected function createAdminSession()
    {
        $this->createUserSession([
            'user_id' => 999,
            'username' => 'admin',
            'email' => 'admin@example.com',
            'role' => 'admin'
        ]);
    }
    
    /**
     * Mock POST request
     * @param array $data POST data
     * @param array $files FILES data
     */
    protected function mockPostRequest($data = [], $files = [])
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = $data;
        $_FILES = $files;
    }
    
    /**
     * Mock GET request
     * @param array $data GET data
     */
    protected function mockGetRequest($data = [])
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = $data;
    }
    
    /**
     * Assert that a redirect header was sent
     * @param string $expectedUrl Expected redirect URL
     */
    protected function assertRedirect($expectedUrl)
    {
        $headers = headers_list();
        $redirectFound = false;
        
        foreach ($headers as $header) {
            if (stripos($header, 'Location:') === 0) {
                $redirectFound = true;
                $this->assertStringContains($expectedUrl, $header);
                break;
            }
        }
        
        $this->assertTrue($redirectFound, 'No redirect header found');
    }
    
    /**
     * Assert that an error was logged
     * @param string $message Expected error message
     */
    protected function assertErrorLogged($message)
    {
        $logFile = $_SERVER['DOCUMENT_ROOT'] . '/logs/error-' . date('Y-m-d') . '.log';
        
        if (!file_exists($logFile)) {
            $this->fail('Error log file does not exist');
        }
        
        $logContent = file_get_contents($logFile);
        $this->assertStringContains($message, $logContent);
    }
    
    /**
     * Create a temporary file for testing
     * @param string $content File content
     * @param string $filename Optional filename
     * @return string File path
     */
    protected function createTempFile($content, $filename = null)
    {
        if ($filename === null) {
            $filename = 'test_' . uniqid() . '.tmp';
        }
        
        $path = sys_get_temp_dir() . '/' . $filename;
        file_put_contents($path, $content);
        
        return $path;
    }
    
    /**
     * Clean up test files
     */
    protected function clearTestFiles()
    {
        // Clear test cache files
        $cacheDir = $_SERVER['DOCUMENT_ROOT'] . '/cache';
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '/test_*.cache');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
        
        // Clear test log entries (optional)
        // We might want to keep logs for debugging
    }
    
    /**
     * Assert that a string contains another string (case-insensitive)
     * @param string $needle String to search for
     * @param string $haystack String to search in
     * @param string $message Optional assertion message
     */
    protected function assertStringContains($needle, $haystack, $message = '')
    {
        $this->assertTrue(
            stripos($haystack, $needle) !== false,
            $message ?: "Failed asserting that '{$haystack}' contains '{$needle}'"
        );
    }
    
    /**
     * Assert that an array has the expected structure
     * @param array $expectedKeys Expected keys
     * @param array $actual Actual array
     */
    protected function assertArrayStructure($expectedKeys, $actual)
    {
        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $actual, "Missing key: {$key}");
        }
    }
    
    /**
     * Mock $_SERVER variables for a specific request
     * @param string $method HTTP method
     * @param string $uri Request URI
     * @param string $host Host name
     */
    protected function mockRequest($method = 'GET', $uri = '/test', $host = 'localhost')
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['HTTP_HOST'] = $host;
        $_SERVER['REQUEST_TIME'] = time();
        $_SERVER['REQUEST_TIME_FLOAT'] = microtime(true);
    }
    
    /**
     * Capture output from a function or code block
     * @param callable $callback Function to execute
     * @return string Captured output
     */
    protected function captureOutput($callback)
    {
        ob_start();
        try {
            $callback();
            return ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }
}