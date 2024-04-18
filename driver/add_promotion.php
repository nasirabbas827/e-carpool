<?php
session_start();
include('config.php');

// Check if user is logged in and is a driver
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] !== "driver") {
    header("location: login.php");
    exit;
}

// Define variables and initialize with empty values
$title = $description = $validFrom = $validTo = $discount = $status = "";
$title_err = $description_err = $validFrom_err = $validTo_err = $discount_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate title
    if (empty(trim($_POST["title"]))) {
        $title_err = "Please enter a title.";
    } else {
        $title = trim($_POST["title"]);
    }

    // Validate description
    if (empty(trim($_POST["description"]))) {
        $description_err = "Please enter a description.";
    } else {
        $description = trim($_POST["description"]);
    }

    // Validate valid from date
    if (empty(trim($_POST["validFrom"]))) {
        $validFrom_err = "Please select a valid from date.";
    } else {
        $validFrom = trim($_POST["validFrom"]);
    }

    // Validate valid to date
    if (empty(trim($_POST["validTo"]))) {
        $validTo_err = "Please select a valid to date.";
    } else {
        $validTo = trim($_POST["validTo"]);
    }

    // Validate discount
    if (empty(trim($_POST["discount"]))) {
        $discount_err = "Please enter a discount amount or percentage.";
    } else {
        $discount = trim($_POST["discount"]);
    }

    // Check input errors before inserting into database
    if (empty($title_err) && empty($description_err) && empty($validFrom_err) && empty($validTo_err) && empty($discount_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO promotions (Title, Description, ValidFrom, ValidTo, DiscountAmount, DriverID, Status) VALUES (?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssssis", $param_title, $param_description, $param_validFrom, $param_validTo, $param_discount, $param_driverID, $param_status);

            // Set parameters
            $param_title = $title;
            $param_description = $description;
            $param_validFrom = $validFrom;
            $param_validTo = $validTo;
            $param_discount = $discount;
            $param_driverID = $_SESSION["id"];
            $param_status = "Pending";

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to promotions page after successful creation
                header("location: view_promotions.php");
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
    <title>Add Promotion</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Add Promotion</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label>Title:</label>
            <input type="text" name="title" class="form-control <?php echo (!empty($title_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $title; ?>">
            <span class="invalid-feedback"><?php echo $title_err; ?></span>
        </div>
        <div class="form-group">
            <label>Description:</label>
            <textarea name="description" class="form-control <?php echo (!empty($description_err)) ? 'is-invalid' : ''; ?>"><?php echo $description; ?></textarea>
            <span class="invalid-feedback"><?php echo $description_err; ?></span>
        </div>
        <div class="form-group">
            <label>Valid From:</label>
            <input type="date" name="validFrom" class="form-control <?php echo (!empty($validFrom_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $validFrom; ?>">
            <span class="invalid-feedback"><?php echo $validFrom_err; ?></span>
        </div>
        <div class="form-group">
            <label>Valid To:</label>
            <input type="date" name="validTo" class="form-control <?php echo (!empty($validTo_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $validTo; ?>">
            <span class="invalid-feedback"><?php echo $validTo_err; ?></span>
        </div>
        <div class="form-group">
            <label>Discount Amount</label>
            <input type="text" name="discount" class="form-control <?php echo (!empty($discount_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $discount; ?>">
            <span class="invalid-feedback"><?php echo $discount_err; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit">
            <a href="view_promotions.php" class="btn btn-outline-secondary">View Promotions</a>
        </div>
    </form>
</div>

</body>
</html>
