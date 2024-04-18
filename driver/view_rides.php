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

// Fetch the rides of the logged-in driver from the database
$sql = "SELECT r.RideID, r.RouteID, r.StartDateTime, r.EndDateTime, r.AvailableSeats, r.Fare, r.Status, rt.SourceLocation, rt.DestinationLocation 
        FROM rides r
        INNER JOIN routes rt ON r.RouteID = rt.RouteID
        WHERE r.DriverID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $driverID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $rideID, $routeID, $startDateTime, $endDateTime, $availableSeats, $fare, $status, $sourceLocation, $destinationLocation);

// Store rides in an array
$rides = [];
while (mysqli_stmt_fetch($stmt)) {
    $rides[] = [
        "RideID" => $rideID,
        "SourceLocation" => $sourceLocation,
        "DestinationLocation" => $destinationLocation,
        "StartDateTime" => $startDateTime,
        "EndDateTime" => $endDateTime,
        "AvailableSeats" => $availableSeats,
        "Fare" => $fare,
        "Status" => $status
    ];
}
mysqli_stmt_close($stmt);

// Process ride deletion
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["delete"]) && $_GET["delete"] == "true" && isset($_GET["RideID"])) {
    $rideID = $_GET["RideID"];
    $delete_sql = "DELETE FROM rides WHERE RideID = ?";
    $delete_stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($delete_stmt, "i", $rideID);
    if (mysqli_stmt_execute($delete_stmt)) {
        echo '<script>alert("Ride deleted successfully.");</script>';
        header("Refresh:0; url=view_rides.php");
        exit;
    } else {
        echo '<script>alert("Error deleting ride.");</script>';
    }
    mysqli_stmt_close($delete_stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Rides</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>My Rides</h2>
    <table class="table">
        <thead>
            <tr>
                <th>RideID</th>
                <th>Source Location</th>
                <th>Destination Location</th>
                <th>Start Date and Time</th>
                <th>End Date and Time</th>
                <th>Available Seats</th>
                <th>Fare</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rides as $ride): ?>
                <tr>
                    <td><?php echo $ride['RideID']; ?></td>
                    <td><?php echo $ride['SourceLocation']; ?></td>
                    <td><?php echo $ride['DestinationLocation']; ?></td>
                    <td><?php echo $ride['StartDateTime']; ?></td>
                    <td><?php echo $ride['EndDateTime']; ?></td>
                    <td><?php echo $ride['AvailableSeats']; ?></td>
                    <td><?php echo $ride['Fare']; ?></td>
                    <td><?php echo $ride['Status']; ?></td>
                    <td>
                        <a href="edit_ride.php?RideID=<?php echo $ride['RideID']; ?>" class="btn btn-primary">Edit</a>
                        <a href="view_rides.php?delete=true&RideID=<?php echo $ride['RideID']; ?>" class="mt-2 btn btn-danger" onclick="return confirm('Are you sure you want to delete this ride?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
