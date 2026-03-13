<?php
namespace App\Models;

/**
 * Order Model
 * Model untuk mengelola data pesanan catering
 * Menerapkan: Inheritance, Complex Queries
 */
class Order extends BaseModel {
    protected static $table = 'orders';
    protected static $primaryKey = 'id';
    
    /**
     * Fillable fields
     * @var array
     */
    protected $fillable = ['user_id', 'order_number', 'order_date', 'delivery_date', 'delivery_address', 'total_amount', 'status', 'notes'];
    
    /**
     * Validation rules
     * @var array
     */
    protected $rules = [
        'user_id' => 'required',
        'delivery_date' => 'required',
        'delivery_address' => 'required|min:10'
    ];
    
    /**
     * Create order with items
     * Method untuk create order lengkap dengan items
     * Demonstrasi Transaction
     * 
     * @param array $orderData Order data
     * @param array $items Order items array
     * @return int|false Order ID or false on failure
     */
    public function createWithItems(array $orderData, array $items) {
        try {
            // Begin transaction
            $this->db->beginTransaction();
            
            // Generate order number
            $orderData['order_number'] = $this->generateOrderNumber();
            $orderData['order_date'] = date('Y-m-d H:i:s');
            
            // Calculate total amount dari items (demonstrasi loop dan array)
            $totalAmount = 0;
            foreach ($items as $item) {
                $totalAmount += $item['price'] * $item['quantity'];
            }
            $orderData['total_amount'] = $totalAmount;
            
            // Insert order
            $orderId = $this->create($orderData);
            
            // Insert order items (loop demonstration)
            $productModel = new Product();
            
            foreach ($items as $item) {
                // Insert order item
                $this->db->query("INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) 
                                VALUES (:order_id, :product_id, :quantity, :price, :subtotal)");
                
                $this->db->bind(':order_id', $orderId);
                $this->db->bind(':product_id', $item['product_id']);
                $this->db->bind(':quantity', $item['quantity']);
                $this->db->bind(':price', $item['price']);
                $this->db->bind(':subtotal', $item['price'] * $item['quantity']);
                $this->db->execute();
                
                // Update product stock
                $productModel->updateStock($item['product_id'], $item['quantity'], 'subtract');
            }
            
            // Commit transaction
            $this->db->commit();
            
            return $orderId;
            
        } catch (\Exception $e) {
            // Rollback on error
            $this->db->rollback();
            return false;
        }
    }
    
    /**
     * Generate unique order number
     * Format: ORD-YYYYMMDD-XXXXX
     * 
     * @return string Order number
     */
    private function generateOrderNumber() {
        $date = date('Ymd');
        $random = str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        return "ORD-$date-$random";
    }
    
    /**
     * Get order with items
     * Join dengan order items dan products
     * 
     * @param int $orderId Order ID
     * @return array|false Order data with items
     */
    public function getOrderWithItems($orderId) {
        // Get order data
        $order = $this->read($orderId);
        
        if (!$order) {
            return false;
        }
        
        // Get order items
        $sql = "SELECT oi.*, p.name as product_name, p.image as product_image
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = :order_id";
        
        $this->db->query($sql);
        $this->db->bind(':order_id', $orderId);
        
        $order['items'] = $this->db->resultSet();
        
        return $order;
    }
    
    /**
     * Get orders by user
     * 
     * @param int $userId User ID
     * @param string $status Order status (optional)
     * @return array User orders
     */
    public function getOrdersByUser($userId, $status = null) {
        $sql = "SELECT * FROM " . self::$table . " WHERE user_id = :user_id";
        
        // Control structure: if untuk conditional query
        if ($status !== null) {
            $sql .= " AND status = :status";
        }
        
        $sql .= " ORDER BY order_date DESC";
        
        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        
        if ($status !== null) {
            $this->db->bind(':status', $status);
        }
        
        return $this->db->resultSet();
    }
    
    /**
     * Update order status
     * 
     * @param int $orderId Order ID
     * @param string $newStatus New status
     * @return bool Update status
     */
    public function updateStatus($orderId, $newStatus) {
        // Validate status (control structure: in_array)
        $validStatuses = ['pending', 'confirmed', 'processing', 'delivered', 'cancelled'];
        
        if (!in_array($newStatus, $validStatuses)) {
            return false;
        }
        
        return $this->updateRecord($orderId, ['status' => $newStatus]);
    }
    
    /**
     * Get order statistics
     * Method untuk dashboard statistics
     * 
     * @param string $startDate Start date (optional)
     * @param string $endDate End date (optional)
     * @return array Statistics data
     */
    public function getStatistics($startDate = null, $endDate = null) {
        $sql = "SELECT 
                    COUNT(*) as total_orders,
                    SUM(total_amount) as total_revenue,
                    AVG(total_amount) as average_order_value,
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_orders,
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_orders,
                    COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_orders
                FROM " . self::$table;
        
        // Control structure: conditional SQL berdasarkan parameters
        $conditions = [];
        if ($startDate) {
            $conditions[] = "order_date >= :start_date";
        }
        if ($endDate) {
            $conditions[] = "order_date <= :end_date";
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $this->db->query($sql);
        
        if ($startDate) {
            $this->db->bind(':start_date', $startDate);
        }
        if ($endDate) {
            $this->db->bind(':end_date', $endDate);
        }
        
        return $this->db->single();
    }
    
    /**
     * Update order with items
     * Updates order data and replaces order items
     * 
     * @param int $orderId Order ID
     * @param array $orderData Order data
     * @param array $items New order items array
     * @return bool Update status
     */
    public function updateWithItems($orderId, array $orderData, array $items) {
        try {
            // Begin transaction
            $this->db->beginTransaction();
            
            // Get existing order items to restore stock
            $existingItems = $this->getOrderItems($orderId);
            $productModel = new Product();
            
            // Restore stock for existing items
            foreach ($existingItems as $item) {
                $productModel->updateStock($item['product_id'], $item['quantity'], 'add');
            }
            
            // Calculate new total amount
            $totalAmount = 0;
            foreach ($items as $item) {
                $totalAmount += $item['price'] * $item['quantity'];
            }
            $orderData['total_amount'] = $totalAmount;
            
            // Update order
            $this->updateRecord($orderId, $orderData);
            
            // Delete existing order items
            $this->db->query("DELETE FROM order_items WHERE order_id = :order_id");
            $this->db->bind(':order_id', $orderId);
            $this->db->execute();
            
            // Insert new order items
            foreach ($items as $item) {
                // Insert order item
                $this->db->query("INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) 
                                VALUES (:order_id, :product_id, :quantity, :price, :subtotal)");
                
                $this->db->bind(':order_id', $orderId);
                $this->db->bind(':product_id', $item['product_id']);
                $this->db->bind(':quantity', $item['quantity']);
                $this->db->bind(':price', $item['price']);
                $this->db->bind(':subtotal', $item['price'] * $item['quantity']);
                $this->db->execute();
                
                // Update product stock (subtract)
                $productModel->updateStock($item['product_id'], $item['quantity'], 'subtract');
            }
            
            // Commit transaction
            $this->db->commit();
            
            return true;
            
        } catch (\Exception $e) {
            // Rollback on error
            $this->db->rollback();
            return false;
        }
    }
    
    /**
     * Get order items only
     * 
     * @param int $orderId Order ID
     * @return array Order items
     */
    private function getOrderItems($orderId) {
        $sql = "SELECT * FROM order_items WHERE order_id = :order_id";
        $this->db->query($sql);
        $this->db->bind(':order_id', $orderId);
        return $this->db->resultSet();
    }
    
    /**
     * Delete order and its items
     * Restores product stock
     * 
     * @param int $orderId Order ID
     * @return bool Delete status
     */
    public function deleteOrder($orderId) {
        try {
            // Begin transaction
            $this->db->beginTransaction();
            
            // Get order items to restore stock
            $orderItems = $this->getOrderItems($orderId);
            $productModel = new Product();
            
            // Restore stock for all items
            foreach ($orderItems as $item) {
                $productModel->updateStock($item['product_id'], $item['quantity'], 'add');
            }
            
            // Delete order items
            $this->db->query("DELETE FROM order_items WHERE order_id = :order_id");
            $this->db->bind(':order_id', $orderId);
            $this->db->execute();
            
            // Delete order
            $this->deleteRecord($orderId);
            
            // Commit transaction
            $this->db->commit();
            
            return true;
            
        } catch (\Exception $e) {
            // Rollback on error
            $this->db->rollback();
            return false;
        }
    }
}