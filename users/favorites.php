<?php
include '../includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect(BASE_URL . 'login.php');
}

// Get user's favorite hotels
$query = "SELECT h.* FROM hotels h
          JOIN favorites f ON h.id = f.hotel_id
          WHERE f.user_id = ?
          ORDER BY f.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<h1 class="mb-4">My Favorite Hotels</h1>

<div class="row">
    <?php
    if ($result->num_rows > 0) {
        while ($hotel = $result->fetch_assoc()) {
            ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="<?php echo $hotel['image_url']; ?>" class="card-img-top hotel-image" alt="<?php echo $hotel['name']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $hotel['name']; ?></h5>
                        <p class="card-text text-muted"><?php echo $hotel['location']; ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="rating">
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
                            <span class="text-primary fw-bold">$<?php echo $hotel['price_per_night']; ?>/night</span>
                        </div>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-between">
                        <a href="<?php echo BASE_URL; ?>hotel.php?id=<?php echo $hotel['id']; ?>" class="btn btn-primary">View Details</a>
                        <form action="toggle_favorite.php" method="POST">
                            <input type="hidden" name="hotel_id" value="<?php echo $hotel['id']; ?>">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-heart"></i> Remove
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo '<div class="col-12"><div class="alert alert-info">You have no favorite hotels yet. Browse our <a href="' . BASE_URL . 'hotels.php">hotels</a> to add some!</div></div>';
    }
    ?>
</div>

<?php include '../includes/footer.php'; ?>