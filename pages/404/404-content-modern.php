<style>
    .error-page {
        min-height: 60vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 0;
    }
    .error-container {
        max-width: 600px;
        text-align: center;
        padding: 40px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        margin: 0 auto;
    }
    .error-code {
        font-size: 120px;
        font-weight: 700;
        color: #28a745;
        line-height: 1;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    }
    .error-title {
        font-size: 48px;
        font-weight: 600;
        color: #333;
        margin-bottom: 20px;
    }
    .error-message {
        font-size: 20px;
        color: #666;
        line-height: 1.6;
        margin-bottom: 40px;
    }
    .search-box-404 {
        max-width: 400px;
        margin: 0 auto 30px;
    }
    .search-form {
        display: flex;
        gap: 10px;
        background: #f8f9fa;
        padding: 8px;
        border-radius: 50px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .search-form input {
        flex: 1;
        border: none;
        background: transparent;
        padding: 12px 20px;
        font-size: 16px;
        outline: none;
    }
    .search-form button {
        background: #28a745;
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 50px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .search-form button:hover {
        background: #218838;
        transform: scale(1.05);
    }
    .error-actions {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
    }
    .btn-home {
        display: inline-block;
        padding: 15px 35px;
        background: #28a745;
        color: white;
        text-decoration: none;
        border-radius: 50px;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }
    .btn-home:hover {
        background: #218838;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        color: white;
    }
    .btn-back {
        display: inline-block;
        padding: 15px 35px;
        background: white;
        color: #28a745;
        text-decoration: none;
        border-radius: 50px;
        font-weight: 500;
        border: 2px solid #28a745;
        transition: all 0.3s ease;
    }
    .btn-back:hover {
        background: #28a745;
        color: white;
        transform: translateY(-2px);
    }
    .error-icon {
        font-size: 80px;
        color: #28a745;
        margin-bottom: 20px;
        animation: bounce 2s infinite;
    }
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-10px);
        }
        60% {
            transform: translateY(-5px);
        }
    }
    @media (max-width: 768px) {
        .error-code {
            font-size: 80px;
        }
        .error-title {
            font-size: 36px;
        }
        .error-message {
            font-size: 18px;
        }
        .error-container {
            padding: 30px 20px;
        }
        .error-actions {
            flex-direction: column;
            align-items: stretch;
        }
        .btn-home, .btn-back {
            width: 100%;
            text-align: center;
        }
    }
</style>

<div class="error-page">
    <div class="container">
        <div class="error-container">
            <div class="error-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="error-code">404</div>
            <h1 class="error-title">Ой-ой!</h1>
            <p class="error-message">
                Возможно, это ваша ошибка, а может быть, это наша,<br>
                но здесь нет нужной вам страницы.
            </p>
            
            <div class="search-box-404">
                <form action="/search-process" method="get" class="search-form">
                    <input type="text" name="query" placeholder="Искать на 11klassniki.ru..." required>
                    <button type="submit">Найти</button>
                </form>
            </div>
            
            <div class="error-actions">
                <a href="/" class="btn-home">
                    <i class="fas fa-home"></i> Перейти на главную страницу
                </a>
                <a href="javascript:history.back()" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Вернуться назад
                </a>
            </div>
        </div>
    </div>
</div>