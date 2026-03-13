<?php
namespace App\Models;

/**
 * Room Model
 * Model untuk mengelola data ruangan meeting
 * Menerapkan: Inheritance
 */
class Room extends BaseModel {
    protected static $table = 'rooms';
    protected static $primaryKey = 'id';
    
    /**
     * Fillable fields
     * @var array
     */
    protected $fillable = ['name', 'capacity', 'facilities', 'price_per_hour', 'description', 'image', 'status'];
    
    /**
     * Validation rules
     * @var array
     */
    protected $rules = [
        'name' => 'required|min:3|max:100',
        'capacity' => 'required',
        'price_per_hour' => 'required'
    ];
    
    /**
     * Get available rooms
     * Room yang sedang tidak dibooking
     * 
     * @param string $date Date to check (YYYY-MM-DD)
     * @param string $startTime Start time (HH:MM)
     * @param string $endTime End time (HH:MM)
     * @return array Available rooms
     */
    public function getAvailableRooms($date, $startTime, $endTime) {
        $sql = "SELECT r.* FROM " . self::$table . " r
                WHERE r.status = 'active'
                AND r.id NOT IN (
                    SELECT room_id FROM bookings 
                    WHERE booking_date = :date 
                    AND status != 'cancelled'
                    AND (
                        (:start_time >= start_time AND :start_time < end_time)
                        OR (:end_time > start_time AND :end_time <= end_time)
                        OR (:start_time <= start_time AND :end_time >= end_time)
                    )
                )";
        
        $this->db->query($sql);
        $this->db->bind(':date', $date);
        $this->db->bind(':start_time', $startTime);
        $this->db->bind(':end_time', $endTime);
        
        return $this->db->resultSet();
    }
    
    /**
     * Get rooms by capacity
     * Filter room berdasarkan kapasitas minimum
     * 
     * @param int $minCapacity Minimum capacity required
     * @return array Rooms matching capacity
     */
    public function getByCapacity($minCapacity) {
        $this->db->query("SELECT * FROM " . self::$table . " WHERE capacity >= :capacity AND status = 'active' ORDER BY capacity");
        $this->db->bind(':capacity', $minCapacity);
        return $this->db->resultSet();
    }
    
    /**
     * Check room availability
     * Method untuk cek apakah room available di waktu tertentu
     * 
     * @param int $roomId Room ID
     * @param string $date Booking date
     * @param string $startTime Start time
     * @param string $endTime End time
     * @return bool True if available, false otherwise
     */
    public function isAvailable($roomId, $date, $startTime, $endTime) {
        $sql = "SELECT COUNT(*) as count FROM bookings 
                WHERE room_id = :room_id 
                AND booking_date = :date 
                AND status != 'cancelled'
                AND (
                    (:start_time >= start_time AND :start_time < end_time)
                    OR (:end_time > start_time AND :end_time <= end_time)
                    OR (:start_time <= start_time AND :end_time >= end_time)
                )";
        
        $this->db->query($sql);
        $this->db->bind(':room_id', $roomId);
        $this->db->bind(':date', $date);
        $this->db->bind(':start_time', $startTime);
        $this->db->bind(':end_time', $endTime);
        
        $result = $this->db->single();
        return $result['count'] == 0;
    }
    
    /**
     * Check if room is available (excluding a specific booking)
     * Used when updating a booking
     * 
     * @param int $roomId Room ID
     * @param string $date Booking date
     * @param string $startTime Start time
     * @param string $endTime End time
     * @param int $excludeBookingId Booking ID to exclude from check
     * @return bool True if available
     */
    public function isAvailableExcept($roomId, $date, $startTime, $endTime, $excludeBookingId) {
        $sql = "SELECT COUNT(*) as count FROM bookings 
                WHERE room_id = :room_id 
                AND booking_date = :date 
                AND status != 'cancelled'
                AND id != :exclude_id
                AND (
                    (:start_time >= start_time AND :start_time < end_time)
                    OR (:end_time > start_time AND :end_time <= end_time)
                    OR (:start_time <= start_time AND :end_time >= end_time)
                )";
        
        $this->db->query($sql);
        $this->db->bind(':room_id', $roomId);
        $this->db->bind(':date', $date);
        $this->db->bind(':start_time', $startTime);
        $this->db->bind(':end_time', $endTime);
        $this->db->bind(':exclude_id', $excludeBookingId);
        
        $result = $this->db->single();
        return $result['count'] == 0;
    }
    
    /**
     * Get room booking history
     * 
     * @param int $roomId Room ID
     * @param int $limit Number of records
     * @return array Booking history
     */
    public function getBookingHistory($roomId, $limit = 10) {
        $sql = "SELECT b.*, u.full_name, u.email 
                FROM bookings b
                JOIN users u ON b.user_id = u.id
                WHERE b.room_id = :room_id
                ORDER BY b.booking_date DESC, b.start_time DESC
                LIMIT :limit";
        
        $this->db->query($sql);
        $this->db->bind(':room_id', $roomId);
        $this->db->bind(':limit', $limit, \PDO::PARAM_INT);
        
        return $this->db->resultSet();
    }
}
