<?php
namespace App\Services;

/**
 * Validation Service
 * Service untuk validasi data dan file upload
 * Demonstrasi: Helper Functions, File Upload Handling
 */
class ValidationService {
    
    /**
     * Validation errors
     * @var array
     */
    private $errors = [];
    
    /**
     * Validate required fields
     * 
     * @param array $data Data to validate
     * @param array $requiredFields Required field names
     * @return bool Validation status
     */
    public function validateRequired(array $data, array $requiredFields) {
        $isValid = true;
        
        // Loop through required fields
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $this->errors[$field] = ucfirst($field) . " is required";
                $isValid = false;
            }
        }
        
        return $isValid;
    }
    
    /**
     * Validate email format
     * 
     * @param string $email Email address
     * @return bool Validation status
     */
    public function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = "Invalid email format";
            return false;
        }
        return true;
    }
    
    /**
     * Validate min length
     * 
     * @param string $value Value to check
     * @param int $minLength Minimum length
     * @param string $fieldName Field name for error message
     * @return bool Validation status
     */
    public function validateMinLength($value, $minLength, $fieldName) {
        if (strlen($value) < $minLength) {
            $this->errors[$fieldName] = ucfirst($fieldName) . " must be at least $minLength characters";
            return false;
        }
        return true;
    }
    
    /**
     * Validate file upload
     * Demonstrasi file handling
     * 
     * @param array $file $_FILES array element
     * @param array $allowedTypes Allowed MIME types
     * @param int $maxSize Max file size in bytes
     * @return bool Validation status
     */
    public function validateFile($file, array $allowedTypes = [], $maxSize = 5242880) {
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            $this->errors['file'] = "No file uploaded";
            return false;
        }
        
        // Check upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors['file'] = "File upload error: " . $file['error'];
            return false;
        }
        
        // Check file size
        if ($file['size'] > $maxSize) {
            $maxSizeMB = $maxSize / 1024 / 1024;
            $this->errors['file'] = "File size exceeds maximum allowed size of {$maxSizeMB}MB";
            return false;
        }
        
        // Check file type (control structure)
        if (!empty($allowedTypes)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mimeType, $allowedTypes)) {
                $this->errors['file'] = "Invalid file type. Allowed types: " . implode(', ', $allowedTypes);
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Upload file
     * Method untuk upload file ke server
     * 
     * @param array $file $_FILES array element
     * @param string $uploadDir Upload directory path
     * @param string $prefix Filename prefix (optional)
     * @return string|false Uploaded filename or false on failure
     */
    public function uploadFile($file, $uploadDir, $prefix = '') {
        // Validate upload directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $prefix . uniqid() . '_' . time() . '.' . $extension;
        $targetPath = $uploadDir . $filename;
        
        // Move uploaded file (control structure: if)
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $filename;
        }
        
        $this->errors['upload'] = "Failed to upload file";
        return false;
    }
    
    /**
     * Get validation errors
     * 
     * @return array Validation errors
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Clear errors
     * 
     * @return void
     */
    public function clearErrors() {
        $this->errors = [];
    }
    
    /**
     * Sanitize input
     * Clean user input dari HTML/JS
     * 
     * @param string|array $data Data to sanitize
     * @return string|array Sanitized data
     */
    public function sanitize($data) {
        // Recursive untuk array (demonstrasi recursion)
        if (is_array($data)) {
            $sanitized = [];
            foreach ($data as $key => $value) {
                $sanitized[$key] = $this->sanitize($value);
            }
            return $sanitized;
        }
        
        // Sanitize string
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        
        return $data;
    }
}
