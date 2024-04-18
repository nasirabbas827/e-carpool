<?php
session_start();
include('config.php');

// Check if user is logged in and is a passenger
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] !== "passenger") {
    header("location: login.php");
    exit;
}

// Get RouteID from URL
if (!isset($_GET['RouteID'])) {
    header("location: dashboard.php");
    exit;
}

// Fetch route details with driver's information from the database
$RouteID = $_GET['RouteID'];
$sql = "SELECT r.RouteID, r.SourceLocation, r.DestinationLocation, r.Frequency, u.full_name, u.profile_picture
        FROM routes r
        INNER JOIN Users u ON r.DriverID = u.id
        WHERE r.RouteID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $RouteID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $RouteID, $SourceLocation, $DestinationLocation, $Frequency, $DriverName, $DriverProfilePicture);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Fetch all rides for the selected route from the database
$sql = "SELECT RideID, StartDateTime, EndDateTime, AvailableSeats, Fare, Status FROM rides WHERE RouteID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $RouteID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $RideID, $StartDateTime, $EndDateTime, $AvailableSeats, $Fare, $Status);

// Store rides in an array
$rides = [];
while (mysqli_stmt_fetch($stmt)) {
    $rides[] = [
        "RideID" => $RideID,
        "StartDateTime" => $StartDateTime,
        "EndDateTime" => $EndDateTime,
        "AvailableSeats" => $AvailableSeats,
        "Fare" => $Fare,
        "Status" => $Status
    ];
}
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Rides</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .driver-profile-picture {
            width: 150px; /* Set the width */
            height: 150px; /* Set the height */
            object-fit: cover; /* Ensure the image covers the entire container */
            border-radius: 50%; /* Make the image circular */
        }
    </style>
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>View Rides</h2>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Route Details</h5>
            <p><strong>Source Location:</strong> <?php echo $SourceLocation; ?></p>
            <p><strong>Destination Location:</strong> <?php echo $DestinationLocation; ?></p>
            <p><strong>Frequency:</strong> <?php echo $Frequency; ?></p>
            <p><strong>Driver:</strong> <?php echo $DriverName; ?></p>
            <img src="../driver/<?php echo $DriverProfilePicture; ?>" alt="Driver Profile Picture" class="img-fluid mb-2 driver-profile-picture">
        </div>
    </div>
    <div class="row">
        <?php foreach ($rides as $ride): ?>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Ride ID: <?php echo $ride['RideID']; ?></h5>
                        <p><strong>Start Date/Time:</strong> <?php echo $ride['StartDateTime']; ?></p>
                        <p><strong>End Date/Time:</strong> <?php echo $ride['EndDateTime']; ?></p>
                        <p><strong>Available Seats:</strong> <?php echo $ride['AvailableSeats']; ?></p>
                        <p><strong>Fare:</strong> <?php echo $ride['Fare']; ?></p>
                        <p><strong>Status:</strong> <?php echo $ride['Status']; ?></p>
                        <?php if ($ride['Status'] === "Upcoming"): ?>
                            <a href="book_ride.php?RideID=<?php echo $ride['RideID']; ?>" class="btn btn-primary">Book Now</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
