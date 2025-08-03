// Header Dropdown Fix - Robust version with null checks
document.addEventListener('DOMContentLoaded', function() {
    console.log('Header dropdown fix initializing...');
    
    // Wait for page to be fully loaded
    setTimeout(function() {
        initializeDropdowns();
    }, 100);
    
    function initializeDropdowns() {
        // Find all dropdown triggers with null checks
        const dropdownTriggers = document.querySelectorAll('[data-bs-toggle="dropdown"]');
        console.log('Found dropdown triggers:', dropdownTriggers.length);
        
        dropdownTriggers.forEach(function(trigger, index) {
            if (!trigger) {
                console.warn('Null trigger found at index:', index);
                return;
            }
            
            console.log('Setting up trigger:', trigger);
            
            // Remove existing event listeners to avoid duplicates
            trigger.removeEventListener('click', handleDropdownClick);
            
            // Add click event listener with null checks
            trigger.addEventListener('click', handleDropdownClick);
        });
        
        // Close dropdowns when clicking outside (with null checks)
        document.removeEventListener('click', handleOutsideClick);
        document.addEventListener('click', handleOutsideClick);
        
        // Close dropdowns on Escape key
        document.removeEventListener('keydown', handleEscapeKey);
        document.addEventListener('keydown', handleEscapeKey);
    }
    
    function handleDropdownClick(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const trigger = e.currentTarget;
        if (!trigger) {
            console.error('No trigger found in click event');
            return;
        }
        
        console.log('Dropdown clicked:', trigger);
        
        // Find the dropdown menu - check multiple possible locations
        let dropdown = null;
        
        // First try: next sibling
        if (trigger.nextElementSibling && trigger.nextElementSibling.classList && trigger.nextElementSibling.classList.contains('dropdown-menu')) {
            dropdown = trigger.nextElementSibling;
        }
        // Second try: parent's next sibling (for nested structures)
        else if (trigger.parentElement && trigger.parentElement.nextElementSibling && 
                 trigger.parentElement.nextElementSibling.classList && 
                 trigger.parentElement.nextElementSibling.classList.contains('dropdown-menu')) {
            dropdown = trigger.parentElement.nextElementSibling;
        }
        // Third try: look within parent for dropdown-menu
        else if (trigger.parentElement) {
            dropdown = trigger.parentElement.querySelector('.dropdown-menu');
        }
        // Fourth try: look in closest dropdown container
        else {
            const dropdownContainer = trigger.closest('.dropdown');
            if (dropdownContainer) {
                dropdown = dropdownContainer.querySelector('.dropdown-menu');
            }
        }
        
        if (!dropdown) {
            console.error('No dropdown menu found for trigger:', trigger);
            return;
        }
        
        console.log('Found dropdown menu:', dropdown);
        
        // Toggle dropdown visibility
        const isShown = dropdown.classList.contains('show') || dropdown.classList.contains('d-block');
        
        // Close all other dropdowns first
        closeAllDropdowns();
        
        if (!isShown) {
            // Show the dropdown
            dropdown.classList.add('show');
            dropdown.style.display = 'block';
            trigger.setAttribute('aria-expanded', 'true');
            console.log('Dropdown opened');
        } else {
            // Hide the dropdown
            dropdown.classList.remove('show');
            dropdown.style.display = 'none';
            trigger.setAttribute('aria-expanded', 'false');
            console.log('Dropdown closed');
        }
    }
    
    function handleOutsideClick(e) {
        if (!e.target.closest('.dropdown')) {
            closeAllDropdowns();
        }
    }
    
    function handleEscapeKey(e) {
        if (e.key === 'Escape') {
            closeAllDropdowns();
        }
    }
    
    function closeAllDropdowns() {
        const allDropdowns = document.querySelectorAll('.dropdown-menu');
        allDropdowns.forEach(function(menu) {
            if (menu && menu.classList) {
                menu.classList.remove('show');
                menu.style.display = 'none';
            }
        });
        
        const allTriggers = document.querySelectorAll('[data-bs-toggle="dropdown"]');
        allTriggers.forEach(function(trigger) {
            if (trigger && trigger.setAttribute) {
                trigger.setAttribute('aria-expanded', 'false');
            }
        });
    }
    
    // Reinitialize function for dynamic content
    window.reinitializeDropdowns = initializeDropdowns;
});

// Debug function to check if Bootstrap is loaded
function checkBootstrap() {
    if (typeof bootstrap === 'undefined') {
        console.warn('Bootstrap JS is not loaded properly');
        return false;
    }
    return true;
}

// Initialize dropdown functionality
function initializeDropdowns() {
    checkBootstrap();
    
    // Re-initialize all dropdowns
    const dropdowns = document.querySelectorAll('.dropdown-toggle');
    dropdowns.forEach(function(dropdown) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
            new bootstrap.Dropdown(dropdown);
        }
    });
}