<?php
/**
 * Input Sanitizer Unit Tests
 */

require_once __DIR__ . '/../../TestCase.php';
require_once __DIR__ . '/../../../includes/security/input_sanitizer.php';

class InputSanitizerTest extends TestCase
{
    public function testSanitizeStringBasic()
    {
        $input = "  Hello World  ";
        $result = InputSanitizer::sanitizeString($input);
        
        $this->assertEquals("Hello World", $result);
    }
    
    public function testSanitizeStringWithHTML()
    {
        $input = "<script>alert('xss')</script>Hello <b>World</b>";
        $result = InputSanitizer::sanitizeString($input);
        
        $this->assertEquals("Hello World", $result);
        $this->assertStringNotContains('<script>', $result);
        $this->assertStringNotContains('<b>', $result);
    }
    
    public function testSanitizeStringAllowHTML()
    {
        $input = "<script>alert('xss')</script>Hello <b>World</b><p>Test</p>";
        $result = InputSanitizer::sanitizeString($input, true, ['b', 'p']);
        
        $this->assertEquals("Hello <b>World</b><p>Test</p>", $result);
        $this->assertStringNotContains('<script>', $result);
    }
    
    public function testSanitizeStringWithNullBytes()
    {
        $input = "Hello" . chr(0) . "World";
        $result = InputSanitizer::sanitizeString($input);
        
        $this->assertEquals("HelloWorld", $result);
    }
    
    public function testSanitizeEmail()
    {
        $validEmail = "test@example.com";
        $result = InputSanitizer::sanitizeEmail($validEmail);
        
        $this->assertEquals($validEmail, $result);
    }
    
    public function testSanitizeEmailInvalid()
    {
        $invalidEmail = "not-an-email";
        $result = InputSanitizer::sanitizeEmail($invalidEmail);
        
        $this->assertFalse($result);
    }
    
    public function testSanitizeEmailWithSpaces()
    {
        $emailWithSpaces = "  test@example.com  ";
        $result = InputSanitizer::sanitizeEmail($emailWithSpaces);
        
        $this->assertEquals("test@example.com", $result);
    }
    
    public function testSanitizeURL()
    {
        $validURL = "https://example.com/path";
        $result = InputSanitizer::sanitizeURL($validURL);
        
        $this->assertEquals($validURL, $result);
    }
    
    public function testSanitizeURLInvalid()
    {
        $invalidURL = "not-a-url";
        $result = InputSanitizer::sanitizeURL($invalidURL);
        
        $this->assertFalse($result);
    }
    
    public function testSanitizeInt()
    {
        $result = InputSanitizer::sanitizeInt("123");
        $this->assertEquals(123, $result);
        
        $result = InputSanitizer::sanitizeInt("123.45");
        $this->assertFalse($result);
        
        $result = InputSanitizer::sanitizeInt("abc");
        $this->assertFalse($result);
    }
    
    public function testSanitizeIntWithRange()
    {
        $result = InputSanitizer::sanitizeInt("50", 0, 100);
        $this->assertEquals(50, $result);
        
        $result = InputSanitizer::sanitizeInt("150", 0, 100);
        $this->assertFalse($result);
        
        $result = InputSanitizer::sanitizeInt("-10", 0, 100);
        $this->assertFalse($result);
    }
    
    public function testSanitizeFloat()
    {
        $result = InputSanitizer::sanitizeFloat("123.45");
        $this->assertEquals(123.45, $result);
        
        $result = InputSanitizer::sanitizeFloat("abc");
        $this->assertFalse($result);
    }
    
    public function testSanitizeFilename()
    {
        $result = InputSanitizer::sanitizeFilename("../../../etc/passwd");
        $this->assertEquals("passwd", $result);
        
        $result = InputSanitizer::sanitizeFilename("file name with spaces.txt");
        $this->assertEquals("filenamewithspaces.txt", $result);
        
        $result = InputSanitizer::sanitizeFilename(".hidden-file");
        $this->assertEquals("hidden-file", $result);
    }
    
    public function testValidatePassword()
    {
        $weakPassword = "123";
        $result = InputSanitizer::validatePassword($weakPassword);
        
        $this->assertFalse($result['valid']);
        $this->assertGreaterThan(0, count($result['errors']));
        $this->assertEquals(0, $result['score']);
    }
    
    public function testValidatePasswordStrong()
    {
        $strongPassword = "MyStr0ng!Password";
        $result = InputSanitizer::validatePassword($strongPassword);
        
        $this->assertTrue($result['valid']);
        $this->assertEquals(0, count($result['errors']));
        $this->assertEquals(5, $result['score']);
    }
    
    public function testSanitizeArray()
    {
        $input = [
            'email' => '  test@example.com  ',
            'age' => '25',
            'website' => 'https://example.com',
            'name' => '<script>alert("xss")</script>John'
        ];
        
        $rules = [
            'email' => ['type' => 'email'],
            'age' => ['type' => 'int', 'min' => 0, 'max' => 150],
            'website' => ['type' => 'url'],
            'name' => ['type' => 'string']
        ];
        
        $result = InputSanitizer::sanitizeArray($input, $rules);
        
        $this->assertEquals('test@example.com', $result['email']);
        $this->assertEquals(25, $result['age']);
        $this->assertEquals('https://example.com', $result['website']);
        $this->assertEquals('John', $result['name']);
    }
    
    public function testEscapeHTML()
    {
        $input = '<script>alert("xss")</script>';
        $result = InputSanitizer::escapeHTML($input);
        
        $this->assertStringContains('&lt;script&gt;', $result);
        $this->assertStringContains('&quot;', $result);
    }
    
    public function testEscapeJS()
    {
        $input = 'alert("Hello");';
        $result = InputSanitizer::escapeJS($input);
        
        $this->assertStringContains('\u0022', $result); // Escaped quotes
        $this->assertIsString($result);
    }
    
    public function testEscapeCSS()
    {
        $input = 'color: red; background: url("javascript:alert(1)")';
        $result = InputSanitizer::escapeCSS($input);
        
        // Should only contain safe characters
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9\-_]*$/', $result);
    }
    
    public function testSanitizeHTML()
    {
        $input = '<p>Hello</p><script>alert("xss")</script><a href="javascript:alert(1)">Link</a>';
        $result = InputSanitizer::sanitizeHTML($input, ['p', 'a']);
        
        $this->assertStringContains('<p>Hello</p>', $result);
        $this->assertStringNotContains('<script>', $result);
        $this->assertStringNotContains('javascript:', $result);
    }
}