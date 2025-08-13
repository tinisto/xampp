# Dark Mode Implementation Guide

## Overview

This guide explains how to implement dark mode support across the site by replacing inline styles with CSS classes that automatically adapt to the user's theme preference.

## CSS Files

### 1. `/css/dark-mode-fix.css`
The main CSS file containing:
- CSS variables for light and dark themes
- Core utility classes for text, backgrounds, and borders
- Article content styling
- Component-specific classes

### 2. `/css/dark-mode-utilities.css`
Additional utility classes for:
- Cards and containers
- Tables and lists
- Badges and alerts
- Forms and inputs
- Special components

## Core CSS Variables

```css
/* Light mode (default) */
--color-text-primary: #212529;
--color-text-secondary: #6c757d;
--color-text-heading: #000000;
--color-bg-primary: #ffffff;
--color-bg-secondary: #f8f9fa;
--color-border: #dee2e6;

/* Dark mode */
--color-text-primary: #f1f5f9;
--color-text-secondary: #94a3b8;
--color-text-heading: #ffffff;
--color-bg-primary: #0f172a;
--color-bg-secondary: #1e293b;
--color-border: #334155;
```

## Common Replacements

### Text Colors

| Old Inline Style | New Class |
|-----------------|-----------|
| `style="color: #000"` | `class="text-primary-adaptive"` |
| `style="color: #333"` | `class="text-primary-adaptive"` |
| `style="color: #666"` | `class="text-secondary-adaptive"` |
| `style="color: #999"` | `class="text-muted-adaptive"` |
| Link colors | `class="text-link-adaptive"` |

### Backgrounds

| Old Inline Style | New Class |
|-----------------|-----------|
| `style="background: white"` | `class="bg-primary-adaptive"` |
| `style="background: #f8f9fa"` | `class="bg-secondary-adaptive"` |
| `style="background: #e9ecef"` | `class="bg-tertiary-adaptive"` |

### Components

| Component | Class |
|-----------|-------|
| Article content | `class="article-content"` |
| Share buttons | `class="share-button share-button-vk"` |
| Navigation buttons | `class="nav-button-primary"` |
| Form inputs | `class="form-input-adaptive"` |
| Form labels | `class="form-label-adaptive"` |

## Implementation Examples

### 1. Basic Text
```html
<!-- Before -->
<p style="color: #666;">Some text</p>

<!-- After -->
<p class="text-secondary-adaptive">Some text</p>
```

### 2. Headings
```html
<!-- Before -->
<h1 style="color: #000; font-size: 32px;">Title</h1>

<!-- After -->
<h1 class="text-heading-adaptive" style="font-size: 32px;">Title</h1>
```

### 3. Cards
```html
<!-- Before -->
<div style="background: white; border: 1px solid #ddd;">
    <h3 style="color: #333;">Card Title</h3>
    <p style="color: #666;">Card content</p>
</div>

<!-- After -->
<div class="card-adaptive">
    <h3 class="text-heading-adaptive">Card Title</h3>
    <p class="text-primary-adaptive">Card content</p>
</div>
```

### 4. Buttons
```html
<!-- Before -->
<a href="#" style="background: #28a745; color: white; padding: 10px 20px;">
    Button
</a>

<!-- After -->
<a href="#" class="nav-button-primary">
    Button
</a>
```

### 5. Forms
```html
<!-- Before -->
<label style="color: #333;">Name</label>
<input type="text" style="border: 2px solid #ddd; color: #333;">

<!-- After -->
<label class="form-label-adaptive">Name</label>
<input type="text" class="form-input-adaptive">
```

## Page Structure

### Including CSS
Add this to the top of your page content:
```html
<link rel="stylesheet" href="/css/dark-mode-fix.css">
```

### Section Wrappers
```html
<!-- Primary section (white/dark background) -->
<div class="section-adaptive">
    <!-- Content -->
</div>

<!-- Secondary section (light gray/darker background) -->
<div class="section-alt-adaptive">
    <!-- Content -->
</div>
```

## Best Practices

1. **Always use adaptive classes** instead of hardcoded colors
2. **Maintain other styles** - Only replace color-related styles
3. **Test in both modes** - Check appearance in light and dark themes
4. **Use semantic classes** - Choose classes that describe the content role
5. **Avoid !important** - Let the cascade work naturally

## Migration Strategy

1. **Add CSS file** to pages that need dark mode support
2. **Replace inline colors** with appropriate classes
3. **Test thoroughly** in both light and dark modes
4. **Update gradually** - Start with high-traffic pages

## Helper Functions

Use the PHP helper functions for automatic conversion:

```php
require_once '/includes/dark-mode-helpers.php';

// Apply dark mode classes to HTML
$html = apply_dark_mode_classes($html);

// Include CSS
echo include_dark_mode_css();
```

## Troubleshooting

### Text not changing color
- Check if inline styles have `!important`
- Ensure CSS file is loaded
- Verify correct class is applied

### Background issues
- Check parent element backgrounds
- Look for conflicting styles
- Use browser inspector to debug

### Form elements
- Bootstrap form controls may need additional classes
- Check focus states
- Test placeholder text visibility

## Future Improvements

1. Add more component-specific classes
2. Create SCSS version for easier customization
3. Add JavaScript helpers for dynamic content
4. Implement theme transition animations