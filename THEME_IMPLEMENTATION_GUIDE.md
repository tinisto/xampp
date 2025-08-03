# Modern Theme Implementation Guide

## Overview
This guide explains how to implement YouTube-style dark mode using CSS variables across the 11klassniki.ru website.

## Core Principles

### 1. CSS Variables (Custom Properties)
Instead of hardcoding colors, use semantic CSS variables:

```css
/* ❌ Bad - Hardcoded */
.card {
  background: #ffffff;
  color: #333333;
}

/* ✅ Good - CSS Variables */
.card {
  background: var(--color-surface-primary);
  color: var(--color-text-primary);
}
```

### 2. Semantic Naming Convention
Use descriptive names that indicate purpose, not appearance:

```css
/* ❌ Bad */
--white-color: #ffffff;
--gray-light: #f8f9fa;

/* ✅ Good */
--color-surface-primary: #ffffff;
--color-surface-secondary: #f8f9fa;
```

### 3. Variable Categories

#### Surface Colors (Backgrounds)
- `--color-surface-primary`: Main background (cards, modals)
- `--color-surface-secondary`: Subtle backgrounds
- `--color-surface-tertiary`: Even more subtle
- `--color-surface-elevated`: Dropdowns, popovers

#### Text Colors
- `--color-text-primary`: Main text
- `--color-text-secondary`: Muted text
- `--color-text-tertiary`: Very muted text
- `--color-text-inverse`: Text on colored backgrounds

#### Interactive Elements
- `--color-primary`: Primary brand color
- `--color-primary-hover`: Hover state
- `--color-link`: Links
- `--color-link-hover`: Link hover

#### Borders
- `--color-border-primary`: Main borders
- `--color-border-secondary`: Subtle borders

#### Effects
- `--color-bg-hover`: Hover backgrounds (rgba)
- `--color-bg-active`: Active/pressed states
- `--color-shadow-sm/md/lg`: Shadows

## Implementation Steps

### 1. Include Theme Variables CSS
Add to your template's `<head>`:
```html
<link rel="stylesheet" href="/css/theme-variables.css">
```

### 2. Update Component Styles
Replace hardcoded colors:

```css
/* Before */
.header {
  background: #ffffff;
  border-bottom: 1px solid #e2e8f0;
}

/* After */
.header {
  background: var(--color-header-bg);
  border-bottom: 1px solid var(--color-border-primary);
}
```

### 3. Handle Hover States (YouTube-style)
Use subtle opacity backgrounds:

```css
.button:hover {
  background: var(--color-bg-hover); /* rgba(0,0,0,0.05) light / rgba(255,255,255,0.08) dark */
}
```

### 4. Theme Toggle Implementation
```javascript
function toggleTheme() {
  const currentTheme = document.documentElement.getAttribute('data-theme');
  const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
  
  document.documentElement.setAttribute('data-theme', newTheme);
  localStorage.setItem('preferred-theme', newTheme);
}
```

## Converting Existing Components

### Example: Card Component

#### Before:
```css
.post-card {
  background: white;
  border: 1px solid #e2e8f0;
  color: #333;
}

.post-card:hover {
  background: #f8f9fa;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

[data-theme="dark"] .post-card {
  background: #334155;
  border-color: #475569;
  color: #f1f1f1;
}
```

#### After:
```css
.post-card {
  background: var(--color-card-bg);
  border: 1px solid var(--color-border-primary);
  color: var(--color-text-primary);
}

.post-card:hover {
  background: var(--color-bg-hover);
  box-shadow: 0 4px 12px var(--color-shadow-sm);
}

/* No need for separate dark mode styles! */
```

## Best Practices

### 1. Transitions
Add smooth transitions for theme changes:
```css
* {
  transition: background-color 250ms ease,
              color 250ms ease,
              border-color 250ms ease;
}
```

### 2. Avoid !important
CSS variables cascade naturally, so `!important` is rarely needed.

### 3. Test Both Themes
Always check your components in both light and dark modes.

### 4. Accessibility
Ensure sufficient contrast ratios:
- Normal text: 4.5:1
- Large text: 3:1
- Interactive elements: 3:1

## Migration Checklist

- [ ] Include `/css/theme-variables.css` in template
- [ ] Replace hardcoded colors with variables
- [ ] Remove duplicate dark mode CSS
- [ ] Test hover/active states
- [ ] Verify transitions work smoothly
- [ ] Check mobile responsiveness
- [ ] Test in both themes

## Common Patterns

### Subtle Hover Effect (YouTube-style)
```css
.item:hover {
  background: var(--color-bg-hover);
}
```

### Card with Border
```css
.card {
  background: var(--color-card-bg);
  border: 1px solid var(--color-border-primary);
}
```

### Primary Button
```css
.btn-primary {
  background: var(--color-primary);
  color: var(--color-text-inverse);
}

.btn-primary:hover {
  background: var(--color-primary-hover);
}
```

### Form Input
```css
input {
  background: var(--color-surface-secondary);
  border: 1px solid var(--color-border-primary);
  color: var(--color-text-primary);
}

input:focus {
  background: var(--color-surface-primary);
  border-color: var(--color-primary);
}
```

## Debugging

If colors aren't working:
1. Check if theme-variables.css is loaded
2. Verify `data-theme` attribute on `<html>`
3. Check for typos in variable names
4. Look for hardcoded colors overriding variables

## Resources

- Test page: `/test-theme.php`
- Migration script: `/scripts/migrate-to-css-variables.php`
- Example header: `/common-components/header-modern-vars.php`
- Template engine: `/common-components/template-engine-modern.php`