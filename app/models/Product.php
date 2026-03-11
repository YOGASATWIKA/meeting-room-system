<?php
namespace App\Models;

/**
 * Product Model
 * Model untuk mengelola data produk catering
 * Menerapkan: Inheritance dan Method Overriding (Polymorphism)
 */
class Product extends BaseModel {
    protected static $table = 'products';
    protected static $primaryKey = 'id';
    
    /**
     * Fillable fields
     * @var array
     */
    protected $fillable = ['name', 'description', 'price', 'category', 'stock', 'image', 'status'];
    
    /**
     * Validation rules
     * @var array
     */
    protected $rules = [
        'name' => 'required|min:3|max:100',
        'price' => 'required',
        'category' => 'required',
        'stock' => 'required'
    ];
    
    /**
     * Get products by category
     * Custom method specific untuk Product
     * 
     * @param string $category Category name
     * @return array Products in category
     */
    public function getByCategory($category) {
        $this->db->query("SELECT * FROM " . self::$table . " WHERE category = :category AND status = 'active'");
        $this->db->bind(':category', $category);
        return $this->db->resultSet();
    }
    
    /**
     * Get available products (in stock)
     * Demonstrasi method dengan kondisi
     * 
     * @return array Available products
     */
    public function getAvailableProducts() {
        $this->db->query("SELECT * FROM " . self::$table . " WHERE stock > 0 AND status = 'active' ORDER BY name");
        return $this->db->resultSet();
    }
    
    /**
     * Search products (Method Overriding - Polymorphism)
     * Override parent search dengan custom logic
     * 
     * @param string $keyword Search keyword
     * @param array $fields Fields to search (default: name, description)
     * @return array Search results
     */
    public function search($keyword, array $fields = ['name', 'description']) {
        // Custom search logic for products: juga filter by status
        $whereClauses = [];
        
        foreach ($fields as $field) {
            $whereClauses[] = "$field LIKE :keyword";
        }
        
        $sql = "SELECT * FROM " . self::$table . " WHERE (" . implode(' OR ', $whereClauses) . ") AND status = 'active'";
        
        $this->db->query($sql);
        $this->db->bind(':keyword', "%$keyword%");
        
        return $this->db->resultSet();
    }
    
    /**
     * Update stock
     * Method untuk update stok produk
     * 
     * @param int $productId Product ID
     * @param int $quantity Quantity to add/subtract
     * @param string $operation Operation: 'add' or 'subtract'
     * @return bool Update status
     */
    public function updateStock($productId, $quantity, $operation = 'subtract') {
        $product = $this->read($productId);
        
        if (!$product) {
            return false;
        }
        
        $newStock = $product['stock'];
        
        // Control structure: if-else untuk determine operation
        if ($operation === 'add') {
            $newStock += $quantity;
        } else if ($operation === 'subtract') {
            $newStock -= $quantity;
            // Validate stock tidak minus
            if ($newStock < 0) {
                return false;
            }
        }
        
        return $this->updateRecord($productId, ['stock' => $newStock]);
    }
    
    /**
     * Get popular products
     * Produk yang paling banyak dipesan
     * 
     * @param int $limit Number of products
     * @return array Popular products
     */
    public function getPopularProducts($limit = 5) {
        $sql = "SELECT p.*, COUNT(oi.id) as order_count 
                FROM " . self::$table . " p
                LEFT JOIN order_items oi ON p.id = oi.product_id
                WHERE p.status = 'active'
                GROUP BY p.id
                ORDER BY order_count DESC
                LIMIT :limit";
        
        $this->db->query($sql);
        $this->db->bind(':limit', $limit, \PDO::PARAM_INT);
        return $this->db->resultSet();
    }
    
    /**
     * Get products with low stock
     * Warning untuk produk dengan stok menipis
     * 
     * @param int $threshold Stock threshold
     * @return array Products with low stock
     */
    public function getLowStockProducts($threshold = 10) {
        $this->db->query("SELECT * FROM " . self::$table . " WHERE stock <= :threshold AND stock > 0 ORDER BY stock ASC");
        $this->db->bind(':threshold', $threshold);
        return $this->db->resultSet();
    }
}
