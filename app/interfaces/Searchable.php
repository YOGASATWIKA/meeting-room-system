<?php
namespace App\Interfaces;

/**
 * Searchable Interface
 * Interface untuk fitur pencarian
 * Demonstrasi penggunaan multiple interfaces
 */
interface Searchable {
    /**
     * Search records by keyword
     * 
     * @param string $keyword Search keyword
     * @param array $fields Fields to search in
     * @return array Search results
     */
    public function search($keyword, array $fields = []);
    
    /**
     * Filter records by criteria
     * 
     * @param array $criteria Filter criteria
     * @return array Filtered results
     */
    public function filter(array $criteria);
}
