# HEADER BUG ROOT CAUSE ANALYSIS

## Problem Statement
User is seeing different headers on `/news` vs `/category/abiturientam` pages:
- One header shows GREEN primary color (`#28a745`)
- Another header shows BLUE primary color (`#007bff`) 

## Root Cause Identified

There are **TWO DIFFERENT HEADER FILES** in the codebase:

1. `/common-components/header.php` - Uses GREEN theme (`--primary-color: #28a745`)
2. `/common-components/real_header.php` - Uses BLUE theme (`--primary-color: #007bff`)

## URL Routing Analysis

Based on `.htaccess` file:
- `/news` → `news-new.php` → includes `/pages/common/news/news.php` → uses `real_template.php` → includes `real_header.php` (BLUE)
- `/category/abiturientam` → `category-new.php` → includes `/pages/category/category.php` → uses `real_template.php` → includes `real_header.php` (BLUE)

**Both should be using the same header file!**

## Investigation Results

### 1. Template System
Both pages correctly use `real_template.php`:
- ✅ `news-new.php` includes `real_template.php`
- ✅ `category-new.php` includes `real_template.php`

### 2. Header Inclusion
`real_template.php` correctly includes:
```php
<?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_header.php'; ?>
```

### 3. Legacy Files Still Using OLD Header
Found **26+ files** still incorrectly using the old green header:

**Critical Legacy Files:**
- `pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php`
- `pages/category/category-standalone.php` 
- `pages/category-news/category-news.php`
- `pages/category-news/category-news-standalone.php`
- Multiple test and working files

## Potential Causes

### Theory 1: Browser Cache
- User might have cached CSS from old header file
- Different headers served from browser cache vs fresh requests

### Theory 2: JavaScript Dynamic Loading
- Some page might be dynamically switching header files via JavaScript
- Conditional logic in template might be loading different headers

### Theory 3: Server-Side Routing Issues  
- Some condition in the routing might be serving different template files
- `.htaccess` might be routing to wrong files under certain conditions

### Theory 4: Template File Conflicts
Found multiple suspicious template files that might interfere:
- `real_template_current.php`
- `real_template_fixed.php` 
- `real_template_broken.php`
- Various other template backups

### Theory 5: Dynamic Template Selection
`real_template.php` has conditional logic for `/test/news` path:
```php
// Check if this is /test/news and set content accordingly
$requestUri = $_SERVER['REQUEST_URI'];
if (strpos($requestUri, '/test/news') !== false) {
    // Special handling for test/news
}
```

Could there be similar logic affecting other URLs?

## Recommended Investigation Steps

### Immediate Browser Testing
1. Access `/news` in incognito mode (no cache)
2. View page source and search for `primary-color`
3. Note if it shows `#007bff` (blue) or `#28a745` (green)
4. Access `/category/abiturientam` in same incognito session
5. Compare the primary color values

### Server-Side Testing  
1. Add debug output to `real_template.php` to log which header is being included
2. Add timestamp to both header files to see which was loaded
3. Check server error logs for any include failures

### Cache Testing
1. Clear browser cache completely
2. Disable browser cache in DevTools
3. Test with different browsers
4. Check if problem persists in all browsers

### File System Check
1. Verify file modification dates
2. Check if any symbolic links exist
3. Ensure no duplicate files are being served

## Next Actions Required

**DO NOT FIX YET** - First need to confirm:

1. **Browser Test Results**: What colors actually appear?
2. **Cache Status**: Does clearing cache fix the issue?
3. **Multiple Browser Test**: Does issue appear in all browsers?
4. **Server Logs**: Any PHP errors or warnings?

Once we confirm the EXACT cause, we can implement the precise fix.

## Suspicious Files to Monitor

These files still use the OLD green header and might be getting loaded somehow:
- `common-components/header.php` (GREEN theme)
- `pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php`
- Legacy template files in root directory

The bug is likely related to one of these legacy files being loaded instead of the correct unified system.