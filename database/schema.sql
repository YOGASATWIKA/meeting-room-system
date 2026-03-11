-- Room & Catering Management System Database Schema
-- Database: room_catering_db

CREATE DATABASE IF NOT EXISTS room_catering_db;
USE room_catering_db;

-- Table: users
-- Menyimpan data pengguna sistem
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'user', 'staff') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: products
-- Menyimpan data produk catering
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: rooms
-- Menyimpan data ruangan meeting
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    capacity INT NOT NULL,
    facilities TEXT,
    price_per_hour DECIMAL(10, 2) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    status ENUM('active', 'inactive', 'maintenance') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_capacity (capacity),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: orders
-- Menyimpan data pesanan catering
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    order_date DATETIME NOT NULL,
    delivery_date DATE NOT NULL,
    delivery_address TEXT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'processing', 'delivered', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_order_number (order_number),
    INDEX idx_status (status),
    INDEX idx_order_date (order_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: order_items
-- Detail item pesanan (relasi many-to-many antara orders dan products)
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    INDEX idx_order_id (order_id),
    INDEX idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: bookings
-- Menyimpan data booking ruangan
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    booking_number VARCHAR(50) UNIQUE NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    purpose TEXT,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE RESTRICT,
    INDEX idx_user_id (user_id),
    INDEX idx_room_id (room_id),
    INDEX idx_booking_date (booking_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample admin user
-- Password default: admin123
INSERT INTO users (username, email, password, full_name, role) VALUES
('admin', 'admin@roomcatering.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin');

-- Insert sample products
INSERT INTO products (name, description, price, category, stock, status) VALUES
('Nasi Box Premium', 'Nasi box dengan lauk lengkap dan minuman', 25000, 'Catering Package', 100, 'active'),
('Snack Box Meeting', 'Paket snack untuk meeting (roti, kue, minuman)', 15000, 'Snack Box', 100, 'active'),
('Coffee Break Package', 'Kopi, teh, snack ringan untuk coffee break', 10000, 'Beverage', 100, 'active'),
('Lunch Prasmanan', 'Paket prasmanan untuk 50 orang', 1250000, 'Catering Package', 20, 'active'),
('Tumpeng Mini', 'Tumpeng kecil untuk acara syukuran', 150000, 'Special Order', 50, 'active');

-- Insert sample rooms
INSERT INTO rooms (name, capacity, facilities, price_per_hour, description, status) VALUES
('Meeting Room A', 10, 'Projector, Whiteboard, AC, WiFi', 100000, 'Ruang meeting kecil untuk 10 orang', 'active'),
('Meeting Room B', 20, 'Projector, Whiteboard, AC, WiFi, Sound System', 200000, 'Ruang meeting menengah untuk 20 orang', 'active'),
('Conference Hall', 50, 'Projector, Whiteboard, AC, WiFi, Sound System, Stage', 500000, 'Ruang konferensi besar untuk 50 orang', 'active'),
('Training Room', 30, 'Projector, Whiteboard, AC, WiFi, Tables & Chairs', 300000, 'Ruang training untuk 30 orang', 'active');
