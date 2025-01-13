<?php
include '../index.php';
require '../verify_sessionID.php';

header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sessionID = $_COOKIE['sessionID'] ?? null;

    if ($sessionID) {
        $response = CheckSession($conn);

        if (isset($response['role']) && $response['role'] == 'Admin') {
            $data = json_decode(file_get_contents('php://input'), true);
            $productID = $data['Id'] ?? null;

            if ($productID) {
                $check_sql = "SELECT Image FROM shopitems WHERE itemID = ?";
                $check_stmt = $conn->prepare($check_sql);
                $check_stmt->bind_param("i", $productID);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();

                if ($check_result->num_rows > 0) {
                    $product = $check_result->fetch_assoc();
                    $images = explode(' ', $product['Image']); 

                    foreach ($images as $image) {
                        $imagePath = "../../images/products/" . basename($image);
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                    }

                    $delete_sql = "DELETE FROM shopitems WHERE itemID = ?";
                    $delete_stmt = $conn->prepare($delete_sql);
                    $delete_stmt->bind_param("i", $productID);
                    $delete_stmt->execute();

                    if ($delete_stmt->affected_rows > 0) {
                        $response['status'] = 'success';
                        $response['message'] = 'Product deleted successfully.';
                    } else {
                        $response['message'] = 'Failed to delete product.';
                    }

                    $delete_stmt->close();
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