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

// Initialize variables
$routeID = $startDateTime = $endDateTime = $availableSeats = $fare = $status = "";
$routeID_err = $startDateTime_err = $endDateTime_err = $availableSeats_err = $fare_err = $status_err = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate route ID
    if (empty(trim($_POST["routeID"]))) {
        $routeID_err = "Please select a route.";
    } else {
        $routeID = trim($_POST["routeID"]);
    }

    // Validate start date and time
    if (empty(trim($_POST["startDateTime"]))) {
        $startDateTime_err = "Please enter the start date and time.";
    } else {
        $startDateTime = trim($_POST["startDateTime"]);
    }

    // Validate end date and time
    if (empty(trim($_POST["endDateTime"]))) {
        $endDateTime_err = "Please enter the end date and time.";
    } else {
        $endDateTime = trim($_POST["endDateTime"]);
    }

    // Validate available seats
    if (empty(trim($_POST["availableSeats"]))) {
        $availableSeats_err = "Please enter the available seats.";
    } else {
        $availableSeats = trim($_POST["availableSeats"]);
    }

    // Validate fare
    if (empty(trim($_POST["fare"]))) {
        $fare_err = "Please enter the fare.";
    } else {
        $fare = trim($_POST["fare"]);
    }

    // Validate status
    if (empty(trim($_POST["status"]))) {
        $status_err = "Please select the status.";
    } else {
        $status = trim($_POST["status"]);
    }

    // If no errors, insert ride into database
    if (empty($routeID_err) && empty($startDateTime_err) && empty($endDateTime_err) && empty($availableSeats_err) && empty($fare_err) && empty($status_err)) {
        $sql = "INSERT INTO rides (DriverID, RouteID, StartDateTime, EndDateTime, AvailableSeats, Fare, Status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iisssds", $driverID, $routeID, $startDateTime, $endDateTime, $availableSeats, $fare, $status);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        echo '<div class="alert alert-success" role="alert">Ride added successfully.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Ride</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5 mb-5">
    <h2>Add Ride</h2>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
            <label for="routeID">Select Route:</label>
            <select class="form-control <?php echo (!empty($routeID_err)) ? 'is-invalid' : ''; ?>" name="routeID">
                <option value="" selected disabled>Select route</option>
                <?php
                // Fetch routes for the driver from the database
                $sql = "SELECT RouteID, SourceLocation, DestinationLocation FROM routes WHERE DriverID = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $driverID);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $routeID, $sourceLocation, $destinationLocation);
                while (mysqli_stmt_fetch($stmt)) {
                    echo "<option value='$routeID'>$sourceLocation - $destinationLocation</option>";
                }
                mysqli_stmt_close($stmt);
                ?>
            </select>
            <span class="invalid-feedback"><?php echo $routeID_err; ?></span>
        </div>
        <div class="form-group">
            <label for="startDateTime">Start Date and Time:</label>
            <input type="datetime-local" class="form-control <?php echo (!empty($startDateTime_err)) ? 'is-invalid' : ''; ?>" name="startDateTime" value="<?php echo $startDateTime; ?>">
            <span class="invalid-feedback"><?php echo $startDateTime_err; ?></span>
        </div>
        <div class="form-group">
            <label for="endDateTime">End Date and Time:</label>
            <input type="datetime-local" class="form-control <?php echo (!empty($endDateTime_err)) ? 'is-invalid' : ''; ?>" name="endDateTime" value="<?php echo $endDateTime; ?>">
            <span class="invalid-feedback"><?php echo $endDateTime_err; ?></span>
        </div>
        <div class="form-group">
            <label for="availableSeats">Available Seats:</label>
            <input type="number" class="form-control <?php echo (!empty($availableSeats_err)) ? 'is-invalid' : ''; ?>" name="availableSeats" value="<?php echo $availableSeats; ?>">
            <span class="invalid-feedback"><?php echo $availableSeats_err; ?></span>
        </div>
        <div class="form-group">
            <label for="fare">Fare:</label>
            <input type="number" class="form-control <?php echo (!empty($fare_err)) ? 'is-invalid' : ''; ?>" name="fare" value="<?php echo $fare; ?>">
            <span class="invalid-feedback"><?php echo $fare_err; ?></span>
        </div>
        <div class="form-group">
            <label for="status">Status:</label>
            <select class="form-control <?php echo (!empty($status_err)) ? 'is-invalid' : ''; ?>" name="status">
                <option value="" selected disabled>Select status</option>
                <option value="Upcoming">Upcoming</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
            </select>
            <span class="invalid-feedback"><?php echo $status_err; ?></span>
        </div>
        <button type="submit" class="btn btn-primary">Add Ride</button>
        <a class="btn btn-outline-dark" href="view_rides.php">View Rides</a>

    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
