<?php
/**
 * CSRF Protection Unit Tests
 */

require_once __DIR__ . '/../../TestCase.php';
require_once __DIR__ . '/../../../includes/security/csrf.php';

class CSRFProtectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear any existing CSRF tokens
        unset($_SESSION['csrf_tokens']);
    }
    
    public function testGenerateToken()
    {
        $token = CSRFProtection::generateToken();
        
        $this->assertNotEmpty($token);
        $this->assertEquals(64, strlen($token)); // 32 bytes = 64 hex chars
        $this->assertIsString($token);
    }
    
    public function testGenerateTokenWithFormName()
    {
        $token1 = CSRFProtection::generateToken('login');
        $token2 = CSRFProtection::generateToken('comment');
        
        $this->assertNotEmpty($token1);
        $this->assertNotEmpty($token2);
        $this->assertNotEquals($token1, $token2);
    }
    
    public function testValidateTokenSuccess()
    {
        $token = CSRFProtection::generateToken('test_form');
        
        $isValid = CSRFProtection::validateToken($token, 'test_form');
        
        $this->assertTrue($isValid);
    }
    
    public function testValidateTokenFailure()
    {
        CSRFProtection::generateToken('test_form');
        
        $isValid = CSRFProtection::validateToken('invalid_token', 'test_form');
        
        $this->assertFalse($isValid);
    }
    
    public function testValidateTokenExpired()
    {
        $token = CSRFProtection::generateToken('test_form');
        
        // Manually set expiration time to past
        $_SESSION['csrf_tokens']['test_form']['timestamp'] = time() - 7200; // 2 hours ago
        
        $isValid = CSRFProtection::validateToken($token, 'test_form', 3600); // 1 hour max age
        
        $this->assertFalse($isValid);
    }
    
    public function testValidateAndConsumeToken()
    {
        $token = CSRFProtection::generateToken('test_form');
        
        // First validation should succeed and consume token
        $isValid1 = CSRFProtection::validateAndConsumeToken($token, 'test_form');
        $this->assertTrue($isValid1);
        
        // Second validation should fail as token was consumed
        $isValid2 = CSRFProtection::validateAndConsumeToken($token, 'test_form');
        $this->assertFalse($isValid2);
    }
    
    public function testGetTokenField()
    {
        $html = CSRFProtection::getTokenField('test_form');
        
        $this->assertStringContains('input', $html);
        $this->assertStringContains('type="hidden"', $html);
        $this->assertStringContains('name="csrf_token"', $html);
        $this->assertStringContains('value=', $html);
    }
    
    public function testGetTokenValue()
    {
        $token = CSRFProtection::getTokenValue('test_form');
        
        $this->assertNotEmpty($token);
        $this->assertEquals(64, strlen($token));
    }
    
    public function testGetMetaTag()
    {
        $html = CSRFProtection::getMetaTag();
        
        $this->assertStringContains('<meta', $html);
        $this->assertStringContains('name="csrf-token"', $html);
        $this->assertStringContains('content=', $html);
    }
    
    public function testCheckRequestSuccess()
    {
        $token = CSRFProtection::generateToken('test_form');
        
        $data = ['csrf_token' => $token, 'other_field' => 'value'];
        
        // Should not throw exception
        $this->expectNotToPerformAssertions();
        CSRFProtection::checkRequest($data, 'test_form');
    }
    
    public function testCheckRequestMissingToken()
    {
        $this->expectOutputRegex('/CSRF token missing/');
        
        $data = ['other_field' => 'value'];
        CSRFProtection::checkRequest($data, 'test_form');
    }
    
    public function testCheckRequestInvalidToken()
    {
        CSRFProtection::generateToken('test_form');
        
        $this->expectOutputRegex('/Invalid CSRF token/');
        
        $data = ['csrf_token' => 'invalid_token', 'other_field' => 'value'];
        CSRFProtection::checkRequest($data, 'test_form');
    }
    
    public function testMultipleTokens()
    {
        $loginToken = CSRFProtection::generateToken('login');
        $commentToken = CSRFProtection::generateToken('comment');
        $profileToken = CSRFProtection::generateToken('profile');
        
        // All tokens should be valid
        $this->assertTrue(CSRFProtection::validateToken($loginToken, 'login'));
        $this->assertTrue(CSRFProtection::validateToken($commentToken, 'comment'));
        $this->assertTrue(CSRFProtection::validateToken($profileToken, 'profile'));
        
        // Cross-validation should fail
        $this->assertFalse(CSRFProtection::validateToken($loginToken, 'comment'));
        $this->assertFalse(CSRFProtection::validateToken($commentToken, 'profile'));
    }
}