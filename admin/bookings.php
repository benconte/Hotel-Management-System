<?php include 'includes/header.php'; ?>

<h1 class="mb-4">Bookings Management</h1>

<?php
// Process booking status change
if (isset($_GET['update_status']) && !empty($_GET['update_status']) && isset($_GET['status']) && !empty($_GET['status'])) {
    $booking_id = sanitize($_GET['update_status']);
    $status = sanitize($_GET['status']);
    
    // Validate status
    if ($status == 'confirmed' || $status == 'pending' || $status == 'cancelled') {
        $query = "UPDATE bookings SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $status, $booking_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Booking status updated successfully";
        } else {
            $_SESSION['error_message'] = "Failed to update booking status";
        }
    } else {
        $_SESSION['error_message'] = "Invalid status";
    }
    
    // Redirect to remove the query string
    redirect(ADMIN_URL . 'bookings.php');
}

// Get filter values
$status_filter = isset($_GET['status_filter']) ? sanitize($_GET['status_filter']) : '';
$date_filter = isset($_GET['date_filter']) ? sanitize($_GET['date_filter']) : '';

// Build query based on filters
$query = "SELECT b.*, h.name as hotel_name, u.name as user_name, u.email as user_email
          FROM bookings b
          JOIN hotels h ON b.hotel_id = h.id
          JOIN users u ON b.user_id = u.id
          WHERE 1=1";

$params = [];
$types = "";

if (!empty($status_filter)) {
    $query .= " AND b.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if (!empty($date_filter)) {
    $query .= " AND (b.check_in = ? OR b.check_out = ?)";
    $params[] = $date_filter;
    $params[] = $date_filter;
    $types .= "ss";
}

$query .= " ORDER BY b.created_at DESC";

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Filter Bookings</h5>
    </div>
    <div class="card-body">
        <form action="" method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="status_filter" class="form-label">Status</label>
                <select class="form-select" id="status_filter" name="status_filter">
                    <option value="">All Statuses</option>
                    <option value="confirmed" <?php echo $status_filter == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="cancelled" <?php echo $status_filter == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="date_filter" class="form-label">Check-in/Check-out Date</label>
                <input type="date" class="form-control" id="date_filter" name="date_filter" value="<?php echo $date_filter; ?>">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="d-grid w-100">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hotel</th>
                        <th>User</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
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
                            <tr>
                                <td><?php echo $booking['id']; ?></td>
                                <td><?php echo $booking['hotel_name']; ?></td>
                                <td>
                                    <?php echo $booking['user_name']; ?><br>
                                    <small class="text-muted"><?php echo $booking['user_email']; ?></small>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($booking['check_in'])); ?></td>
                                <td><?php echo date('M d, Y', strtotime($booking['check_out'])); ?></td>
                                <td>$<?php echo $booking['total_price']; ?></td>
                                <td><span class="badge bg-<?php echo $status_class; ?>"><?php echo ucfirst($booking['status']); ?></span></td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewBookingModal" 
                                                data-id="<?php echo $booking['id']; ?>"
                                                data-hotel="<?php echo $booking['hotel_name']; ?>"
                                                data-user="<?php echo $booking['user_name']; ?>"
                                                data-email="<?php echo $booking['user_email']; ?>"
                                                data-checkin="<?php echo date('M d, Y', strtotime($booking['check_in'])); ?>"
                                                data-checkout="<?php echo date('M d, Y', strtotime($booking['check_out'])); ?>"
                                                data-price="<?php echo $booking['total_price']; ?>"
                                                data-status="<?php echo $booking['status']; ?>"
                                                data-created="<?php echo date('M d, Y H:i', strtotime($booking['created_at'])); ?>">
                                                <i class="fas fa-eye"></i> View Details
                                            </a></li>
                                            <?php if ($booking['status'] != 'confirmed'): ?>
                                                <li><a class="dropdown-item" href="bookings.php?update_status=<?php echo $booking['id']; ?>&status=confirmed">
                                                    <i class="fas fa-check text-success"></i> Mark as Confirmed
                                                </a></li>
                                            <?php endif; ?>
                                            <?php if ($booking['status'] != 'pending'): ?>
                                                <li><a class="dropdown-item" href="bookings.php?update_status=<?php echo $booking['id']; ?>&status=pending">
                                                    <i class="fas fa-clock text-warning"></i> Mark as Pending
                                                </a></li>
                                            <?php endif; ?>
                                            <?php if ($booking['status'] != 'cancelled'): ?>
                                                <li><a class="dropdown-item" href="bookings.php?update_status=<?php echo $booking['id']; ?>&status=cancelled">
                                                    <i class="fas fa-times text-danger"></i> Mark as Cancelled
                                                </a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo '<tr><td colspan="8" class="text-center">No bookings found</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- View Booking Modal -->
<div class="modal fade" id="viewBookingModal" tabindex="-1" aria-labelledby="viewBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewBookingModalLabel">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6>Booking ID</h6>
                    <p id="view_booking_id" class="mb-0"></p>
                </div>
                <div class="mb-3">
                    <h6>Hotel</h6>
                    <p id="view_hotel" class="mb-0"></p>
                </div>
                <div class="mb-3">
                    <h6>User</h6>
                    <p id="view_user" class="mb-0"></p>
                    <p id="view_email" class="mb-0 text-muted"></p>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6>Check-in Date</h6>
                        <p id="view_checkin" class="mb-0"></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Check-out Date</h6>
                        <p id="view_checkout" class="mb-0"></p>
                    </div>
                </div>
                <div class="mb-3">
                    <h6>Total Price</h6>
                    <p id="view_price" class="mb-0"></p>
                </div>
                <div class="mb-3">
                    <h6>Status</h6>
                    <p id="view_status" class="mb-0"></p>
                </div>
                <div class="mb-3">
                    <h6>Created At</h6>
                    <p id="view_created" class="mb-0"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set view booking modal values
    const viewLinks = document.querySelectorAll('[data-bs-target="#viewBookingModal"]');
    viewLinks.forEach(link => {
        link.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const hotel = this.getAttribute('data-hotel');
            const user = this.getAttribute('data-user');
            const email = this.getAttribute('data-email');
            const checkin = this.getAttribute('data-checkin');
            const checkout = this.getAttribute('data-checkout');
            const price = this.getAttribute('data-price');
            const status = this.getAttribute('data-status');
            const created = this.getAttribute('data-created');
            
            document.getElementById('view_booking_id').textContent = id;
            document.getElementById('view_hotel').textContent = hotel;
            document.getElementById('view_user').textContent = user;
            document.getElementById('view_email').textContent = email;
            document.getElementById('view_checkin').textContent = checkin;
            document.getElementById('view_checkout').textContent = checkout;
            document.getElementById('view_price').textContent = '$' + price;
            
            let statusHtml = '';
            switch (status) {
                case 'confirmed':
                    statusHtml = '<span class="badge bg-success">Confirmed</span>';
                    break;
                case 'pending':
                    statusHtml = '<span class="badge bg-warning">Pending</span>';
                    break;
                case 'cancelled':
                    statusHtml = '<span class="badge bg-danger">Cancelled</span>';
                    break;
            }
            document.getElementById('view_status').innerHTML = statusHtml;
            document.getElementById('view_created').textContent = created;
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>