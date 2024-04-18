<?php
session_start();
include('config.php');

// Check if user is logged in and is a passenger
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] !== "passenger") {
    header("location: login.php");
    exit;
}

// Get RideID from URL
if (!isset($_GET['RideID'])) {
    header("location: passenger_dashboard.php");
    exit;
}

// Get RideID and other details
$RideID = $_GET['RideID'];
$sql = "SELECT StartDateTime, EndDateTime, AvailableSeats, Fare FROM rides WHERE RideID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $RideID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $StartDateTime, $EndDateTime, $AvailableSeats, $Fare);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $PassengerID = $_SESSION["id"];
    $BookingDateTime = date("Y-m-d H:i:s");
    $Status = "Pending";
    $PaymentMethod = "Online";
    $SpecialDescription = $_POST["special_description"];
    $SeatsToBook = $_POST["seats_to_book"];

    // Validate input
    $SeatsToBook = intval($SeatsToBook);
    if ($SeatsToBook <= 0 || $SeatsToBook > $AvailableSeats) {
        $error = "Invalid number of seats.";
    } else {
        // Insert booking into database
        $sql = "INSERT INTO bookings (PassengerID, RideID, BookingDateTime, Status, PaymentMethod, SpecialDescription, BookedSeats) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iissssi", $PassengerID, $RideID, $BookingDateTime, $Status, $PaymentMethod, $SpecialDescription, $SeatsToBook);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Update available seats for the ride
        $sql = "UPDATE rides SET AvailableSeats = AvailableSeats - ? WHERE RideID = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $SeatsToBook, $RideID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Redirect to a success page or do further processing
        $success_message = "Booking successful!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Ride</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Book Ride</h2>
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $success_message; ?>
            <a href="view_bookings.php" class="btn btn-primary ml-2">View Booking</a>
        </div>
    <?php else: ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?RideID=" . $RideID; ?>">
            <div class="form-group">
                <label for="seats_to_book">Seats to Book:</label>
                <input type="number" class="form-control" name="seats_to_book" min="1" max="<?php echo $AvailableSeats; ?>" required>
            </div>
            <div class="form-group">
                <label for="special_description">Special Description:</label>
                <textarea class="form-control" name="special_description" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Book Now</button>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger mt-3" role="alert"><?php echo $error; ?></div>
            <?php endif; ?>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
