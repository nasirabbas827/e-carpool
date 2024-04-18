<?php
include('config.php');

// Fetch active promotions from the database
$sqlPromotions = "SELECT * FROM Promotions WHERE Status = 'Active'";
$resultPromotions = mysqli_query($conn, $sqlPromotions);
$promotions = mysqli_fetch_all($resultPromotions, MYSQLI_ASSOC);

// Fetch advertisements from the database
$sqlAdvertisements = "SELECT * FROM Advertisements";
$resultAdvertisements = mysqli_query($conn, $sqlAdvertisements);
$advertisements = mysqli_fetch_all($resultAdvertisements, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotions and Advertisements</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <link rel="stylesheet" href="./css/style.css">

</head>
<body>

<?php
include('navbar.php');
?>
    <div class="container mt-5">
        <h2>Promotions</h2>
        <div class="row">
            <?php foreach ($promotions as $promotion) : ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $promotion['Title']; ?></h5>
                            <p class="card-text"><?php echo $promotion['Description']; ?></p>
                            <p class="card-text">Discount: <?php echo $promotion['DiscountAmount']; ?></p>
                            <p class="card-text">Valid from <?php echo $promotion['ValidFrom']; ?> to <?php echo $promotion['ValidTo']; ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <h2 class="mt-5">Advertisements</h2>
        <div class="row">
            <?php foreach ($advertisements as $advertisement) : ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <img src="./admin/<?php echo $advertisement['ImageURL']; ?>" class="card-img-top" alt="Advertisement Image">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $advertisement['Title']; ?></h5>
                            <p class="card-text"><?php echo $advertisement['Description']; ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>
