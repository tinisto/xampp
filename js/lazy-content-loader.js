/**
 * Lazy content loader with context-aware placeholders
 */

class LazyContentLoader {
    constructor(options = {}) {
        this.options = {
            rootMargin: '50px',
            threshold: 0.01,
            placeholderType: 'card',
            ...options
        };
        
        this.observer = null;
        this.loadedElements = new Set();
        this.init();
    }
    
    init() {
        // Set up Intersection Observer
        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !this.loadedElements.has(entry.target)) {
                    this.loadContent(entry.target);
                }
            });
        }, {
            rootMargin: this.options.rootMargin,
            threshold: this.options.threshold
        });
        
        // Start observing all lazy-load elements
        this.observeElements();
    }
    
    observeElements() {
        const elements = document.querySelectorAll('[data-lazy-load]');
        elements.forEach(element => {
            // Show placeholder immediately
            this.showPlaceholder(element);
            // Start observing for lazy load
            this.observer.observe(element);
        });
    }
    
    showPlaceholder(element) {
        const type = element.dataset.placeholderType || this.options.placeholderType;
        const count = parseInt(element.dataset.placeholderCount) || 1;
        const columns = parseInt(element.dataset.placeholderColumns) || 4;
        
        // Create placeholder container
        const placeholderHtml = this.getPlaceholderHtml(type, count, columns);
        element.innerHTML = placeholderHtml;
    }
    
    getPlaceholderHtml(type, count, columns) {
        // This would be generated server-side in real implementation
        // For demo purposes, using simple HTML structure
        let html = `<div class="placeholder-grid" style="display: grid; grid-template-columns: repeat(${columns}, 1fr); gap: 20px;">`;
        
        for (let i = 0; i < count; i++) {
            html += this.getPlaceholderTemplate(type);
        }
        
        html += '</div>';
        return html;
    }
    
    getPlaceholderTemplate(type) {
        const templates = {
            'news-card': `
                <div class="placeholder-news-card" style="border: 1px solid var(--border-color, #e2e8f0); border-radius: 8px; padding: 20px; background: var(--surface, #ffffff);">
                    <div class="skeleton skeleton-animated" style="height: 24px; width: 85%; margin-bottom: 8px; border-radius: 4px;"></div>
                    <div class="skeleton skeleton-animated" style="height: 16px; width: 100%; margin-bottom: 8px; border-radius: 4px;"></div>
                    <div class="skeleton skeleton-animated" style="height: 16px; width: 90%; border-radius: 4px;"></div>
                </div>
            `,
            'post-card': `
                <div class="placeholder-post-card" style="border: 1px solid var(--border-color, #e2e8f0); border-radius: 12px; overflow: hidden; background: var(--surface, #ffffff);">
                    <div class="skeleton skeleton-animated" style="width: 100%; height: 180px;"></div>
                    <div style="padding: 20px;">
                        <div class="skeleton skeleton-animated" style="height: 20px; width: 90%; margin-bottom: 8px; border-radius: 4px;"></div>
                        <div class="skeleton skeleton-animated" style="height: 14px; width: 100%; margin-bottom: 6px; border-radius: 4px;"></div>
                        <div class="skeleton skeleton-animated" style="height: 14px; width: 80%; border-radius: 4px;"></div>
                    </div>
                </div>
            `,
            'school-card': `
                <div class="placeholder-school-card" style="padding: 20px; background: var(--surface, #ffffff); border: 1px solid var(--border-color, #e2e8f0); border-radius: 8px;">
                    <div class="skeleton skeleton-animated" style="height: 24px; width: 70%; margin-bottom: 12px; border-radius: 4px;"></div>
                    <div class="skeleton skeleton-animated" style="height: 16px; width: 200px; margin-bottom: 8px; border-radius: 4px;"></div>
                    <div class="skeleton skeleton-animated" style="height: 16px; width: 150px; border-radius: 4px;"></div>
                </div>
            `
        };
        
        return templates[type] || templates['news-card'];
    }
    
    async loadContent(element) {
        const url = element.dataset.lazyLoad;
        const type = element.dataset.contentType || 'html';
        
        try {
            this.loadedElements.add(element);
            
            // Simulate network delay for demo
            if (element.dataset.simulateDelay) {
                await new Promise(resolve => setTimeout(resolve, 1000));
            }
            
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error('Failed to load content');
            }
            
            if (type === 'json') {
                const data = await response.json();
                this.renderContent(element, data);
            } else {
                const html = await response.text();
                this.replaceContent(element, html);
            }
            
        } catch (error) {
            console.error('Error loading content:', error);
            this.showError(element);
        }
    }
    
    renderContent(element, data) {
        // Custom rendering based on content type
        const renderer = element.dataset.renderer;
        
        if (renderer && window[renderer]) {
            const html = window[renderer](data);
            this.replaceContent(element, html);
        } else {
            console.error('No renderer found for:', renderer);
        }
    }
    
    replaceContent(element, html) {
        // Fade out placeholder
        element.style.opacity = '0.5';
        
        setTimeout(() => {
            element.innerHTML = html;
            element.style.opacity = '1';
            
            // Trigger custom event
            element.dispatchEvent(new CustomEvent('content-loaded', {
                bubbles: true,
                detail: { element }
            }));
        }, 200);
    }
    
    showError(element) {
        element.innerHTML = `
            <div style="padding: 40px; text-align: center; color: var(--text-secondary, #666);">
                <i class="fas fa-exclamation-circle" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                <p>Не удалось загрузить контент</p>
                <button onclick="lazyLoader.retry(this.closest('[data-lazy-load]'))" 
                        style="margin-top: 16px; padding: 8px 16px; background: var(--primary-color, #28a745); 
                               color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Повторить
                </button>
            </div>
        `;
    }
    
    retry(element) {
        this.loadedElements.delete(element);
        this.showPlaceholder(element);
        setTimeout(() => this.loadContent(element), 100);
    }
    
    // Public methods
    refresh() {
        this.observeElements();
    }
    
    disconnect() {
        if (this.observer) {
            this.observer.disconnect();
        }
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    window.lazyLoader = new LazyContentLoader();
});

// Add skeleton animation CSS if not already present
if (!document.getElementById('skeleton-styles')) {
    const style = document.createElement('style');
    style.id = 'skeleton-styles';
    style.innerHTML = `
        .skeleton {
            background: linear-gradient(90deg, 
                var(--skeleton-base, #e2e8f0) 25%, 
                var(--skeleton-highlight, #edf2f7) 50%, 
                var(--skeleton-base, #e2e8f0) 75%
            );
            background-size: 200% 100%;
        }
        
        .skeleton-animated {
            animation: skeleton-loading 1.4s ease-in-out infinite;
        }
        
        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        [data-theme="dark"] .skeleton {
            background: linear-gradient(90deg, 
                var(--skeleton-base, #374151) 25%, 
                var(--skeleton-highlight, #4b5563) 50%, 
                var(--skeleton-base, #374151) 75%
            );
            background-size: 200% 100%;
        }
    `;
    document.head.appendChild(style);
}