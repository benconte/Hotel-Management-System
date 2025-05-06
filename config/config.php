<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Site URL
define('BASE_URL', 'http://localhost/hotel-management/');
define('ADMIN_URL', BASE_URL . 'admin/');
define('USER_URL', BASE_URL . 'users/');
define('ASSETS_URL', BASE_URL . 'assets/');

// Include database connection
require_once __DIR__ . '/db_connect.php';

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']); // Check if user ID is set in session, return true if logged in, false otherwise
}

// Function to check if user is admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'; // Check if user role is set in session and if it is 'admin', return true if admin, false otherwise
}

// Redirect function
function redirect($url) {
    // Check if headers have already been sent
    if (headers_sent()) {
        // If headers already sent, use JavaScript redirection
        echo "<script>window.location.href='$url';</script>";
        // Provide a fallback for browsers with JavaScript disabled
        echo "<noscript><meta http-equiv='refresh' content='0;url=$url'></noscript>";
        exit;
    } else {
        // If headers not sent, use standard header redirection
        header("Location: $url");
        exit;
    }
}

// Function to sanitize input
function sanitize($input) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($input));
}
?>