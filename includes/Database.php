<?php
class Database {
    private $connection;
    private $queryCache = [];
    private $cacheEnabled = true;
    private $cacheExpiry = 3600; // 1 hour
    private static $instance = null;
    
    public function __construct($connection) {
        $this->connection = $connection;
    }
    
    public static function getInstance($connection = null) {
        if (self::$instance === null) {
            if ($connection === null) {
                require_once dirname(__DIR__) . '/database/db_connections.php';
                global $connection;
            }
            self::$instance = new self($connection);
        }
        return self::$instance;
    }
    
    public function prepare($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        
        if (!empty($params)) {
            $types = '';
            $values = [];
            
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_double($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
                $values[] = $param;
            }
            
            if (!empty($values)) {
                $stmt->bind_param($types, ...$values);
            }
        }
        
        return $stmt;
    }
    
    public function query($sql, $params = []) {
        // Check cache for SELECT queries
        if ($this->cacheEnabled && stripos(trim($sql), 'SELECT') === 0) {
            $cacheKey = md5($sql . serialize($params));
            if (isset($this->queryCache[$cacheKey])) {
                $cached = $this->queryCache[$cacheKey];
                if ($cached['expiry'] > time()) {
                    return $cached['result'];
                }
                unset($this->queryCache[$cacheKey]);
            }
        }
        
        $stmt = $this->prepare($sql, $params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Cache SELECT results
        if ($this->cacheEnabled && stripos(trim($sql), 'SELECT') === 0) {
            $this->queryCache[$cacheKey] = [
                'result' => $result,
                'expiry' => time() + $this->cacheExpiry
            ];
        }
        
        return $result;
    }
    
    public function queryAll($sql, $params = []) {
        $result = $this->query($sql, $params);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function queryOne($sql, $params = []) {
        $result = $this->query($sql, $params);
        return $result->fetch_assoc();
    }
    
    public function execute($sql, $params = []) {
        $stmt = $this->prepare($sql, $params);
        return $stmt->execute();
    }
    
    public function insert($table, $data) {
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->escapeIdentifier($table),
            implode(', ', array_map([$this, 'escapeIdentifier'], $fields)),
            implode(', ', $placeholders)
        );
        
        $this->execute($sql, $values);
        return $this->lastInsertId();
    }
    
    public function update($table, $data, $where, $whereParams = []) {
        $fields = [];
        $values = [];
        
        foreach ($data as $field => $value) {
            $fields[] = $this->escapeIdentifier($field) . ' = ?';
            $values[] = $value;
        }
        
        $sql = sprintf(
            "UPDATE %s SET %s WHERE %s",
            $this->escapeIdentifier($table),
            implode(', ', $fields),
            $where
        );
        
        $values = array_merge($values, $whereParams);
        return $this->execute($sql, $values);
    }
    
    public function delete($table, $where, $params = []) {
        $sql = sprintf(
            "DELETE FROM %s WHERE %s",
            $this->escapeIdentifier($table),
            $where
        );
        
        return $this->execute($sql, $params);
    }
    
    public function count($table, $where = '1=1', $params = []) {
        $sql = sprintf(
            "SELECT COUNT(*) as count FROM %s WHERE %s",
            $this->escapeIdentifier($table),
            $where
        );
        
        $result = $this->queryOne($sql, $params);
        return $result['count'] ?? 0;
    }
    
    public function lastInsertId() {
        return $this->connection->insert_id;
    }
    
    public function escapeIdentifier($identifier) {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
    
    public function beginTransaction() {
        return $this->connection->begin_transaction();
    }
    
    public function commit() {
        return $this->connection->commit();
    }
    
    public function rollback() {
        return $this->connection->rollback();
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function clearCache() {
        $this->queryCache = [];
    }
    
    public function setCacheEnabled($enabled) {
        $this->cacheEnabled = $enabled;
    }
}