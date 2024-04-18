<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch all advertisements with driver names from the database
$sql = "SELECT a.*, u.full_name AS driver_name
        FROM Advertisements a
        INNER JOIN Users u ON a.DriverID = u.id";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "Error: " . mysqli_error($conn);
}

$advertisements = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_free_result($result);

// Delete Advertisement
if(isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM Advertisements WHERE AdID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: view_advertisements.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - View Advertisements</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2>View Advertisements</h2>
    <?php if (empty($advertisements)): ?>
        <p>No advertisements found.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Ad ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Published By</th>
                    <th>Publish Date Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($advertisements as $advertisement): ?>
                    <tr>
                        <td><?php echo $advertisement['AdID']; ?></td>
                        <td><?php echo $advertisement['Title']; ?></td>
                        <td><?php echo $advertisement['Description']; ?></td>
                        <td><img src="<?php echo $advertisement['ImageURL']; ?>" alt="Advertisement Image" style="max-width: 150px;"></td>
                        <td><?php echo $advertisement['driver_name']; ?></td>
                        <td><?php echo $advertisement['PublishDateTime']; ?></td>
                        <td>
                            <a href="edit_advertisement.php?AdID=<?php echo $advertisement['AdID']; ?>" class="btn btn-primary">Edit</a>
                            <a href="?delete_id=<?php echo $advertisement['AdID']; ?>" class="btn btn-danger delete-btn">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Script for delete confirmation -->
<script>
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (confirm('Are you sure you want to delete this advertisement?')) {
                // Proceed with deletion
            } else {
                // Cancel deletion
                event.preventDefault();
            }
        });
    });
</script>

</body>
</html>
