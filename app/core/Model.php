<?php
namespace App\Core;

abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function find($id) {
        $sql = "SELECT * FROM {$this->db->escapeIdentifier($this->table)} WHERE {$this->db->escapeIdentifier($this->primaryKey)} = ?";
        return $this->db->queryOne($sql, [$id]);
    }
    
    public function findAll($conditions = [], $orderBy = null, $limit = null) {
        $sql = "SELECT * FROM {$this->db->escapeIdentifier($this->table)}";
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = $this->db->escapeIdentifier($field) . " = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        
        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        
        return $this->db->queryAll($sql, $params);
    }
    
    public function create($data) {
        $filteredData = $this->filterFillable($data);
        return $this->db->insert($this->table, $filteredData);
    }
    
    public function update($id, $data) {
        $filteredData = $this->filterFillable($data);
        $where = $this->db->escapeIdentifier($this->primaryKey) . " = ?";
        return $this->db->update($this->table, $filteredData, $where, [$id]);
    }
    
    public function delete($id) {
        $where = $this->db->escapeIdentifier($this->primaryKey) . " = ?";
        return $this->db->delete($this->table, $where, [$id]);
    }
    
    public function count($conditions = []) {
        if (empty($conditions)) {
            return $this->db->count($this->table);
        }
        
        $where = [];
        $params = [];
        foreach ($conditions as $field => $value) {
            $where[] = $this->db->escapeIdentifier($field) . " = ?";
            $params[] = $value;
        }
        
        return $this->db->count($this->table, implode(" AND ", $where), $params);
    }
    
    protected function filterFillable($data) {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    public function paginate($page = 1, $perPage = 10, $conditions = []) {
        $offset = ($page - 1) * $perPage;
        $total = $this->count($conditions);
        
        $sql = "SELECT * FROM {$this->db->escapeIdentifier($this->table)}";
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = $this->db->escapeIdentifier($field) . " = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $sql .= " LIMIT $perPage OFFSET $offset";
        
        $items = $this->db->queryAll($sql, $params);
        
        return [
            'items' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }
}