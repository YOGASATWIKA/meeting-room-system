<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Services\AuthService;
use App\Services\ValidationService;

/**
 * Bookings Controller
 * Controller untuk manajemen booking ruangan
 * Menerapkan: Transaction, Complex Logic, Date/Time Processing
 */
class Bookings extends Controller {
    
    private $bookingModel;
    private $roomModel;
    private $authService;
    private $validationService;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->bookingModel = new Booking();
        $this->roomModel = new Room();
        $this->authService = new AuthService();
        $this->validationService = new ValidationService();
    }
    
    /**
     * Index - List user's bookings
     * 
     * @return void
     */
    public function index() {
        $this->requireLogin();
        
        $user = $this->authService->getCurrentUser();
        
        // Control structure: if untuk admin vs user
        if ($user['role'] === 'admin') {
            // Admin sees all bookings with details
            $bookings = $this->bookingModel->getAllWithDetails();
        } else {
            // User sees only their bookings
            $bookings = $this->bookingModel->getBookingsByUser($user['id']);
        }
        
        $data = [
            'title' => 'My Bookings',
            'bookings' => $bookings,
            'user' => $user
        ];
        
        $this->view('bookings/index', $data);
    }
    
    /**
     * Show booking detail
     * 
     * @param int $id Booking ID
     * @return void
     */
    public function show($id) {
        $this->requireLogin();
        
        $booking = $this->bookingModel->getBookingWithDetails($id);
        $user = $this->authService->getCurrentUser();
        
        // Control structure: check access permission
        if (!$booking) {
            $this->flash('error', 'Booking not found');
            $this->redirect('bookings/index');
        }
        
        // User can only see their own bookings (unless admin)
        if ($user['role'] !== 'admin' && $booking['user_id'] != $user['id']) {
            $this->flash('error', 'Access denied');
            $this->redirect('bookings/index');
        }
        
        $data = [
            'title' => 'Booking Detail - ' . $booking['booking_number'],
            'booking' => $booking,
            'user' => $user
        ];
        
        $this->view('bookings/show', $data);
    }
    
    /**
     * Create new booking form
     * 
     * @return void
     */
    public function create() {
        $this->requireLogin();
        
        // Get all active rooms
        $rooms = array_filter(Room::all(), function($room) {
            return $room['status'] === 'active';
        });
        
        $data = [
            'title' => 'Create Booking',
            'rooms' => $rooms,
            'user' => $this->authService->getCurrentUser()
        ];
        
        $this->view('bookings/create', $data);
    }
    
    /**
     * Store new booking (POST)
     * Demonstrasi: Date/Time validation, Business logic
     * 
     * @return void
     */
    public function store() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('bookings/create');
        }
        
        $user = $this->authService->getCurrentUser();
        
        // Get form data
        $roomId = intval($_POST['room_id'] ?? 0);
        $bookingDate = $_POST['booking_date'] ?? '';
        $startTime = $_POST['start_time'] ?? '';
        $endTime = $_POST['end_time'] ?? '';
        $purpose = $this->validationService->sanitize($_POST['purpose'] ?? '');
        
        // Validate required fields
        if (!$roomId || !$bookingDate || !$startTime || !$endTime) {
            $this->flash('error', 'Please fill all required fields');
            $this->redirect('bookings/create');
        }
        
        // Validate date is not in the past
        if (strtotime($bookingDate) < strtotime(date('Y-m-d'))) {
            $this->flash('error', 'Booking date cannot be in the past');
            $this->redirect('bookings/create');
        }
        
        // Validate end time is after start time
        if (strtotime($endTime) <= strtotime($startTime)) {
            $this->flash('error', 'End time must be after start time');
            $this->redirect('bookings/create');
        }
        
        // Get room details
        $room = Room::find($roomId);
        
        if (!$room) {
            $this->flash('error', 'Room not found');
            $this->redirect('bookings/create');
        }
        
        // Check if room is available
        if (!$this->roomModel->isAvailable($roomId, $bookingDate, $startTime, $endTime)) {
            $this->flash('error', 'Room is not available for the selected time slot');
            $this->redirect('bookings/create');
        }
        
        // Calculate total price (hours * price_per_hour)
        $startTimestamp = strtotime($startTime);
        $endTimestamp = strtotime($endTime);
        $hours = ($endTimestamp - $startTimestamp) / 3600; // Convert seconds to hours
        $totalPrice = $hours * $room['price_per_hour'];
        
        // Generate booking number
        $bookingNumber = $this->bookingModel->generateBookingNumber();
        
        // Prepare booking data
        $bookingData = [
            'user_id' => $user['id'],
            'room_id' => $roomId,
            'booking_number' => $bookingNumber,
            'booking_date' => $bookingDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'purpose' => $purpose,
            'total_price' => $totalPrice,
            'status' => 'pending'
        ];
        
        // Create booking
        $bookingId = $this->bookingModel->create($bookingData);
        
        if ($bookingId) {
            $this->flash('success', 'Booking created successfully. Booking Number: ' . $bookingNumber);
            $this->redirect('bookings/show/' . $bookingId);
        } else {
            $this->flash('error', 'Failed to create booking');
            $this->redirect('bookings/create');
        }
    }
    
    /**
     * Cancel booking
     * 
     * @param int $id Booking ID
     * @return void
     */
    public function cancel($id) {
        $this->requireLogin();
        
        $booking = Booking::find($id);
        $user = $this->authService->getCurrentUser();
        
        if (!$booking) {
            $this->flash('error', 'Booking not found');
            $this->redirect('bookings/index');
        }
        
        // Check permission
        if ($user['role'] !== 'admin' && $booking['user_id'] != $user['id']) {
            $this->flash('error', 'Access denied');
            $this->redirect('bookings/index');
        }
        
        // Cannot cancel completed bookings
        if ($booking['status'] === 'completed') {
            $this->flash('error', 'Cannot cancel completed booking');
            $this->redirect('bookings/show/' . $id);
        }
        
        // Update status to cancelled
        if ($this->bookingModel->update($id, ['status' => 'cancelled'])) {
            $this->flash('success', 'Booking cancelled successfully');
        } else {
            $this->flash('error', 'Failed to cancel booking');
        }
        
        $this->redirect('bookings/show/' . $id);
    }
    
    /**
     * Edit booking form
     * Only pending bookings can be edited
     * 
     * @param int $id Booking ID
     * @return void
     */
    public function edit($id) {
        $this->requireLogin();
        
        $booking = Booking::find($id);
        $user = $this->authService->getCurrentUser();
        
        if (!$booking) {
            $this->flash('error', 'Booking not found');
            $this->redirect('bookings/index');
        }
        
        // Check permission
        if ($user['role'] !== 'admin' && $booking['user_id'] != $user['id']) {
            $this->flash('error', 'Access denied');
            $this->redirect('bookings/index');
        }
        
        // Only pending bookings can be edited
        if ($booking['status'] !== 'pending') {
            $this->flash('error', 'Only pending bookings can be edited');
            $this->redirect('bookings/show/' . $id);
        }
        
        // Get all active rooms for dropdown
        $rooms = $this->roomModel->getActiveRooms();
        
        $data = [
            'title' => 'Edit Booking',
            'booking' => $booking,
            'rooms' => $rooms,
            'user' => $user
        ];
        
        $this->view('bookings/edit', $data);
    }
    
    /**
     * Update booking (POST)
     * 
     * @param int $id Booking ID
     * @return void
     */
    public function update($id) {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('bookings/edit/' . $id);
        }
        
        $booking = Booking::find($id);
        $user = $this->authService->getCurrentUser();
        
        if (!$booking) {
            $this->flash('error', 'Booking not found');
            $this->redirect('bookings/index');
        }
        
        // Check permission
        if ($user['role'] !== 'admin' && $booking['user_id'] != $user['id']) {
            $this->flash('error', 'Access denied');
            $this->redirect('bookings/index');
        }
        
        // Only pending bookings can be edited
        if ($booking['status'] !== 'pending') {
            $this->flash('error', 'Only pending bookings can be edited');
            $this->redirect('bookings/show/' . $id);
        }
        
        // Get form data
        $roomId = intval($_POST['room_id'] ?? 0);
        $bookingDate = $_POST['booking_date'] ?? '';
        $startTime = $_POST['start_time'] ?? '';
        $endTime = $_POST['end_time'] ?? '';
        $purpose = $this->validationService->sanitize($_POST['purpose'] ?? '');
        
        // Validate required fields
        if (!$roomId || !$bookingDate || !$startTime || !$endTime) {
            $this->flash('error', 'Please fill all required fields');
            $this->redirect('bookings/edit/' . $id);
        }
        
        // Validate date is not in the past
        if (strtotime($bookingDate) < strtotime(date('Y-m-d'))) {
            $this->flash('error', 'Booking date cannot be in the past');
            $this->redirect('bookings/edit/' . $id);
        }
        
        // Validate end time is after start time
        if (strtotime($endTime) <= strtotime($startTime)) {
            $this->flash('error', 'End time must be after start time');
            $this->redirect('bookings/edit/' . $id);
        }
        
        // Get room details
        $room = Room::find($roomId);
        
        if (!$room) {
            $this->flash('error', 'Room not found');
            $this->redirect('bookings/edit/' . $id);
        }
        
        // Check if room is available (exclude current booking)
        if (!$this->roomModel->isAvailableExcept($roomId, $bookingDate, $startTime, $endTime, $id)) {
            $this->flash('error', 'Room is not available for the selected time slot');
            $this->redirect('bookings/edit/' . $id);
        }
        
        // Calculate total price
        $startTimestamp = strtotime($startTime);
        $endTimestamp = strtotime($endTime);
        $hours = ($endTimestamp - $startTimestamp) / 3600;
        $totalPrice = $hours * $room['price_per_hour'];
        
        // Prepare update data
        $bookingData = [
            'room_id' => $roomId,
            'booking_date' => $bookingDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'purpose' => $purpose,
            'total_price' => $totalPrice
        ];
        
        // Update booking
        if ($this->bookingModel->updateRecord($id, $bookingData)) {
            $this->flash('success', 'Booking updated successfully');
            $this->redirect('bookings/show/' . $id);
        } else {
            $this->flash('error', 'Failed to update booking');
            $this->redirect('bookings/edit/' . $id);
        }
    }
    
    /**
     * Delete booking
     * Only pending bookings can be deleted
     * 
     * @param int $id Booking ID
     * @return void
     */
    public function delete($id) {
        $this->requireLogin();
        
        $booking = Booking::find($id);
        $user = $this->authService->getCurrentUser();
        
        if (!$booking) {
            $this->flash('error', 'Booking not found');
            $this->redirect('bookings/index');
        }
        
        // Check permission
        if ($user['role'] !== 'admin' && $booking['user_id'] != $user['id']) {
            $this->flash('error', 'Access denied');
            $this->redirect('bookings/index');
        }
        
        // Only pending bookings can be deleted
        if ($booking['status'] !== 'pending') {
            $this->flash('error', 'Only pending bookings can be deleted');
            $this->redirect('bookings/show/' . $id);
        }
        
        if ($this->bookingModel->deleteRecord($id)) {
            $this->flash('success', 'Booking deleted successfully');
        } else {
            $this->flash('error', 'Failed to delete booking');
        }
        
        $this->redirect('bookings/index');
    }
    
    /**
     * Update booking status (Admin only)
     * 
     * @param int $id Booking ID
     * @return void
     */
    public function updateStatus($id) {
        $this->requireLogin();
        
        if (!$this->authService->hasRole('admin')) {
            $this->flash('error', 'Access denied');
            $this->redirect('bookings/index');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('bookings/show/' . $id);
        }
        
        $booking = Booking::find($id);
        
        if (!$booking) {
            $this->flash('error', 'Booking not found');
            $this->redirect('bookings/index');
        }
        
        $newStatus = $_POST['status'] ?? '';
        $validStatuses = ['pending', 'confirmed', 'cancelled', 'completed'];
        
        if (!in_array($newStatus, $validStatuses)) {
            $this->flash('error', 'Invalid status');
            $this->redirect('bookings/show/' . $id);
        }
        
        if ($this->bookingModel->update($id, ['status' => $newStatus])) {
            $this->flash('success', 'Booking status updated successfully');
        } else {
            $this->flash('error', 'Failed to update booking status');
        }
        
        $this->redirect('bookings/show/' . $id);
    }
    
    /**
     * Get available rooms for a time slot (AJAX)
     * 
     * @return void
     */
    public function getAvailableRooms() {
        header('Content-Type: application/json');
        
        $date = $_GET['date'] ?? '';
        $startTime = $_GET['start_time'] ?? '';
        $endTime = $_GET['end_time'] ?? '';
        
        if (!$date || !$startTime || !$endTime) {
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            return;
        }
        
        $availableRooms = $this->roomModel->getAvailableRooms($date, $startTime, $endTime);
        
        echo json_encode([
            'success' => true,
            'rooms' => $availableRooms
        ]);
    }
}
