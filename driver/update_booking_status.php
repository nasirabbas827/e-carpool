<?php
session_start();
include('config.php');

// Check if user is logged in and is a driver
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] !== "driver") {
    header("location: login.php");
    exit;
}

// Get BookingID from the URL
if (!isset($_GET['BookingID'])) {
    header("location: view_bookings.php");
    exit;
}

$BookingID = $_GET['BookingID'];

// Fetch booking details from the database
$sql = "SELECT RideID, Status FROM bookings WHERE BookingID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $BookingID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $RideID, $Status);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newStatus = $_POST["status"];

    // Update booking status in the database
    $sql = "UPDATE bookings SET Status = ? WHERE BookingID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $newStatus, $BookingID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Redirect back to view_bookings.php
    header("location: view_bookings.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Booking Status</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Update Booking Status</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?BookingID=" . $BookingID; ?>">
        <div class="form-group">
            <label for="status">Status:</label>
            <select class="form-control" name="status" required>
                <option value="Confirmed" <?php if ($Status === "Confirmed") echo "selected"; ?>>Confirmed</option>
                <option value="Cancelled" <?php if ($Status === "Cancelled") echo "selected"; ?>>Cancelled</option>
                <option value="Pending" <?php if ($Status === "Pending") echo "selected"; ?>>Pending</option>
                <!-- Add more status options if needed -->
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Status</button>
    </form>
</div>

</body>
</html>
