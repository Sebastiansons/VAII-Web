<?php
    include 'index.php';

    header('Content-Type: application/json');

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (isset($data['ItemID'])) {
        $itemID = intval($data['ItemID']);
        $sql = "DELETE FROM ShopItems WHERE ItemID = $itemID";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
        }

        mysqli_close($conn);
    } else {
        echo json_encode(['success' => false, 'error' => 'No ItemID provided']);
    }
?>
