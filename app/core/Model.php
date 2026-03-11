<?php
namespace App\Core;

use App\Core\Database;

/**
 * Base Model Class
 * Abstract parent class untuk semua model
 * Mengimplementasikan Active Record Pattern
 */
abstract class Model {
    protected static $table = '';
    protected static $primaryKey = 'id';
    protected $db;
    
    /**
     * Constructor
     * Inisialisasi database connection
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get All Records
     * Method untuk mengambil semua data (static method)
     * 
     * @return array Array of records
     */
    public static function all() {
        $instance = new static();
        $instance->db->query("SELECT * FROM " . static::$table);
        return $instance->db->resultSet();
    }
    
    /**
     * Find Record by ID
     * 
     * @param int $id Record ID
     * @return array|false Record data
     */
    public static function find($id) {
        $instance = new static();
        $instance->db->query("SELECT * FROM " . static::$table . " WHERE " . static::$primaryKey . " = :id");
        $instance->db->bind(':id', $id);
        return $instance->db->single();
    }
    
    /**
     * Insert Record
     * Method untuk menyimpan data baru
     * 
     * @param array $data Data to insert
     * @return int Last insert ID
     */
    public function insert($data) {
        // Build insert query dynamically (demonstrasi array usage dan loop)
        $columns = [];
        $placeholders = [];
        
        foreach ($data as $key => $value) {
            $columns[] = $key;
            $placeholders[] = ':' . $key;
        }
        
        $sql = "INSERT INTO " . static::$table . " (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $this->db->query($sql);
        
        // Bind parameters (loop demonstration)
        foreach ($data as $key => $value) {
            $this->db->bind(':' . $key, $value);
        }
        
        $this->db->execute();
        return $this->db->lastInsertId();
    }
    
    /**
     * Update Record
     * 
     * @param int $id Record ID
     * @param array $data Data to update
     * @return bool Update status
     */
    public function update($id, $data) {
        // Build update query dynamically
        $setParts = [];
        
        foreach ($data as $key => $value) {
            $setParts[] = "$key = :$key";
        }
        
        $sql = "UPDATE " . static::$table . " SET " . implode(', ', $setParts) . 
               " WHERE " . static::$primaryKey . " = :id";
        
        $this->db->query($sql);
        
        foreach ($data as $key => $value) {
            $this->db->bind(':' . $key, $value);
        }
        
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }
    
    /**
     * Delete Record
     * 
     * @param int $id Record ID
     * @return bool Delete status
     */
    public static function delete($id) {
        $instance = new static();
        $instance->db->query("DELETE FROM " . static::$table . " WHERE " . static::$primaryKey . " = :id");
        $instance->db->bind(':id', $id);
        return $instance->db->execute();
    }
    
    /**
     * Count Records
     * 
     * @return int Total records
     */
    public static function count() {
        $instance = new static();
        $instance->db->query("SELECT COUNT(*) as total FROM " . static::$table);
        $result = $instance->db->single();
        return $result['total'];
    }
}
