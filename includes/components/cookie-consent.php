<?php
/**
 * Cookie Consent Banner Component
 * GDPR and Russian Law Compliant
 */

function renderCookieConsent() {
    // Don't show if already consented
    if (isset($_COOKIE['cookie_consent'])) {
        return '';
    }
    
    ob_start();
    ?>
    <div id="cookie-consent-banner" class="cookie-consent-banner">
        <div class="cookie-consent-content">
            <div class="cookie-consent-text">
                <p><strong>🍪 Мы используем файлы cookie</strong></p>
                <p>Наш сайт использует файлы cookie для улучшения работы сайта, персонализации контента и аналитики. Продолжая использовать сайт, вы соглашаетесь с использованием файлов cookie.</p>
            </div>
            <div class="cookie-consent-buttons">
                <button id="cookie-accept" class="cookie-btn cookie-accept">Принять все</button>
                <button id="cookie-essential" class="cookie-btn cookie-essential">Только необходимые</button>
                <a href="/privacy" class="cookie-link">Подробнее</a>
            </div>
        </div>
    </div>

    <style>
        .cookie-consent-banner {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            border-top: 3px solid #28a745;
            box-shadow: 0 -2px 20px rgba(0,0,0,0.1);
            z-index: 10000;
            padding: 20px;
            animation: slideUp 0.3s ease;
        }
        
        @keyframes slideUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }
        
        .cookie-consent-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }
        
        .cookie-consent-text {
            flex: 1;
        }
        
        .cookie-consent-text p {
            margin: 0 0 8px 0;
            font-size: 14px;
            line-height: 1.4;
            color: #333;
        }
        
        .cookie-consent-text p:first-child {
            font-weight: 600;
            color: #28a745;
        }
        
        .cookie-consent-buttons {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .cookie-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .cookie-accept {
            background: #28a745;
            color: white;
        }
        
        .cookie-accept:hover {
            background: #218838;
            transform: translateY(-1px);
        }
        
        .cookie-essential {
            background: #6c757d;
            color: white;
        }
        
        .cookie-essential:hover {
            background: #5a6268;
        }
        
        .cookie-link {
            color: #28a745;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
        }
        
        .cookie-link:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .cookie-consent-content {
                flex-direction: column;
                text-align: center;
            }
            
            .cookie-consent-buttons {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const acceptBtn = document.getElementById('cookie-accept');
            const essentialBtn = document.getElementById('cookie-essential');
            const banner = document.getElementById('cookie-consent-banner');
            
            if (acceptBtn) {
                acceptBtn.addEventListener('click', function() {
                    setCookieConsent('all');
                    hideBanner();
                });
            }
            
            if (essentialBtn) {
                essentialBtn.addEventListener('click', function() {
                    setCookieConsent('essential');
                    hideBanner();
                });
            }
            
            function setCookieConsent(level) {
                // Set consent cookie with proper security
                const expires = new Date();
                expires.setFullYear(expires.getFullYear() + 1);
                
                document.cookie = `cookie_consent=${level};expires=${expires.toUTCString()};path=/;secure;samesite=lax`;
                
                // Set analytics preference
                if (level === 'all') {
                    document.cookie = `analytics_consent=true;expires=${expires.toUTCString()};path=/;secure;samesite=lax`;
                } else {
                    document.cookie = `analytics_consent=false;expires=${expires.toUTCString()};path=/;secure;samesite=lax`;
                }
                
                // Trigger custom event for other scripts
                window.dispatchEvent(new CustomEvent('cookieConsentUpdated', { 
                    detail: { level: level } 
                }));
            }
            
            function hideBanner() {
                if (banner) {
                    banner.style.animation = 'slideDown 0.3s ease';
                    setTimeout(() => {
                        banner.style.display = 'none';
                    }, 300);
                }
            }
        });
        
        // Add slide down animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideDown {
                from { transform: translateY(0); }
                to { transform: translateY(100%); }
            }
        `;
        document.head.appendChild(style);
    </script>
    <?php
    return ob_get_clean();
}

/**
 * Check if user has consented to specific cookie type
 */
function hasCookieConsent($type = 'essential') {
    if (!isset($_COOKIE['cookie_consent'])) {
        return false;
    }
    
    $consent = $_COOKIE['cookie_consent'];
    
    switch ($type) {
        case 'essential':
            return true; // Essential cookies always allowed
        case 'analytics':
            return $consent === 'all' || (isset($_COOKIE['analytics_consent']) && $_COOKIE['analytics_consent'] === 'true');
        case 'all':
            return $consent === 'all';
        default:
            return false;
    }
}

/**
 * Set cookie with proper security settings
 */
function setSecureCookie($name, $value, $expires = null, $essential = false) {
    // Don't set non-essential cookies without consent
    if (!$essential && !hasCookieConsent('all')) {
        return false;
    }
    
    if ($expires === null) {
        $expires = time() + (365 * 24 * 60 * 60); // 1 year default
    }
    
    $options = [
        'expires' => $expires,
        'path' => '/',
        'domain' => '',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Lax'
    ];
    
    return setcookie($name, $value, $options);
}
?>