# Code Quality Tools for 11klassniki

This project uses automated code quality tools to maintain high standards.

## 🛠️ Installed Tools

### 1. **PHPStan** - Static Analysis
- Finds bugs without running the code
- Checks for type errors, undefined variables, etc.
- Similar to TypeScript for PHP

### 2. **PHP_CodeSniffer** - Code Style
- Enforces consistent code style (PSR-12 standard)
- Like ESLint for PHP

### 3. **PHP CS Fixer** - Auto-formatter
- Automatically fixes code style issues
- Like Prettier for PHP

## 🚀 Quick Commands

```bash
# Run all checks
./check-code.sh

# Or use make commands:
make stan      # Run PHPStan only
make cs        # Check code style
make fix       # Auto-fix code style
make check     # Run all checks
```

## 📋 Pre-commit Hook

A pre-commit hook is installed that automatically checks your code before each commit:
- Runs PHPStan on changed PHP files
- Checks code style with PHP_CodeSniffer
- Prevents commit if errors are found

To skip the hook temporarily:
```bash
git commit --no-verify
```

## 🔧 Configuration Files

- `phpstan.neon` - PHPStan configuration
- `phpcs.xml` - PHP_CodeSniffer rules
- `.php-cs-fixer.php` - PHP CS Fixer settings

## 📊 Understanding Errors

### PHPStan Levels
- **Level 0**: Basic checks
- **Level 5**: Moderate strictness (current setting)
- **Level 9**: Maximum strictness

### Common Fixes

**Undefined variable:**
```php
// Bad
echo $undefinedVar;

// Good
$undefinedVar = '';
echo $undefinedVar;
```

**Type mismatch:**
```php
// Bad
function add(int $a, int $b) {
    return $a + $b;
}
add("1", "2"); // Strings passed to int parameters

// Good
add(1, 2); // Integers passed
```

## 🎯 Benefits

1. **Catch bugs early** - Before they reach production
2. **Consistent code** - Easier to read and maintain
3. **Best practices** - Enforces PHP standards
4. **Team collaboration** - Everyone follows same rules

## 💡 Tips

- Run `./check-code.sh` before pushing code
- Use `make fix` to auto-fix style issues
- Start with fixing critical errors first
- Gradually increase PHPStan level as code improves