<?php
require_once '../config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect(BASE_URL . 'login.php');
}

// Check if hotel ID is provided
if (!isset($_POST['hotel_id']) || empty($_POST['hotel_id'])) {
    redirect(BASE_URL . 'hotels.php');
}

// Get hotel ID and user ID
$hotel_id = sanitize($_POST['hotel_id']);
$user_id = $_SESSION['user_id'];

// Check if hotel exists in favorites
$query = "SELECT id FROM favorites WHERE user_id = ? AND hotel_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $hotel_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Hotel is already in favorites, remove it
    $query = "DELETE FROM favorites WHERE user_id = ? AND hotel_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $hotel_id);
    $stmt->execute();
} else {
    // Hotel is not in favorites, add it
    $query = "INSERT INTO favorites (user_id, hotel_id) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $hotel_id);
    $stmt->execute();
}

// Redirect back to hotel page
redirect(BASE_URL . 'hotel.php?id=' . $hotel_id);
?>