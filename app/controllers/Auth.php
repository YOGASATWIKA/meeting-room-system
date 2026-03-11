<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;
use App\Services\ValidationService;

/**
 * Auth Controller
 * Controller untuk autentikasi (Login, Register, Logout)
 * Menerapkan: Methods, Procedures, Control Structures
 */
class Auth extends Controller {
    
    private $authService;
    private $validationService;
    
    /**
     * Constructor
     * Dependency Injection
     */
    public function __construct() {
        $this->authService = new AuthService();
        $this->validationService = new ValidationService();
    }
    
    /**
     * Login Page
     * Menampilkan form login
     * 
     * @return void
     */
    public function login() {
        // Redirect jika sudah login (control structure: if)
        if ($this->authService->isLoggedIn()) {
            $this->redirect('home/dashboard');
        }
        
        $data = [
            'title' => 'Login'
        ];
        
        $this->view('auth/login', $data);
    }
    
    /**
     * Process Login
     * Method untuk proses login (POST)
     * Demonstrasi: Form Processing, Validation, Session
     * 
     * @return void
     */
    public function processLogin() {
        // Check if POST request (control structure)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('auth/login');
        }
        
        // Get and sanitize input (array usage)
        $username = $this->validationService->sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validate required fields
        if (empty($username) || empty($password)) {
            $this->flash('error', 'Username and password are required');
            $this->redirect('auth/login');
        }
        
        // Attempt login
        if ($this->authService->login($username, $password)) {
            $this->flash('success', 'Login successful');
            $this->redirect('home/dashboard');
        } else {
            $this->flash('error', 'Invalid username or password');
            $this->redirect('auth/login');
        }
    }
    
    /**
     * Register Page
     * Menampilkan form register
     * 
     * @return void
     */
    public function register() {
        if ($this->authService->isLoggedIn()) {
            $this->redirect('home/dashboard');
        }
        
        $data = [
            'title' => 'Register New Account'
        ];
        
        $this->view('auth/register', $data);
    }
    
    /**
     * Process Registration
     * Method untuk proses registrasi user baru
     * Demonstrasi: Data Validation, Array Processing
     * 
     * @return void
     */
    public function processRegister() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('auth/register');
        }
        
        // Sanitize data (demonstrasi array untuk input processing)
        $userData = [
            'username' => $this->validationService->sanitize($_POST['username'] ?? ''),
            'email' => $this->validationService->sanitize($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? '',
            'full_name' => $this->validationService->sanitize($_POST['full_name'] ?? ''),
            'phone' => $this->validationService->sanitize($_POST['phone'] ?? ''),
            'role' => 'user' // Default role
        ];
        
        // Validate required fields (control structure: if)
        $requiredFields = ['username', 'email', 'password', 'full_name'];
        if (!$this->validationService->validateRequired($userData, $requiredFields)) {
            $errors = $this->validationService->getErrors();
            $this->flash('error', 'Please fill all required fields');
            $this->redirect('auth/register');
        }
        
        // Validate email format
        if (!$this->validationService->validateEmail($userData['email'])) {
            $this->flash('error', 'Invalid email format');
            $this->redirect('auth/register');
        }
        
        // Validate password match (control structure: if-else)
        if ($userData['password'] !== $userData['confirm_password']) {
            $this->flash('error', 'Passwords do not match');
            $this->redirect('auth/register');
        }
        
        // Validate password length
        if (!$this->validationService->validateMinLength($userData['password'], 6, 'password')) {
            $this->flash('error', 'Password must be at least 6 characters');
            $this->redirect('auth/register');
        }
        
        // Remove confirm_password before saving
        unset($userData['confirm_password']);
        
        // Register user
        $userId = $this->authService->register($userData);
        
        if ($userId) {
            $this->flash('success', 'Registration successful. Please login.');
            $this->redirect('auth/login');
        } else {
            $this->flash('error', 'Registration failed. Username or email may already exist.');
            $this->redirect('auth/register');
        }
    }
    
    /**
     * Logout
     * Method untuk logout user
     * 
     * @return void
     */
    public function logout() {
        $this->authService->logout();
        $this->flash('success', 'You have been logged out');
        $this->redirect('auth/login');
    }
}
