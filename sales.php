<?php
require_once('function.php');
date_default_timezone_set('Asia/Jakarta');
session_start();
$conn = new PDO("mysql:host=localhost:3306;dbname=auctioninuas", "admin", "123123");
updateBalance($conn);

if (!isset($_SESSION["username"])) {
  header("Location: login.php");
}

$user = getUser($_SESSION["username"], $conn);

if (isset($_POST["product_name"])) {
  $foto = $_FILES["product_image"];
  $ext = explode(".", $foto['name']);
  $ext = end($ext);
  $ext = strtolower($ext);
  if (in_array($ext, ["jpg", "png", "jpeg"])) {
    $sumber = $foto["tmp_name"];
    $tujuan = 'uploads/' . $foto['name'];
    move_uploaded_file($sumber, $tujuan);
    $status = auctionItem($conn, $user, $_POST, $tujuan);
    if ($status) {
      echo '<h3 class="bg-green-200 px-3 py-1 text-center">Your item is now listed at auction</h3>';
    } else {
      echo '<h3 class="bg-red-200 px-3 py-1 text-center">Something went wrong</h3>';
    }
  } else {
    echo '<h3 class="bg-red-200 px-3 py-1 text-center">Please only upload image file (jpg, jpeg, png)</h3>';
  }
}
?>


<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
  </script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous">
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.min.js" integrity="sha384-VHvPCCyXqtD5DqJeNxl2dtTyhF78xXNXdkwX1CZeRusQfRKp+tA7hAShOK/B/fQ2" crossorigin="anonymous">
  </script>
  <title>Auctionin</title>
</head>

<body>
  <nav class="navbar navbar-light bg-light mw-100 px-32 py-4">
    <a class="navbar-brand my-auto" href="index.php">
      <h1>Auctionin</h1>
    </a>
    <!-- <form class="d-flex my-auto">
      <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
      <button class="btn btn-outline-success" type="submit">Search</button>
    </form> -->
    <?php if (isset($_SESSION["username"])) : ?>
      <div class="d-flex menu">
        <h5 class="balance mr-20 my-auto">Balance: Rp <?= $user["balance"] ?></h5>
        <div class="dropdown my-auto">
          <button class="btn btn-primary dropdown-toggle user-dropdown bg-primary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
  <form class="text-gray-700 body-font overflow-hidden bg-white" method="post" action="sales.php" enctype="multipart/form-data">
    <div class="container px-5 py-24 mx-auto">
      <div class="lg:w-4/5 mx-auto flex flex-wrap">

        <div class="lg:w-1/2 w-full object-cover object-center rounded border border-gray-200 grid place-items-center" style="height: 56vh;">
          <label style="height: 100%;" class="flex justify-center w-full h-32 px-4 transition bg-white border-2 border-gray-300 border-dashed rounded-md appearance-none cursor-pointer hover:border-gray-400 focus:outline-none">
            <span class="flex items-center space-x-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
              </svg>
              <span class="font-medium text-gray-600">
                <span class="text-blue-600 underline">Browse image</span>
              </span>
            </span>
            <input type="file" name="product_image" class="hidden" required>
          </label>
        </div>

        <div class="lg:w-1/2 w-full lg:pl-10 lg:py-6 mt-6 lg:mt-0">
          <h3>Item Name: </h3>
          <input class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" placeholder="Item Name" name="product_name" required>

          <h3>Item Description: </h3>
          <textarea placeholder="Item Description" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mx-1" cols="30" rows="10" name="product_desc" required></textarea>

          <h3>Starting Bid: </h3>
          <input class="appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="number" placeholder="Bid" name="product_price" min="0" required>
          <br>

          <h3>Auction Duration: </h3>
          <select name="expired" id="Duration" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mx-1" required>
            <option value="1min">1 minute (demo)</option>
            <option value="24h">24 hour</option>
            <option value="1w">1 week</option>
            <option value="2w">2 weeks</option>
            <option value="1M">1 month</option>
            <option value="3M">3 months</option>
          </select>

          <!-- Button trigger modal -->
          <button type="button" class="btn btn-primary bg-success mt-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
            Start Auction
          </button>

          <!-- Modal -->
          <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                </div>
                <div class="modal-body text-danger">
                  WARNING: You cannot cancel your listing. Please make sure everthing is correct. 
                  <br>
                  *You can bid you own item to get it back. 
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary bg-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary bg-primary">Auction My Item</button>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </form>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js"></script>

</html>