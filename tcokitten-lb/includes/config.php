<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'cat_store');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site configuration
define('SITE_NAME', 'Take Care of Kitten Lebanon');
define('SITE_URL', 'http://localhost/take-care-of-kitten');
define('ADMIN_EMAIL', 'info@takecareofkitten.lb');
define('PHONE_NUMBER', '+961 1 234 567');

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Set default timezone
date_default_timezone_set('Asia/Beirut');

// Database connection function
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Sanitize input function
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

// Get current page name
function getCurrentPage() {
    $current_page = basename($_SERVER['PHP_SELF'], '.php');
    return $current_page;
}
?>