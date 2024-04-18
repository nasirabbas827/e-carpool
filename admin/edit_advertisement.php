<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Initialize variables
$title = $description = $imageURL = "";
$title_err = $description_err = $imageURL_err = "";

// Process form submission when form is submitted
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



    // Check input errors before updating the advertisement
    if (empty($title_err) && empty($description_err) && empty($imageURL_err)) {
        // Prepare an update statement
        $sql = "UPDATE Advertisements SET Title = ?, Description = ? WHERE AdID = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssi", $param_title, $param_description,  $param_adID);

            // Set parameters
            $param_title = $title;
            $param_description = $description;
            $param_imageURL = $imageURL;
            $param_adID = $_GET["AdID"];

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to view advertisements page
                header("location: view_advertisements.php");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($conn);
} else {
    // Prepare a select statement
    $sql = "SELECT Title, Description, ImageURL FROM Advertisements WHERE AdID = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_adID);

        // Set parameters
        $param_adID = $_GET["AdID"];

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);

            // Check if advertisement exists
            if (mysqli_stmt_num_rows($stmt) == 1) {
                // Bind result variables
                mysqli_stmt_bind_result($stmt, $title, $description, $imageURL);
                mysqli_stmt_fetch($stmt);
            } else {
                // Advertisement not found, redirect to error page
                header("location: error.php");
                exit();
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }

        // Close statement
        mysqli_stmt_close($stmt);
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
    <title>Edit Advertisement</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2>Edit Advertisement</h2>
    <p>Please update the details of the advertisement.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?AdID=' . $_GET["AdID"]; ?>" method="post">
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
            <input type="submit" class="btn btn-primary" value="Update">
            <a href="view_advertisements.php" class="btn btn-secondary ml-2">Cancel</a>
        </div>
    </form>
</div>
</body>
</html>
