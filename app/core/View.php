<?php
namespace App\Core;

class View {
    private $layout = 'main';
    private $viewPath = __DIR__ . '/../views/';
    
    public function render($view, $data = []) {
        extract($data);
        
        $viewFile = $this->viewPath . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($viewFile)) {
            throw new \Exception("View file not found: $view");
        }
        
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        
        if ($this->layout) {
            $layoutFile = $this->viewPath . 'layouts/' . $this->layout . '.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                echo $content;
            }
        } else {
            echo $content;
        }
    }
    
    public function setLayout($layout) {
        $this->layout = $layout;
    }
    
    public function partial($partial, $data = []) {
        extract($data);
        $partialFile = $this->viewPath . 'partials/' . str_replace('.', '/', $partial) . '.php';
        
        if (file_exists($partialFile)) {
            require $partialFile;
        }
    }
    
    public function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}