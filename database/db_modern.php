<?php
// Modern database abstraction layer with SQLite support for local development
require_once __DIR__ . '/../config/loadEnv.php';

class Database {
    private static $instance = null;
    private $connection = null;
    private $isLocal = false;
    private $dbType = 'mysql'; // mysql or sqlite
    
    private function __construct() {
        $this->isLocal = (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] === 'localhost') || php_sapi_name() === 'cli';
        $this->connect();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function connect() {
        // Always use MySQL to connect to your iPage database
        $this->connectMySQL();
    }
    
    private function connectSQLite() {
        try {
            $dbPath = $_SERVER['DOCUMENT_ROOT'] . '/database/local.sqlite';
            $this->connection = new PDO('sqlite:' . $dbPath);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->dbType = 'sqlite';
            
            // Create tables if they don't exist
            $this->createLocalTables();
            
            // Seed with sample data if empty
            $this->seedLocalData();
            
        } catch (PDOException $e) {
            die("SQLite connection failed: " . $e->getMessage());
        }
    }
    
    private function connectMySQL() {
        try {
            // Config already loaded at top of file
            
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $this->connection = new PDO($dsn, DB_USER, DB_PASS);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->dbType = 'mysql';
            
        } catch (PDOException $e) {
            error_log("MySQL connection failed: " . $e->getMessage());
            die("Database connection error. Please try again later.");
        }
    }
    
    private function createLocalTables() {
        // News table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS news (
                id_news INTEGER PRIMARY KEY AUTOINCREMENT,
                title_news TEXT NOT NULL,
                text_news TEXT,
                image_news TEXT,
                url_news TEXT UNIQUE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                views INTEGER DEFAULT 0,
                category_id INTEGER,
                author_id INTEGER,
                is_published INTEGER DEFAULT 1
            )
        ");
        
        // Categories table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                slug TEXT UNIQUE,
                description TEXT
            )
        ");
        
        // Users table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                username TEXT UNIQUE,
                email TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                role TEXT DEFAULT 'user',
                avatar TEXT,
                is_active INTEGER DEFAULT 1,
                last_login DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // VPO table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS vpo (
                id_university INTEGER PRIMARY KEY AUTOINCREMENT,
                name_vpo TEXT NOT NULL,
                url_slug TEXT UNIQUE,
                region_id INTEGER,
                town_id INTEGER,
                description TEXT,
                logo TEXT,
                website TEXT,
                phone TEXT,
                email TEXT,
                address TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // SPO table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS spo (
                id_college INTEGER PRIMARY KEY AUTOINCREMENT,
                name_spo TEXT NOT NULL,
                url_slug TEXT UNIQUE,
                region_id INTEGER,
                town_id INTEGER,
                description TEXT,
                logo TEXT,
                website TEXT,
                phone TEXT,
                email TEXT,
                address TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Schools table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS schools (
                id_school INTEGER PRIMARY KEY AUTOINCREMENT,
                name_school TEXT NOT NULL,
                url_slug TEXT UNIQUE,
                region_id INTEGER,
                town_id INTEGER,
                description TEXT,
                logo TEXT,
                website TEXT,
                phone TEXT,
                email TEXT,
                address TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Posts table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title_post TEXT NOT NULL,
                text_post TEXT,
                url_slug TEXT UNIQUE,
                date_post DATETIME DEFAULT CURRENT_TIMESTAMP,
                category INTEGER,
                author_id INTEGER,
                views INTEGER DEFAULT 0,
                is_published INTEGER DEFAULT 1
            )
        ");
        
        // Favorites table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS favorites (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                item_type TEXT NOT NULL,
                item_id INTEGER NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                UNIQUE(user_id, item_type, item_id)
            )
        ");
        
        // Comments table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS comments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                item_type TEXT NOT NULL,
                item_id INTEGER NOT NULL,
                parent_id INTEGER DEFAULT NULL,
                comment_text TEXT NOT NULL,
                is_approved INTEGER DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Ratings table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS ratings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                item_type TEXT NOT NULL,
                item_id INTEGER NOT NULL,
                rating INTEGER NOT NULL CHECK (rating >= 1 AND rating <= 5),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                UNIQUE(user_id, item_type, item_id)
            )
        ");
        
        // Reading lists/bookmarks table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS reading_lists (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                name TEXT NOT NULL,
                description TEXT,
                is_public INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Reading list items table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS reading_list_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                list_id INTEGER NOT NULL,
                item_type TEXT NOT NULL,
                item_id INTEGER NOT NULL,
                notes TEXT,
                is_read INTEGER DEFAULT 0,
                added_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                read_at DATETIME,
                UNIQUE(list_id, item_type, item_id),
                FOREIGN KEY (list_id) REFERENCES reading_lists(id) ON DELETE CASCADE
            )
        ");
        
        // Notifications table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS notifications (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                type TEXT NOT NULL,
                title TEXT NOT NULL,
                message TEXT,
                link TEXT,
                is_read INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Events table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS events (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                description TEXT,
                event_type TEXT NOT NULL, -- 'deadline', 'exam', 'open_day', 'conference', 'other'
                start_date DATE NOT NULL,
                end_date DATE,
                start_time TIME,
                end_time TIME,
                location TEXT,
                organizer TEXT,
                target_audience TEXT, -- 'students', 'graduates', 'parents', 'all'
                institution_type TEXT, -- 'schools', 'vpo', 'spo', 'all'
                is_public INTEGER DEFAULT 1,
                is_featured INTEGER DEFAULT 0,
                created_by INTEGER, -- user_id who created event
                views INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Event reminders/subscriptions table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS event_subscriptions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                event_id INTEGER NOT NULL,
                reminder_minutes INTEGER DEFAULT 60, -- minutes before event to remind
                is_reminded INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                UNIQUE(user_id, event_id),
                FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
            )
        ");
        
        // Contact messages table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS contact_messages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                subject TEXT NOT NULL,
                message TEXT NOT NULL,
                status TEXT DEFAULT 'new', -- 'new', 'read', 'replied'
                ip_address TEXT,
                user_agent TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                read_at DATETIME,
                replied_at DATETIME
            )
        ");
    }
    
    private function seedLocalData() {
        // Check if we already have data
        $count = $this->connection->query("SELECT COUNT(*) FROM news")->fetchColumn();
        if ($count > 0) return;
        
        // Seed categories
        $categories = [
            ['name' => 'Образование', 'slug' => 'education'],
            ['name' => 'ЕГЭ', 'slug' => 'ege'],
            ['name' => 'ВПО', 'slug' => 'vpo'],
            ['name' => 'СПО', 'slug' => 'spo'],
            ['name' => 'Школы', 'slug' => 'schools']
        ];
        
        foreach ($categories as $cat) {
            $this->connection->prepare("INSERT OR IGNORE INTO categories (name, slug) VALUES (?, ?)")
                ->execute([$cat['name'], $cat['slug']]);
        }
        
        // Seed NEWS items (actual news, not articles)
        $newsTemplates = [
            'Минобрнауки объявило о новых правилах приема',
            'В России стартовала регистрация на ЕГЭ 2025',
            'Открыт прием заявок на олимпиаду школьников',
            'Новые стандарты образования вступят в силу',
            'Увеличены квоты на бюджетные места',
            'Запущена программа поддержки молодых учителей',
            'В вузах начались дни открытых дверей',
            'Объявлены результаты всероссийской олимпиады',
            'Стартовал конкурс на получение грантов',
            'Изменились правила поступления в магистратуру'
        ];
        
        for ($i = 1; $i <= 496; $i++) {
            $template = $newsTemplates[($i - 1) % count($newsTemplates)];
            $title = $template . ' - ' . date('d.m.Y', strtotime("-$i days"));
            $slug = 'novost-' . $i;
            $text = "Это содержание новости номер $i. " . str_repeat("Lorem ipsum dolor sit amet, consectetur adipiscing elit. ", 10);
            $categoryId = ($i % 5) + 1;
            
            $this->connection->prepare("
                INSERT OR IGNORE INTO news (title_news, text_news, url_news, category_id, views) 
                VALUES (?, ?, ?, ?, ?)
            ")->execute([$title, $text, $slug, $categoryId, rand(10, 1000)]);
        }
        
        // Seed POSTS (articles/guides)
        $postTemplates = [
            'Как выбрать университет: полное руководство',
            'Подготовка к ЕГЭ по математике: советы экспертов',
            'Топ-10 востребованных профессий 2025 года',
            'Гайд по поступлению в медицинский вуз',
            'Все о целевом обучении: плюсы и минусы',
            'Как получить красный диплом: пошаговая инструкция',
            'Лучшие онлайн-курсы для школьников',
            'Стипендии и гранты: как получить финансирование',
            'Профориентация: как найти свое призвание',
            'Учеба за границей: с чего начать'
        ];
        
        for ($i = 1; $i <= 100; $i++) {
            $template = $postTemplates[($i - 1) % count($postTemplates)];
            $title = $template;
            $slug = 'statya-' . $i;
            $text = "Это полезная статья номер $i. " . str_repeat("Lorem ipsum dolor sit amet, consectetur adipiscing elit. ", 20);
            $categoryId = ($i % 5) + 1;
            
            $this->connection->prepare("
                INSERT OR IGNORE INTO posts (title_post, text_post, url_slug, category, views) 
                VALUES (?, ?, ?, ?, ?)
            ")->execute([$title, $text, $slug, $categoryId, rand(100, 5000)]);
        }
        
        // Seed some VPO, SPO, and schools
        for ($i = 1; $i <= 100; $i++) {
            // VPO
            $this->connection->prepare("
                INSERT OR IGNORE INTO vpo (name_vpo, url_slug, region_id, description) 
                VALUES (?, ?, ?, ?)
            ")->execute([
                "Университет №$i",
                "university-$i",
                rand(1, 85),
                "Описание университета №$i"
            ]);
            
            // SPO
            $this->connection->prepare("
                INSERT OR IGNORE INTO spo (name_spo, url_slug, region_id, description) 
                VALUES (?, ?, ?, ?)
            ")->execute([
                "Колледж №$i",
                "college-$i",
                rand(1, 85),
                "Описание колледжа №$i"
            ]);
            
            // Schools
            $this->connection->prepare("
                INSERT OR IGNORE INTO schools (name_school, url_slug, region_id, description) 
                VALUES (?, ?, ?, ?)
            ")->execute([
                "Школа №$i",
                "school-$i",
                rand(1, 85),
                "Описание школы №$i"
            ]);
        }
        
        // Create admin user if doesn't exist
        $adminExists = $this->connection->query("SELECT COUNT(*) FROM users WHERE email = 'admin@11klassniki.ru'")->fetchColumn();
        if (!$adminExists) {
            $this->connection->prepare("
                INSERT INTO users (name, email, password, role, is_active)
                VALUES (?, ?, ?, ?, ?)
            ")->execute([
                'Администратор',
                'admin@11klassniki.ru',
                password_hash('admin123', PASSWORD_DEFAULT),
                'admin',
                1
            ]);
            
            // Create default reading lists for admin
            $adminId = $this->connection->lastInsertId();
            
            $defaultLists = [
                ['name' => 'Читать позже', 'description' => 'Материалы для изучения позже', 'is_public' => 0],
                ['name' => 'Избранные статьи', 'description' => 'Лучшие образовательные материалы', 'is_public' => 1],
                ['name' => 'ЕГЭ 2025', 'description' => 'Подготовка к ЕГЭ', 'is_public' => 1]
            ];
            
            foreach ($defaultLists as $list) {
                $this->connection->prepare("
                    INSERT OR IGNORE INTO reading_lists (user_id, name, description, is_public)
                    VALUES (?, ?, ?, ?)
                ")->execute([$adminId, $list['name'], $list['description'], $list['is_public']]);
            }
        }
        
        // Seed sample events
        $eventsExist = $this->connection->query("SELECT COUNT(*) FROM events")->fetchColumn();
        if (!$eventsExist) {
            $eventTypes = ['deadline', 'exam', 'open_day', 'conference', 'other'];
            $targetAudiences = ['students', 'graduates', 'parents', 'all'];
            $institutionTypes = ['schools', 'vpo', 'spo', 'all'];
            
            $eventTemplates = [
                ['title' => 'Подача документов в вузы', 'type' => 'deadline', 'audience' => 'graduates'],
                ['title' => 'День открытых дверей', 'type' => 'open_day', 'audience' => 'all'],
                ['title' => 'ЕГЭ по математике', 'type' => 'exam', 'audience' => 'students'],
                ['title' => 'Олимпиада школьников', 'type' => 'other', 'audience' => 'students'],
                ['title' => 'Конференция по образованию', 'type' => 'conference', 'audience' => 'all'],
                ['title' => 'Прием документов в колледжи', 'type' => 'deadline', 'audience' => 'graduates'],
                ['title' => 'ЕГЭ по русскому языку', 'type' => 'exam', 'audience' => 'students'],
                ['title' => 'Ярмарка профессий', 'type' => 'other', 'audience' => 'all'],
                ['title' => 'Родительское собрание', 'type' => 'other', 'audience' => 'parents'],
                ['title' => 'Окончание приема документов', 'type' => 'deadline', 'audience' => 'graduates']
            ];
            
            for ($i = 0; $i < 50; $i++) {
                $template = $eventTemplates[$i % count($eventTemplates)];
                $startDate = date('Y-m-d', strtotime('+' . rand(-30, 180) . ' days'));
                $endDate = date('Y-m-d', strtotime($startDate . ' +' . rand(0, 3) . ' days'));
                $startTime = rand(8, 18) . ':' . (rand(0, 1) ? '00' : '30');
                $endTime = date('H:i', strtotime($startTime . ' +' . rand(1, 4) . ' hours'));
                
                $locations = ['Москва', 'Санкт-Петербург', 'Новосибирск', 'Екатеринбург', 'Казань', 'Онлайн'];
                $organizers = ['МОН РФ', 'Рособрнадзор', 'ФИПИ', 'Университет', 'Колледж', 'Школа №' . rand(1, 100)];
                
                $this->connection->prepare("
                    INSERT OR IGNORE INTO events 
                    (title, description, event_type, start_date, end_date, start_time, end_time, 
                     location, organizer, target_audience, institution_type, is_public, is_featured)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ")->execute([
                    $template['title'] . ' ' . date('Y', strtotime($startDate)),
                    "Описание события: " . $template['title'] . ". Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                    $template['type'],
                    $startDate,
                    $endDate,
                    $startTime,
                    $endTime,
                    $locations[rand(0, count($locations) - 1)],
                    $organizers[rand(0, count($organizers) - 1)],
                    $template['audience'],
                    $institutionTypes[rand(0, count($institutionTypes) - 1)],
                    1,
                    rand(0, 1) ? 1 : 0
                ]);
            }
        }
    }
    
    // Query methods
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            
            // Bind parameters with proper types
            foreach ($params as $key => $value) {
                $paramType = PDO::PARAM_STR;
                if (is_int($value)) {
                    $paramType = PDO::PARAM_INT;
                } elseif (is_bool($value)) {
                    $paramType = PDO::PARAM_BOOL;
                } elseif (is_null($value)) {
                    $paramType = PDO::PARAM_NULL;
                }
                $stmt->bindValue($key + 1, $value, $paramType);
            }
            
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query failed: " . $e->getMessage() . " SQL: " . $sql);
            return false;
        }
    }
    
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
    
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
    }
    
    public function fetchColumn($sql, $params = [], $column = 0) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetchColumn($column) : null;
    }
    
    public function insert($table, $data) {
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = "INSERT INTO $table (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        if ($this->query($sql, $values)) {
            return $this->connection->lastInsertId();
        }
        return false;
    }
    
    public function update($table, $data, $where, $whereParams = []) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        
        $sql = "UPDATE $table SET " . implode(', ', $fields) . " WHERE $where";
        $values = array_merge($values, $whereParams);
        
        return $this->query($sql, $values);
    }
    
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM $table WHERE $where";
        return $this->query($sql, $params);
    }
    
    public function isLocal() {
        return $this->isLocal;
    }
    
    public function getConnection() {
        return $this->connection;
    }
}

// Helper functions for backward compatibility
function db() {
    return Database::getInstance();
}

function db_query($sql, $params = []) {
    return db()->query($sql, $params);
}

function db_fetch_all($sql, $params = []) {
    return db()->fetchAll($sql, $params);
}

function db_fetch_one($sql, $params = []) {
    return db()->fetchOne($sql, $params);
}

function db_fetch_column($sql, $params = [], $column = 0) {
    return db()->fetchColumn($sql, $params, $column);
}

function db_insert_id($sql, $params = []) {
    if (db()->query($sql, $params)) {
        return db()->getConnection()->lastInsertId();
    }
    return false;
}

function db_execute($sql, $params = []) {
    return db()->query($sql, $params) !== false;
}
?>