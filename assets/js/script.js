// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize date pickers if they exist
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    
    if (checkInInput && checkOutInput) {
        // Set min date to today for check-in
        const today = new Date().toISOString().split('T')[0];
        checkInInput.min = today;
        
        // Update check-out min date when check-in changes
        checkInInput.addEventListener('change', function() {
            checkOutInput.min = this.value;
            // If check-out date is before check-in date, reset it
            if (checkOutInput.value && checkOutInput.value < this.value) {
                checkOutInput.value = this.value;
            }
        });
    }
    
    // Calculate total price when dates change
    if (document.getElementById('booking-form')) {
        const pricePerNight = parseFloat(document.getElementById('price_per_night').value);
        
        function calculateTotal() {
            if (checkInInput.value && checkOutInput.value) {
                const checkIn = new Date(checkInInput.value);
                const checkOut = new Date(checkOutInput.value);
                const nights = Math.round((checkOut - checkIn) / (1000 * 60 * 60 * 24));
                
                if (nights > 0) {
                    const totalPrice = nights * pricePerNight;
                    document.getElementById('total_price').textContent = totalPrice.toFixed(2);
                    document.getElementById('total_price_input').value = totalPrice.toFixed(2);
                    document.getElementById('nights').textContent = nights;
                }
            }
        }
        
        if (checkInInput && checkOutInput) {
            checkInInput.addEventListener('change', calculateTotal);
            checkOutInput.addEventListener('change', calculateTotal);
        }
    }
});