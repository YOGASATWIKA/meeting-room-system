<?php
namespace App\Models;

/**
 * User Model
 * Menerapkan: Inheritance (extends BaseModel)
 * Model untuk mengelola data user/pengguna
 */
class User extends BaseModel {
    protected static $table = 'users';
    protected static $primaryKey = 'id';
    
    /**
     * Fillable fields - Array usage
     * @var array
     */
    protected $fillable = ['username', 'email', 'password', 'full_name', 'role', 'phone'];
    
    /**
     * Validation rules
     * @var array
     */
    protected $rules = [
        'username' => 'required|min:4|max:50',
        'email' => 'required|email',
        'password' => 'required|min:6',
        'full_name' => 'required|min:3',
        'role' => 'required'
    ];
    
    /**
     * Get user by username
     * Custom Method
     * 
     * @param string $username Username
     * @return array|false User data
     */
    public function findByUsername($username) {
        $this->db->query("SELECT * FROM " . self::$table . " WHERE username = :username");
        $this->db->bind(':username', $username);
        return $this->db->single();
    }
    
    /**
     * Get user by email
     * 
     * @param string $email Email address
     * @return array|false User data
     */
    public function findByEmail($email) {
        $this->db->query("SELECT * FROM " . self::$table . " WHERE email = :email");
        $this->db->bind(':email', $email);
        return $this->db->single();
    }
    
    /**
     * Verify user login
     * Method untuk autentikasi user
     * 
     * @param string $username Username or email
     * @param string $password Password
     * @return array|false User data if valid, false otherwise
     */
    public function verifyLogin($username, $password) {
        // Control structure: if-else untuk cek username atau email
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $user = $this->findByEmail($username);
        } else {
            $user = $this->findByUsername($username);
        }
        
        // Verify password
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Create new user with hashed password
     * Method overriding (Polymorphism)
     * 
     * @param array $data User data
     * @return int User ID
     */
    public function create(array $data) {
        // Hash password jika ada
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        // Set default role if not provided
        if (!isset($data['role'])) {
            $data['role'] = 'user';
        }
        
        return parent::create($data);
    }
    
    /**
     * Get users by role
     * Demonstrasi filter dengan parameter
     * 
     * @param string $role User role (admin, user, staff)
     * @return array Users with specified role
     */
    public function getUsersByRole($role) {
        $this->db->query("SELECT * FROM " . self::$table . " WHERE role = :role");
        $this->db->bind(':role', $role);
        return $this->db->resultSet();
    }
    
    /**
     * Get recent users
     * 
     * @param int $limit Number of users to fetch
     * @return array Recent users
     */
    public function getRecentUsers($limit = 10) {
        $this->db->query("SELECT * FROM " . self::$table . " ORDER BY id DESC LIMIT :limit");
        $this->db->bind(':limit', $limit, \PDO::PARAM_INT);
        return $this->db->resultSet();
    }
}
