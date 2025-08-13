/**
 * Lazy Loading for Images
 * Improves page load performance by loading images only when they're needed
 */

document.addEventListener('DOMContentLoaded', function() {
    // Check if Intersection Observer is supported
    if ('IntersectionObserver' in window) {
        initLazyLoading();
    } else {
        // Fallback for older browsers
        loadAllImages();
    }
});

function initLazyLoading() {
    const images = document.querySelectorAll('img.lazy-load');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                loadImage(img);
                observer.unobserve(img);
            }
        });
    }, {
        rootMargin: '50px 0px' // Start loading 50px before the image enters the viewport
    });
    
    images.forEach(img => {
        imageObserver.observe(img);
    });
}

function loadImage(img) {
    const src = img.dataset.src;
    if (src) {
        // Create a new image to preload
        const newImg = new Image();
        newImg.onload = function() {
            img.src = src;
            img.classList.remove('lazy-load');
            img.classList.add('lazy-loaded');
        };
        newImg.onerror = function() {
            img.src = '/images/posts-images/default.png';
            img.classList.remove('lazy-load');
            img.classList.add('lazy-error');
        };
        newImg.src = src;
    }
}

function loadAllImages() {
    const images = document.querySelectorAll('img.lazy-load');
    images.forEach(img => {
        loadImage(img);
    });
}

// Add loading animation styles
const style = document.createElement('style');
style.textContent = `
    .lazy-load {
        background: #f0f0f0;
        background-image: 
            linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        background-size: 200px 100%;
        background-position: -200px 0;
        background-repeat: no-repeat;
        animation: loading 1.5s infinite;
    }
    
    @keyframes loading {
        0% { background-position: -200px 0; }
        100% { background-position: calc(200px + 100%) 0; }
    }
    
    .lazy-loaded {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .lazy-error {
        opacity: 0.5;
        filter: grayscale(100%);
    }
`;
document.head.appendChild(style);