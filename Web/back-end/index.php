<?php
    $servername = "localhost";
    $username = "root"; // Predvolen� u��vate� v XAMPP je "root" bez hesla
    $password = "";
    $dbname = "s_market"; // n�zov datab�zy, ktor� si vytvoril

    // Vytvorenie spojenia
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Kontrola spojenia
    if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
    }
?>
