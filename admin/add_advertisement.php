<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Define variables and initialize with empty values
$title = $description = $imageURL = "";
$title_err = $description_err = $imageURL_err = "";

// Fetch all drivers from the users table
$sql = "SELECT id, full_name FROM users WHERE usertype = 'driver'";
$result = mysqli_query($conn, $sql);

$drivers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $drivers[] = $row;
}
mysqli_free_result($result);

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate title
    if (empty(trim($_POST["title"]))) {
        $title_err = "Please enter the advertisement title.";
    } else {
        $title = trim($_POST["title"]);
    }

    // Validate description
    if (empty(trim($_POST["description"]))) {
        $description_err = "Please enter the advertisement description.";
    } else {
        $description = trim($_POST["description"]);
    }

    // Validate image URL
    if (empty($_FILES["image"]["name"])) {
        $imageURL_err = "Please select an image file.";
    } else {
        $target_dir = "advertisement_images/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check file type
        $allowed_types = array('jpg', 'jpeg', 'png');
        if (!in_array($imageFileType, $allowed_types)) {
            $imageURL_err = "Only JPG, JPEG, PNG files are allowed.";
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $imageURL = $target_file;
            } else {
                $imageURL_err = "Error uploading the file.";
            }
        }
    }

    // Check input errors before inserting into database
    if (empty($title_err) && empty($description_err) && empty($imageURL_err)) {

        // Prepare an insert statement
        $sql = "INSERT INTO Advertisements (DriverID, Title, Description, ImageURL, PublishDateTime) VALUES (?, ?, ?, ?, NOW())";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "isss", $param_driverid, $param_title, $param_description, $param_imageURL);

            // Set parameters
            $param_driverid = $_POST["driver"];
            $param_title = $title;
            $param_description = $description;
            $param_imageURL = $imageURL;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to advertisement list page
                header("location: view_advertisements.php");
                exit;
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Advertisement</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include('admin_navbar.php'); ?>
    <div class="container">
        <h2>Add Advertisement</h2>
        <p>Please fill in the details of the advertisement.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" class="form-control <?php echo (!empty($title_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $title; ?>">
                <span class="invalid-feedback"><?php echo $title_err; ?></span>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control <?php echo (!empty($description_err)) ? 'is-invalid' : ''; ?>"><?php echo $description; ?></textarea>
                <span class="invalid-feedback"><?php echo $description_err; ?></span>
            </div>
            <div class="form-group">
                <label>Image</label>
                <input type="file" name="image" class="form-control-file <?php echo (!empty($imageURL_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $imageURL_err; ?></span>
            </div>
            <div class="form-group">
                <label>Driver</label>
                <select name="driver" class="form-control">
                    <?php foreach ($drivers as $driver): ?>
                        <option value="<?php echo $driver['id']; ?>"><?php echo $driver['full_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a href="view_advertisements.php" class="btn btn-outline-secondary ml-2">View Advertisements</a>
            </div>
        </form>
    </div>
</body>

</html>
