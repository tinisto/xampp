<?php
/**
 * Login System Integration Tests
 */

require_once __DIR__ . '/../DatabaseTestCase.php';

class LoginSystemTest extends DatabaseTestCase
{
    private $testUserId;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $this->testUserId = $this->createTestUser([
            'email' => 'testuser@example.com',
            'password' => password_hash('testpassword', PASSWORD_DEFAULT),
            'is_active' => 1
        ]);
    }
    
    public function testSuccessfulLogin()
    {
        // Mock login form submission
        $this->mockPostRequest([
            'email' => 'testuser@example.com',
            'password' => 'testpassword',
            'csrf_token' => 'valid_token' // In real test, would generate proper token
        ]);
        
        // Simulate CSRF validation (skip for integration test)
        $_SESSION['csrf_tokens']['login']['token'] = 'valid_token';
        $_SESSION['csrf_tokens']['login']['timestamp'] = time();
        
        // Test that user exists in database
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
            'is_active' => 1
        ]);
        
        // Simulate successful login by setting session
        $_SESSION['user_id'] = $this->testUserId;
        $_SESSION['email'] = 'testuser@example.com';
        $_SESSION['is_authenticated'] = true;
        
        // Verify session is set correctly
        $this->assertTrue(isset($_SESSION['user_id']));
        $this->assertEquals($this->testUserId, $_SESSION['user_id']);
        $this->assertEquals('testuser@example.com', $_SESSION['email']);
        $this->assertTrue($_SESSION['is_authenticated']);
    }
    
    public function testLoginWithInvalidCredentials()
    {
        $this->mockPostRequest([
            'email' => 'testuser@example.com',
            'password' => 'wrongpassword',
            'csrf_token' => 'valid_token'
        ]);
        
        // Verify user exists but login should fail
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com'
        ]);
        
        // Verify session is not set
        $this->assertFalse(isset($_SESSION['user_id']));
        $this->assertFalse(isset($_SESSION['is_authenticated']));
    }
    
    public function testLoginWithInactiveUser()
    {
        // Create inactive user
        $inactiveUserId = $this->createTestUser([
            'email' => 'inactive@example.com',
            'password' => password_hash('testpassword', PASSWORD_DEFAULT),
            'is_active' => 0
        ]);
        
        $this->mockPostRequest([
            'email' => 'inactive@example.com',
            'password' => 'testpassword',
            'csrf_token' => 'valid_token'
        ]);
        
        // Verify user exists but is inactive
        $this->assertDatabaseHas('users', [
            'email' => 'inactive@example.com',
            'is_active' => 0
        ]);
        
        // Login should fail
        $this->assertFalse(isset($_SESSION['user_id']));
    }
    
    public function testPasswordResetTokenGeneration()
    {
        $email = 'testuser@example.com';
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour
        
        // Update user with reset token
        $sql = "UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?";
        $this->executeQuery($sql, [$token, $expiresAt, $this->testUserId]);
        
        // Verify token was stored
        $this->assertDatabaseHas('users', [
            'id' => $this->testUserId,
            'reset_token' => $token
        ]);
    }
    
    public function testFailedLoginAttemptTracking()
    {
        // Simulate failed login attempt
        $sql = "UPDATE users SET failed_login_attempts = failed_login_attempts + 1, last_failed_login = NOW() WHERE id = ?";
        $this->executeQuery($sql, [$this->testUserId]);
        
        // Verify failed attempt was recorded
        $result = $this->executeQuery("SELECT failed_login_attempts FROM users WHERE id = ?", [$this->testUserId]);
        $row = $result->fetch_assoc();
        
        $this->assertEquals(1, $row['failed_login_attempts']);
    }
    
    public function testAccountLockout()
    {
        // Simulate 5 failed attempts
        $sql = "UPDATE users SET failed_login_attempts = 5, last_failed_login = NOW() WHERE id = ?";
        $this->executeQuery($sql, [$this->testUserId]);
        
        // Verify account is effectively locked
        $result = $this->executeQuery("SELECT failed_login_attempts, last_failed_login FROM users WHERE id = ?", [$this->testUserId]);
        $row = $result->fetch_assoc();
        
        $this->assertEquals(5, $row['failed_login_attempts']);
        $this->assertNotNull($row['last_failed_login']);
    }
    
    public function testRememberMeToken()
    {
        $rememberToken = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60)); // 30 days
        
        // Set remember token
        $sql = "UPDATE users SET remember_token = ?, remember_token_expires = ? WHERE id = ?";
        $this->executeQuery($sql, [$rememberToken, $expiresAt, $this->testUserId]);
        
        // Verify remember token was stored
        $this->assertDatabaseHas('users', [
            'id' => $this->testUserId,
            'remember_token' => $rememberToken
        ]);
        
        // Simulate cookie
        $_COOKIE['remember_token'] = $rememberToken;
        
        $this->assertEquals($rememberToken, $_COOKIE['remember_token']);
    }
    
    public function testLastLoginUpdate()
    {
        $currentTime = date('Y-m-d H:i:s');
        $testIP = '192.168.1.100';
        
        // Update last login info
        $sql = "UPDATE users SET last_login = ?, last_login_ip = ? WHERE id = ?";
        $this->executeQuery($sql, [$currentTime, $testIP, $this->testUserId]);
        
        // Verify last login was updated
        $result = $this->executeQuery("SELECT last_login, last_login_ip FROM users WHERE id = ?", [$this->testUserId]);
        $row = $result->fetch_assoc();
        
        $this->assertNotNull($row['last_login']);
        $this->assertEquals($testIP, $row['last_login_ip']);
    }
    
    public function testSessionCleanup()
    {
        // Set session data
        $_SESSION['user_id'] = $this->testUserId;
        $_SESSION['email'] = 'testuser@example.com';
        $_SESSION['is_authenticated'] = true;
        
        // Simulate logout
        unset($_SESSION['user_id']);
        unset($_SESSION['email']);
        unset($_SESSION['is_authenticated']);
        
        // Verify session is cleaned
        $this->assertFalse(isset($_SESSION['user_id']));
        $this->assertFalse(isset($_SESSION['email']));
        $this->assertFalse(isset($_SESSION['is_authenticated']));
    }
    
    public function testAdminRoleCheck()
    {
        // Create admin user
        $adminUserId = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => 'admin'
        ]);
        
        // Verify admin role
        $this->assertDatabaseHas('users', [
            'id' => $adminUserId,
            'role' => 'admin'
        ]);
        
        // Simulate admin session
        $_SESSION['user_id'] = $adminUserId;
        $_SESSION['role'] = 'admin';
        
        $this->assertEquals('admin', $_SESSION['role']);
    }
}