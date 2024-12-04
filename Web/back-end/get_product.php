<?php
    include 'index.php';

    header('Content-Type: application/json');

    if (isset($_GET['ItemID'])) {
        $itemID = intval($_GET['ItemID']);
        $sql = "SELECT ItemID, CategoryID, Name, Description, Price FROM ShopItems WHERE ItemID = $itemID";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $item = mysqli_fetch_assoc($result);
            echo json_encode($item);
        } else {
            echo json_encode(['success' => false, 'error' => 'Item not found']);
        }

        mysqli_close($conn);
    } else {
        echo json_encode(['success' => false, 'error' => 'No ItemID provided']);
    }
?>
