# Integration Guide for Security Improvements

The security files are deployed but need to be integrated into existing pages.

## Quick Integration Steps

### 1. Add to the top of EVERY PHP page:
```php
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';
?>
```

### 2. For pages with forms, update them:
```php
<!-- Old form -->
<form method="post" action="...">

<!-- New form with CSRF -->
<form method="post" action="...">
    <?php echo csrf_field(); ?>
```

### 3. For database queries, replace:
```php
// OLD - Vulnerable
$query = "SELECT * FROM users WHERE email = '$email'";
$result = mysqli_query($connection, $query);

// NEW - Secure
$db = Database::getInstance();
$user = $db->queryOne("SELECT * FROM users WHERE email = ?", [$email]);
```

### 4. For output, replace:
```php
// OLD - XSS vulnerable
echo $user_input;

// NEW - XSS protected
echo h($user_input);
```

## Priority Files to Update

1. `/login/index.php` - Add CSRF to login form
2. `/search/index.php` - Use secure search
3. `/registration/index.php` - Add CSRF and validation
4. Any file that connects to database

## Testing After Integration

1. Try to search for: `<script>alert('xss')</script>`
   - Should display safely, not execute

2. Try to submit a form without CSRF token
   - Should be rejected

3. Check `/logs/errors.log` for any issues

## Important Notes

- The new includes are in `/includes/` directory
- Database class handles all SQL injection prevention
- `h()` function escapes all output
- CSRF tokens expire after 1 hour