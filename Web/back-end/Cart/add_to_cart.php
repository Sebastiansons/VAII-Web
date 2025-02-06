<?php
include '../index.php';
require '../verify_sessionID.php';

header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sessionID = $_COOKIE['sessionID'] ?? null;

    if ($sessionID) {
        $response = CheckSession($conn);

        if (isset($response['role']) && $response['role'] == 'Customer') {
            $itemID = $_POST['ItemID'] ?? null;
            $clientID = $response['user_id'] ?? null;

            if ($itemID && $clientID) {
                // Skontrolujeme, èi už položka existuje v košíku
                $check_sql = "SELECT quantity FROM cart WHERE client_id = ? AND item_id = ?";
                $check_stmt = $conn->prepare($check_sql);
                $check_stmt->bind_param("ii", $clientID, $itemID);
                $check_stmt->execute();
                $check_stmt->store_result();

                if ($check_stmt->num_rows > 0) {
                    // Ak položka existuje, zvýšime jej množstvo o 1
                    $check_stmt->bind_result($quantity);
                    $check_stmt->fetch();
                    $new_quantity = $quantity + 1;

                    $update_sql = "UPDATE cart SET quantity = ? WHERE client_id = ? AND item_id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("iii", $new_quantity, $clientID, $itemID);
                    $update_stmt->execute();

                    if ($update_stmt->affected_rows > 0) {
                        $response['status'] = 'success';
                        $response['message'] = 'Item quantity updated successfully.';
                    } else {
                        $response['message'] = 'Failed to update item quantity.';
                    }

                    $update_stmt->close();
                } else {
                    // Ak položka neexistuje, pridáme ju do košíka
                    $insert_sql = "INSERT INTO cart (client_id, item_id, quantity) VALUES (?, ?, 1)";
                    $insert_stmt = $conn->prepare($insert_sql);
                    $insert_stmt->bind_param("ii", $clientID, $itemID);
                    $insert_stmt->execute();

                    if ($insert_stmt->affected_rows > 0) {
                        $response['status'] = 'success';
                        $response['message'] = 'Item added to cart successfully.';
                    } else {
                        $response['message'] = 'Failed to add item to cart.';
                    }

                    $insert_stmt->close();
                }

                $check_stmt->close();
            } else {
                $response['message'] = 'Item ID and Client ID are required.';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'You do not have permission.';
        }
    } else {
        $response['message'] = 'SessionID is required.';
    }

    $conn->close();
}

echo json_encode($response);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_last_error_msg();
}
?>