<?php
    include 'index.php';

    header('Content-Type: application/json');

    $sql = "SELECT CategoryID, Name, Description, Icon, IsNew, IsUnavailable FROM ShopCategories";
    $result = mysqli_query($conn, $sql);

    $categories = array();

    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
    }

    echo json_encode($categories);

    mysqli_close($conn);
?>
