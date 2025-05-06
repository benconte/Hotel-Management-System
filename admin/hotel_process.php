<?php
require_once '../config/config.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect(ADMIN_URL);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = sanitize($_POST['action']);
    
    // Process based on action
    if ($action == 'add') {
        // Add new hotel
        $name = sanitize($_POST['name']);
        $description = sanitize($_POST['description']);
        $location = sanitize($_POST['location']);
        $price_per_night = sanitize($_POST['price_per_night']);
        $rating = sanitize($_POST['rating']);
        $image_url = sanitize($_POST['image_url']);
        
        // Validate input
        $errors = [];
        
        if (empty($name)) {
            $errors[] = "Name is required";
        }
        if (empty($description)) {
            $errors[] = "Description is required";
        }
        if (empty($location)) {
            $errors[] = "Location is required";
        }
        if (empty($price_per_night) || !is_numeric($price_per_night) || $price_per_night < 0) {
            $errors[] = "Valid price is required";
        }
        if (empty($rating) || !is_numeric($rating) || $rating < 0 || $rating > 5) {
            $errors[] = "Rating must be between 0 and 5";
        }
        if (empty($image_url)) {
            $errors[] = "Image URL is required";
        }
        
        // If no errors, proceed with adding hotel
        if (empty($errors)) {
            // Insert hotel into database
            $query = "INSERT INTO hotels (name, description, location, price_per_night, rating, image_url) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssdds", $name, $description, $location, $price_per_night, $rating, $image_url);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Hotel added successfully";
            } else {
                $_SESSION['error_message'] = "Failed to add hotel";
            }
        } else {
            $_SESSION['error_message'] = implode("<br>", $errors);
        }
    } elseif ($action == 'edit') {
        // Edit existing hotel
        $hotel_id = sanitize($_POST['hotel_id']);
        $name = sanitize($_POST['name']);
        $description = sanitize($_POST['description']);
        $location = sanitize($_POST['location']);
        $price_per_night = sanitize($_POST['price_per_night']);
        $rating = sanitize($_POST['rating']);
        $image_url = sanitize($_POST['image_url']);
        
        // Validate input
        $errors = [];
        
        if (empty($name)) {
            $errors[] = "Name is required";
        }
        if (empty($description)) {
            $errors[] = "Description is required";
        }
        if (empty($location)) {
            $errors[] = "Location is required";
        }
        if (empty($price_per_night) || !is_numeric($price_per_night) || $price_per_night < 0) {
            $errors[] = "Valid price is required";
        }
        if (empty($rating) || !is_numeric($rating) || $rating < 0 || $rating > 5) {
            $errors[] = "Rating must be between 0 and 5";
        }
        if (empty($image_url)) {
            $errors[] = "Image URL is required";
        }
        
        // If no errors, proceed with updating hotel
        if (empty($errors)) {
            // Update hotel details
            $query = "UPDATE hotels SET name = ?, description = ?, location = ?, 
                      price_per_night = ?, rating = ?, image_url = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssddsi", $name, $description, $location, $price_per_night, $rating, $image_url, $hotel_id);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Hotel updated successfully";
            } else {
                $_SESSION['error_message'] = "Failed to update hotel";
            }
        } else {
            $_SESSION['error_message'] = implode("<br>", $errors);
        }
    }
    
    // Redirect back to hotels page
    redirect(ADMIN_URL . 'hotels.php');
} else {
    // Redirect if accessed directly
    redirect(ADMIN_URL . 'hotels.php');
}
?>