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

// Update available seats for the ride
$sql = "UPDATE rides SET AvailableSeats = AvailableSeats + ? WHERE RideID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $BookedSeats, $RideID);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Delete the booking from the database
$sql = "DELETE FROM bookings WHERE BookingID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $BookingID);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Redirect back to the view bookings page
header("location: view_bookings.php");
exit;
?>
