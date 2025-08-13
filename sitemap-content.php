<div class="container">
    <style>
        .sitemap-container {
            padding: 2rem 0;
        }
        
        .sitemap-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .sitemap-section {
            margin-bottom: 3rem;
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1rem;
            border-bottom: 2px solid var(--accent-primary);
            padding-bottom: 0.5rem;
        }
        
        .sitemap-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        
        .sitemap-link {
            display: block;
            padding: 0.75rem 1rem;
            background: transparent;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .sitemap-link:hover {
            border-color: var(--accent-primary);
            color: var(--accent-primary);
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
        }
        
        .sitemap-link i {
            margin-right: 0.5rem;
            color: var(--accent-primary);
        }
    </style>
    
    <div class="sitemap-container">
        <h1 class="sitemap-title">Карта сайта</h1>
        
        <!-- Основные разделы -->
        <div class="sitemap-section">
            <h2 class="section-title">Основные разделы</h2>
            <div class="sitemap-links">
                <a href="/" class="sitemap-link">
                    <i class="fas fa-home"></i>
                    Главная страница
                </a>
                <a href="/about" class="sitemap-link">
                    <i class="fas fa-info-circle"></i>
                    О сайте
                </a>
                <a href="/news" class="sitemap-link">
                    <i class="fas fa-newspaper"></i>
                    Новости
                </a>
                <a href="/tests" class="sitemap-link">
                    <i class="fas fa-brain"></i>
                    Тесты
                </a>
                <a href="/search" class="sitemap-link">
                    <i class="fas fa-search"></i>
                    Поиск
                </a>
            </div>
        </div>
        
        <!-- Образовательные учреждения -->
        <div class="sitemap-section">
            <h2 class="section-title">Образовательные учреждения</h2>
            <div class="sitemap-links">
                <a href="/vpo" class="sitemap-link">
                    <i class="fas fa-university"></i>
                    ВУЗы (Высшее образование)
                </a>
                <a href="/spo" class="sitemap-link">
                    <i class="fas fa-graduation-cap"></i>
                    Колледжи (Среднее профессиональное)
                </a>
                <a href="/schools" class="sitemap-link">
                    <i class="fas fa-school"></i>
                    Школы
                </a>
                <a href="/schools-all-regions" class="sitemap-link">
                    <i class="fas fa-map"></i>
                    Школы по регионам
                </a>
            </div>
        </div>
        
        <!-- Пользователь -->
        <div class="sitemap-section">
            <h2 class="section-title">Пользователь</h2>
            <div class="sitemap-links">
                <a href="/login" class="sitemap-link">
                    <i class="fas fa-sign-in-alt"></i>
                    Вход
                </a>
                <a href="/registration" class="sitemap-link">
                    <i class="fas fa-user-plus"></i>
                    Регистрация
                </a>
                <a href="/account" class="sitemap-link">
                    <i class="fas fa-user"></i>
                    Личный кабинет
                </a>
            </div>
        </div>
        
        <!-- Контент -->
        <div class="sitemap-section">
            <h2 class="section-title">Контент</h2>
            <div class="sitemap-links">
                <a href="/posts" class="sitemap-link">
                    <i class="fas fa-file-alt"></i>
                    Статьи
                </a>
                <?php
                // Display recent categories
                if ($connection && !$connection->connect_error) {
                    $stmt = $connection->prepare("SELECT id_category, title_category, url_category FROM categories ORDER BY title_category LIMIT 10");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    while ($row = $result->fetch_assoc()) {
                        echo '<a href="/category/' . htmlspecialchars($row['url_category']) . '" class="sitemap-link">';
                        echo '<i class="fas fa-folder"></i>';
                        echo htmlspecialchars($row['title_category']);
                        echo '</a>';
                    }
                }
                ?>
            </div>
        </div>
        
        <!-- Дополнительно -->
        <div class="sitemap-section">
            <h2 class="section-title">Дополнительно</h2>
            <div class="sitemap-links">
                <a href="/robots.txt" class="sitemap-link" target="_blank">
                    <i class="fas fa-robot"></i>
                    Robots.txt
                </a>
                <a href="mailto:info@11классники.ru" class="sitemap-link">
                    <i class="fas fa-envelope"></i>
                    Связаться с нами
                </a>
            </div>
        </div>
    </div>
</div>