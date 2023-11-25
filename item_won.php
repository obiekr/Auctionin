<?php
require_once('function.php');
session_start();
$conn = new PDO("mysql:host=localhost:3306;dbname=auctioninuas", "admin", "123123");
date_default_timezone_set('Asia/Jakarta');
updateBalance($conn);

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
}

$user = getUser($_SESSION["username"], $conn);

$products = getWonProduct($conn, $user);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- CSS only -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/footer.css">
    <title>Auctionin</title>
</head>

<body>

    <nav class="navbar navbar-light bg-light mw-100 px-5">
        <a class="navbar-brand" href="index.php">
            <h1>Auctionin</h1>
        </a>
        <!-- <form class="d-flex">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form> -->
        <?php if (isset($_SESSION["username"])) : ?>
            <div class="d-flex menu">
                <h5 class="balance">Balance: Rp <?= $user["balance"] ?></h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle user-dropdown" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?= $_SESSION["username"] ?>
                    </button>
                    <div class="dropdown-menu user-dropdown-item" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="sales.php">Auction Your Item</a>
                        <a class="dropdown-item" href="item_won.php">Items You Won</a>
                        <a class="dropdown-item" href="deposit.php">Deposit Balance</a>
                        <a class="dropdown-item" href="logout.php">Logout</a>
                    </div>
                </div>
            </div>
        <?php else :   ?>
            <a href="login.php" class="btn btn-primary">Login</a>

        <?php endif; ?>

    </nav>


    <div class="container daxem mt-5">
        <div class="row justify-content-center">
            <?php foreach ($products as $item) : ?>
                <div class="col-md-3 col-sm-6 col-xs-3 content__item">
                    <div class="card  mb-3">
                        <img height="350" src="<?= $item["product_image"] ?>" class="card-img-top" alt="">
                        <div class="card-body">
                            <h5 class="card-title"><?= $item["product_name"] ?></h5>
                            <p class="card-text">Last bid: Rp <?= $item["product_price"] ?></p>
                            <p class="card-text text-danger">Expired on: Rp <?= $item["product_expired"] ?></p>
                            <a href="product.php?id=<?= $item["product_id"] ?>" class="btn btn-primary">Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>



    <div class="container mt-5 border-top mb-5">
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="footer__item">
                    <h3>Address</h3>
                    <p>
                        BSD
                    </p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="footer__item">
                    <h3>Contact</h3>
                    <p>
                        08123123123
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>


</html>