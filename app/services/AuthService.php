<?php
namespace App\Services;

use App\Models\User;

/**
 * Authentication Service
 * Service class untuk handle autentikasi
 * Demonstrasi: Separation of Concerns, Service Layer Pattern
 */
class AuthService {
    private $userModel;
    
    /**
     * Constructor
     * Dependency Injection
     */
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * Login user
     * 
     * @param string $username Username or email
     * @param string $password Password
     * @return bool Login status
     */
    public function login($username, $password) {
        $user = $this->userModel->verifyLogin($username, $password);
        
        // Control structure: if untuk check login success
        if ($user) {
            // Set session (demonstrasi array usage)
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['login_time'] = time();
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Logout user
     * 
     * @return void
     */
    public function logout() {
        session_destroy();
        session_start();
    }
    
    /**
     * Check if user is logged in
     * 
     * @return bool
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Get current user data
     * 
     * @return array|null Current user data
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        // Return user data dari session (array)
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'full_name' => $_SESSION['full_name'],
            'role' => $_SESSION['role'],
            'email' => $_SESSION['email']
        ];
    }
    
    /**
     * Check if user has specific role
     * 
     * @param string|array $roles Allowed roles
     * @return bool
     */
    public function hasRole($roles) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        // Convert to array if single role
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        return in_array($_SESSION['role'], $roles);
    }
    
    /**
     * Register new user
     * 
     * @param array $userData User data
     * @return int|false User ID or false on failure
     */
    public function register(array $userData) {
        // Validate data
        $errors = $this->userModel->validate($userData);
        
        if (!empty($errors)) {
            return false;
        }
        
        // Check if username exists
        if ($this->userModel->findByUsername($userData['username'])) {
            return false;
        }
        
        // Check if email exists
        if ($this->userModel->findByEmail($userData['email'])) {
            return false;
        }
        
        // Create user
        return $this->userModel->create($userData);
    }
    
    /**
     * Change password
     * 
     * @param int $userId User ID
     * @param string $oldPassword Old password
     * @param string $newPassword New password
     * @return bool Success status
     */
    public function changePassword($userId, $oldPassword, $newPassword) {
        $user = $this->userModel->read($userId);
        
        if (!$user) {
            return false;
        }
        
        // Verify old password
        if (!password_verify($oldPassword, $user['password'])) {
            return false;
        }
        
        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->userModel->updateRecord($userId, ['password' => $hashedPassword]);
    }
}
