<?php include 'includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Hotels Management</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHotelModal">
        <i class="fas fa-plus"></i> Add Hotel
    </button>
</div>

<?php
// Process hotel deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $hotel_id = sanitize($_GET['delete']);
    
    // Delete hotel
    $query = "DELETE FROM hotels WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $hotel_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Hotel deleted successfully";
    } else {
        $_SESSION['error_message'] = "Failed to delete hotel";
    }
    
    // Redirect to remove the query string
    redirect(ADMIN_URL . 'hotels.php');
}

// Get all hotels
$query = "SELECT * FROM hotels ORDER BY id DESC";
$result = $conn->query($query);
?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Price/Night</th>
                        <th>Rating</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($hotel = $result->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?php echo $hotel['id']; ?></td>
                                <td>
                                    <img src="<?php echo $hotel['image_url']; ?>" alt="<?php echo $hotel['name']; ?>" width="50" height="50" class="rounded">
                                </td>
                                <td><?php echo $hotel['name']; ?></td>
                                <td><?php echo $hotel['location']; ?></td>
                                <td>$<?php echo $hotel['price_per_night']; ?></td>
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
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary edit-hotel" data-bs-toggle="modal" data-bs-target="#editHotelModal" 
                                        data-id="<?php echo $hotel['id']; ?>"
                                        data-name="<?php echo $hotel['name']; ?>"
                                        data-description="<?php echo htmlspecialchars($hotel['description']); ?>"
                                        data-location="<?php echo $hotel['location']; ?>"
                                        data-price="<?php echo $hotel['price_per_night']; ?>"
                                        data-rating="<?php echo $hotel['rating']; ?>"
                                        data-image="<?php echo $hotel['image_url']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="hotels.php?delete=<?php echo $hotel['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this hotel?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo '<tr><td colspan="7" class="text-center">No hotels found</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Hotel Modal -->
<div class="modal fade" id="addHotelModal" tabindex="-1" aria-labelledby="addHotelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="hotel_process.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="addHotelModalLabel">Add Hotel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>
                    <div class="mb-3">
                        <label for="price_per_night" class="form-label">Price per Night ($)</label>
                        <input type="number" class="form-control" id="price_per_night" name="price_per_night" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating (0-5)</label>
                        <input type="number" class="form-control" id="rating" name="rating" step="0.1" min="0" max="5" required>
                    </div>
                    <div class="mb-3">
                        <label for="image_url" class="form-label">Image URL</label>
                        <input type="text" class="form-control" id="image_url" name="image_url" required>
                        <small class="text-muted">Enter a URL for the hotel image</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Hotel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Hotel Modal -->
<div class="modal fade" id="editHotelModal" tabindex="-1" aria-labelledby="editHotelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="hotel_process.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editHotelModalLabel">Edit Hotel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="hotel_id" id="edit_hotel_id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="edit_location" name="location" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_price_per_night" class="form-label">Price per Night ($)</label>
                        <input type="number" class="form-control" id="edit_price_per_night" name="price_per_night" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_rating" class="form-label">Rating (0-5)</label>
                        <input type="number" class="form-control" id="edit_rating" name="rating" step="0.1" min="0" max="5" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_image_url" class="form-label">Image URL</label>
                        <input type="text" class="form-control" id="edit_image_url" name="image_url" required>
                        <small class="text-muted">Enter a URL for the hotel image</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Hotel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set edit hotel form values
    const editButtons = document.querySelectorAll('.edit-hotel');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const hotelId = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const description = this.getAttribute('data-description');
            const location = this.getAttribute('data-location');
            const price = this.getAttribute('data-price');
            const rating = this.getAttribute('data-rating');
            const image = this.getAttribute('data-image');
            
            document.getElementById('edit_hotel_id').value = hotelId;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_location').value = location;
            document.getElementById('edit_price_per_night').value = price;
            document.getElementById('edit_rating').value = rating;
            document.getElementById('edit_image_url').value = image;
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>