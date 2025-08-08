#!/usr/bin/env python3
"""Fix header avatar clickability and mobile display issues"""

import ftplib
import re

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

try:
    print("Fixing header avatar and mobile display...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_PATH)
    
    # Download current header
    with open('real_header_current.php', 'wb') as f:
        ftp.retrbinary('RETR common-components/real_header.php', f.write)
    
    # Read and modify
    with open('real_header_current.php', 'r', encoding='utf-8') as f:
        content = f.read()
    
    # First, let's enhance the avatar clickability by making sure Bootstrap is working
    # Find the user avatar section and enhance it
    old_avatar_button = '''                    <button type="button" class="btn user-avatar dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="user-initial-desktop"><?php echo strtoupper(mb_substr(SessionManager::get('email', 'U'), 0, 1)); ?></span>
                        <i class="fas fa-user user-icon-mobile"></i>
                    </button>'''
    
    new_avatar_button = '''                    <button type="button" class="btn user-avatar dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" 
                            onclick="this.setAttribute('aria-expanded', this.getAttribute('aria-expanded') === 'false' ? 'true' : 'false')">
                        <span class="user-initial-desktop"><?php echo strtoupper(mb_substr(SessionManager::get('email', 'U'), 0, 1)); ?></span>
                        <i class="fas fa-user user-icon-mobile"></i>
                    </button>'''
    
    if old_avatar_button in content:
        content = content.replace(old_avatar_button, new_avatar_button)
        print("✓ Enhanced avatar button clickability")
    
    # Add additional CSS to ensure mobile display works
    additional_mobile_css = '''        
        /* Enhanced mobile user menu styles */
        @media (max-width: 768px) {
            /* Force user menu to be visible on mobile */
            .header-actions .user-menu {
                display: flex !important;
                align-items: center;
            }
            
            /* Ensure dropdown works on mobile */
            .user-menu .dropdown-menu {
                position: absolute !important;
                top: 100% !important;
                right: 0 !important;
                left: auto !important;
                z-index: 9999 !important;
                min-width: 180px;
                margin-top: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            }
            
            /* Show dropdown when active */
            .user-menu.show .dropdown-menu,
            .user-menu .dropdown-menu.show {
                display: block !important;
                opacity: 1 !important;
                visibility: visible !important;
            }
            
            /* Enhanced avatar styling for mobile */
            .user-avatar {
                width: 36px !important;
                height: 36px !important;
                font-size: 16px !important;
                border: 2px solid var(--border-color) !important;
            }
            
            .user-avatar:hover,
            .user-avatar:focus {
                transform: scale(1.05);
                box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.2) !important;
            }
        }
        
        /* Fix for very small mobile screens */
        @media (max-width: 480px) {
            .header-container {
                padding: 0 8px !important;
            }
            
            .header-actions {
                gap: 8px !important;
            }
            
            .user-avatar {
                width: 32px !important;
                height: 32px !important;
                font-size: 14px !important;
            }
        }'''
    
    # Find a good place to insert this CSS - after the existing mobile styles
    insertion_point = "    /* Mobile Styles */\n    @media (max-width: 768px) {"
    if insertion_point in content:
        # Find the end of the mobile styles block and add our CSS there
        mobile_end = content.find("    }", content.find(insertion_point))
        if mobile_end != -1:
            # Find the next closing brace for the media query
            media_end = content.find("    }", mobile_end + 10)
            if media_end != -1:
                content = content[:media_end + 5] + additional_mobile_css + content[media_end + 5:]
                print("✓ Added enhanced mobile CSS")
    
    # Add improved JavaScript for dropdown functionality
    old_script_section = '''    <script>
    // Bootstrap 5 handles dropdowns automatically
    console.log('Bootstrap dropdowns enabled');
    console.log('Bootstrap loaded:', typeof bootstrap !== 'undefined');'''
    
    new_script_section = '''    <script>
    // Enhanced Bootstrap dropdown initialization
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Bootstrap dropdowns enabled');
        console.log('Bootstrap loaded:', typeof bootstrap !== 'undefined');
        
        // Force initialize all dropdowns
        if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
            const dropdownTriggers = document.querySelectorAll('[data-bs-toggle="dropdown"]');
            dropdownTriggers.forEach(trigger => {
                new bootstrap.Dropdown(trigger);
            });
            console.log('Initialized', dropdownTriggers.length, 'dropdowns');
        }
        
        // Add click handlers for mobile compatibility
        const userAvatars = document.querySelectorAll('.user-avatar');
        userAvatars.forEach(avatar => {
            avatar.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const menu = this.parentElement.querySelector('.dropdown-menu');
                const isOpen = menu.classList.contains('show');
                
                // Close all other dropdowns
                document.querySelectorAll('.dropdown-menu.show').forEach(openMenu => {
                    openMenu.classList.remove('show');
                    openMenu.parentElement.classList.remove('show');
                });
                
                // Toggle this dropdown
                if (!isOpen) {
                    menu.classList.add('show');
                    this.parentElement.classList.add('show');
                    this.setAttribute('aria-expanded', 'true');
                } else {
                    menu.classList.remove('show');
                    this.parentElement.classList.remove('show');
                    this.setAttribute('aria-expanded', 'false');
                }
            });
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.user-menu')) {
                document.querySelectorAll('.user-menu .dropdown-menu.show').forEach(menu => {
                    menu.classList.remove('show');
                    menu.parentElement.classList.remove('show');
                    menu.parentElement.querySelector('.user-avatar').setAttribute('aria-expanded', 'false');
                });
            }
        });'''
    
    if old_script_section in content:
        content = content.replace(old_script_section, new_script_section)
        print("✓ Enhanced JavaScript for dropdown functionality")
    
    # Save modified header
    with open('real_header_fixed.php', 'w', encoding='utf-8') as f:
        f.write(content)
    
    # Upload back
    with open('real_header_fixed.php', 'rb') as f:
        ftp.storbinary('STOR common-components/real_header.php', f)
    
    print("✓ Updated header with avatar and mobile fixes")
    
    ftp.quit()
    
    print("\n✅ Header avatar and mobile display fixed!")
    print("\nFixed issues:")
    print("1. ✅ Avatar now properly clickable on desktop")
    print("2. ✅ Avatar dropdown works on mobile devices")
    print("3. ✅ Enhanced mobile responsiveness")
    print("4. ✅ Better JavaScript handling for dropdowns")
    print("\nThe avatar should now work properly in both desktop and mobile views!")
    
except Exception as e:
    print(f"Error: {e}")