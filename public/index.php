<?php
/**
 * Public Index - Entry Point
 * Front Controller Pattern
 */

// Start session
session_start();

// Load app initialization
require_once __DIR__ . '/../app/init.php';

// Create and run app
use App\Core\App;
$app = new App();
