<?php
session_start();
include('config.php');

// Check if user is logged in and is a passenger
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] !== "passenger") {
    header("location: login.php");
    exit;
}

// Fetch all routes with driver details and ratings from the database
$sql = "SELECT r.RouteID, r.SourceLocation, r.DestinationLocation, r.Frequency, u.full_name, u.profile_picture, AVG(rating) as average_rating
        FROM routes r
        INNER JOIN Users u ON r.DriverID = u.id
        LEFT JOIN ratings f ON r.DriverID = f.DriverID
        GROUP BY r.RouteID";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "Error: " . mysqli_error($conn);
}

// Fetch all ratings and feedback along with driver and passenger details from the database
$rat = "SELECT r.*, u.full_name AS passenger_name, d.full_name AS driver_name, d.profile_picture AS driver_picture
        FROM ratings r
        INNER JOIN Users u ON r.PassengerID = u.id
        INNER JOIN Users d ON r.DriverID = d.id";
$rerat = mysqli_query($conn, $rat);

if (!$result) {
    echo "Error: " . mysqli_error($conn);
}

$ratings = mysqli_fetch_all($rerat, MYSQLI_ASSOC);
mysqli_free_result($rerat);

$routes = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_free_result($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .driver_image {
            width: 150px; /* Set the width */
            height: 150px; /* Set the height */
            object-fit: cover; /* Ensure the image covers the entire container */
            border-radius: 50%; /* Make the image circular */
            margin: 30px;
        }
        .fas {
            color:gold;
        }
    </style>
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h1>Welcome to Passenger Dashboard</h1>
    <?php if (empty($routes)): ?>
        <p>No routes available.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($routes as $route): ?>
                <div class="col-md-4 mb-3">
                    <img src="../driver/<?php echo $route['profile_picture']; ?>" alt="Driver Profile Picture" class="img-fluid mb-2">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Route Details</h5>
                            <p><strong>Source Location:</strong> <?php echo $route['SourceLocation']; ?></p>
                            <p><strong>Destination Location:</strong> <?php echo $route['DestinationLocation']; ?></p>
                            <p><strong>Frequency:</strong> <?php echo $route['Frequency']; ?></p>
                            <p><strong>Driver:</strong> <?php echo $route['full_name']; ?></p>
                            <p><strong>Rating:</strong> <?php echo generateStarRating($route['average_rating']); ?></p>
                            <a href="view_rides.php?RouteID=<?php echo $route['RouteID']; ?>" class="btn btn-primary mt-3">View Rides</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<div class="container mt-5">
    <h1>Ratings and Feedback</h1>
    <div class="row">
        <?php foreach ($ratings as $rating): ?>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <img src="../driver/<?php echo $rating['driver_picture']; ?>" alt="Driver Profile Picture" class="driver_image">
                    <div class="card-body">
                        <h5 class="card-title">Rating</h5>
                        <p><strong>Driver:</strong> <?php echo $rating['driver_name']; ?></p>
                        <p><strong>Passenger:</strong> <?php echo $rating['passenger_name']; ?></p>
                        <p><strong>Rating:</strong> <?php echo generateStarRating($rating['Rating']); ?></p>
                        <p><strong>Feedback:</strong> <?php echo $rating['Feedback']; ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>

<?php
// Function to generate star rating icons based on the average rating
function generateStarRating($rating) {
    $roundedRating = round($rating);
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $roundedRating) {
            $stars .= '<i class="fas fa-star"></i>';
        } else {
            $stars .= '<i class="far fa-star"></i>';
        }
    }
    return $stars;
}
?>
