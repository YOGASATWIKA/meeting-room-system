<?php
/**
 * Application Configuration File
 * Mendefinisikan konstanta-konstanta yang dibutuhkan aplikasi
 */

// Base URL Configuration
define('BASEURL', 'http://localhost/meeting-room-system/public');

// Upload Directory Configuration
define('UPLOADPATH', $_SERVER['DOCUMENT_ROOT'] . '/meeting-room-system/public/uploads/');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'room_catering_db');

// Application Configuration
define('APP_NAME', 'Room & Catering Management System');
define('APP_VERSION', '1.0.0');

// Session Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
