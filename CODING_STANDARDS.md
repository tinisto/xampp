# Coding Standards for 11-классники

## PHP Standards

### 1. PSR Standards
- Follow PSR-1 and PSR-2 coding standards
- Use PSR-4 for autoloading

### 2. Naming Conventions
- **Classes**: PascalCase (e.g., `UserController`, `SchoolModel`)
- **Methods/Functions**: camelCase (e.g., `getUserById`, `validateEmail`)
- **Variables**: camelCase (e.g., `$userName`, `$isActive`)
- **Constants**: UPPER_SNAKE_CASE (e.g., `MAX_LOGIN_ATTEMPTS`)
- **Database tables**: snake_case (e.g., `user_accounts`, `school_info`)
- **Database columns**: snake_case (e.g., `user_id`, `created_at`)

### 3. File Organization
```
/app
  /controllers   - MVC Controllers
  /models       - Data models
  /views        - View templates
  /core         - Core framework classes
/includes       - Helper functions and utilities
/config         - Configuration files
/public         - Public assets (css, js, images)
/database       - Database connections and migrations
/logs          - Application logs
```

### 4. Security Best Practices
- Always use prepared statements for database queries
- Escape all output with `htmlspecialchars()` or helper `h()`
- Validate all input data
- Use CSRF tokens for all forms
- Hash passwords with `password_hash()`
- Never commit sensitive data (.env files, passwords)

### 5. Database Access
```php
// Use the Database class for all queries
$db = Database::getInstance();

// Good: Using prepared statements
$user = $db->queryOne("SELECT * FROM users WHERE email = ?", [$email]);

// Bad: Direct string concatenation
$user = $db->query("SELECT * FROM users WHERE email = '$email'");
```

### 6. Error Handling
```php
// Use try-catch for database operations
try {
    $result = $db->insert('users', $userData);
} catch (Exception $e) {
    ErrorHandler::log($e->getMessage(), 'error');
    // Handle error appropriately
}
```

### 7. Comments and Documentation
```php
/**
 * Get user by email address
 * 
 * @param string $email User's email address
 * @return array|null User data or null if not found
 */
public function getUserByEmail($email) {
    // Implementation
}
```

## JavaScript Standards

### 1. ES6+ Features
- Use `const` and `let` instead of `var`
- Use arrow functions where appropriate
- Use template literals for string concatenation

### 2. Naming Conventions
- **Variables/Functions**: camelCase
- **Constants**: UPPER_SNAKE_CASE
- **Classes**: PascalCase

### 3. Code Style
```javascript
// Good
const userName = 'John';
const getUserData = async (userId) => {
    try {
        const response = await fetch(`/api/users/${userId}`);
        return await response.json();
    } catch (error) {
        console.error('Error fetching user:', error);
    }
};

// Bad
var user_name = "John";
function get_user_data(user_id) {
    // ...
}
```

## CSS Standards

### 1. Naming Convention
- Use BEM methodology for CSS classes
- Use kebab-case for class names

### 2. Organization
```css
/* Component */
.user-card {
    /* ... */
}

/* Element */
.user-card__title {
    /* ... */
}

/* Modifier */
.user-card--featured {
    /* ... */
}
```

### 3. Best Practices
- Avoid inline styles
- Use CSS variables for colors and common values
- Keep specificity low
- Mobile-first approach

## Git Commit Messages

### Format
```
<type>: <subject>

<body>
```

### Types
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc)
- `refactor`: Code refactoring
- `test`: Test additions or changes
- `chore`: Build process or auxiliary tool changes

### Examples
```
feat: add user registration validation

fix: prevent SQL injection in search functionality

refactor: convert procedural code to MVC pattern
```

## Code Review Checklist

- [ ] No SQL injection vulnerabilities
- [ ] All output is properly escaped
- [ ] CSRF protection on forms
- [ ] Input validation implemented
- [ ] Error handling in place
- [ ] Code follows naming conventions
- [ ] No hardcoded credentials
- [ ] Database queries use prepared statements
- [ ] Functions are documented
- [ ] No console.log() in production code