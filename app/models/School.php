<?php
namespace App\Models;

use App\Core\Model;

class School extends Model {
    protected $table = 'schools';
    protected $primaryKey = 'id_school';
    protected $fillable = ['school_name', 'short_name', 'email', 'website', 'address', 'phone', 'region_id', 'town_id'];
    
    public function search($query) {
        $sql = "SELECT * FROM {$this->db->escapeIdentifier($this->table)} 
                WHERE school_name LIKE ? OR short_name LIKE ? OR email LIKE ?";
        $searchTerm = "%$query%";
        return $this->db->queryAll($sql, [$searchTerm, $searchTerm, $searchTerm]);
    }
    
    public function getWithRegion($id) {
        $sql = "SELECT s.*, r.region_name, t.town_name 
                FROM {$this->db->escapeIdentifier($this->table)} s
                LEFT JOIN regions r ON s.region_id = r.id_regions
                LEFT JOIN towns t ON s.town_id = t.id_towns
                WHERE s.{$this->primaryKey} = ?";
        return $this->db->queryOne($sql, [$id]);
    }
    
    public function getByRegion($regionId) {
        return $this->findAll(['region_id' => $regionId], 'school_name ASC');
    }
}