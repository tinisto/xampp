<?php
// Modern database layer that connects to your iPage MySQL database
require_once __DIR__ . '/../config/loadEnv.php';

class Database {
    private static $instance = null;
    private $connection = null;
    
    private function __construct() {
        $this->connectMySQL();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function connectMySQL() {
        try {
            // Use your iPage database credentials
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $this->connection = new PDO($dsn, DB_USER, DB_PASS);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Set timezone
            $this->connection->exec("SET time_zone = '+03:00'");
            
        } catch (PDOException $e) {
            die("MySQL connection failed: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage());
            return false;
        }
    }
    
    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetch() : false;
    }
    
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }
    
    public function fetchColumn($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetchColumn() : false;
    }
    
    public function execute($sql, $params = []) {
        return $this->query($sql, $params) !== false;
    }
    
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
}

// Helper functions for backward compatibility
function db_query($sql, $params = []) {
    return Database::getInstance()->query($sql, $params);
}

function db_fetch($sql, $params = []) {
    return Database::getInstance()->fetch($sql, $params);
}

function db_fetch_all($sql, $params = []) {
    return Database::getInstance()->fetchAll($sql, $params);
}

function db_fetch_column($sql, $params = []) {
    return Database::getInstance()->fetchColumn($sql, $params);
}

function db_execute($sql, $params = []) {
    return Database::getInstance()->execute($sql, $params);
}

function db_last_insert_id() {
    return Database::getInstance()->lastInsertId();
}

// Initialize database connection
Database::getInstance();