<?php
// Loading Placeholder Components
?>
<style>
    /* Loading Placeholder Styles */
    .placeholder-wrapper {
        position: relative;
        overflow: hidden;
        background: #f8f9fa;
    }
    
    .placeholder-shimmer {
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6), transparent);
        animation: shimmer 1.5s infinite;
    }
    
    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }
    
    /* Card Placeholder */
    .card-placeholder {
        background: #f8f9fa;
        border-radius: 12px;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .card-placeholder-image {
        width: 100%;
        height: 200px;
        background: #e9ecef;
        position: relative;
    }
    
    .card-placeholder-content {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .placeholder-badge {
        width: 80px;
        height: 20px;
        background: #e9ecef;
        border-radius: 15px;
        margin-bottom: 12px;
    }
    
    .placeholder-title {
        width: 100%;
        height: 18px;
        background: #e9ecef;
        border-radius: 4px;
        margin-bottom: 8px;
    }
    
    .placeholder-title-short {
        width: 70%;
        height: 18px;
        background: #e9ecef;
        border-radius: 4px;
        margin-bottom: 12px;
    }
    
    .placeholder-date {
        width: 60px;
        height: 14px;
        background: #e9ecef;
        border-radius: 4px;
        margin-top: auto;
    }
    
    /* List Item Placeholder */
    .list-placeholder {
        display: flex;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .list-placeholder-icon {
        width: 40px;
        height: 40px;
        background: #e9ecef;
        border-radius: 8px;
        margin-right: 15px;
        flex-shrink: 0;
    }
    
    .list-placeholder-content {
        flex: 1;
    }
    
    .list-placeholder-title {
        width: 100%;
        height: 16px;
        background: #e9ecef;
        border-radius: 4px;
        margin-bottom: 8px;
    }
    
    .list-placeholder-subtitle {
        width: 70%;
        height: 14px;
        background: #e9ecef;
        border-radius: 4px;
    }
    
    /* Hero Section Placeholder */
    .hero-placeholder {
        background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
        padding: 80px 0;
        margin-bottom: 60px;
    }
    
    .hero-placeholder-title {
        width: 60%;
        height: 48px;
        background: rgba(255,255,255,0.3);
        border-radius: 8px;
        margin: 0 auto 20px;
    }
    
    .hero-placeholder-subtitle {
        width: 40%;
        height: 20px;
        background: rgba(255,255,255,0.2);
        border-radius: 4px;
        margin: 0 auto 30px;
    }
    
    .hero-placeholder-search {
        max-width: 600px;
        height: 60px;
        background: rgba(255,255,255,0.4);
        border-radius: 50px;
        margin: 0 auto;
    }
    
    @media (max-width: 768px) {
        .hero-placeholder-title {
            width: 80%;
            height: 32px;
        }
        .hero-placeholder-subtitle {
            width: 60%;
            height: 18px;
        }
        .hero-placeholder {
            padding: 50px 0;
        }
    }
</style>

<?php
// Card Placeholder Function
function renderCardPlaceholder($count = 6) {
    for ($i = 0; $i < $count; $i++) {
        echo '<div class="col-lg-4 col-md-6 mb-4">
                <div class="card-placeholder placeholder-wrapper">
                    <div class="placeholder-shimmer"></div>
                    <div class="card-placeholder-image"></div>
                    <div class="card-placeholder-content">
                        <div class="placeholder-badge placeholder-wrapper">
                            <div class="placeholder-shimmer"></div>
                        </div>
                        <div class="placeholder-title placeholder-wrapper">
                            <div class="placeholder-shimmer"></div>
                        </div>
                        <div class="placeholder-title-short placeholder-wrapper">
                            <div class="placeholder-shimmer"></div>
                        </div>
                        <div class="placeholder-date placeholder-wrapper">
                            <div class="placeholder-shimmer"></div>
                        </div>
                    </div>
                </div>
              </div>';
    }
}

// List Placeholder Function
function renderListPlaceholder($count = 10) {
    for ($i = 0; $i < $count; $i++) {
        echo '<div class="list-placeholder placeholder-wrapper">
                <div class="placeholder-shimmer"></div>
                <div class="list-placeholder-icon"></div>
                <div class="list-placeholder-content">
                    <div class="list-placeholder-title placeholder-wrapper">
                        <div class="placeholder-shimmer"></div>
                    </div>
                    <div class="list-placeholder-subtitle placeholder-wrapper">
                        <div class="placeholder-shimmer"></div>
                    </div>
                </div>
              </div>';
    }
}

// Hero Placeholder Function
function renderHeroPlaceholder() {
    echo '<div class="hero-placeholder">
            <div class="container text-center">
                <div class="hero-placeholder-title placeholder-wrapper">
                    <div class="placeholder-shimmer"></div>
                </div>
                <div class="hero-placeholder-subtitle placeholder-wrapper">
                    <div class="placeholder-shimmer"></div>
                </div>
                <div class="hero-placeholder-search placeholder-wrapper">
                    <div class="placeholder-shimmer"></div>
                </div>
            </div>
          </div>';
}
?>