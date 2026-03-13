<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Room;
use App\Services\AuthService;
use App\Services\ValidationService;

/**
 * Rooms Controller
 * Controller untuk manajemen ruangan meeting
 * Menerapkan: CRUD Operations, File Upload, Search
 */
class Rooms extends Controller {
    
    private $roomModel;
    private $authService;
    private $validationService;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->roomModel = new Room();
        $this->authService = new AuthService();
        $this->validationService = new ValidationService();
    }
    
    /**
     * Index - List all rooms
     * Demonstrasi: Array processing, Search functionality
     * 
     * @return void
     */
    public function index() {
        // Get search parameters
        $keyword = $_GET['search'] ?? null;
        $minCapacity = $_GET['capacity'] ?? null;
        
        // Control structure: if-else untuk conditional data fetching
        if ($keyword) {
            $rooms = $this->roomModel->search($keyword);
        } elseif ($minCapacity) {
            $rooms = $this->roomModel->getByCapacity(intval($minCapacity));
        } else {
            $rooms = Room::all();
        }
        
        $data = [
            'title' => 'Meeting Rooms',
            'rooms' => $rooms,
            'search_keyword' => $keyword,
            'min_capacity' => $minCapacity,
            'user' => $this->authService->getCurrentUser()
        ];
        
        $this->view('rooms/index', $data);
    }
    
    /**
     * Show single room detail
     * 
     * @param int $id Room ID
     * @return void
     */
    public function show($id) {
        $room = Room::find($id);
        
        // Control structure: if untuk check room exists
        if (!$room) {
            $this->flash('error', 'Room not found');
            $this->redirect('rooms/index');
        }
        
        $data = [
            'title' => $room['name'],
            'room' => $room,
            'user' => $this->authService->getCurrentUser()
        ];
        
        $this->view('rooms/show', $data);
    }
    
    /**
     * Create room form (Admin only)
     * 
     * @return void
     */
    public function create() {
        $this->requireLogin();
        
        // Check admin role (control structure)
        if (!$this->authService->hasRole('admin')) {
            $this->flash('error', 'Access denied. Admin only.');
            $this->redirect('rooms/index');
        }
        
        $data = [
            'title' => 'Add New Room'
        ];
        
        $this->view('rooms/create', $data);
    }
    
    /**
     * Store new room (POST)
     * Demonstrasi: File Upload, Data Validation, Array
     * 
     * @return void
     */
    public function store() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('rooms/create');
        }
        
        // Prepare room data (array)
        $price = floatval($_POST['price_per_hour'] ?? 0);
        
        // Validate price range
        if ($price < 0 || $price > 99999999.99) {
            $this->flash('error', 'Price per hour must be between 0 and 99,999,999');
            $this->redirect('rooms/create');
        }
        
        $roomData = [
            'name' => $this->validationService->sanitize($_POST['name'] ?? ''),
            'description' => $this->validationService->sanitize($_POST['description'] ?? ''),
            'capacity' => intval($_POST['capacity'] ?? 0),
            'facilities' => $this->validationService->sanitize($_POST['facilities'] ?? ''),
            'price_per_hour' => $price,
            'status' => $_POST['status'] ?? 'active'
        ];
        
        // Validate required fields
        $requiredFields = ['name', 'capacity', 'price_per_hour'];
        if (!$this->validationService->validateRequired($roomData, $requiredFields)) {
            $this->flash('error', 'Please fill all required fields');
            $this->redirect('rooms/create');
        }
        
        // Handle file upload (demonstrasi file handling)
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Validate file
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            
            if ($this->validationService->validateFile($_FILES['image'], $allowedTypes, 2097152)) {
                // Generate unique filename
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = 'room_' . time() . '_' . uniqid() . '.' . $extension;
                
                // Create upload directory if not exists
                $uploadDir = UPLOADPATH . 'rooms/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $uploadPath = $uploadDir . $filename;
                
                // Move uploaded file
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    $roomData['image'] = $filename;
                } else {
                    $this->flash('error', 'Failed to upload image');
                    $this->redirect('rooms/create');
                }
            } else {
                $errors = $this->validationService->getErrors();
                $this->flash('error', implode(', ', $errors));
                $this->redirect('rooms/create');
            }
        }
        
        // Create room
        $roomId = $this->roomModel->create($roomData);
        
        if ($roomId) {
            $this->flash('success', 'Room created successfully');
            $this->redirect('rooms/show/' . $roomId);
        } else {
            $this->flash('error', 'Failed to create room');
            $this->redirect('rooms/create');
        }
    }
    
    /**
     * Edit room form (Admin only)
     * 
     * @param int $id Room ID
     * @return void
     */
    public function edit($id) {
        $this->requireLogin();
        
        if (!$this->authService->hasRole('admin')) {
            $this->flash('error', 'Access denied');
            $this->redirect('rooms/index');
        }
        
        $room = Room::find($id);
        
        if (!$room) {
            $this->flash('error', 'Room not found');
            $this->redirect('rooms/index');
        }
        
        $data = [
            'title' => 'Edit Room',
            'room' => $room
        ];
        
        $this->view('rooms/edit', $data);
    }
    
    /**
     * Update room (POST)
     * 
     * @param int $id Room ID
     * @return void
     */
    public function update($id) {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('rooms/edit/' . $id);
        }
        
        $room = Room::find($id);
        
        if (!$room) {
            $this->flash('error', 'Room not found');
            $this->redirect('rooms/index');
        }
        
        // Prepare update data
        $price = floatval($_POST['price_per_hour'] ?? 0);
        
        // Validate price range
        if ($price < 0 || $price > 99999999.99) {
            $this->flash('error', 'Price per hour must be between 0 and 99,999,999');
            $this->redirect('rooms/edit/' . $id);
        }
        
        $roomData = [
            'name' => $this->validationService->sanitize($_POST['name'] ?? ''),
            'description' => $this->validationService->sanitize($_POST['description'] ?? ''),
            'capacity' => intval($_POST['capacity'] ?? 0),
            'facilities' => $this->validationService->sanitize($_POST['facilities'] ?? ''),
            'price_per_hour' => $price,
            'status' => $_POST['status'] ?? 'active'
        ];
        
        // Handle file upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            
            if ($this->validationService->validateFile($_FILES['image'], $allowedTypes, 2097152)) {
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = 'room_' . time() . '_' . uniqid() . '.' . $extension;
                
                $uploadDir = UPLOADPATH . 'rooms/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $uploadPath = $uploadDir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    // Delete old image if exists
                    if (!empty($room['image']) && file_exists($uploadDir . $room['image'])) {
                        unlink($uploadDir . $room['image']);
                    }
                    $roomData['image'] = $filename;
                }
            }
        }
        
        // Update room
        if ($this->roomModel->update($id, $roomData)) {
            $this->flash('success', 'Room updated successfully');
            $this->redirect('rooms/show/' . $id);
        } else {
            $this->flash('error', 'Failed to update room');
            $this->redirect('rooms/edit/' . $id);
        }
    }
    
    /**
     * Delete room (Admin only)
     * 
     * @param int $id Room ID
     * @return void
     */
    public function delete($id) {
        $this->requireLogin();
        
        if (!$this->authService->hasRole('admin')) {
            $this->flash('error', 'Access denied');
            $this->redirect('rooms/index');
        }
        
        $room = Room::find($id);
        
        if (!$room) {
            $this->flash('error', 'Room not found');
            $this->redirect('rooms/index');
        }
        
        // Delete image file if exists
        if (!empty($room['image'])) {
            $uploadDir = UPLOADPATH . 'rooms/';
            $imagePath = $uploadDir . $room['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        // Delete room
        if ($this->roomModel->delete($id)) {
            $this->flash('success', 'Room deleted successfully');
        } else {
            $this->flash('error', 'Failed to delete room');
        }
        
        $this->redirect('rooms/index');
    }
    
    /**
     * Check room availability
     * AJAX endpoint
     * 
     * @return void
     */
    public function checkAvailability() {
        header('Content-Type: application/json');
        
        $roomId = $_GET['room_id'] ?? null;
        $date = $_GET['date'] ?? null;
        $startTime = $_GET['start_time'] ?? null;
        $endTime = $_GET['end_time'] ?? null;
        
        if (!$roomId || !$date || !$startTime || !$endTime) {
            echo json_encode(['available' => false, 'message' => 'Missing parameters']);
            return;
        }
        
        $isAvailable = $this->roomModel->isAvailable($roomId, $date, $startTime, $endTime);
        
        echo json_encode([
            'available' => $isAvailable,
            'message' => $isAvailable ? 'Room is available' : 'Room is already booked for this time'
        ]);
    }
}
