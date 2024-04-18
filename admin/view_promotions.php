<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch all promotions with driver names from the database
$sql = "SELECT p.*, u.full_name AS driver_name
        FROM Promotions p
        INNER JOIN Users u ON p.DriverID = u.id";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "Error: " . mysqli_error($conn);
}

$promotions = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_free_result($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - View Promotions</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2>View Promotions</h2>
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
                    <th>Discount Amount</th>
                    <th>Driver Name</th>
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
                        <td><?php echo $promotion['driver_name']; ?></td>
                        <td><?php echo $promotion['Status']; ?></td>
                        <td>
                            <?php if ($promotion['Status'] === "Pending"): ?>
                                <a href="update_promotion_status.php?PromotionID=<?php echo $promotion['PromotionID']; ?>&status=active" class="btn btn-success">Activate</a>
                            <?php endif; ?>
                            <a href="delete_promotion.php?PromotionID=<?php echo $promotion['PromotionID']; ?>" class="btn btn-danger delete-btn">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
