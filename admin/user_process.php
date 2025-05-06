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
        // Add new user
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];
        $role = sanitize($_POST['role']);
        
        // Validate input
        $errors = [];
        
        if (empty($name)) {
            $errors[] = "Name is required";
        }
        if (empty($email)) {
            $errors[] = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }
        if (empty($password)) {
            $errors[] = "Password is required";
        } elseif (strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters";
        }
        if ($role != 'user' && $role != 'admin') {
            $errors[] = "Invalid role";
        }
        
        // Check if email already exists
        $query = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "Email is already registered";
        }
        
        // If no errors, proceed with adding user
        if (empty($errors)) {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user into database
            $query = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "User added successfully";
            } else {
                $_SESSION['error_message'] = "Failed to add user";
            }
        } else {
            $_SESSION['error_message'] = implode("<br>", $errors);
        }
    } elseif ($action == 'edit') {
        // Edit existing user
        $user_id = sanitize($_POST['user_id']);
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];
        $role = sanitize($_POST['role']);
        
        // Validate input
        $errors = [];
        
        if (empty($name)) {
            $errors[] = "Name is required";
        }
        if (empty($email)) {
            $errors[] = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }
        if (!empty($password) && strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters";
        }
        if ($role != 'user' && $role != 'admin') {
            $errors[] = "Invalid role";
        }
        
        // Check if email already exists (for other users)
        $query = "SELECT id FROM users WHERE email = ? AND id != ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "Email is already registered";
        }
        
        // If no errors, proceed with updating user
        if (empty($errors)) {
            // Update user details
            if (empty($password)) {
                // Without password
                $query = "UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssi", $name, $email, $role, $user_id);
            } else {
                // With password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $query = "UPDATE users SET name = ?, email = ?, password = ?, role = ? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssssi", $name, $email, $hashed_password, $role, $user_id);
            }
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "User updated successfully";
            } else {
                $_SESSION['error_message'] = "Failed to update user";
            }
        } else {
            $_SESSION['error_message'] = implode("<br>", $errors);
        }
    }
    
    // Redirect back to users page
    redirect(ADMIN_URL . 'users.php');
} else {
    // Redirect if accessed directly
    redirect(ADMIN_URL . 'users.php');
}
?>