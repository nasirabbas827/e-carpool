<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch all ratings and feedbacks from the database
$sql = "SELECT r.RatingID, r.DriverID, r.PassengerID, r.Rating, r.Feedback, u.username AS DriverUsername, p.username AS PassengerUsername
        FROM ratings r
        INNER JOIN users u ON r.DriverID = u.id
        INNER JOIN users p ON r.PassengerID = p.id";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "Error: " . mysqli_error($conn);
}

$ratings_feedbacks = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_free_result($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - View Ratings and Feedbacks</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2>View Ratings and Feedbacks</h2>
    <?php if (empty($ratings_feedbacks)): ?>
        <p>No ratings and feedbacks found.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Rating ID</th>
                    <th>Driver Username</th>
                    <th>Passenger Username</th>
                    <th>Rating</th>
                    <th>Feedback</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ratings_feedbacks as $rating_feedback): ?>
                    <tr>
                        <td><?php echo $rating_feedback['RatingID']; ?></td>
                        <td><?php echo $rating_feedback['DriverUsername']; ?></td>
                        <td><?php echo $rating_feedback['PassengerUsername']; ?></td>
                        <td><?php echo $rating_feedback['Rating']; ?></td>
                        <td><?php echo $rating_feedback['Feedback']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
