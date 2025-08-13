<style>
    /* CNN-style About Page */
    .about-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1rem;
    }
    
    /* Breaking News Bar */
    .breaking-bar {
        background: #c00;
        color: white;
        padding: 0.75rem 1rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.875rem;
        letter-spacing: 0.05em;
    }
    
    .breaking-bar i {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    
    /* Main Grid Layout */
    .about-grid {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 2rem;
    }
    
    /* Main Content Area */
    .about-main {
        background: transparent;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        overflow: hidden;
    }
    
    .about-header {
        background: #222;
        color: white;
        padding: 2rem;
        border-bottom: 4px solid #c00;
    }
    
    .about-title {
        font-size: 3rem;
        font-weight: 900;
        margin-bottom: 0.5rem;
        line-height: 1;
    }
    
    .about-tagline {
        font-size: 1.25rem;
        color: #ccc;
        font-weight: 300;
    }
    
    /* Content Sections */
    .content-section {
        padding: 2rem;
        border-bottom: 1px solid var(--border-color);
    }
    
    .content-section:last-child {
        border-bottom: none;
    }
    
    .section-headline {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 3px solid #c00;
        display: inline-block;
    }
    
    .section-text {
        font-size: 1.125rem;
        line-height: 1.75;
        color: var(--text-primary);
        margin-bottom: 1rem;
    }
    
    .section-text a {
        color: #c00;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s;
    }
    
    .section-text a:hover {
        text-decoration: underline;
    }
    
    /* Key Points Grid */
    .key-points {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin: 2rem 0;
    }
    
    .key-point {
        background: transparent;
        border: 1px solid var(--border-color);
        border-left: 4px solid #c00;
        padding: 1.5rem;
        border-radius: 4px;
    }
    
    .key-point-number {
        font-size: 2.5rem;
        font-weight: 900;
        color: #c00;
        line-height: 1;
        margin-bottom: 0.5rem;
    }
    
    .key-point-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }
    
    .key-point-text {
        color: var(--text-secondary);
        line-height: 1.6;
    }
    
    /* Statistics Bar */
    .stats-bar {
        background: #111;
        color: white;
        padding: 2rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 2rem;
        text-align: center;
        margin: 2rem 0;
        border-radius: 4px;
    }
    
    .stat-item {
        border-right: 1px solid #444;
    }
    
    .stat-item:last-child {
        border-right: none;
    }
    
    .stat-value {
        font-size: 2.5rem;
        font-weight: 900;
        color: #c00;
        display: block;
        margin-bottom: 0.5rem;
    }
    
    .stat-label {
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #ccc;
    }
    
    /* Quote Box */
    .quote-box {
        background: transparent;
        border: 1px solid var(--border-color);
        padding: 2rem;
        margin: 2rem 0;
        position: relative;
        border-radius: 4px;
    }
    
    .quote-box::before {
        content: '"';
        font-size: 4rem;
        color: #c00;
        position: absolute;
        top: -10px;
        left: 20px;
        font-family: Georgia, serif;
    }
    
    .quote-text {
        font-size: 1.5rem;
        font-style: italic;
        color: var(--text-primary);
        margin-bottom: 1rem;
        padding-left: 2rem;
    }
    
    .quote-author {
        text-align: right;
        color: var(--text-secondary);
        font-weight: 600;
    }
    
    /* Sidebar */
    .about-sidebar {
        position: sticky;
        top: 1rem;
        height: fit-content;
    }
    
    .sidebar-box {
        background: transparent;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    
    .sidebar-header {
        background: #c00;
        color: white;
        padding: 1rem;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.875rem;
    }
    
    .sidebar-content {
        padding: 1.5rem;
    }
    
    .quick-link {
        display: block;
        padding: 0.75rem 0;
        color: var(--text-primary);
        text-decoration: none;
        border-bottom: 1px solid var(--border-color);
        transition: all 0.2s;
        font-weight: 600;
    }
    
    .quick-link:last-child {
        border-bottom: none;
    }
    
    .quick-link:hover {
        color: #c00;
        padding-left: 0.5rem;
    }
    
    .fact-box {
        background: #f5f5f5;
        border: 1px solid #ddd;
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 4px;
    }
    
    .fact-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #666;
        margin-bottom: 0.25rem;
    }
    
    .fact-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: #333;
    }
    
    /* Responsive */
    @media (max-width: 968px) {
        .about-grid {
            grid-template-columns: 1fr;
        }
        
        .about-sidebar {
            position: static;
        }
        
        .stats-bar {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .about-title {
            font-size: 2rem;
        }
        
        .content-section {
            padding: 1.5rem;
        }
        
        .key-points {
            grid-template-columns: 1fr;
        }
        
        .stats-bar {
            grid-template-columns: 1fr;
        }
        
        .stat-item {
            border-right: none;
            border-bottom: 1px solid #444;
            padding-bottom: 1rem;
        }
        
        .stat-item:last-child {
            border-bottom: none;
        }
    }
</style>

<div class="about-wrapper">
    <!-- Breaking News Style Header -->
    <div class="breaking-bar">
        <i class="fas fa-circle"></i>
        <span>ABOUT 11KLASSNIKI.RU</span>
    </div>
    
    <div class="about-grid">
        <!-- Main Content -->
        <div class="about-main">
            <div class="about-header">
                <h1 class="about-title">11KLASSNIKI.RU</h1>
                <p class="about-tagline">Your trusted source for graduate insights and education news</p>
            </div>
            
            <!-- Mission Section -->
            <div class="content-section">
                <h2 class="section-headline">OUR MISSION</h2>
                <p class="section-text">
                    <strong>11klassniki.ru</strong> is Russia's premier platform dedicated to high school graduates navigating their future. We provide real stories, expert guidance, and comprehensive educational resources.
                </p>
                <p class="section-text">
                    Founded with the vision of empowering students at life's crucial crossroads, we connect graduates with the information they need to make informed decisions about their education and career paths.
                </p>
            </div>
            
            <!-- Key Coverage Areas -->
            <div class="content-section">
                <h2 class="section-headline">KEY COVERAGE AREAS</h2>
                <div class="key-points">
                    <div class="key-point">
                        <div class="key-point-number">01</div>
                        <div class="key-point-title">Graduate Interviews</div>
                        <div class="key-point-text">
                            Exclusive interviews with 11th graders from across Russia sharing their experiences, challenges, and aspirations
                        </div>
                    </div>
                    <div class="key-point">
                        <div class="key-point-number">02</div>
                        <div class="key-point-title">University Insights</div>
                        <div class="key-point-text">
                            First-hand accounts from freshmen about exam preparation, admission processes, and campus life
                        </div>
                    </div>
                    <div class="key-point">
                        <div class="key-point-number">03</div>
                        <div class="key-point-title">Education Database</div>
                        <div class="key-point-text">
                            Comprehensive listings of universities, colleges, and schools with detailed information and ratings
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Statistics -->
            <div class="stats-bar">
                <div class="stat-item">
                    <span class="stat-value">1,000+</span>
                    <span class="stat-label">Graduate Stories</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">500+</span>
                    <span class="stat-label">Educational Institutions</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">89</span>
                    <span class="stat-label">Regions Covered</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">24/7</span>
                    <span class="stat-label">Access Available</span>
                </div>
            </div>
            
            <!-- Content Philosophy -->
            <div class="content-section">
                <h2 class="section-headline">EDITORIAL STANDARDS</h2>
                <p class="section-text">
                    Our platform features in-depth <a href='/category/11-klassniki'>interviews with graduating students</a> from diverse backgrounds across Russia. These candid conversations cover career choices, university applications, exam preparation, and the challenges of transitioning to adulthood.
                </p>
                <p class="section-text">
                    The <a href='/category/abiturientam'>"For Applicants"</a> section offers valuable insights from recent graduates now in their first year of university. They share fresh perspectives on entrance exams, application strategies, and the realities of student life.
                </p>
                
                <div class="quote-box">
                    <p class="quote-text">Нам не дано предугадать, как слово наше отзовется</p>
                    <p class="quote-author">— Fyodor Tyutchev</p>
                </div>
                
                <p class="section-text">
                    These words reflect our editorial philosophy. Every story we publish, every piece of advice shared, has the potential to influence a young person's future. We take this responsibility seriously.
                </p>
            </div>
            
            <!-- Our Commitment -->
            <div class="content-section">
                <h2 class="section-headline">OUR COMMITMENT</h2>
                <p class="section-text">
                    In an era of information overload, we strive to be a trusted voice for Russian youth. Our goal is to provide balanced, accurate, and inspiring content that helps students navigate one of life's most important transitions.
                </p>
                <p class="section-text">
                    We believe in the power of shared experiences and peer support. <strong>11klassniki.ru</strong> is more than a website—it's a community where graduates find guidance, encouragement, and the confidence to pursue their dreams.
                </p>
            </div>
        </div>
        
        <!-- Sidebar -->
        <aside class="about-sidebar">
            <!-- Quick Links -->
            <div class="sidebar-box">
                <div class="sidebar-header">QUICK LINKS</div>
                <div class="sidebar-content">
                    <a href="/category/11-klassniki" class="quick-link">Graduate Interviews</a>
                    <a href="/category/abiturientam" class="quick-link">For Applicants</a>
                    <a href="/vpo" class="quick-link">Universities</a>
                    <a href="/spo" class="quick-link">Colleges</a>
                    <a href="/schools" class="quick-link">Schools</a>
                    <a href="/tests" class="quick-link">Career Tests</a>
                </div>
            </div>
            
            <!-- Key Facts -->
            <div class="sidebar-box">
                <div class="sidebar-header">KEY FACTS</div>
                <div class="sidebar-content">
                    <div class="fact-box">
                        <div class="fact-label">Founded</div>
                        <div class="fact-value">2020</div>
                    </div>
                    <div class="fact-box">
                        <div class="fact-label">Coverage</div>
                        <div class="fact-value">All Russia</div>
                    </div>
                    <div class="fact-box">
                        <div class="fact-label">Focus</div>
                        <div class="fact-value">Education & Youth</div>
                    </div>
                    <div class="fact-box">
                        <div class="fact-label">Content Type</div>
                        <div class="fact-value">News & Resources</div>
                    </div>
                </div>
            </div>
            
            <!-- Contact -->
            <div class="sidebar-box">
                <div class="sidebar-header">CONTACT US</div>
                <div class="sidebar-content">
                    <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                        Have a story to share? Want to contribute?
                    </p>
                    <a href="/write" class="quick-link" style="color: #c00;">
                        <i class="fas fa-envelope"></i> Write to Editorial
                    </a>
                </div>
            </div>
        </aside>
    </div>
</div>