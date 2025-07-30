<?php
namespace App\Models;

use App\Core\Model;

class Post extends Model {
    protected $table = 'posts';
    protected $primaryKey = 'id_posts';
    protected $fillable = ['title_post', 'text_post', 'url_post', 'author_id', 'category_id', 'status'];
    
    public function getPublished($limit = null) {
        return $this->findAll(['status' => 'published'], 'created_at DESC', $limit);
    }
    
    public function getByUrl($url) {
        $sql = "SELECT p.*, u.username as author_name 
                FROM {$this->db->escapeIdentifier($this->table)} p
                LEFT JOIN users u ON p.author_id = u.id_users
                WHERE p.url_post = ?";
        return $this->db->queryOne($sql, [$url]);
    }
    
    public function search($query) {
        $sql = "SELECT * FROM {$this->db->escapeIdentifier($this->table)} 
                WHERE (title_post LIKE ? OR text_post LIKE ?) AND status = 'published'";
        $searchTerm = "%$query%";
        return $this->db->queryAll($sql, [$searchTerm, $searchTerm]);
    }
    
    public function incrementViews($id) {
        $sql = "UPDATE {$this->db->escapeIdentifier($this->table)} 
                SET views = views + 1 WHERE {$this->primaryKey} = ?";
        return $this->db->execute($sql, [$id]);
    }
}