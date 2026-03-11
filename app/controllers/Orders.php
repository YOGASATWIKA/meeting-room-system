<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\AuthService;
use App\Services\ValidationService;

/**
 * Orders Controller
 * Controller untuk manajemen pesanan catering
 * Menerapkan: Transaction, Complex Logic, Array Processing
 */
class Orders extends Controller {
    
    private $orderModel;
    private $productModel;
    private $authService;
    private $validationService;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->orderModel = new Order();
        $this->productModel = new Product();
        $this->authService = new AuthService();
        $this->validationService = new ValidationService();
    }
    
    /**
     * Index - List user's orders
     * 
     * @return void
     */
    public function index() {
        $this->requireLogin();
        
        $user = $this->authService->getCurrentUser();
        
        // Control structure: if untuk admin vs user
        if ($user['role'] === 'admin') {
            // Admin sees all orders
            $orders = Order::all();
        } else {
            // User sees only their orders
            $orders = $this->orderModel->getOrdersByUser($user['id']);
        }
        
        $data = [
            'title' => 'Orders',
            'orders' => $orders,
            'user' => $user
        ];
        
        $this->view('orders/index', $data);
    }
    
    /**
     * Show order detail
     * 
     * @param int $id Order ID
     * @return void
     */
    public function show($id) {
        $this->requireLogin();
        
        $order = $this->orderModel->getOrderWithItems($id);
        $user = $this->authService->getCurrentUser();
        
        // Control structure: check access permission
        if (!$order) {
            $this->flash('error', 'Order not found');
            $this->redirect('orders/index');
        }
        
        // User can only see their own orders (unless admin)
        if ($user['role'] !== 'admin' && $order['user_id'] != $user['id']) {
            $this->flash('error', 'Access denied');
            $this->redirect('orders/index');
        }
        
        $data = [
            'title' => 'Order Detail - ' . $order['order_number'],
            'order' => $order,
            'user' => $user
        ];
        
        $this->view('orders/show', $data);
    }
    
    /**
     * Create new order form
     * Shopping cart concept
     * 
     * @return void
     */
    public function create() {
        $this->requireLogin();
        
        // Get cart from session (demonstrasi array dalam session)
        $cart = $_SESSION['cart'] ?? [];
        $cartItems = [];
        $totalAmount = 0;
        
        // Calculate cart totals (loop demonstration)
        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);
            
            if ($product) {
                $subtotal = $product['price'] * $quantity;
                $totalAmount += $subtotal;
                
                $cartItems[] = [
                    'product_id' => $productId,
                    'product' => $product,
                    'quantity' => $quantity,
                    'price' => $product['price'],
                    'subtotal' => $subtotal
                ];
            }
        }
        
        $data = [
            'title' => 'Create Order',
            'cart_items' => $cartItems,
            'total_amount' => $totalAmount,
            'user' => $this->authService->getCurrentUser()
        ];
        
        $this->view('orders/create', $data);
    }
    
    /**
     * Add product to cart
     * 
     * @param int $productId Product ID
     * @return void
     */
    public function addToCart($productId) {
        $this->requireLogin();
        
        $product = Product::find($productId);
        
        if (!$product) {
            $this->flash('error', 'Product not found');
            $this->redirect('products/index');
        }
        
        $quantity = intval($_POST['quantity'] ?? 1);
        
        // Check stock availability (control structure)
        if ($product['stock'] < $quantity) {
            $this->flash('error', 'Insufficient stock');
            $this->redirect('products/show/' . $productId);
        }
        
        // Initialize cart if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Add to cart (array manipulation)
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
        
        $this->flash('success', 'Product added to cart');
        $this->redirect('products/index');
    }
    
    /**
     * Remove product from cart
     * 
     * @param int $productId Product ID
     * @return void
     */
    public function removeFromCart($productId) {
        $this->requireLogin();
        
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            $this->flash('success', 'Product removed from cart');
        }
        
        $this->redirect('orders/create');
    }
    
    /**
     * Process order creation (POST)
     * Demonstrasi: Transaction, Complex Business Logic
     * 
     * @return void
     */
    public function store() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('orders/create');
        }
        
        $user = $this->authService->getCurrentUser();
        $cart = $_SESSION['cart'] ?? [];
        
        // Validate cart not empty (control structure)
        if (empty($cart)) {
            $this->flash('error', 'Cart is empty');
            $this->redirect('products/index');
        }
        
        // Prepare order data
        $orderData = [
            'user_id' => $user['id'],
            'delivery_date' => $_POST['delivery_date'] ?? '',
            'delivery_address' => $this->validationService->sanitize($_POST['delivery_address'] ?? ''),
            'notes' => $this->validationService->sanitize($_POST['notes'] ?? ''),
            'status' => 'pending'
        ];
        
        // Validate required
        if (empty($orderData['delivery_date']) || empty($orderData['delivery_address'])) {
            $this->flash('error', 'Delivery date and address are required');
            $this->redirect('orders/create');
        }
        
        // Prepare order items (array processing)
        $orderItems = [];
        
        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);
            
            if ($product) {
                $orderItems[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $product['price']
                ];
            }
        }
        
        // Create order with items (demonstrasi transaction)
        $orderId = $this->orderModel->createWithItems($orderData, $orderItems);
        
        if ($orderId) {
            // Clear cart setelah order berhasil
            unset($_SESSION['cart']);
            
            $this->flash('success', 'Order created successfully');
            $this->redirect('orders/show/' . $orderId);
        } else {
            $this->flash('error', 'Failed to create order');
            $this->redirect('orders/create');
        }
    }
    
    /**
     * Update order status (Admin only)
     * 
     * @param int $id Order ID
     * @return void
     */
    public function updateStatus($id) {
        $this->requireLogin();
        
        if (!$this->authService->hasRole('admin')) {
            $this->flash('error', 'Access denied');
            $this->redirect('orders/index');
        }
        
        $newStatus = $_POST['status'] ?? '';
        
        if ($this->orderModel->updateStatus($id, $newStatus)) {
            $this->flash('success', 'Order status updated');
        } else {
            $this->flash('error', 'Failed to update status');
        }
        
        $this->redirect('orders/show/' . $id);
    }
    
    /**
     * Cancel order
     * 
     * @param int $id Order ID
     * @return void
     */
    public function cancel($id) {
        $this->requireLogin();
        
        $user = $this->authService->getCurrentUser();
        $order = Order::find($id);
        
        // Validate order belongs to user (unless admin)
        if ($user['role'] !== 'admin' && $order['user_id'] != $user['id']) {
            $this->flash('error', 'Access denied');
            $this->redirect('orders/index');
        }
        
        // Can only cancel pending orders
        if ($order['status'] !== 'pending') {
            $this->flash('error', 'Only pending orders can be cancelled');
            $this->redirect('orders/show/' . $id);
        }
        
        if ($this->orderModel->updateStatus($id, 'cancelled')) {
            $this->flash('success', 'Order cancelled');
        } else {
            $this->flash('error', 'Failed to cancel order');
        }
        
        $this->redirect('orders/show/' . $id);
    }
}
