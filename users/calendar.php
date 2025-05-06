<?php
include '../includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect(BASE_URL . 'login.php');
}

// Get user bookings
$query = "SELECT b.*, h.name as hotel_name 
          FROM bookings b
          JOIN hotels h ON b.hotel_id = h.id
          WHERE b.user_id = ? AND b.status != 'cancelled'
          ORDER BY b.check_in ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$bookings = [];

while ($booking = $result->fetch_assoc()) {
    $bookings[] = $booking;
}

// Get current month and year
$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Ensure valid month and year
if ($month < 1) {
    $month = 12;
    $year--;
} elseif ($month > 12) {
    $month = 1;
    $year++;
}

// Get previous and next month links
$prev_month = $month - 1;
$prev_year = $year;
if ($prev_month < 1) {
    $prev_month = 12;
    $prev_year--;
}

$next_month = $month + 1;
$next_year = $year;
if ($next_month > 12) {
    $next_month = 1;
    $next_year++;
}

// Get month name
$month_name = date('F', mktime(0, 0, 0, $month, 1, $year));

// Get the first day of the month
$first_day = date('N', mktime(0, 0, 0, $month, 1, $year));
// Get the number of days in the month
$days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));

// Create calendar
$calendar = [];
$day = 1;

// Fill empty cells for the first week
for ($i = 1; $i < $first_day; $i++) {
    $calendar[1][$i] = '';
}

// Fill calendar with days
$week = 1;
for ($day = 1; $day <= $days_in_month; $day++) {
    $day_of_week = date('N', mktime(0, 0, 0, $month, $day, $year));
    
    $calendar[$week][$day_of_week] = $day;
    
    if ($day_of_week == 7) {
        $week++;
    }
}

// Fill empty cells for the last week
if ($day_of_week < 7) {
    for ($i = $day_of_week + 1; $i <= 7; $i++) {
        $calendar[$week][$i] = '';
    }
}
?>

<h1 class="mb-4">My Booking Calendar</h1>

<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="?month=<?php echo $prev_month; ?>&year=<?php echo $prev_year; ?>" class="btn btn-outline-primary">
                <i class="fas fa-chevron-left"></i> Previous Month
            </a>
            <h3 class="mb-0"><?php echo $month_name . ' ' . $year; ?></h3>
            <a href="?month=<?php echo $next_month; ?>&year=<?php echo $next_year; ?>" class="btn btn-outline-primary">
                Next Month <i class="fas fa-chevron-right"></i>
            </a>
        </div>
        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Monday</th>
                    <th>Tuesday</th>
                    <th>Wednesday</th>
                    <th>Thursday</th>
                    <th>Friday</th>
                    <th>Saturday</th>
                    <th>Sunday</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($calendar as $week): ?>
                    <tr class="calendar-row" style="height: 120px;">
                        <?php for ($day_of_week = 1; $day_of_week <= 7; $day_of_week++): ?>
                            <td class="calendar-day">
                                <?php if (isset($week[$day_of_week]) && $week[$day_of_week] != ''): ?>
                                    <div class="date"><?php echo $week[$day_of_week]; ?></div>
                                    
                                    <?php
                                    // Check for bookings on this day
                                    $current_date = sprintf('%04d-%02d-%02d', $year, $month, $week[$day_of_week]);
                                    
                                    foreach ($bookings as $booking) {
                                        $check_in = $booking['check_in'];
                                        $check_out = $booking['check_out'];
                                        
                                        if ($current_date >= $check_in && $current_date <= $check_out) {
                                            $status_class = $booking['status'] == 'confirmed' ? 'success' : 'warning';
                                            echo '<div class="booking-event bg-' . $status_class . ' p-1 rounded mb-1 text-white small">';
                                            if ($current_date == $check_in) {
                                                echo '<i class="fas fa-sign-in-alt"></i> ';
                                            } elseif ($current_date == $check_out) {
                                                echo '<i class="fas fa-sign-out-alt"></i> ';
                                            }
                                            echo $booking['hotel_name'];
                                            echo '</div>';
                                        }
                                    }
                                    ?>
                                <?php endif; ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Upcoming Bookings</h5>
    </div>
    <div class="card-body">
        <?php
        $upcoming_bookings = false;
        
        foreach ($bookings as $booking) {
            if ($booking['check_in'] >= date('Y-m-d')) {
                $upcoming_bookings = true;
                $days_until = floor((strtotime($booking['check_in']) - time()) / (60 * 60 * 24));
                
                echo '<div class="mb-3 pb-3 border-bottom">';
                echo '<h5>' . $booking['hotel_name'] . '</h5>';
                echo '<div class="d-flex justify-content-between mb-2">';
                echo '<span><i class="fas fa-calendar-check text-primary"></i> Check-in:</span>';
                echo '<span>' . date('M d, Y', strtotime($booking['check_in'])) . ' (' . ($days_until > 0 ? "in $days_until days" : "today") . ')</span>';
                echo '</div>';
                echo '<div class="d-flex justify-content-between">';
                echo '<span><i class="fas fa-calendar-times text-primary"></i> Check-out:</span>';
                echo '<span>' . date('M d, Y', strtotime($booking['check_out'])) . '</span>';
                echo '</div>';
                echo '</div>';
            }
        }
        
        if (!$upcoming_bookings) {
            echo '<p class="mb-0">You have no upcoming bookings.</p>';
        }
        ?>
    </div>
</div>

<style>
.calendar-day {
    position: relative;
    height: 120px;
    vertical-align: top;
}

.date {
    position: absolute;
    top: 5px;
    right: 5px;
    font-weight: bold;
}

.booking-event {
    margin-top: 25px;
    font-size: 12px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>

<?php include '../includes/footer.php'; ?>