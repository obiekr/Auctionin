<?php
session_start();

session_unset();
session_destroy();
// echo "Session nama dan user_id sudah di clear.";
header("Location: index.php");


