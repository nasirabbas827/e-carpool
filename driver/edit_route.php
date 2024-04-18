<?php
session_start();
include('config.php');

// Check if user is logged in and is a driver
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] !== "driver") {
    header("location: login.php");
    exit;
}

// Check if RouteID is provided in the URL
if (!isset($_GET["RouteID"])) {
    header("location: view_routes.php");
    exit;
}

// Get the driver ID from the session
$driverID = $_SESSION["id"];

// Get the RouteID from the URL
$routeID = $_GET["RouteID"];

// Fetch route details from the database
$sql = "SELECT SourceLocation, DestinationLocation, Frequency FROM routes WHERE RouteID = ? AND DriverID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $routeID, $driverID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $sourceLocation, $destinationLocation, $frequency);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newSourceLocation = $_POST["sourceLocation"];
    $newDestinationLocation = $_POST["destinationLocation"];
    $newFrequency = $_POST["frequency"];

    // Update route details in the database
    $update_sql = "UPDATE routes 
                   SET SourceLocation = ?, DestinationLocation = ?, Frequency = ? 
                   WHERE RouteID = ? AND DriverID = ?";
    
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "sssii", $newSourceLocation, $newDestinationLocation, $newFrequency, $routeID, $driverID);
    
    if (mysqli_stmt_execute($update_stmt)) {
        echo '<script>alert("Route updated successfully.");</script>';
    } else {
        echo '<script>alert("Error updating route.");</script>';
    }

    mysqli_stmt_close($update_stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Route</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Edit Route</h2>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?RouteID=' . $routeID; ?>">
        <div class="form-group">
            <label for="sourceLocation">Source Location:</label>
            <input type="text" class="form-control" name="sourceLocation" value="<?php echo $sourceLocation; ?>">
        </div>
        <div class="form-group">
            <label for="destinationLocation">Destination Location:</label>
            <input type="text" class="form-control" name="destinationLocation" value="<?php echo $destinationLocation; ?>">
        </div>
        <div class="form-group">
            <label for="frequency">Frequency:</label>
            <select class="form-control" name="frequency">
                <option value="Daily" <?php if ($frequency === "Daily") echo "selected"; ?>>Daily</option>
                <option value="Weekly" <?php if ($frequency === "Weekly") echo "selected"; ?>>Weekly</option>
                <option value="Monthly" <?php if ($frequency === "Monthly") echo "selected"; ?>>Monthly</option>
                <option value="Trip-based" <?php if ($frequency === "Trip-based") echo "selected"; ?>>Trip-based</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Route</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
