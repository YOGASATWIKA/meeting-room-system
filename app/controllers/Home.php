<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Room;
use App\Models\Order;
use App\Services\AuthService;

/**
 * Home Controller
 * Controller untuk halaman utama
 * Menerapkan: Method dengan berbagai fungsi, Array usage
 */
class Home extends Controller {
    
    private $authService;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->authService = new AuthService();
    }
    
    /**
     * Index Method - Homepage
     * Tampilan utama website
     * 
     * @return void
     */
    public function index() {
        $productModel = new Product();
        $roomModel = new Room();
        
        // Prepare data untuk view (demonstrasi array)
        $data = [
            'title' => 'Welcome to Room & Catering System',
            'featured_products' => $productModel->getPopularProducts(6),
            'available_rooms' => array_slice(Room::all(), 0, 6),
            'user' => $this->authService->getCurrentUser()
        ];
        
        $this->view('home/index', $data);
    }
    
    /**
     * Dashboard Method
     * Admin/User dashboard berdasarkan role
     * Demonstrasi: Control Structure (if-else), Role-based view
     * 
     * @return void
     */
    public function dashboard() {
        $this->requireLogin();
        
        $user = $this->authService->getCurrentUser();
        $orderModel = new Order();
        $productModel = new Product();
        
        // Control structure: if-else untuk different dashboard by role
        if ($user['role'] === 'admin') {
            // Admin dashboard dengan statistics
            $data = [
                'title' => 'Admin Dashboard',
                'user' => $user,
                'statistics' => $orderModel->getStatistics(),
                'low_stock_products' => $productModel->getLowStockProducts(10),
                'recent_orders' => array_slice(Order::all(), 0, 10)
            ];
            
            $this->view('home/admin_dashboard', $data);
        } else {
            // User dashboard
            $data = [
                'title' => 'My Dashboard',
                'user' => $user,
                'my_orders' => $orderModel->getOrdersByUser($user['id']),
                'order_count' => count($orderModel->getOrdersByUser($user['id']))
            ];
            
            $this->view('home/user_dashboard', $data);
        }
    }
    
    /**
     * About Page
     * 
     * @return void
     */
    public function about() {
        $data = [
            'title' => 'About Us',
            'company_info' => [
                'name' => APP_NAME,
                'version' => APP_VERSION,
                'description' => 'Professional Room and Catering Management System',
                'features' => [
                    'Room Booking Management',
                    'Catering Order System',
                    'User Management',
                    'Inventory Tracking',
                    'Reports and Analytics'
                ]
            ]
        ];
        
        $this->view('home/about', $data);
    }
}
