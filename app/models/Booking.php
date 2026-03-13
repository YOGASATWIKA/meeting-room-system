<?php
namespace App\Models;

/**
 * Booking Model
 * Model untuk mengelola data booking ruangan
 * Menerapkan: Inheritance, Complex Queries
 */
class Booking extends BaseModel {
    protected static $table = 'bookings';
    protected static $primaryKey = 'id';
    
    /**
     * Fillable fields
     * @var array
     */
    protected $fillable = ['user_id', 'room_id', 'booking_number', 'booking_date', 'start_time', 'end_time', 'purpose', 'total_price', 'status'];
    
    /**
     * Validation rules
     * @var array
     */
    protected $rules = [
        'user_id' => 'required',
        'room_id' => 'required',
        'booking_date' => 'required',
        'start_time' => 'required',
        'end_time' => 'required',
        'total_price' => 'required'
    ];
    
    /**
     * Get bookings by user
     * 
     * @param int $userId User ID
     * @return array User's bookings
     */
    public function getBookingsByUser($userId) {
        $sql = "SELECT b.*, r.name as room_name, r.capacity 
                FROM " . self::$table . " b
                LEFT JOIN rooms r ON b.room_id = r.id
                WHERE b.user_id = :user_id
                ORDER BY b.booking_date DESC, b.start_time DESC";
        
        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        
        return $this->db->resultSet();
    }
    
    /**
     * Get booking with details (with room and user info)
     * 
     * @param int $id Booking ID
     * @return array|null Booking with details
     */
    public function getBookingWithDetails($id) {
        $sql = "SELECT b.*, 
                r.name as room_name, r.capacity, r.facilities, r.price_per_hour,
                u.full_name as user_name, u.email as user_email, u.phone as user_phone
                FROM " . self::$table . " b
                LEFT JOIN rooms r ON b.room_id = r.id
                LEFT JOIN users u ON b.user_id = u.id
                WHERE b.id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }
    
    /**
     * Get all bookings with room and user details
     * 
     * @return array All bookings with details
     */
    public function getAllWithDetails() {
        $sql = "SELECT b.*, 
                r.name as room_name, 
                u.full_name as user_name
                FROM " . self::$table . " b
                LEFT JOIN rooms r ON b.room_id = r.id
                LEFT JOIN users u ON b.user_id = u.id
                ORDER BY b.booking_date DESC, b.start_time DESC";
        
        $this->db->query($sql);
        
        return $this->db->resultSet();
    }
    
    /**
     * Get bookings by room
     * 
     * @param int $roomId Room ID
     * @param string $date Date in Y-m-d format (optional)
     * @return array Room bookings
     */
    public function getBookingsByRoom($roomId, $date = null) {
        if ($date) {
            $sql = "SELECT b.*, u.full_name as user_name
                    FROM " . self::$table . " b
                    LEFT JOIN users u ON b.user_id = u.id
                    WHERE b.room_id = :room_id 
                    AND b.booking_date = :date
                    AND b.status != 'cancelled'
                    ORDER BY b.start_time";
            
            $this->db->query($sql);
            $this->db->bind(':room_id', $roomId);
            $this->db->bind(':date', $date);
        } else {
            $sql = "SELECT b.*, u.full_name as user_name
                    FROM " . self::$table . " b
                    LEFT JOIN users u ON b.user_id = u.id
                    WHERE b.room_id = :room_id
                    AND b.status != 'cancelled'
                    ORDER BY b.booking_date DESC, b.start_time";
            
            $this->db->query($sql);
            $this->db->bind(':room_id', $roomId);
        }
        
        return $this->db->resultSet();
    }
    
    /**
     * Generate unique booking number
     * 
     * @return string Booking number (format: BK-YYYYMMDD-XXXX)
     */
    public function generateBookingNumber() {
        $date = date('Ymd');
        $prefix = 'BK-' . $date . '-';
        
        // Get last booking number for today
        $sql = "SELECT booking_number FROM " . self::$table . " 
                WHERE booking_number LIKE :prefix 
                ORDER BY id DESC LIMIT 1";
        
        $this->db->query($sql);
        $this->db->bind(':prefix', $prefix . '%');
        $result = $this->db->single();
        
        if ($result) {
            // Extract number and increment
            $lastNumber = intval(substr($result['booking_number'], -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get statistics for bookings
     * 
     * @return array Statistics
     */
    public function getStatistics() {
        // Total bookings
        $sql = "SELECT COUNT(*) as total_bookings,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_bookings,
                SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_bookings,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_bookings,
                SUM(total_price) as total_revenue
                FROM " . self::$table;
        
        $this->db->query($sql);
        return $this->db->single();
    }
    
    /**
     * Get upcoming bookings
     * 
     * @param int $limit Limit results
     * @return array Upcoming bookings
     */
    public function getUpcomingBookings($limit = 10) {
        $sql = "SELECT b.*, r.name as room_name, u.full_name as user_name
                FROM " . self::$table . " b
                LEFT JOIN rooms r ON b.room_id = r.id
                LEFT JOIN users u ON b.user_id = u.id
                WHERE b.booking_date >= CURDATE()
                AND b.status IN ('pending', 'confirmed')
                ORDER BY b.booking_date, b.start_time
                LIMIT :limit";
        
        $this->db->query($sql);
        $this->db->bind(':limit', $limit);
        
        return $this->db->resultSet();
    }
    
    /**
     * Check if time slot is available
     * 
     * @param int $roomId Room ID
     * @param string $date Booking date
     * @param string $startTime Start time
     * @param string $endTime End time
     * @param int $excludeBookingId Exclude this booking ID (for edit)
     * @return bool True if available
     */
    public function isTimeSlotAvailable($roomId, $date, $startTime, $endTime, $excludeBookingId = null) {
        $sql = "SELECT COUNT(*) as count FROM " . self::$table . "
                WHERE room_id = :room_id 
                AND booking_date = :date 
                AND status != 'cancelled'";
        
        if ($excludeBookingId) {
            $sql .= " AND id != :exclude_id";
        }
        
        $sql .= " AND (
                    (:start_time >= start_time AND :start_time < end_time)
                    OR (:end_time > start_time AND :end_time <= end_time)
                    OR (:start_time <= start_time AND :end_time >= end_time)
                )";
        
        $this->db->query($sql);
        $this->db->bind(':room_id', $roomId);
        $this->db->bind(':date', $date);
        $this->db->bind(':start_time', $startTime);
        $this->db->bind(':end_time', $endTime);
        
        if ($excludeBookingId) {
            $this->db->bind(':exclude_id', $excludeBookingId);
        }
        
        $result = $this->db->single();
        
        return $result['count'] == 0;
    }
}
