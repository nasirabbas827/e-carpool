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

// Delete promotion if PromotionID is provided in the URL
if (isset($_GET['PromotionID'])) {
    $promotionID = $_GET['PromotionID'];
    $sql = "DELETE FROM Promotions WHERE PromotionID = ? AND DriverID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $promotionID, $DriverID);
    mysqli_stmt_execute($stmt);
    $deleted = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
}

// Fetch promotions added by the driver from the database
$sql = "SELECT * FROM Promotions WHERE DriverID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $DriverID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$promotions = [];
while ($row = mysqli_fetch_assoc($result)) {
    $promotions[] = $row;
}
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Promotions</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>My Promotions</h2>
    <?php if (isset($deleted) && $deleted > 0): ?>
        <div class="alert alert-success" role="alert">
            Promotion deleted successfully.
        </div>
    <?php endif; ?>
    <?php if (empty($promotions)): ?>
        <p>No promotions found.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Promotion ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Valid From</th>
                    <th>Valid To</th>
                    <th>Discount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($promotions as $promotion): ?>
                    <tr>
                        <td><?php echo $promotion['PromotionID']; ?></td>
                        <td><?php echo $promotion['Title']; ?></td>
                        <td><?php echo $promotion['Description']; ?></td>
                        <td><?php echo $promotion['ValidFrom']; ?></td>
                        <td><?php echo $promotion['ValidTo']; ?></td>
                        <td><?php echo $promotion['DiscountAmount']; ?></td>
                        <td><?php echo $promotion['Status']; ?></td>
                        <td>
                            <a href="delete_promotion.php?PromotionID=<?php echo $promotion['PromotionID']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this promotion?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
