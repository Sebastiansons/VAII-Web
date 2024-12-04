<?php
    include 'index.php';

    header('Content-Type: application/json');

    $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
    $search_name = isset($_GET['search_name']) ? mysqli_real_escape_string($conn, $_GET['search_name']) : '';
    $max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 0;

    $sql = "SELECT ItemID, Name, Description, Price, Image FROM ShopItems WHERE 1=1";

    if ($category_id > 0) {
        $sql .= " AND CategoryID = $category_id";
    }

    if (!empty($search_name)) {
        $sql .= " AND Name LIKE '%$search_name%'";
    }

    if ($max_price > 0) {
        $sql .= " AND Price <= $max_price";
    }

    $result = mysqli_query($conn, $sql);

    $items = array();

    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $items[] = $row;
        }
    }

    echo json_encode($items);

    mysqli_close($conn);
?>
