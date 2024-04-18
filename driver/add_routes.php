<?php
session_start();
include('config.php');

// Check if user is logged in and is a driver
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] !== "driver") {
    header("location: login.php");
    exit;
}

// Initialize variables
$driverID = $_SESSION["id"];
$sourceLocation = $destinationLocation = $frequency = "";
$sourceLocation_err = $destinationLocation_err = $frequency_err = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate source location
    if (empty(trim($_POST["sourceLocation"]))) {
        $sourceLocation_err = "Please enter the source location.";
    } else {
        $sourceLocation = trim($_POST["sourceLocation"]);
    }

    // Validate destination location
    if (empty(trim($_POST["destinationLocation"]))) {
        $destinationLocation_err = "Please enter the destination location.";
    } else {
        $destinationLocation = trim($_POST["destinationLocation"]);
    }

    // Validate frequency
    if (empty(trim($_POST["frequency"]))) {
        $frequency_err = "Please select the frequency.";
    } else {
        $frequency = trim($_POST["frequency"]);
    }

    // If no errors, insert route into database
    if (empty($sourceLocation_err) && empty($destinationLocation_err) && empty($frequency_err)) {
        $sql = "INSERT INTO routes (DriverID, SourceLocation, DestinationLocation, Frequency) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isss", $driverID, $sourceLocation, $destinationLocation, $frequency);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        echo '<div class="alert alert-success" role="alert">Route added successfully.</div>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Route</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Add Route</h2>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
            <label for="sourceLocation">Source Location:</label>
            <input type="text" class="form-control <?php echo (!empty($sourceLocation_err)) ? 'is-invalid' : ''; ?>" name="sourceLocation" value="<?php echo $sourceLocation; ?>">
            <span class="invalid-feedback"><?php echo $sourceLocation_err; ?></span>
        </div>
        <div class="form-group">
            <label for="destinationLocation">Destination Location:</label>
            <input type="text" class="form-control <?php echo (!empty($destinationLocation_err)) ? 'is-invalid' : ''; ?>" name="destinationLocation" value="<?php echo $destinationLocation; ?>">
            <span class="invalid-feedback"><?php echo $destinationLocation_err; ?></span>
        </div>
        <div class="form-group">
            <label for="frequency">Frequency:</label>
            <select class="form-control <?php echo (!empty($frequency_err)) ? 'is-invalid' : ''; ?>" name="frequency">
                <option value="" selected disabled>Select frequency</option>
                <option value="Daily">Daily</option>
                <option value="Weekly">Weekly</option>
                <option value="Monthly">Monthly</option>
                <option value="Trip-based">Trip-based</option>
            </select>
            <span class="invalid-feedback"><?php echo $frequency_err; ?></span>
        </div>
        <button type="submit" class="btn btn-primary">Add Route</button>
        <a class="btn btn-outline-dark" href="view_routes.php">View Routes</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
