# Manual Deployment Guide - News System Fix

## Issue
- Header "Новости" still opens dropdown menu instead of going to /news
- /news URL shows 404 because files not uploaded

## Required Files to Upload

### 1. CRITICAL: Header Fix
**File:** `common-components/header.php`
**Key change:** Line 321-322 should be:
```php
<li class="nav-item">
    <a class="nav-link" href="/news">Новости</a>
</li>
```
**NOT:**
```php
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Новости</a>
    <ul class="dropdown-menu">...
```

### 2. Main News Page
**File:** `pages/news/news-main.php` (NEW FILE)
**Directory:** Create `pages/news/` folder first
**Purpose:** Main news hub page similar to schoolbuscircle.com

### 3. URL Routing
**File:** `.htaccess`
**Key additions:** Lines should include:
```apache
# Handle requests for main /news page
RewriteRule ^news$ pages/news/news-main.php [QSA,NC,L]

# Redirect old category-news URLs to new structure  
RewriteRule ^category-news/([^/]+)$ /news/$1 [R=301,L]

# Handle news categories
RewriteRule ^news/(novosti-.+)$ pages/category-news/category-news.php?url_category_news=$1 [QSA,NC,L]

# Handle individual news articles
RewriteRule ^news/([^/]+)$ pages/common/news/news.php?url_news=$1 [QSA,NC,L]
```

## Expected Results After Upload

### ✅ Working URLs:
- **https://11klassniki.ru/news** → Main news page
- **https://11klassniki.ru/news/novosti-vuzov** → University news  
- **https://11klassniki.ru/news/novosti-ssuzov** → College news
- **https://11klassniki.ru/news/novosti-shkol** → School news

### ✅ Header Behavior:
- **"Новости"** → Direct link to /news (no dropdown)
- **"Тесты"** → Direct link to /tests

### ✅ Redirects:
- **Old URLs** `/category-news/*` → **New URLs** `/news/*`

## Files Ready for Upload:
1. `common-components/header.php` - Fixed navigation
2. `pages/news/news-main.php` - Main news page
3. `.htaccess` - Updated routing

## Priority: Upload header.php first
This will immediately fix the dropdown issue and make "Новости" a clickable link.