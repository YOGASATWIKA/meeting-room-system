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
    
    protected function parseURL() {
        // 1. Ambil PATH dari URL (Misal: /auth/register)
        // parse_url digunakan untuk membuang query string seperti ?id=1
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // 2. Bersihkan Base Path (Penting jika di lokal pakai folder, di prod tidak)
        // Ini akan menghapus "/meeting-room-system" dari path jika ada
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        
        // Jika scriptName bukan root, kita hapus dari URI
        if ($scriptName !== '/' && $scriptName !== '\\') {
            $uri = str_replace($scriptName, '', $uri);
        }

        // 3. Bersihkan sisa slash dan filter karakter aneh
        $url = trim($uri, '/');

        // 4. Jika setelah dibersihkan URL kosong, return null (biar default Home jalan)
        if ($url === "") {
            return null;
        }

        // 5. Ubah string menjadi array (Misal: "auth/register" jadi ["auth", "register"])
        $url = filter_var($url, FILTER_SANITIZE_URL);
        return explode('/', $url);
    }
}
