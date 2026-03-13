<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Services\AuthService;
use App\Services\ValidationService;

/**
 * Products Controller
 * Controller untuk manajemen produk catering
 * Menerapkan: CRUD Operations, File Upload, Search
 */
class Products extends Controller {
    
    private $productModel;
    private $authService;
    private $validationService;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->productModel = new Product();
        $this->authService = new AuthService();
        $this->validationService = new ValidationService();
    }
    
    /**
     * Index - List all products
     * Demonstrasi: Array processing, Pagination concept
     * 
     * @return void
     */
    public function index() {
        // Get search keyword if exists
        $keyword = $_GET['search'] ?? null;
        $category = $_GET['category'] ?? null;
        
        // Control structure: if-else untuk conditional data fetching
        if ($keyword) {
            $products = $this->productModel->search($keyword);
        } elseif ($category) {
            $products = $this->productModel->getByCategory($category);
        } else {
            $products = Product::all();
        }
        
        // Get unique categories (demonstrasi array processing)
        $allProducts = Product::all();
        $categories = [];
        foreach ($allProducts as $product) {
            if (!empty($product['category']) && !in_array($product['category'], $categories)) {
                $categories[] = $product['category'];
            }
        }
        
        $data = [
            'title' => 'Products',
            'products' => $products,
            'categories' => $categories,
            'current_category' => $category,
            'search_keyword' => $keyword,
            'user' => $this->authService->getCurrentUser()
        ];
        
        $this->view('products/index', $data);
    }
    
    /**
     * Show single product detail
     * 
     * @param int $id Product ID
     * @return void
     */
    public function show($id) {
        $product = Product::find($id);
        
        // Control structure: if untuk check product exists
        if (!$product) {
            $this->flash('error', 'Product not found');
            $this->redirect('products/index');
        }
        
        $data = [
            'title' => $product['name'],
            'product' => $product,
            'user' => $this->authService->getCurrentUser()
        ];
        
        $this->view('products/show', $data);
    }
    
    /**
     * Create product form (Admin only)
     * 
     * @return void
     */
    public function create() {
        $this->requireLogin();
        
        // Check admin role (control structure)
        if (!$this->authService->hasRole('admin')) {
            $this->flash('error', 'Access denied');
            $this->redirect('products/index');
        }
        
        $data = [
            'title' => 'Add New Product'
        ];
        
        $this->view('products/create', $data);
    }
    
    /**
     * Store new product (POST)
     * Demonstrasi: File Upload, Data Validation, Array
     * 
     * @return void
     */
    public function store() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('products/create');
        }
        
        // Prepare product data (array)
        $price = floatval($_POST['price'] ?? 0);
        
        // Validate price range
        if ($price < 0 || $price > 99999999.99) {
            $this->flash('error', 'Price must be between 0 and 99,999,999');
            $this->redirect('products/create');
        }
        
        $productData = [
            'name' => $this->validationService->sanitize($_POST['name'] ?? ''),
            'description' => $this->validationService->sanitize($_POST['description'] ?? ''),
            'price' => $price,
            'category' => $this->validationService->sanitize($_POST['category'] ?? ''),
            'stock' => intval($_POST['stock'] ?? 0),
            'status' => $_POST['status'] ?? 'active'
        ];
        
        // Validate required fields
        $requiredFields = ['name', 'price', 'category', 'stock'];
        if (!$this->validationService->validateRequired($productData, $requiredFields)) {
            $this->flash('error', 'Please fill all required fields');
            $this->redirect('products/create');
        }
        
        // Handle file upload (demonstrasi file handling)
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Validate file
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            
            if ($this->validationService->validateFile($_FILES['image'], $allowedTypes, 2097152)) {
                // Upload file
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/meeting-room-system/public/uploads/products/';
                $filename = $this->validationService->uploadFile($_FILES['image'], $uploadDir, 'product_');
                
                if ($filename) {
                    $productData['image'] = $filename;
                }
            }
        }
        
        // Create product
        $productId = $this->productModel->create($productData);
        
        if ($productId) {
            $this->flash('success', 'Product created successfully');
            $this->redirect('products/show/' . $productId);
        } else {
            $this->flash('error', 'Failed to create product');
            $this->redirect('products/create');
        }
    }
    
    /**
     * Edit product form
     * 
     * @param int $id Product ID
     * @return void
     */
    public function edit($id) {
        $this->requireLogin();
        
        if (!$this->authService->hasRole('admin')) {
            $this->flash('error', 'Access denied');
            $this->redirect('products/index');
        }
        
        $product = Product::find($id);
        
        if (!$product) {
            $this->flash('error', 'Product not found');
            $this->redirect('products/index');
        }
        
        $data = [
            'title' => 'Edit Product',
            'product' => $product
        ];
        
        $this->view('products/edit', $data);
    }
    
    /**
     * Update product (POST)
     * 
     * @param int $id Product ID
     * @return void
     */
    public function update($id) {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('products/edit/' . $id);
        }
        
        // Prepare update data
        $price = floatval($_POST['price'] ?? 0);
        
        // Validate price range
        if ($price < 0 || $price > 99999999.99) {
            $this->flash('error', 'Price must be between 0 and 99,999,999');
            $this->redirect('products/edit/' . $id);
        }
        
        $productData = [
            'name' => $this->validationService->sanitize($_POST['name'] ?? ''),
            'description' => $this->validationService->sanitize($_POST['description'] ?? ''),
            'price' => $price,
            'category' => $this->validationService->sanitize($_POST['category'] ?? ''),
            'stock' => intval($_POST['stock'] ?? 0),
            'status' => $_POST['status'] ?? 'active'
        ];
        
        // Handle file upload if new image provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            
            if ($this->validationService->validateFile($_FILES['image'], $allowedTypes, 2097152)) {
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/meeting-room-system/public/uploads/products/';
                $filename = $this->validationService->uploadFile($_FILES['image'], $uploadDir, 'product_');
                
                if ($filename) {
                    $productData['image'] = $filename;
                }
            }
        }
        
        // Update product
        if ($this->productModel->updateRecord($id, $productData)) {
            $this->flash('success', 'Product updated successfully');
            $this->redirect('products/show/' . $id);
        } else {
            $this->flash('error', 'Failed to update product');
            $this->redirect('products/edit/' . $id);
        }
    }
    
    /**
     * Delete product
     * 
     * @param int $id Product ID
     * @return void
     */
    public function delete($id) {
        $this->requireLogin();
        
        if (!$this->authService->hasRole('admin')) {
            $this->flash('error', 'Access denied');
            $this->redirect('products/index');
        }
        
        if (Product::delete($id)) {
            $this->flash('success', 'Product deleted successfully');
        } else {
            $this->flash('error', 'Failed to delete product');
        }
        
        $this->redirect('products/index');
    }
}
