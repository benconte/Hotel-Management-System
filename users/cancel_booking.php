<?php
require_once '../config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect(BASE_URL . 'login.php');
}

// Check if booking ID is provided
if (!isset($_POST['booking_id']) || empty($_POST['booking_id'])) {
    redirect(USER_URL . 'bookings.php');
}

// Get booking ID
$booking_id = sanitize($_POST['booking_id']);

// Check if booking belongs to user
$query = "SELECT check_in FROM bookings WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error_message'] = "Invalid booking";
    redirect(USER_URL . 'bookings.php');
}

$booking = $result->fetch_assoc();

// Check if booking can be cancelled (more than 24 hours before check-in)
$check_in_time = strtotime($booking['check_in']);
$cancel_deadline = $check_in_time - (24 * 60 * 60); // 24 hours before check-in

if (time() > $cancel_deadline) {
    $_SESSION['error_message'] = "Bookings can only be cancelled up to 24 hours before check-in";
    redirect(USER_URL . 'bookings.php');
}

// Cancel booking
$query = "UPDATE bookings SET status = 'cancelled' WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $booking_id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Booking cancelled successfully";
} else {
    $_SESSION['error_message'] = "Failed to cancel booking";
}

redirect(USER_URL . 'bookings.php');
?>