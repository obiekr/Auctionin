<?php
session_start();

$username = $_POST['uname'];
$password = $_POST['upass'];


$conn = new PDO("mysql:host=localhost;dbname=auctioninuas", "admin", "123123");
if ($conn->connect_error) {
    die('Could not connect to the database.');
}else{
    $sql = "SELECT * FROM user WHERE username = ?";

    $result = $conn->prepare($sql);
    $result->execute([$username]);
    
    if ($row = $result->fetch()) {
        if ($password == $row["password"]) {
            $_SESSION["username"] = $row["username"];
            
            header("Location: index.php");
        }else{
            header("Location: login.php");
        }
    }else{
        header("Location: login.php");
    }
}

