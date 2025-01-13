<?php
include '../index.php';
require '../verify_sessionID.php';

header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sessionID = $_COOKIE['sessionID'] ?? null;

    if ($sessionID) {
        $response = CheckSession($conn);

        if (isset($response['role']) && $response['role'] == 'Admin') { // sessionOK
            $data = json_decode(file_get_contents('php://input'), true);
            $categoryID = $data['Id'] ?? null;

            if ($categoryID) {
                // Overenie, èi kategória existuje
                $check_sql = "SELECT * FROM shopcategories WHERE CategoryID = ?";
                $check_stmt = $conn->prepare($check_sql);
                $check_stmt->bind_param("i", $categoryID);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();

                if ($check_result->num_rows > 0) {
                    // Kategória existuje, môžeme ju vymaza
                    $delete_sql = "DELETE FROM shopcategories WHERE CategoryID = ?";
                    $delete_stmt = $conn->prepare($delete_sql);
                    $delete_stmt->bind_param("i", $categoryID);
                    $delete_stmt->execute();

                    if ($delete_stmt->affected_rows > 0) {
                        $response['status'] = 'success';
                        $response['message'] = 'Category deleted successfully.';
                    } else {
                        $response['message'] = 'Failed to delete category.';
                    }

                    $delete_stmt->close();
                } else {
                    $response['message'] = 'Invalid category ID.';
                }

                $check_stmt->close();
            } else {
                $response['message'] = 'Category ID is required.';
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