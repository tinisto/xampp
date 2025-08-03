<?php
/**
 * Reusable News Category Navigation Component
 * 
 * This component renders a consistent category navigation bar
 * that can be used on both main news page and category pages.
 * 
 * @param string $currentCategoryId - ID of currently active category (null for "Все новости")
 * @param array $categories - Array of all news categories (fetched from database)
 * @param string $basePath - Base path for navigation (default: '/news/')
 */

function renderNewsCategoryNavigation($currentCategoryId = null, $categories = [], $basePath = '/news/') {
    // If categories not provided, fetch them
    if (empty($categories)) {
        global $connection;
        $categoriesQuery = "SELECT * FROM news_categories ORDER BY title_category_news";
        $categoriesResult = mysqli_query($connection, $categoriesQuery);
        $categories = mysqli_fetch_all($categoriesResult, MYSQLI_ASSOC);
    }
    
    // Determine if we're on the main news page (all news)
    $isMainNewsPage = ($currentCategoryId === null);
?>

<style>
    .news-category-nav {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 15px;
        padding: 20px 15px;
        margin: 20px 0 30px 0;
        flex-wrap: wrap;
        background: #f8f9fa;
        border-radius: 10px;
    }
    
    .news-category-nav a {
        color: #666;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        padding: 8px 16px;
        border-radius: 25px;
        border: 1px solid #e0e0e0;
        background: white;
        transition: all 0.3s ease;
        white-space: nowrap;
    }
    
    .news-category-nav a:hover {
        color: #28a745;
        border-color: #28a745;
        background: #f8f9fa;
        text-decoration: none;
    }
    
    .news-category-nav a.active {
        background: #28a745;
        color: white;
        border-color: #28a745;
    }
    
    .news-category-nav .divider {
        color: #ddd;
        font-size: 12px;
        margin: 0 5px;
    }
    
    @media (max-width: 768px) {
        .news-category-nav {
            gap: 10px;
            padding: 15px 10px;
        }
        
        .news-category-nav a {
            font-size: 13px;
            padding: 6px 12px;
        }
        
        .news-category-nav .divider {
            display: none;
        }
    }
    
    @media (max-width: 576px) {
        .news-category-nav {
            gap: 8px;
            padding: 12px 8px;
        }
        
        .news-category-nav a {
            font-size: 12px;
            padding: 5px 10px;
        }
    }
</style>

<div class="news-category-nav">
    <!-- "All News" link - always first, active when no specific category is selected -->
    <a href="/news" 
       class="<?= $isMainNewsPage ? 'active' : '' ?>" 
       title="Все новости"
       data-category="all"
       onclick="return loadNewsCategory(event, 'all')">
        Все новости
    </a>
    
    <?php if (!empty($categories)): ?>
        <?php foreach ($categories as $index => $category): ?>
            <a href="<?= $basePath ?><?= htmlspecialchars($category['url_category_news']) ?>" 
               class="<?= (!$isMainNewsPage && $category['id_category_news'] == $currentCategoryId) ? 'active' : '' ?>"
               title="<?= htmlspecialchars($category['title_category_news']) ?>"
               data-category="<?= htmlspecialchars($category['url_category_news']) ?>"
               onclick="return loadNewsCategory(event, '<?= htmlspecialchars($category['url_category_news']) ?>')">
                <?= htmlspecialchars($category['title_category_news']) ?>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- JavaScript for AJAX news loading -->
<script>
// Include this script only once
if (typeof loadNewsCategory === 'undefined') {
    
    // Global function to load news category without page reload
    function loadNewsCategory(event, category) {
        event.preventDefault();
        
        // Update URL without reload
        const url = category === 'all' ? '/news' : `/news/${category}`;
        window.history.pushState({category: category}, '', url);
        
        // Update active state
        document.querySelectorAll('.news-category-nav a').forEach(link => {
            link.classList.remove('active');
        });
        event.target.classList.add('active');
        
        // Show loading state
        const newsGrid = document.querySelector('.news-grid');
        const paginationContainer = document.querySelector('.pagination-container');
        
        if (newsGrid) {
            newsGrid.innerHTML = '<div class="loading-state" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 80px 20px; text-align: center;"><i class="fas fa-spinner fa-spin fa-3x" style="color: #28a745; margin-bottom: 20px;"></i><p style="margin: 0; color: #666; font-size: 16px;">Загружаем новости...</p></div>';
        }
        
        if (paginationContainer) {
            paginationContainer.innerHTML = '';
        }
        
        // Fetch news data
        fetch(`${window.location.origin}/api/news-data.php?category=${encodeURIComponent(category)}&page=1`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (newsGrid) {
                        newsGrid.innerHTML = data.html;
                    }
                    if (paginationContainer && data.pagination) {
                        paginationContainer.innerHTML = data.pagination;
                    }
                    
                    // Update page title
                    if (category === 'all') {
                        document.title = 'Новости - 11-классники';
                    } else {
                        const activeLink = document.querySelector(`[data-category="${category}"]`);
                        if (activeLink) {
                            document.title = `${activeLink.textContent} - 11-классники`;
                        }
                    }
                } else {
                    console.error('Error loading news:', data.error);
                    if (newsGrid) {
                        newsGrid.innerHTML = '<div class="empty-state" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 80px 20px; text-align: center;"><i class="fas fa-exclamation-triangle fa-3x" style="color: #dc3545; margin-bottom: 20px;"></i><h3 style="margin: 0 0 10px 0; color: #333;">Ошибка загрузки</h3><p style="margin: 0; color: #666;">Попробуйте обновить страницу</p></div>';
                    }
                }
            })
            .catch(error => {
                console.error('Network error:', error);
                if (newsGrid) {
                    newsGrid.innerHTML = '<div class="empty-state" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 80px 20px; text-align: center;"><i class="fas fa-exclamation-triangle fa-3x" style="color: #dc3545; margin-bottom: 20px;"></i><h3 style="margin: 0 0 10px 0; color: #333;">Ошибка сети</h3><p style="margin: 0; color: #666;">Проверьте подключение к интернету</p></div>';
                }
            });
        
        return false;
    }
    
    // Handle browser back/forward buttons
    window.addEventListener('popstate', function(event) {
        if (event.state && event.state.category) {
            const category = event.state.category;
            
            // Update active state
            document.querySelectorAll('.news-category-nav a').forEach(link => {
                link.classList.remove('active');
                if (link.dataset.category === category) {
                    link.classList.add('active');
                }
            });
            
            // Load the appropriate category
            loadNewsCategory({preventDefault: () => {}, target: document.querySelector(`[data-category="${category}"]`)}, category);
        }
    });
    
    // Set initial state
    window.history.replaceState({category: document.querySelector('.news-category-nav a.active')?.dataset.category || 'all'}, '', window.location.href);
}
</script>

<?php
}
?>