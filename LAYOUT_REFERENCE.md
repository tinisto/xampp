# Layout Reference Files

## Final Working Layout Structure

### Key Files:
- `test-real-layout.php` - Complete working layout with all fixes
- `common-components/header.php` - Updated header with mobile navigation
- `common-components/footer-unified.php` - Compact mobile footer
- `common-components/page-section-header.php` - Consistent spacing

### Layout Structure:
```html
<body style="background: #212529; overflow: hidden;">
  <header class="main-header" style="background: white;">
    <!-- Header component -->
  </header>
  
  <div class="yellow-bg-wrapper" style="background: yellow; flex: 1;">
    <div class="page-header">
      <!-- Green section -->
    </div>
    <main class="content" style="background: red;">
      <!-- Main content -->
    </main>
    <div class="comments-section" style="background: blue;">
      <!-- Comments -->
    </div>
  </div>
  
  <footer class="main-footer" style="background: #f8f9fa;">
    <!-- Footer component -->
  </footer>
</body>
```

### Key CSS Features:
- Body: Dark overscroll background, no scrollbars
- Mobile spacing: 10px padding/margins
- Desktop spacing: 40px padding/margins
- Header/Footer: No width constraints, full width
- Flexbox layout: Header/footer fixed, middle sections flexible
- No white/yellow overscroll areas

### Upload Scripts:
- `upload-fix-overscroll-final.py` - Latest deployment script

### Test URL:
https://11klassniki.ru/test-real-layout.php