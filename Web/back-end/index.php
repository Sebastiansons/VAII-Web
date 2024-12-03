<?php
    $servername = "localhost";
    $username = "root"; // Predvolený užívate¾ v XAMPP je "root" bez hesla
    $password = "";
    $dbname = "s_market"; // názov databázy, ktorú si vytvoril

    // Vytvorenie spojenia
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Kontrola spojenia
    if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
    }
?>
