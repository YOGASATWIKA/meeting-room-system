<?php
/**
 * Application Configuration File
 * Mendefinisikan konstanta-konstanta yang dibutuhkan aplikasi.
 * Mendukung pembacaan dari System Environment (Railway) dan file .env.
 */

// Fungsi Helper untuk mengambil env dengan fallback ke getenv()
function get_env($key, $default = null) {
    return $_ENV[$key] ?? getenv($key) ?? $default;
}

// --- LOGIKA DATABASE ---
$dbUrl = get_env('MYSQL_URL'); // Mengambil dari Railway Reference

if ($dbUrl) {
    // Jika ada MYSQL_URL (Format: mysql://user:pass@host:port/db)
    $parsedUrl = parse_url($dbUrl);
    define('DB_HOST', $parsedUrl['host']);
    define('DB_USER', $parsedUrl['user']);
    define('DB_PASS', $parsedUrl['pass'] ?? '');
    define('DB_NAME', ltrim($parsedUrl['path'], '/'));
    define('DB_PORT', $parsedUrl['port'] ?? '3306');
} else {
    // Fallback untuk lokal (menggunakan variabel individu atau default)
    define('DB_HOST', get_env('DB_HOST', 'localhost'));
    define('DB_USER', get_env('DB_USER', 'root'));
    define('DB_PASS', get_env('DB_PASS', ''));
    define('DB_NAME', get_env('DB_NAME', ''));
    define('DB_PORT', get_env('DB_PORT', '3306'));
}

// --- KONFIGURASI LAINNYA ---

// Base URL Configuration
define('BASEURL', get_env('APP_URL', 'http://localhost/meeting-room-system/public'));

// Upload Directory Configuration
$defaultUpload = $_SERVER['DOCUMENT_ROOT'] . '/meeting-room-system/public/uploads/';
define('UPLOADPATH', get_env('UPLOAD_PATH', $defaultUpload));

// Application Configuration
define('APP_NAME', get_env('APP_NAME', 'Room Management System'));
define('APP_VERSION', get_env('APP_VERSION', '1.0.0'));

// Session Configuration
define('SESSION_TIMEOUT', (int)get_env('SESSION_TIMEOUT', 3600));