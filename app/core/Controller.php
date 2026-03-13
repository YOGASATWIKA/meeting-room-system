<?php
namespace App\Core;

/**
 * Base Controller Class
 * Parent class untuk semua controller
 * Menyediakan method helper untuk view dan model
 */
class Controller {
    
    /**
     * Load View File
     * Method untuk me-load view dengan data
     * 
     * @param string $view View file name
     * @param array $data Data to pass to view (default: empty array)
     * @return void
     */
    public function view($view, $data = []) {
        // Extract array menjadi variabel (demonstrasi array usage)
        extract($data);
        
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        
        // Check if view file exists (control structure: if-else)
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("View file not found: " . $view);
        }
    }
    
    /**
     * Load Model
     * Method untuk instantiate model class
     * 
     * @param string $model Model class name
     * @return object Model instance
     */
    public function model($model) {
        $modelClass = "\\App\\Models\\" . $model;
        
        if (class_exists($modelClass)) {
            return new $modelClass();
        } else {
            die("Model not found: " . $model);
        }
    }
    
    /**
     * Redirect Helper
     * 
     * @param string $url Target URL
     * @return void
     */
    protected function redirect($url) {
        $target = ltrim($url, '/');
        header('Location: ' . BASEURL . '/' . $target);
        exit();
    }
    
    /**
     * Set Flash Message
     * 
     * @param string $type Message type (success, error, warning, info)
     * @param string $message Flash message
     * @return void
     */
    protected function flash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }
    
    /**
     * Check if User is Logged In
     * 
     * @return bool
     */
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Require Login
     * Redirect to login if not authenticated
     * 
     * @return void
     */
    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            $this->flash('error', 'Please login to access this page');
            $this->redirect('auth/login');
        }
    }
}
