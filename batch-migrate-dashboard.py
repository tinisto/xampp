#!/usr/bin/env python3
"""
Batch migration script for dashboard pages
Analyzes dashboard pages and provides migration plan
"""

import os
import re
from datetime import datetime

# Dashboard pages directory
DASHBOARD_DIR = "/Applications/XAMPP/xamppfiles/htdocs/pages/dashboard"

def analyze_dashboard_pages():
    """Analyze all dashboard pages that need migration"""
    pages_to_migrate = []
    
    for root, dirs, files in os.walk(DASHBOARD_DIR):
        for file in files:
            if file.endswith('.php'):
                file_path = os.path.join(root, file)
                
                # Check if file uses old template
                with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                    content = f.read()
                    
                if 'template-engine-ultimate.php' in content:
                    # Extract relative path
                    rel_path = os.path.relpath(file_path, DASHBOARD_DIR)
                    pages_to_migrate.append({
                        'path': file_path,
                        'relative': rel_path,
                        'name': file,
                        'category': get_category(rel_path)
                    })
    
    return pages_to_migrate

def get_category(rel_path):
    """Determine category based on path"""
    if 'users' in rel_path:
        return 'User Management'
    elif 'news' in rel_path:
        return 'News Management'
    elif 'posts' in rel_path:
        return 'Posts Management'
    elif 'comments' in rel_path:
        return 'Comments Management'
    elif 'schools' in rel_path:
        return 'Schools Management'
    elif 'vpo' in rel_path or 'universities' in rel_path:
        return 'VPO Management'
    elif 'spo' in rel_path or 'colleges' in rel_path:
        return 'SPO Management'
    elif 'edit' in rel_path:
        return 'Edit Forms'
    elif 'create' in rel_path:
        return 'Create Forms'
    else:
        return 'Other'

def generate_migration_plan(pages):
    """Generate migration plan grouped by category"""
    plan = {}
    
    for page in pages:
        category = page['category']
        if category not in plan:
            plan[category] = []
        plan[category].append(page)
    
    return plan

def print_migration_report(plan):
    """Print detailed migration report"""
    total_pages = sum(len(pages) for pages in plan.values())
    
    print(f"Dashboard Migration Analysis")
    print(f"{'='*60}")
    print(f"Total dashboard pages to migrate: {total_pages}")
    print(f"Generated: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    print(f"{'='*60}\n")
    
    for category, pages in sorted(plan.items()):
        print(f"\n{category} ({len(pages)} pages)")
        print(f"{'-'*40}")
        
        for page in sorted(pages, key=lambda x: x['name']):
            print(f"  - {page['relative']}")
    
    print(f"\n{'='*60}")
    print("\nMigration Strategy:")
    print("1. High Priority: User, News, Posts, Comments management")
    print("2. Medium Priority: Create/Edit forms")
    print("3. Low Priority: Schools, VPO, SPO management")
    print("\nRecommended approach:")
    print("- Create template functions for common dashboard patterns")
    print("- Migrate category by category")
    print("- Test admin functionality after each batch")

def generate_example_migration():
    """Generate example migration for a dashboard page"""
    example = """
# Example Dashboard Page Migration Template

```php
<?php
// [Page Name] - migrated to use real_template.php

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check admin access
if (!isset($_SESSION['occupation']) || $_SESSION['occupation'] !== 'admin') {
    header('Location: /unauthorized');
    exit();
}

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// [Page specific logic here]

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('[Page Title]', [
    'fontSize' => '28px',
    'margin' => '30px 0',
    'subtitle' => '[Subtitle if needed]'
]);
$greyContent1 = ob_get_clean();

// Section 2: Navigation/Actions
ob_start();
// [Navigation or action buttons]
$greyContent2 = ob_get_clean();

// Section 3-4: Additional sections as needed
$greyContent3 = '';
$greyContent4 = '';

// Section 5: Main content
ob_start();
// [Main page content - tables, forms, etc]
$greyContent5 = ob_get_clean();

// Section 6: Pagination if needed
$greyContent6 = '';

// Blue section: Empty for dashboard
$blueContent = '';

// Page title
$pageTitle = '[Page Title] - 11-классники';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
```
"""
    print(example)

if __name__ == "__main__":
    # Analyze pages
    pages = analyze_dashboard_pages()
    plan = generate_migration_plan(pages)
    
    # Print report
    print_migration_report(plan)
    
    # Show example
    print("\n" + "="*60)
    generate_example_migration()
    
    # Create summary file
    with open('dashboard-migration-plan.txt', 'w') as f:
        f.write("Dashboard Migration Plan\n")
        f.write("=" * 60 + "\n\n")
        
        for category, pages in sorted(plan.items()):
            f.write(f"{category} ({len(pages)} pages)\n")
            f.write("-" * 40 + "\n")
            for page in sorted(pages, key=lambda x: x['name']):
                f.write(f"  - {page['relative']}\n")
            f.write("\n")
    
    print(f"\nMigration plan saved to: dashboard-migration-plan.txt")