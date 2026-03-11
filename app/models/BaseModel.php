<?php
namespace App\Models;

use App\Core\Model;
use App\Interfaces\CrudInterface;
use App\Interfaces\Searchable;

/**
 * Base Model Class
 * Abstract class yang mengimplementasikan interface
 * Parent class untuk semua model dengan fitur common
 * Menerapkan: Abstraction, Interface Implementation
 */
abstract class BaseModel extends Model implements CrudInterface, Searchable {
    
    /**
     * Validation Rules
     * @var array
     */
    protected $rules = [];
    
    /**
     * Fillable Fields
     * @var array
     */
    protected $fillable = [];
    
    /**
     * Create new record (Implementation dari CrudInterface)
     * 
     * @param array $data Data to create
     * @return int Created record ID
     */
    public function create(array $data) {
        // Filter only fillable fields
        $filteredData = $this->filterFillable($data);
        return $this->insert($filteredData);
    }
    
    /**
     * Read record (Implementation dari CrudInterface)
     * 
     * @param int $id Record ID
     * @return array|false Record data
     */
    public function read($id) {
        return self::find($id);
    }
    
    /**
     * Update record (Implementation dari CrudInterface)
     * 
     * @param int $id Record ID
     * @param array $data Data to update
     * @return bool Update status
     */
    public function updateRecord($id, array $data) {
        $filteredData = $this->filterFillable($data);
        return $this->update($id, $filteredData);
    }
    
    /**
     * Delete record (Implementation dari CrudInterface)
     * 
     * @param int $id Record ID
     * @return bool Delete status
     */
    public function deleteRecord($id) {
        return self::delete($id);
    }
    
    /**
     * Search records (Implementation dari Searchable)
     * Polymorphism: method ini bisa di-override oleh child class
     * 
     * @param string $keyword Search keyword
     * @param array $fields Fields to search
     * @return array Search results
     */
    public function search($keyword, array $fields = []) {
        // Default implementation: search in fillable fields
        if (empty($fields)) {
            $fields = $this->fillable;
        }
        
        $whereClauses = [];
        
        // Loop untuk build WHERE clause (demonstrasi pengulangan)
        foreach ($fields as $field) {
            $whereClauses[] = "$field LIKE :keyword";
        }
        
        $sql = "SELECT * FROM " . static::$table . " WHERE " . implode(' OR ', $whereClauses);
        
        $this->db->query($sql);
        $this->db->bind(':keyword', "%$keyword%");
        
        return $this->db->resultSet();
    }
    
    /**
     * Filter records (Implementation dari Searchable)
     * 
     * @param array $criteria Filter criteria
     * @return array Filtered results
     */
    public function filter(array $criteria) {
        $whereClauses = [];
        
        foreach ($criteria as $field => $value) {
            $whereClauses[] = "$field = :$field";
        }
        
        $sql = "SELECT * FROM " . static::$table . " WHERE " . implode(' AND ', $whereClauses);
        
        $this->db->query($sql);
        
        foreach ($criteria as $field => $value) {
            $this->db->bind(":$field", $value);
        }
        
        return $this->db->resultSet();
    }
    
    /**
     * Filter fillable fields
     * Helper method untuk filter hanya field yang diizinkan
     * 
     * @param array $data Input data
     * @return array Filtered data
     */
    protected function filterFillable(array $data) {
        $filtered = [];
        
        // Control structure: foreach dengan if
        foreach ($data as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $filtered[$key] = $value;
            }
        }
        
        return $filtered;
    }
    
    /**
     * Validate data against rules
     * 
     * @param array $data Data to validate
     * @return array Validation errors (empty if valid)
     */
    public function validate(array $data) {
        $errors = [];
        
        // Loop through validation rules
        foreach ($this->rules as $field => $rules) {
            $ruleArray = explode('|', $rules);
            
            foreach ($ruleArray as $rule) {
                // Parse rule and parameter
                if (strpos($rule, ':') !== false) {
                    list($ruleName, $parameter) = explode(':', $rule);
                } else {
                    $ruleName = $rule;
                    $parameter = null;
                }
                
                // Apply validation rules (control structure: if-else dan switch)
                switch ($ruleName) {
                    case 'required':
                        if (empty($data[$field])) {
                            $errors[$field][] = ucfirst($field) . " is required";
                        }
                        break;
                    
                    case 'min':
                        if (isset($data[$field]) && strlen($data[$field]) < $parameter) {
                            $errors[$field][] = ucfirst($field) . " must be at least $parameter characters";
                        }
                        break;
                    
                    case 'max':
                        if (isset($data[$field]) && strlen($data[$field]) > $parameter) {
                            $errors[$field][] = ucfirst($field) . " must not exceed $parameter characters";
                        }
                        break;
                    
                    case 'email':
                        if (isset($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = ucfirst($field) . " must be a valid email address";
                        }
                        break;
                }
            }
        }
        
        return $errors;
    }
}
