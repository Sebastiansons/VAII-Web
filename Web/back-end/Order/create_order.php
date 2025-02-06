<?php
include '../index.php';
require '../verify_sessionID.php';

header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => '');

function generateOrderID($conn) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $orderID = '';
    do {
        for ($i = 0; $i < 10; $i++) {
            $orderID .= $characters[rand(0, strlen($characters) - 1)];
        }
        $sql = "SELECT orderID FROM orders WHERE orderID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $orderID);
        $stmt->execute();
        $stmt->store_result();
    } while ($stmt->num_rows > 0);
    $stmt->close();
    return $orderID;
}

$sessionID = $_COOKIE['sessionID'] ?? null;

if ($sessionID) {
    $response = CheckSession($conn);

    if (isset($response['role']) && $response['role'] == 'Customer') {
        $clientID = $response['user_id'] ?? null;

        if ($clientID) {
            $address_sql = "SELECT street, house_number, city, postal_code, c.country_name AS country
                            FROM user_addresses ua
                            JOIN countries c ON ua.country_id = c.country_id
                            WHERE ua.user_id = ?";
            $address_stmt = $conn->prepare($address_sql);
            $address_stmt->bind_param("i", $clientID);
            $address_stmt->execute();
            $address_result = $address_stmt->get_result();
            $address_row = $address_result->fetch_assoc();

            if ($address_row) {
                $deliveryAddress = $address_row['street'] . ' ' . $address_row['house_number'] . ', ' . $address_row['city'] . ', ' . $address_row['postal_code'] . ', ' . $address_row['country'];

                $sql = "SELECT item_id, quantity FROM cart WHERE client_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $clientID);
                $stmt->execute();
                $result = $stmt->get_result();

                $itemIDs = [];
                $quantities = [];
                while ($row = $result->fetch_assoc()) {
                    $itemIDs[] = $row['item_id'];
                    $quantities[] = $row['quantity'];
                }

                if (!empty($itemIDs)) {
                    $orderID = generateOrderID($conn);
                    $itemIDsStr = implode(',', $itemIDs);
                    $quantitiesStr = implode(',', $quantities);
                    $statusID = 1;

                    $insert_sql = "INSERT INTO orders (orderID, clientID, itemIDs, quantities, statusID, deliveryAddress) VALUES (?, ?, ?, ?, ?, ?)";
                    $insert_stmt = $conn->prepare($insert_sql);
                    $insert_stmt->bind_param("sissis", $orderID, $clientID, $itemIDsStr, $quantitiesStr, $statusID, $deliveryAddress);
                    $insert_stmt->execute();

                    if ($insert_stmt->affected_rows > 0) {
                        $delete_sql = "DELETE FROM cart WHERE client_id = ?";
                        $delete_stmt = $conn->prepare($delete_sql);
                        $delete_stmt->bind_param("i", $clientID);
                        $delete_stmt->execute();

                        $response['status'] = 'success';
                        $response['message'] = 'Order created and cart cleared successfully.';
                    } else {
                        $response['message'] = 'Failed to create order.';
                    }

                    $insert_stmt->close();
                    $delete_stmt->close();
                } else {
                    $response['message'] = 'Cart is empty.';
                }

                $stmt->close();
            } else {
                $response['message'] = 'You don\'t have updated your address, update it in your profile.';
            }

            $address_stmt->close();
        } else {
            $response['message'] = 'Client ID is required.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'You do not have permission.';
    }
} else {
    $response['message'] = 'SessionID is required.';
}

$conn->close();

echo json_encode($response);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_last_error_msg();
}
?>