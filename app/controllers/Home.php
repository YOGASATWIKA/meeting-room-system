<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Room;
use App\Models\Booking;
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
        $roomModel = new Room();
        
        // Prepare data untuk view (demonstrasi array)
        $data = [
            'title' => 'Welcome to Room Management System',
            'rooms' => array_slice(Room::all(), 0, 6),
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
        $bookingModel = new Booking();
        
        // Control structure: if-else untuk different dashboard by role
        if ($user['role'] === 'admin') {
            // Admin dashboard dengan statistics
            $data = [
                'title' => 'Admin Dashboard',
                'user' => $user,
                'statistics' => $bookingModel->getStatistics(),
                'upcoming_bookings' => $bookingModel->getUpcomingBookings(10),
                'total_rooms' => Room::count()
            ];
            
            $this->view('home/admin_dashboard', $data);
        } else {
            // User dashboard
            $data = [
                'title' => 'My Dashboard',
                'user' => $user,
                'my_bookings' => $bookingModel->getBookingsByUser($user['id']),
                'booking_count' => count($bookingModel->getBookingsByUser($user['id']))
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
                'description' => 'Professional Room Booking and Management System',
                'features' => [
                    'Room Booking Management',
                    'User Management',
                    'Booking Tracking',
                    'Reports and Analytics'
                ]
            ]
        ];
        
        $this->view('home/about', $data);
    }
}
