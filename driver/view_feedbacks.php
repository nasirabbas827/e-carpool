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

// Fetch all feedbacks and ratings for the driver from the database
$sql = "SELECT f.RatingID, f.Feedback, f.Rating, u.full_name
        FROM ratings f
        INNER JOIN Users u ON f.PassengerID = u.id
        WHERE f.DriverID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $DriverID);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $FeedbackID, $Feedback, $Rating, $PassengerName);

// Store feedbacks and ratings in an array
$feedbacks = [];
while (mysqli_stmt_fetch($stmt)) {
    $feedbacks[] = [
        "FeedbackID" => $FeedbackID,
        "Feedback" => $Feedback,
        "Rating" => $Rating,
        "PassengerName" => $PassengerName
    ];
}
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Feedbacks and Ratings</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Feedbacks and Ratings</h2>
    <?php if (empty($feedbacks)): ?>
        <p>No feedbacks found.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Feedback ID</th>
                    <th>Passenger Name</th>
                    <th>Feedback</th>
                    <th>Rating</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($feedbacks as $feedback): ?>
                    <tr>
                        <td><?php echo $feedback['FeedbackID']; ?></td>
                        <td><?php echo $feedback['PassengerName']; ?></td>
                        <td><?php echo $feedback['Feedback']; ?></td>
                        <td><?php echo $feedback['Rating']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
