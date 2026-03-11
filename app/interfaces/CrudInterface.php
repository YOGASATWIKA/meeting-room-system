<?php
namespace App\Interfaces;

/**
 * CRUD Interface
 * Interface untuk operasi CRUD standard
 * Menerapkan kontrak yang harus diimplementasikan oleh class
 */
interface CrudInterface {
    /**
     * Create new record
     * 
     * @param array $data Data to create
     * @return int Created record ID
     */
    public function create(array $data);
    
    /**
     * Read/Get record by ID
     * 
     * @param int $id Record ID
     * @return array|false Record data
     */
    public function read($id);
    
    /**
     * Update existing record
     * 
     * @param int $id Record ID
     * @param array $data Data to update
     * @return bool Update status
     */
    public function updateRecord($id, array $data);
    
    /**
     * Delete record
     * 
     * @param int $id Record ID
     * @return bool Delete status
     */
    public function deleteRecord($id);
}
