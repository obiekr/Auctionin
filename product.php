<?php
require_once('function.php');
session_start();
$conn = new PDO("mysql:host=localhost:3306;dbname=auctioninuas", "admin", "123123");
date_default_timezone_set('Asia/Jakarta');
updateBalance($conn);

$item = getProduct($_GET["id"], $conn);
$user_id = -1;
if (isset($_SESSION["username"])) {
    $user = getUser($_SESSION["username"], $conn);
    $user_id = $user["user_id"];
}

if (isset($_POST["bidRequest"])) {
    $status = bidRequestHandler($conn, $user, $item, $_POST["bidRequest"]);
    if ($status) {
        header("Refresh:0");
    } else {
        echo '<h3 class="bg-danger px-3 py-1 text-center">You dont have enough balance</h3>';
    }
    // echo $status;
}

if (isset($_POST["send"])) {
    $status = sendProduct($conn, $item);
    if ($status) {
        header("Refresh:0");
    } else {
        echo '<h3 class="bg-danger px-3 py-1 text-center">Something went wrong, please try again</h3>';
    }
    // echo $item["sentToAddress"];
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.min.js" integrity="sha384-VHvPCCyXqtD5DqJeNxl2dtTyhF78xXNXdkwX1CZeRusQfRKp+tA7hAShOK/B/fQ2" crossorigin="anonymous">
    </script>
    <!-- CSS only -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/footer.css">
    <title>Auctionin</title>
</head>

<body>
    <nav class="navbar navbar-light bg-light mw-100 px-5">
        <a class="navbar-brand" href="index.php">
            <!-- <img src="" width="30" height="30" alt=""> -->
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


    <!-- Start#mainSite -->
    <main id="main-site">
        <!-- product -->
        <section id="product" class="py-3">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6">
                        <img src="<?= $item['product_image'] ?>" alt="product" class="img-fluid">
                    </div>
                    <div class="col-sm-6">
                        <h3 class="font-roboto pt-5"><?= $item['product_name'] ?></h3>
                        <p>Expired On: <?= $item['product_expired'] ?></p>

                        <hr class="mr-0">

                        <div class="">
                            <div class="d-flex">
                                <h5>Current Bid: </h5>
                                <h5 class="text-danger ml-2"><?= $item['product_price'] ?></h5>
                            </div>
                            <p class="mt-1"><?= $item['product_desc'] ?></p>
                        </div>
                        <?php if (strtotime($item["product_expired"]) - time() >= 0) : ?>
                            <div class="form-row pt-4 font-size-16 font-roboto">
                                <div class="col">
                                    <form action="product.php?id=<?= $item['product_id'] ?>" method="post">
                                        <?php if ($item["highest_bidder_id"] == $user_id) : ?>
                                            <button type="submit" class="btn btn-primary form-control disabled"> You are the highest bidder</button>
                                        <?php elseif ($user_id == -1) : ?>
                                            <a href="login.php" class="btn btn-primary form-control"> Login To Bid</a>

                                        <?php else : ?>
                                            <h5>How much you want to bid?</h5>
                                            <input type="number" name="bidRequest" min=<?= $item['product_price'] + 10000 ?>>
                                            <p>*Atleast Rp 10.000 gap</p>
                                            <button type="submit" class="btn btn-primary form-control"> Bid Now</button>

                                        <?php endif; ?>

                                    </form>
                                </div>
                            </div>
                        <?php else : ?>
                            <?php if (isset($_SESSION["username"])) : ?>
                                <?php if ($user["user_id"] == $item["highest_bidder_id"]) : ?>
                                    <?php if (!$item["sentToAddress"]) : ?>
                                        <form action="product.php?id=<?= $item['product_id'] ?>" method="post">
                                            <button type="submit" class="btn btn-primary" name="send">Send to my address</button>
                                        </form>
                                    <?php else : ?>
                                        <div type="submit" class="btn bg-primary text-center text-white disabled">This item is sent to your address</div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </section>
        <!-- end of product -->



    </main>


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