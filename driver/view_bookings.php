<?php
session_start();
include('config.php');

// Check if user is logged in and is a driver
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] !== "driver") {
    header("location: login.php");
    exit;
}

// Get DriverID from session
$DriverID = $_SESSION["id"];

// Fetch all rides for the driver from the database
$sql = "SELECT RideID FROM rides WHERE DriverID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $DriverID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $RideID);

// Store ride IDs in an array
$rideIDs = [];
while (mysqli_stmt_fetch($stmt)) {
    $rideIDs[] = $RideID;
}
mysqli_stmt_close($stmt);

// Fetch all bookings for the rides of the driver from the database
$bookings = [];
foreach ($rideIDs as $rideID) {
    $sql = "SELECT b.BookingID, u.full_name, u.email, b.BookingDateTime, b.Status, r.StartDateTime, r.EndDateTime, rt.SourceLocation, rt.DestinationLocation, b.BookedSeats
            FROM bookings b
            INNER JOIN rides r ON b.RideID = r.RideID
            INNER JOIN routes rt ON r.RouteID = rt.RouteID
            INNER JOIN users u ON b.PassengerID = u.id
            WHERE b.RideID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $rideID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $BookingID, $PassengerName, $PassengerEmail, $BookingDateTime, $Status, $StartDateTime, $EndDateTime, $SourceLocation, $DestinationLocation, $BookedSeats);

    // Store bookings in an array
    while (mysqli_stmt_fetch($stmt)) {
        $bookings[] = [
            "BookingID" => $BookingID,
            "PassengerName" => $PassengerName,
            "PassengerEmail" => $PassengerEmail,
            "BookingDateTime" => $BookingDateTime,
            "Status" => $Status,
            "StartDateTime" => $StartDateTime,
            "EndDateTime" => $EndDateTime,
            "SourceLocation" => $SourceLocation,
            "DestinationLocation" => $DestinationLocation,
            "BookedSeats" => $BookedSeats
        ];
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Ride Bookings</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>My Ride Bookings</h2>
    <?php if (empty($bookings)): ?>
        <p>No bookings found for your rides.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Passenger Name</th>
                    <th>Passenger Email</th>
                    <th>Booking DateTime</th>
                    <th>Start DateTime</th>
                    <th>End DateTime</th>
                    <th>Route Source</th>
                    <th>Destination</th>
                    <th>Booked Seats</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?php echo $booking['BookingID']; ?></td>
                        <td><?php echo $booking['PassengerName']; ?></td>
                        <td><?php echo $booking['PassengerEmail']; ?></td>
                        <td><?php echo $booking['BookingDateTime']; ?></td>
                        <td><?php echo $booking['StartDateTime']; ?></td>
                        <td><?php echo $booking['EndDateTime']; ?></td>
                        <td><?php echo $booking['SourceLocation']; ?></td>
                        <td><?php echo $booking['DestinationLocation']; ?></td>
                        <td><?php echo $booking['BookedSeats']; ?></td>
                        <td><?php echo $booking['Status']; ?></td>
                        <td>
                            <a href="update_booking_status.php?BookingID=<?php echo $booking['BookingID']; ?>" class="btn btn-primary">Update Status</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
