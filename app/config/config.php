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

// Deteksi HTTPS yang kompatibel dengan reverse proxy (Railway, Nginx, dll)
function request_is_https() {
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return true;
    }

    if (!empty($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443) {
        return true;
    }

    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        $proto = strtolower(trim(explode(',', $_SERVER['HTTP_X_FORWARDED_PROTO'])[0]));
        return $proto === 'https';
    }

    return false;
}

// Bangun base URL otomatis dari request aktif jika APP_URL tidak diset
function detect_base_url() {
    $scheme = request_is_https() ? 'https' : 'http';

    $host = $_SERVER['HTTP_X_FORWARDED_HOST']
        ?? $_SERVER['HTTP_HOST']
        ?? 'localhost';
    $host = trim(explode(',', $host)[0]);

    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    $basePath = $scriptDir === '/' ? '' : rtrim($scriptDir, '/');

    return $scheme . '://' . $host . $basePath;
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
$configuredAppUrl = get_env('APP_URL');
$baseUrl = $configuredAppUrl ?: detect_base_url();
define('BASEURL', rtrim($baseUrl, '/'));

// Upload Directory Configuration
$defaultUpload = $_SERVER['DOCUMENT_ROOT'] . '/meeting-room-system/public/uploads/';
define('UPLOADPATH', get_env('UPLOAD_PATH', $defaultUpload));

// Application Configuration
define('APP_NAME', get_env('APP_NAME', 'Room Management System'));
define('APP_VERSION', get_env('APP_VERSION', '1.0.0'));

// Session Configuration
define('SESSION_TIMEOUT', (int)get_env('SESSION_TIMEOUT', 3600));