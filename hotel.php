<?php
include 'includes/header.php';

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
    include 'includes/footer.php';
    exit;
}

$hotel = $result->fetch_assoc();

// Check if hotel is in user's favorites
$is_favorite = false;
if (isLoggedIn()) {
    $query = "SELECT id FROM favorites WHERE user_id = ? AND hotel_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $_SESSION['user_id'], $hotel_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $is_favorite = ($result->num_rows > 0);
}
?>

<div class="card mb-4">
    <div class="row g-0">
        <div class="col-md-6">
            <img src="<?php echo $hotel['image_url']; ?>" class="img-fluid hotel-detail-img rounded-start" alt="<?php echo $hotel['name']; ?>">
        </div>
        <div class="col-md-6">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <h1 class="card-title"><?php echo $hotel['name']; ?></h1>
                    <?php if (isLoggedIn()): ?>
                        <form action="users/toggle_favorite.php" method="POST">
                            <input type="hidden" name="hotel_id" value="<?php echo $hotel['id']; ?>">
                            <button type="submit" class="btn btn-link p-0">
                                <?php if ($is_favorite): ?>
                                    <i class="fas fa-heart fa-2x text-danger"></i>
                                <?php else: ?>
                                    <i class="far fa-heart fa-2x text-danger"></i>
                                <?php endif; ?>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
                
                <p class="card-text"><i class="fas fa-map-marker-alt text-primary"></i> <?php echo $hotel['location']; ?></p>
                
                <div class="rating mb-3">
                    <?php
                    $rating = round($hotel['rating']);
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $rating) {
                            echo '<i class="fas fa-star"></i>';
                        } else {
                            echo '<i class="far fa-star"></i>';
                        }
                    }
                    echo " ({$hotel['rating']})";
                    ?>
                </div>
                
                <h4 class="text-primary">$<?php echo $hotel['price_per_night']; ?> <small class="text-muted">per night</small></h4>
                
                <p class="card-text"><?php echo $hotel['description']; ?></p>
                
                <?php if (isLoggedIn()): ?>
                    <a href="users/book.php?id=<?php echo $hotel['id']; ?>" class="btn btn-primary btn-lg mt-3">Book Now</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary btn-lg mt-3">Login to Book</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<h2 class="mb-4">Hotel Features</h2>
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-wifi fa-3x text-primary mb-3"></i>
                <h5 class="card-title">Free WiFi</h5>
                <p class="card-text">Stay connected with high-speed internet access.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-swimming-pool fa-3x text-primary mb-3"></i>
                <h5 class="card-title">Swimming Pool</h5>
                <p class="card-text">Relax and enjoy our swimming facilities.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-utensils fa-3x text-primary mb-3"></i>
                <h5 class="card-title">Restaurant</h5>
                <p class="card-text">Enjoy delicious meals at our on-site restaurant.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>