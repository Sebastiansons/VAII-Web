<?php
include '../index.php';
require '../verify_sessionID.php';

header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => '', 'data' => null);

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sessionID = $_COOKIE['sessionID'] ?? null;

    if ($sessionID) {
        $response = CheckSession($conn);

        if (isset($response['role']) && $response['role'] == 'Admin') {
            $productID = $_GET['productID'] ?? null;

            if ($productID) {
                $check_sql = "SELECT ItemID, CategoryID, Name, Description, Price FROM ShopItems WHERE ItemID = ?";
                $check_stmt = $conn->prepare($check_sql);
                $check_stmt->bind_param("i", $productID);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();

                if ($check_result->num_rows > 0) {
                    $product = $check_result->fetch_assoc();
                    $response['status'] = 'success';
                    $response['data'] = $product;
                } else {
                    $response['message'] = 'Invalid product ID.';
                }

                $check_stmt->close();
            } else {
                $response['message'] = 'Product ID is required.';
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