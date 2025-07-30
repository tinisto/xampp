<?php
namespace App\Core;

abstract class Controller {
    protected $db;
    protected $view;
    
    public function __construct() {
        $this->db = \Database::getInstance();
        $this->view = new View();
    }
    
    protected function model($model) {
        $modelClass = "App\\Models\\$model";
        if (class_exists($modelClass)) {
            return new $modelClass($this->db);
        }
        throw new \Exception("Model $model not found");
    }
    
    protected function view($view, $data = []) {
        $this->view->render($view, $data);
    }
    
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    protected function getParam($key, $default = null) {
        return $_GET[$key] ?? $_POST[$key] ?? $default;
    }
    
    protected function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
    }
    
    protected function requireAdmin() {
        $this->requireAuth();
        if ($_SESSION['user_role'] !== 'admin') {
            $this->redirect('/');
        }
    }
}