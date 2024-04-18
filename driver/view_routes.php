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

// Fetch routes of the logged-in driver from the database
$sql = "SELECT RouteID, SourceLocation, DestinationLocation, Frequency FROM routes WHERE DriverID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $driverID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $routeID, $sourceLocation, $destinationLocation, $frequency);

// Store routes in an array
$routes = [];
while (mysqli_stmt_fetch($stmt)) {
    $routes[] = [
        "RouteID" => $routeID,
        "SourceLocation" => $sourceLocation,
        "DestinationLocation" => $destinationLocation,
        "Frequency" => $frequency
    ];
}
mysqli_stmt_close($stmt);

// Process route deletion
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["deleteRouteID"])) {
    $deleteRouteID = $_GET["deleteRouteID"];
    $delete_sql = "DELETE FROM routes WHERE RouteID = ? AND DriverID = ?";
    $delete_stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($delete_stmt, "ii", $deleteRouteID, $driverID);
    if (mysqli_stmt_execute($delete_stmt)) {
        echo '<script>alert("Route deleted successfully.");</script>';
    } else {
        echo '<script>alert("Error deleting route.");</script>';
    }
    mysqli_stmt_close($delete_stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Routes</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>My Routes</h2>
    <table class="table">
        <thead>
            <tr>
                <th>RouteID</th>
                <th>Source Location</th>
                <th>Destination Location</th>
                <th>Frequency</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($routes as $route): ?>
                <tr>
                    <td><?php echo $route['RouteID']; ?></td>
                    <td><?php echo $route['SourceLocation']; ?></td>
                    <td><?php echo $route['DestinationLocation']; ?></td>
                    <td><?php echo $route['Frequency']; ?></td>
                    <td>
                        <a href="edit_route.php?RouteID=<?php echo $route['RouteID']; ?>" class="btn btn-primary">Edit</a>
                        <button onclick="deleteRoute(<?php echo $route['RouteID']; ?>)" class="btn btn-danger">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
function deleteRoute(routeID) {
    if (confirm("Are you sure you want to delete this route?")) {
        window.location.href = "view_routes.php?deleteRouteID=" + routeID;
    }
}
</script>
</body>
</html>
