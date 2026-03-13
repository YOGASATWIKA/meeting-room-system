<?php
/**
 * Application Configuration File
 * Mendefinisikan konstanta-konstanta yang dibutuhkan aplikasi
 * Nilai diambil dari file .env menggunakan vlucas/phpdotenv
 */

// Base URL Configuration
define('BASEURL', $_ENV['APP_URL'] ?? 'http://localhost/meeting-room-system/public');

// Upload Directory Configuration
$uploadPath = !empty($_ENV['UPLOAD_PATH'])
    ? $_ENV['UPLOAD_PATH']
    : $_SERVER['DOCUMENT_ROOT'] . '/meeting-room-system/public/uploads/';
define('UPLOADPATH', $uploadPath);

// Database Configuration
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? '');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? '');
define('DB_PORT', $_ENV['DB_PORT'] ?? '3306');

// Application Configuration
define('APP_NAME', $_ENV['APP_NAME'] ?? 'Room Management System');
define('APP_VERSION', $_ENV['APP_VERSION'] ?? '1.0.0');

// Session Configuration
define('SESSION_TIMEOUT', (int)($_ENV['SESSION_TIMEOUT'] ?? 3600)); // 1 hour in seconds
