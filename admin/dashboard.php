<?php include 'includes/header.php'; ?>

<h1 class="mb-4">Dashboard</h1>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card admin-dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <?php
                        $query = "SELECT COUNT(*) as total FROM users";
                        $result = $conn->query($query);
                        $total_users = $result->fetch_assoc()['total'];
                        ?>
                        <h6 class="text-muted">Total Users</h6>
                        <h2 class="mb-0"><?php echo $total_users; ?></h2>
                    </div>
                    <div class="admin-dashboard-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white py-2">
                <a href="users.php" class="text-primary text-decoration-none">View Details <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card admin-dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <?php
                        $query = "SELECT COUNT(*) as total FROM hotels";
                        $result = $conn->query($query);
                        $total_hotels = $result->fetch_assoc()['total'];
                        ?>
                        <h6 class="text-muted">Total Hotels</h6>
                        <h2 class="mb-0"><?php echo $total_hotels; ?></h2>
                    </div>
                    <div class="admin-dashboard-icon">
                        <i class="fas fa-hotel"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white py-2">
                <a href="hotels.php" class="text-primary text-decoration-none">View Details <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card admin-dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <?php
                        $query = "SELECT COUNT(*) as total FROM bookings";
                        $result = $conn->query($query);
                        $total_bookings = $result->fetch_assoc()['total'];
                        ?>
                        <h6 class="text-muted">Total Bookings</h6>
                        <h2 class="mb-0"><?php echo $total_bookings; ?></h2>
                    </div>
                    <div class="admin-dashboard-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white py-2">
                <a href="bookings.php" class="text-primary text-decoration-none">View Details <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card admin-dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <?php
                        $query = "SELECT SUM(total_price) as total FROM bookings WHERE status = 'confirmed'";
                        $result = $conn->query($query);
                        $total_revenue = $result->fetch_assoc()['total'] ?? 0;
                        ?>
                        <h6 class="text-muted">Total Revenue</h6>
                        <h2 class="mb-0">$<?php echo number_format($total_revenue, 2); ?></h2>
                    </div>
                    <div class="admin-dashboard-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white py-2">
                <a href="bookings.php" class="text-primary text-decoration-none">View Details <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Recent Bookings</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Hotel</th>
                                <th>User</th>
                                <th>Check-in</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT b.id, h.name as hotel_name, u.name as user_name, b.check_in, b.status
                                      FROM bookings b
                                      JOIN hotels h ON b.hotel_id = h.id
                                      JOIN users u ON b.user_id = u.id
                                      ORDER BY b.created_at DESC
                                      LIMIT 5";
                            $result = $conn->query($query);
                            
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
                                        <td><?php echo $booking['user_name']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($booking['check_in'])); ?></td>
                                        <td><span class="badge bg-<?php echo $status_class; ?>"><?php echo ucfirst($booking['status']); ?></span></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="5" class="text-center">No bookings found</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-2">
                <a href="bookings.php" class="text-primary text-decoration-none">View All Bookings <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Top Hotels</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Hotel</th>
                                <th>Location</th>
                                <th>Rating</th>
                                <th>Bookings</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT h.id, h.name, h.location, h.rating, COUNT(b.id) as booking_count
                                      FROM hotels h
                                      LEFT JOIN bookings b ON h.id = b.hotel_id
                                      GROUP BY h.id
                                      ORDER BY booking_count DESC
                                      LIMIT 5";
                            $result = $conn->query($query);
                            
                            if ($result->num_rows > 0) {
                                while ($hotel = $result->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?php echo $hotel['id']; ?></td>
                                        <td><?php echo $hotel['name']; ?></td>
                                        <td><?php echo $hotel['location']; ?></td>
                                        <td>
                                            <?php
                                            $rating = round($hotel['rating']);
                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($i <= $rating) {
                                                    echo '<i class="fas fa-star text-warning"></i>';
                                                } else {
                                                    echo '<i class="far fa-star text-warning"></i>';
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo $hotel['booking_count']; ?></td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="text-center">No hotels found</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white py-2">
                    <a href="hotels.php" class="text-primary text-decoration-none">View All Hotels <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>