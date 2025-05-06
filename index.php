<?php include 'includes/header.php'; ?>

<div class="jumbotron bg-primary text-white p-5 rounded">
    <h1 class="display-4">Welcome to Hotely. Where your dreams start</h1>
    <p class="lead">Find the perfect hotel for your next vacation or business trip.</p>
    <hr class="my-4">
    <p>Browse our selection of high-quality hotels and book your stay today.</p>
    <a class="btn btn-light btn-lg" href="hotels.php" role="button">View Hotels</a>
</div>

<div class="mt-5">
    <h2 class="text-center mb-4">Featured Hotels</h2>
    <div class="row">
        <?php
        // Get featured hotels (limit to 6)
        $query = "SELECT * FROM hotels ORDER BY rating DESC LIMIT 6";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            while ($hotel = $result->fetch_assoc()) {
                ?>
                <div class="col-md-4">
                    <div class="card">
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
                            <a href="hotel.php?id=<?php echo $hotel['id']; ?>" class="btn btn-primary mt-3 w-100">View Details</a>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<div class="col-12 text-center"><p>No hotels found</p></div>';
        }
        ?>
    </div>
</div>

<div class="mt-5">
    <h2 class="text-center mb-4">Why Choose Us?</h2>
    <div class="row">
        <div class="col-md-4">
            <div class="card h-100 text-center p-4">
                <div class="card-body">
                    <i class="fas fa-hotel fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Best Hotels</h5>
                    <p class="card-text">We partner with only the best hotels to ensure your comfort and satisfaction.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 text-center p-4">
                <div class="card-body">
                    <i class="fas fa-credit-card fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Easy Booking</h5>
                    <p class="card-text">Our simple booking process makes it easy to reserve your room in minutes.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 text-center p-4">
                <div class="card-body">
                    <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">24/7 Support</h5>
                    <p class="card-text">Our customer support team is available around the clock to assist you.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>