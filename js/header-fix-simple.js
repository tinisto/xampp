// Simple Header Dropdown Fix - No Bootstrap conflicts
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for all elements to load
    setTimeout(function() {
        console.log('Initializing simple dropdown fix...');
        
        // Find the user avatar dropdown specifically
        const userAvatarLinks = document.querySelectorAll('.user-avatar, .notification-item a');
        
        userAvatarLinks.forEach(function(avatar) {
            if (!avatar) return;
            
            console.log('Setting up avatar click:', avatar);
            
            avatar.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Avatar clicked');
                
                // Find the dropdown menu
                let dropdownMenu = null;
                
                // Look for dropdown menu in various locations
                const dropdownContainer = avatar.closest('.dropdown');
                if (dropdownContainer) {
                    dropdownMenu = dropdownContainer.querySelector('.dropdown-menu');
                }
                
                if (!dropdownMenu) {
                    console.log('No dropdown menu found');
                    return;
                }
                
                console.log('Found dropdown menu:', dropdownMenu);
                
                // Close all other dropdowns first
                const allDropdowns = document.querySelectorAll('.dropdown-menu');
                allDropdowns.forEach(function(menu) {
                    if (menu !== dropdownMenu) {
                        menu.style.display = 'none';
                        menu.classList.remove('show');
                    }
                });
                
                // Toggle this dropdown
                const isVisible = dropdownMenu.style.display === 'block' || dropdownMenu.classList.contains('show');
                
                if (isVisible) {
                    dropdownMenu.style.display = 'none';
                    dropdownMenu.classList.remove('show');
                    console.log('Dropdown closed');
                } else {
                    dropdownMenu.style.display = 'block';
                    dropdownMenu.classList.add('show');
                    console.log('Dropdown opened');
                }
            });
        });
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                const allDropdowns = document.querySelectorAll('.dropdown-menu');
                allDropdowns.forEach(function(menu) {
                    menu.style.display = 'none';
                    menu.classList.remove('show');
                });
            }
        });
        
        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const allDropdowns = document.querySelectorAll('.dropdown-menu');
                allDropdowns.forEach(function(menu) {
                    menu.style.display = 'none';
                    menu.classList.remove('show');
                });
            }
        });
        
    }, 500); // Wait 500ms to ensure everything is loaded
});