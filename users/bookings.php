<?php
include '../includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect(BASE_URL . 'login.php');
}

// Get user bookings
$query = "SELECT b.*, h.name as hotel_name, h.location, h.image_url 
          FROM bookings b
          JOIN hotels h ON b.hotel_id = h.id
          WHERE b.user_id = ?
          ORDER BY b.check_in DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<h1 class="mb-4">My Bookings</h1>

<div class="row">
    <?php
    if ($result->num_rows > 0) {
        while ($booking = $result->fetch_assoc()) {
            $status_class = '';
            switch ($booking['status']) {
                case 'confirmed':
                    $status_class = 'success';
                    break;
                case 'pending':
                    $status_class = 'warning';
                    break;
                case 'cancelled':
                    $status_class = 'danger';
                    break;
            }
            ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="<?php echo $booking['image_url']; ?>" class="img-fluid rounded-start h-100 hotel-image" alt="<?php echo $booking['hotel_name']; ?>" style="object-fit: cover;">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $booking['hotel_name']; ?></h5>
                                <p class="card-text"><i class="fas fa-map-marker-alt text-primary"></i> <?php echo $booking['location']; ?></p>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span><i class="fas fa-calendar-check text-primary"></i> Check-in:</span>
                                    <span><?php echo date('M d, Y', strtotime($booking['check_in'])); ?></span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span><i class="fas fa-calendar-times text-primary"></i> Check-out:</span>
                                    <span><?php echo date('M d, Y', strtotime($booking['check_out'])); ?></span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-3">
                                    <span><i class="fas fa-money-bill-wave text-primary"></i> Total:</span>
                                    <span class="fw-bold">$<?php echo $booking['total_price']; ?></span>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-<?php echo $status_class; ?>"><?php echo ucfirst($booking['status']); ?></span>
                                    
                                    <?php if ($booking['status'] !== 'cancelled' && strtotime($booking['check_in']) > time()): ?>
                                        <form action="cancel_booking.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo '<div class="col-12"><div class="alert alert-info">You have no bookings yet. Browse our <a href="' . BASE_URL . 'hotels.php">hotels</a> to book one!</div></div>';
    }
    ?>
</div>

<?php include '../includes/footer.php'; ?>