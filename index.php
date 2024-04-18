<?php
session_start();
include('config.php');


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
<html>
<head>
    <title>E-Carpool</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" href="./css/style.css">
    <style>
.jumbotron {
            height: 550px;
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('./images/hotel.jpg');
            background-size: cover;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .jumbotron h1 {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .jumbotron p {
            font-size: 1.5rem;
        }
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

<?php
include('navbar.php');
?>

<div class="jumbotron text-center">
    <h1>Welcome to E-Carpool - Your Online Carpooling Platform</h1>
    <p>Find the best rides and share your journey with others in a convenient way</p>
    <a href="login.php" class="btn btn-primary btn-lg">Login to Start Carpooling</a>
</div>


<div class="container mt-5">
    <h1>Welcome to Passenger Dashboard</h1>
    <?php if (empty($routes)): ?>
        <p>No routes available.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($routes as $route): ?>
                <div class="col-md-4 mb-3">
                    <img src="./driver/<?php echo $route['profile_picture']; ?>" alt="Driver Profile Picture" class="img-fluid mb-2">
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
                    <img src="./driver/<?php echo $rating['driver_picture']; ?>" alt="Driver Profile Picture" class="driver_image">
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


</div>

<footer class="mt-5 py-3 bg-light">
    <div class="container text-center">
        <p>&copy; 2024 E-Carpool. All rights reserved.</p>
    </div>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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