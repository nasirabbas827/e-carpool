<?php
session_start();
include('config.php');

// Check if user is logged in and is a driver
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] !== "driver") {
    header("location: login.php");
    exit;
}

// Get the driver ID from the session
$driverID = $_SESSION["id"];

// Check if RideID is provided in the URL
if (!isset($_GET["RideID"])) {
    header("location: view_rides.php");
    exit;
}

$rideID = $_GET["RideID"];

// Fetch the ride details from the database
$sql = "SELECT RouteID, StartDateTime, EndDateTime, AvailableSeats, Fare, Status FROM rides WHERE RideID = ? AND DriverID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $rideID, $driverID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $routeID, $startDateTime, $endDateTime, $availableSeats, $fare, $status);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $startDateTime = $_POST["startDateTime"];
    $endDateTime = $_POST["endDateTime"];
    $availableSeats = $_POST["availableSeats"];
    $fare = $_POST["fare"];
    $status = $_POST["status"];

    // Update the ride details in the database
    $update_sql = "UPDATE rides SET StartDateTime = ?, EndDateTime = ?, AvailableSeats = ?, Fare = ?, Status = ? WHERE RideID = ? AND DriverID = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "ssidsii", $startDateTime, $endDateTime, $availableSeats, $fare, $status, $rideID, $driverID);
    
    if (mysqli_stmt_execute($update_stmt)) {
        echo '<script>alert("Ride updated successfully.");</script>';
        header("Refresh:0; url=view_rides.php");
        exit;
    } else {
        echo '<script>alert("Error updating ride.");</script>';
    }

    mysqli_stmt_close($update_stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ride</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Edit Ride</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?RideID=" . $rideID; ?>">
        <div class="form-group">
            <label for="startDateTime">Start Date and Time:</label>
            <input type="datetime-local" class="form-control" name="startDateTime" value="<?php echo $startDateTime; ?>" required>
        </div>
        <div class="form-group">
            <label for="endDateTime">End Date and Time:</label>
            <input type="datetime-local" class="form-control" name="endDateTime" value="<?php echo $endDateTime; ?>" required>
        </div>
        <div class="form-group">
            <label for="availableSeats">Available Seats:</label>
            <input type="number" class="form-control" name="availableSeats" value="<?php echo $availableSeats; ?>" required>
        </div>
        <div class="form-group">
            <label for="fare">Fare:</label>
            <input type="number" class="form-control" name="fare" value="<?php echo $fare; ?>" required>
        </div>
        <div class="form-group">
            <label for="status">Status:</label>
            <select class="form-control" name="status" required>
                <option value="Upcoming" <?php if ($status == 'Upcoming') echo 'selected'; ?>>Upcoming</option>
                <option value="Completed" <?php if ($status == 'Completed') echo 'selected'; ?>>Completed</option>
                <option value="Cancelled" <?php if ($status == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Ride</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
