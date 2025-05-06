<?php
include '../includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect(BASE_URL . 'login.php');
}

// Check if hotel ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect(BASE_URL . 'hotels.php');
}

// Get hotel ID
$hotel_id = sanitize($_GET['id']);

// Get hotel details
$query = "SELECT * FROM hotels WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo '<div class="alert alert-danger">Hotel not found</div>';
    include '../includes/footer.php';
    exit;
}

$hotel = $result->fetch_assoc();

// Process booking form
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $check_in = sanitize($_POST['check_in']);
    $check_out = sanitize($_POST['check_out']);
    $total_price = sanitize($_POST['total_price']);
    
    // Validate input
    if (empty($check_in)) {
        $errors[] = "Check-in date is required";
    }
    if (empty($check_out)) {
        $errors[] = "Check-out date is required";
    }
    if ($check_in >= $check_out) {
        $errors[] = "Check-out date must be after check-in date";
    }
    
    // Check if hotel is available for the selected dates
    $query = "SELECT id FROM bookings 
              WHERE hotel_id = ? 
              AND ((check_in <= ? AND check_out > ?) OR (check_in < ? AND check_out >= ?))
              AND status != 'cancelled'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issss", $hotel_id, $check_out, $check_in, $check_out, $check_in);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $errors[] = "Hotel is not available for the selected dates";
    }
    
    // If no errors, proceed with booking
    if (empty($errors)) {
        $user_id = $_SESSION['user_id'];
        $status = 'confirmed'; // Set status directly to confirmed for simplicity
        
        $query = "INSERT INTO bookings (user_id, hotel_id, check_in, check_out, total_price, status) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iissds", $user_id, $hotel_id, $check_in, $check_out, $total_price, $status);
        
        if ($stmt->execute()) {
            $success = true;
        } else {
            $errors[] = "Booking failed. Please try again.";
        }
    }
}
?>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Book Hotel</h4>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <p class="mb-0">Your booking has been confirmed! View your <a href="bookings.php">bookings</a>.</p>
                    </div>
                <?php else: ?>
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <img src="<?php echo $hotel['image_url']; ?>" class="img-fluid rounded" alt="<?php echo $hotel['name']; ?>">
                        </div>
                        <div class="col-md-8">
                            <h4><?php echo $hotel['name']; ?></h4>
                            <p><i class="fas fa-map-marker-alt text-primary"></i> <?php echo $hotel['location']; ?></p>
                            <div class="rating mb-2">
                                <?php
                                $rating = round($hotel['rating']);
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $rating) {
                                        echo '<i class="fas fa-star"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                                ?>
                            </div>
                            <h5 class="text-primary">$<?php echo $hotel['price_per_night']; ?> <small class="text-muted">per night</small></h5>
                        </div>
                    </div>
                    
                    <form id="booking-form" action="" method="POST">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="check_in" class="form-label">Check-in Date</label>
                                <input type="date" class="form-control" id="check_in" name="check_in" required>
                            </div>
                            <div class="col-md-6">
                                <label for="check_out" class="form-label">Check-out Date</label>
                                <input type="date" class="form-control" id="check_out" name="check_out" required>
                            </div>
                        </div>
                        
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Booking Summary</h5>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Price per night:</span>
                                    <span>$<?php echo $hotel['price_per_night']; ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Number of nights:</span>
                                    <span id="nights">0</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Total Price:</span>
                                    <span>$<span id="total_price">0.00</span></span>
                                </div>
                                <input type="hidden" id="price_per_night" value="<?php echo $hotel['price_per_night']; ?>">
                                <input type="hidden" id="total_price_input" name="total_price" value="0">
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Confirm Booking</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Booking Information</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex">
                        <i class="fas fa-info-circle text-primary me-3 mt-1"></i>
                        <div>
                            <strong>Cancellation Policy</strong>
                            <p class="mb-0">Free cancellation up to 24 hours before check-in.</p>
                        </div>
                    </li>
                    <li class="list-group-item d-flex">
                        <i class="fas fa-clock text-primary me-3 mt-1"></i>
                        <div>
                            <strong>Check-in/Check-out</strong>
                            <p class="mb-0">Check-in: 2:00 PM<br>Check-out: 12:00 PM</p>
                        </div>
                    </li>
                    <li class="list-group-item d-flex">
                        <i class="fas fa-credit-card text-primary me-3 mt-1"></i>
                        <div>
                            <strong>Payment</strong>
                            <p class="mb-0">Payment will be processed upon confirmation.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>