<?php
    include 'index.php';

    header('Content-Type: application/json');

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (isset($data['ItemID']) && isset($data['CategoryID']) && isset($data['Name']) && isset($data['Description']) && isset($data['Price'])) {
        $itemID = intval($data['ItemID']);
        $categoryID = intval($data['CategoryID']);
        $name = mysqli_real_escape_string($conn, $data['Name']);
        $description = mysqli_real_escape_string($conn, $data['Description']);
        $price = floatval($data['Price']);

        if ($itemID === 0) {
            $sql = "INSERT INTO ShopItems (CategoryID, Name, Description, Price) VALUES ($categoryID, '$name', '$description', $price)";
        } else {
            $sql = "UPDATE ShopItems SET CategoryID = $categoryID, Name = '$name', Description = '$description', Price = $price WHERE ItemID = $itemID";
        }

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
        }

        mysqli_close($conn);
    } else {
        echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
    }
?>
