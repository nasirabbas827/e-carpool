<?php
session_start();
include('config.php');

// Check if user is logged in and is a passenger
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] !== "passenger") {
    header("location: login.php");
    exit;
}

// Get PassengerID from session
$PassengerID = $_SESSION["id"];

// Fetch all bookings for the passenger from the database
$sql = "SELECT b.BookingID, r.StartDateTime, r.EndDateTime, rt.SourceLocation, rt.DestinationLocation, r.AvailableSeats, r.Fare, b.Status, b.BookedSeats
        FROM bookings b
        INNER JOIN rides r ON b.RideID = r.RideID
        INNER JOIN routes rt ON r.RouteID = rt.RouteID
        WHERE b.PassengerID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $PassengerID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $BookingID, $StartDateTime, $EndDateTime, $SourceLocation, $DestinationLocation, $AvailableSeats, $Fare, $Status, $BookedSeats);

// Store bookings in an array
$bookings = [];
while (mysqli_stmt_fetch($stmt)) {
    $bookings[] = [
        "BookingID" => $BookingID,
        "StartDateTime" => $StartDateTime,
        "EndDateTime" => $EndDateTime,
        "SourceLocation" => $SourceLocation,
        "DestinationLocation" => $DestinationLocation,
        "AvailableSeats" => $AvailableSeats,
        "Fare" => $Fare,
        "Status" => $Status,
        "BookedSeats" => $BookedSeats
    ];
}
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>My Bookings</h2>
    <?php if (empty($bookings)): ?>
        <p>No bookings found.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Source Location</th>
                    <th>Destination Location</th>
                    <th>Start Date/Time</th>
                    <th>End Date/Time</th>
                    <th>Available Seats</th>
                    <th>Booked Seats</th>
                    <th>Fare (Per Seat)</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?php echo $booking['BookingID']; ?></td>
                        <td><?php echo $booking['SourceLocation']; ?></td>
                        <td><?php echo $booking['DestinationLocation']; ?></td>
                        <td><?php echo $booking['StartDateTime']; ?></td>
                        <td><?php echo $booking['EndDateTime']; ?></td>
                        <td><?php echo $booking['AvailableSeats']; ?></td>
                        <td><?php echo $booking['BookedSeats']; ?></td>
                        <td><?php echo $booking['Fare']; ?></td>
                        <td><?php echo $booking['Status']; ?></td>
                        <td>
                            <?php if ($booking['Status'] === "Pending"): ?>
                                <a href="update_booking.php?BookingID=<?php echo $booking['BookingID']; ?>" class="btn btn-primary">Update</a>
                                <button class="mt-2 btn btn-danger delete-btn" data-booking-id="<?php echo $booking['BookingID']; ?>">Cancel</button>
                            <?php elseif ($booking['Status'] === "Confirmed"): ?>
                                <a href="give_feedback.php?BookingID=<?php echo $booking['BookingID']; ?>" class="btn btn-success">Give Feedback</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Add event listener to delete buttons
        var deleteButtons = document.querySelectorAll(".delete-btn");
        deleteButtons.forEach(function(button) {
            button.addEventListener("click", function() {
                var bookingID = this.getAttribute("data-booking-id");
                if (confirm("Are you sure you want to delete this booking?")) {
                    // Perform deletion asynchronously
                    var xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            // Reload the page after successful deletion
                            location.reload();
                        }
                    };
                    xhr.open("GET", "delete_booking.php?BookingID=" + bookingID, true);
                    xhr.send();
                }
            });
        });
    });
</script>

</body>
</html>
