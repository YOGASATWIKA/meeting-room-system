<?php
namespace App\Core;

/**
 * Main Application Class
 * Menghandle routing dan request dispatching
 * Menerapkan Front Controller Pattern
 */
class App {
    // Default controller, method, dan parameters
    protected $controller = 'Home';
    protected $method = 'index';
    protected $params = [];
    
    /**
     * Constructor
     * Parse URL dan dispatch request
     */
    public function __construct() {
        $url = $this->parseURL();
        
        // Get Controller (if-else control structure)
        if ($url !== null && isset($url[0])) {
            $controllerName = ucfirst($url[0]);
            $controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';
            
            if (file_exists($controllerFile)) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
        }
        
        // Require controller file
        $controllerClass = "\\App\\Controllers\\" . $this->controller;
        require_once __DIR__ . '/../controllers/' . $this->controller . '.php';
        $this->controller = new $controllerClass;
        
        // Get Method
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }
        
        // Get Parameters (array usage)
        if (!empty($url)) {
            $this->params = array_values($url);
        }
        
        // Run controller with method and params
        call_user_func_array([$this->controller, $this->method], $this->params);
    }
    
    /**
     * Parse URL from Request
     * Method untuk mem-parse URL dari request
     * 
     * @return array|null Parsed URL segments
     */
    protected function parseURL() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return null;
    }
}
