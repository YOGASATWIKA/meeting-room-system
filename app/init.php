<?php
// Load composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Load configuration
require_once 'config/config.php';

// Load core classes
require_once 'core/App.php';
require_once 'core/Controller.php';
require_once 'core/Database.php';
require_once 'core/Model.php';

// Load interfaces
require_once 'interfaces/CrudInterface.php';
require_once 'interfaces/Searchable.php';

// Load models
require_once 'models/BaseModel.php';
require_once 'models/User.php';
require_once 'models/Product.php';
require_once 'models/Room.php';
require_once 'models/Order.php';
require_once 'models/Booking.php';

// Load services
require_once 'services/AuthService.php';
require_once 'services/ValidationService.php';
