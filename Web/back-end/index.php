<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "s_market"; 

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
