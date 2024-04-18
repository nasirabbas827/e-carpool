<?php
session_start();
include('config.php');

// Check if user is logged in and is a passenger
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] !== "passenger") {
    header("location: login.php");
    exit;
}

// Check if BookingID is provided in the URL
if (!isset($_GET['BookingID'])) {
    header("location: view_bookings.php");
    exit;
}

// Get BookingID from the URL
$BookingID = $_GET['BookingID'];

// Fetch the booking details from the database
$sql = "SELECT RideID, BookedSeats FROM bookings WHERE BookingID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $BookingID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $RideID, $BookedSeats);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Get the available seats for the ride
$sql = "SELECT AvailableSeats FROM rides WHERE RideID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $RideID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $AvailableSeats);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $UpdatedSeats = $_POST["updated_seats"];
    $SpecialDescription = $_POST["special_description"];

    // Validate input
    $UpdatedSeats = intval($UpdatedSeats);
    if ($UpdatedSeats <= 0 || $UpdatedSeats > ($AvailableSeats + $BookedSeats)) {
        $error = "Invalid number of seats.";
    } else {
        // Calculate the difference in booked seats
        $SeatDifference = $UpdatedSeats - $BookedSeats;

        // Update booked seats for the booking
        $sql = "UPDATE bookings SET BookedSeats = ?, SpecialDescription = ? WHERE BookingID = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isi", $UpdatedSeats, $SpecialDescription, $BookingID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Update available seats for the ride
        $sql = "UPDATE rides SET AvailableSeats = AvailableSeats - ? WHERE RideID = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $SeatDifference, $RideID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Redirect back to the view bookings page
        header("location: view_bookings.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Booking</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Update Booking</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?BookingID=" . $BookingID; ?>">
        <div class="form-group">
            <label for="updated_seats">Updated Seats:</label>
            <input type="number" class="form-control" name="updated_seats" min="1" max="<?php echo ($AvailableSeats + $BookedSeats); ?>" required>
        </div>
        <div class="form-group">
            <label for="special_description">Special Description:</label>
            <textarea class="form-control" name="special_description" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger mt-3" role="alert"><?php echo $error; ?></div>
        <?php endif; ?>
    </form>
</div>

</body>
</html>
