<?php
namespace App\Models;

use App\Core\Model;

class User extends Model {
    protected $table = 'users';
    protected $primaryKey = 'id_users';
    protected $fillable = ['username', 'email', 'password', 'full_name'];
    
    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->db->escapeIdentifier($this->table)} WHERE email = ?";
        return $this->db->queryOne($sql, [$email]);
    }
    
    public function findByUsername($username) {
        $sql = "SELECT * FROM {$this->db->escapeIdentifier($this->table)} WHERE username = ?";
        return $this->db->queryOne($sql, [$username]);
    }
    
    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    public function updateLastLogin($userId) {
        return $this->db->update($this->table, [
            'last_login' => date('Y-m-d H:i:s')
        ], "{$this->primaryKey} = ?", [$userId]);
    }
}