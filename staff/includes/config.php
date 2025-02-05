<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session configuration
// ini_set('session.cookie_httponly', 1);
// ini_set('session.use_only_cookies', 1);
// ini_set('session.cookie_secure', 1);

// Time zone
date_default_timezone_set('UTC');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'stan-inventory');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application configuration
define('SITE_NAME', 'Stan Inventory');
define('SITE_URL', 'http://localhost/stan-inventory');

// File paths
define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('INCLUDES_PATH', ROOT_PATH . 'includes' . DIRECTORY_SEPARATOR); 