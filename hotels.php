<?php include 'includes/header.php'; ?>

<h1 class="mb-4">All Hotels</h1>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Filter Hotels</h5>
            </div>
            <div class="card-body">
                <form action="" method="GET">
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="min_price" class="form-label">Min Price</label>
                        <input type="number" class="form-control" id="min_price" name="min_price" min="0" value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="max_price" class="form-label">Max Price</label>
                        <input type="number" class="form-control" id="max_price" name="max_price" min="0" value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="rating" class="form-label">Min Rating</label>
                        <select class="form-select" id="rating" name="rating">
                            <option value="">Any Rating</option>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo (isset($_GET['rating']) && $_GET['rating'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?> Star<?php echo $i > 1 ? 's' : ''; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="row">
            <?php
            // Build query based on filters
            $query = "SELECT * FROM hotels WHERE 1=1";
            $params = [];
            $types = "";
            
            if (isset($_GET['location']) && !empty($_GET['location'])) {
                $location = '%' . sanitize($_GET['location']) . '%';
                $query .= " AND location LIKE ?";
                $params[] = $location;
                $types .= "s";
            }
            
            if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
                $min_price = sanitize($_GET['min_price']);
                $query .= " AND price_per_night >= ?";
                $params[] = $min_price;
                $types .= "d";
            }
            
            if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
                $max_price = sanitize($_GET['max_price']);
                $query .= " AND price_per_night <= ?";
                $params[] = $max_price;
                $types .= "d";
            }
            
            if (isset($_GET['rating']) && !empty($_GET['rating'])) {
                $rating = sanitize($_GET['rating']);
                $query .= " AND rating >= ?";
                $params[] = $rating;
                $types .= "d";
            }
            
            $query .= " ORDER BY name ASC";
            
            $stmt = $conn->prepare($query);
            
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
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
                            <div class="card-footer bg-white border-top-0">
                                <a href="hotel.php?id=<?php echo $hotel['id']; ?>" class="btn btn-primary w-100">View Details</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="col-12"><div class="alert alert-info">No hotels found matching your criteria.</div></div>';
            }
            ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>