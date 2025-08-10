#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import re
import os
import glob

def extract_htaccess_routes(filepath):
    """Extract all routes from .htaccess file"""
    routes = {}
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.readlines()
        
        for line in content:
            # Match RewriteRule patterns
            match = re.match(r'\s*RewriteRule\s+\^([^$\s]+)\$?\s+([^\s]+)\s+\[.*\]', line)
            if match:
                route = match.group(1).replace('/?', '').replace('\\', '').replace('$', '')
                target = match.group(2)
                routes[route] = target
    except Exception as e:
        print(f"Error reading .htaccess: {e}")
    
    return routes

def find_all_links(directory):
    """Find all href links in PHP files"""
    links = {}
    php_files = glob.glob(os.path.join(directory, "**/*.php"), recursive=True)
    
    # Common link patterns
    patterns = [
        r'href=["\']([^"\']+)["\']',
        r'location\.href\s*=\s*["\']([^"\']+)["\']',
        r'window\.location\s*=\s*["\']([^"\']+)["\']',
        r'header\s*\(\s*["\']Location:\s*([^"\']+)["\']',
        r'redirect\s*\(\s*["\']([^"\']+)["\']'
    ]
    
    for php_file in php_files:
        if 'vendor' in php_file or 'node_modules' in php_file:
            continue
            
        try:
            with open(php_file, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
            
            for pattern in patterns:
                matches = re.findall(pattern, content, re.IGNORECASE)
                for match in matches:
                    # Only process internal links
                    if match.startswith('/') and not match.startswith('//'):
                        # Clean up the link
                        link = match.split('?')[0].split('#')[0].rstrip('/')
                        if link:  # Skip empty links
                            if link not in links:
                                links[link] = []
                            links[link].append(os.path.basename(php_file))
        except Exception as e:
            pass
    
    return links

def main():
    print("🔍 Analyzing all routes and links...\n")
    
    # Get current directory
    current_dir = "/Applications/XAMPP/xamppfiles/htdocs"
    htaccess_path = os.path.join(current_dir, ".htaccess")
    
    # Extract routes from .htaccess
    print("📋 Extracting routes from .htaccess...")
    routes = extract_htaccess_routes(htaccess_path)
    print(f"Found {len(routes)} routes in .htaccess\n")
    
    # Find all links in PHP files
    print("🔍 Scanning PHP files for links...")
    links = find_all_links(current_dir)
    print(f"Found {len(links)} unique internal links\n")
    
    # Analyze discrepancies
    print("📊 Analysis Results:\n")
    
    # Group routes by category
    route_categories = {
        'auth': ['login', 'registration', 'forgot-password', 'reset-password', 'logout', 'account'],
        'content': ['news', 'post', 'category', 'tests', 'test'],
        'institutions': ['schools', 'vpo', 'spo', 'educational'],
        'admin': ['dashboard', 'create', 'edit'],
        'static': ['about', 'privacy', 'terms', 'write', 'search']
    }
    
    # Check each category
    for category, keywords in route_categories.items():
        print(f"\n🔸 {category.upper()} ROUTES:")
        
        # Find routes in this category
        category_routes = {}
        for route, target in routes.items():
            if any(keyword in route.lower() for keyword in keywords):
                category_routes[route] = target
        
        if category_routes:
            print(f"  📍 Routes in .htaccess:")
            for route, target in sorted(category_routes.items()):
                print(f"    /{route} → {target}")
        
        # Find links in this category
        category_links = {}
        for link, files in links.items():
            if any(keyword in link.lower() for keyword in keywords):
                category_links[link] = files
        
        if category_links:
            print(f"\n  🔗 Links found in PHP files:")
            for link in sorted(category_links.keys())[:10]:  # Show first 10
                print(f"    {link} (used in {len(category_links[link])} files)")
    
    # Find problematic links (not in .htaccess)
    print("\n\n⚠️  POTENTIAL ISSUES:")
    print("Links used in PHP files but not defined in .htaccess:\n")
    
    problematic_links = []
    for link in links:
        # Clean link for comparison
        clean_link = link.strip('/')
        
        # Check if this link has a route
        found = False
        for route in routes:
            # Handle regex patterns in routes
            route_pattern = route.replace('([^/]+)', '[^/]+').replace('(\\d+)', '\\d+')
            if re.match(f"^{route_pattern}$", clean_link):
                found = True
                break
        
        if not found and not link.endswith('.php') and not link.startswith('/pages/'):
            problematic_links.append((link, len(links[link])))
    
    # Sort by usage frequency
    problematic_links.sort(key=lambda x: x[1], reverse=True)
    
    for link, count in problematic_links[:20]:  # Show top 20
        print(f"  ❌ {link} (used in {count} files)")
    
    # Recommendations
    print("\n\n💡 RECOMMENDATIONS:")
    print("1. Add missing routes to .htaccess for links that return 404")
    print("2. Update links in PHP files to match existing routes")
    print("3. Ensure all auth routes (/login, /registration, etc.) work with and without trailing slash")
    print("4. Consider creating redirects for commonly mistyped URLs")
    
    # Generate fix script
    print("\n\n📝 Generating fix recommendations...")
    
    with open('route-fixes.txt', 'w') as f:
        f.write("# Recommended .htaccess additions:\n\n")
        
        # Auth routes that should work with/without trailing slash
        auth_routes = ['login', 'registration', 'logout', 'account', 'forgot-password', 'reset-password']
        for route in auth_routes:
            f.write(f"RewriteRule ^{route}/?$ {route}-template.php [QSA,NC,L]\n")
        
        f.write("\n# Catch-all rules for common variations:\n")
        f.write("RewriteRule ^signin/?$ /login [R=301,L]\n")
        f.write("RewriteRule ^signup/?$ /registration [R=301,L]\n")
        f.write("RewriteRule ^register/?$ /registration [R=301,L]\n")
    
    print("✅ Fix recommendations saved to route-fixes.txt")

if __name__ == "__main__":
    main()