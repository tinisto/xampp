/**
 * Global dropdown fix to ensure dropdowns work on all pages
 */

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dropdown fix loaded');
    
    // Re-initialize dropdown functionality
    function initializeDropdowns() {
        // Remove any existing listeners to prevent duplicates
        document.querySelectorAll('.dropdown-toggle').forEach(element => {
            element.removeEventListener('click', handleDropdownClick);
        });
        
        // Add fresh listeners
        document.querySelectorAll('.dropdown-toggle').forEach(element => {
            element.addEventListener('click', handleDropdownClick);
        });
        
        // Also handle user avatar specifically
        const userAvatar = document.querySelector('.user-avatar.dropdown-toggle');
        if (userAvatar) {
            userAvatar.style.cursor = 'pointer';
            userAvatar.removeEventListener('click', handleDropdownClick);
            userAvatar.addEventListener('click', handleDropdownClick);
        }
    }
    
    // Handle dropdown click
    function handleDropdownClick(event) {
        event.preventDefault();
        event.stopPropagation();
        
        const element = this;
        console.log('Dropdown clicked', element);
        
        // Close all other dropdowns
        document.querySelectorAll('.dropdown.show').forEach(dropdown => {
            if (dropdown !== element.closest('.dropdown')) {
                dropdown.classList.remove('show');
            }
        });
        
        // Toggle current dropdown
        const dropdownElement = element.closest('.dropdown');
        if (dropdownElement) {
            dropdownElement.classList.toggle('show');
            
            // Force visibility for dropdown menu
            const menu = dropdownElement.querySelector('.dropdown-menu');
            if (menu && dropdownElement.classList.contains('show')) {
                menu.style.display = 'block';
                menu.style.visibility = 'visible';
                menu.style.opacity = '1';
                menu.style.pointerEvents = 'auto';
                menu.style.zIndex = '9999';
            }
        }
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown.show').forEach(dropdown => {
                dropdown.classList.remove('show');
                const menu = dropdown.querySelector('.dropdown-menu');
                if (menu) {
                    menu.style.display = '';
                    menu.style.visibility = '';
                    menu.style.opacity = '';
                    menu.style.pointerEvents = '';
                }
            });
        }
    });
    
    // Initialize on load
    initializeDropdowns();
    
    // Re-initialize after any dynamic content changes
    const observer = new MutationObserver(function(mutations) {
        initializeDropdowns();
    });
    
    // Observe header for changes
    const header = document.querySelector('header');
    if (header) {
        observer.observe(header, { childList: true, subtree: true });
    }
});