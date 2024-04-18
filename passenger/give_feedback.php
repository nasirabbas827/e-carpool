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
    header("location: passenger_dashboard.php");
    exit;
}

// Get BookingID from URL
$BookingID = $_GET['BookingID'];

// Fetch booking details from the database
$sql = "SELECT r.DriverID, u.full_name
        FROM bookings b
        INNER JOIN rides r ON b.RideID = r.RideID
        INNER JOIN Users u ON r.DriverID = u.id
        WHERE b.BookingID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $BookingID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $DriverID, $DriverName);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Feedback = $_POST["feedback"];
    $Rating = $_POST["rating"];

    // Insert feedback and rating into the database
    $sql = "INSERT INTO ratings (DriverID, PassengerID, Feedback, Rating) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iisi", $DriverID, $_SESSION["id"], $Feedback, $Rating);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Redirect to a success page or do further processing
    $success_message = "Feedback submitted successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Give Feedback</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Give Feedback to <?php echo $DriverName; ?></h2>
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $success_message; ?>
        </div>
    <?php else: ?>
        <form method="post" action="">
            <div class="form-group">
                <label for="feedback">Feedback:</label>
                <textarea class="form-control" name="feedback" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="rating">Rating:</label>
                <input type="number" class="form-control" name="rating" min="1" max="5" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit Feedback</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
