<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Initialize variables
$username = $email = $phone = $age = $usertype = "";
$username_err = $email_err = $phone_err = $age_err = $usertype_err = "";

// Process form submission on POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user ID from URL
    $userID = $_GET['UserID'];

    // Validate form fields
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email address.";
    } else {
        $email = trim($_POST["email"]);
    }

    if (empty(trim($_POST["phone"]))) {
        $phone_err = "Please enter a phone number.";
    } else {
        $phone = trim($_POST["phone"]);
    }

    if (empty(trim($_POST["age"]))) {
        $age_err = "Please enter your age.";
    } else {
        $age = trim($_POST["age"]);
    }

    $usertype = $_POST["usertype"];

    // Update user details in the database if there are no errors
    if (empty($username_err) && empty($email_err) && empty($phone_err) && empty($age_err) && empty($usertype_err)) {
        $sql = "UPDATE users SET username=?, email=?, phone=?, age=?, usertype=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssi", $username, $email, $phone, $age, $usertype, $userID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        header("location: view_users.php");
        exit;
    }
} else {
    // Fetch user details based on UserID from the URL
    $userID = $_GET['UserID'];
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    // Populate variables with user details
    $username = $user['username'];
    $email = $user['email'];
    $phone = $user['phone'];
    $age = $user['age'];
    $usertype = $user['usertype'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2>Edit User</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?UserID=' . $userID; ?>" method="post">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
            <span class="invalid-feedback"><?php echo $username_err; ?></span>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
            <span class="invalid-feedback"><?php echo $email_err; ?></span>
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $phone; ?>">
            <span class="invalid-feedback"><?php echo $phone_err; ?></span>
        </div>
        <div class="form-group">
            <label>Age</label>
            <input type="text" name="age" class="form-control <?php echo (!empty($age_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $age; ?>">
            <span class="invalid-feedback"><?php echo $age_err; ?></span>
        </div>
        <div class="form-group">
            <label>User Type</label>
            <select class="form-control" name="usertype">
                <option value="driver" <?php if ($usertype === 'driver') echo 'selected'; ?>>Driver</option>
                <option value="passenger" <?php if ($usertype === 'passenger') echo 'selected'; ?>>Passenger</option>
            </select>
        </div>
        <div class="form-group text-center">
            <input type="submit" class="btn btn-primary" value="Update">
            <a href="view_users.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>
