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
            $categoryID = $data['categoryID'] ?? null;
            $categoryName = $data['categoryName'] ?? null;
            $categoryDescription = $data['categoryDescription'] ?? null;
            $categoryIcon = $data['categoryIcon'] ?? 'bi-placeholder';

            if ($categoryName && $categoryIcon && preg_match('/^bi-/', $categoryIcon)) {
                if ($categoryID == 0) {
                    $insert_sql = "INSERT INTO shopcategories (Name, Description, Icon) VALUES (?, ?, ?)";
                    $insert_stmt = $conn->prepare($insert_sql);
                    $insert_stmt->bind_param("sss", $categoryName, $categoryDescription, $categoryIcon);
                    $insert_stmt->execute();

                    if ($insert_stmt->affected_rows > 0) {
                        $response['status'] = 'success';
                        $response['message'] = 'Category created successfully.';
                    } else {
                        $response['message'] = 'Failed to create category.';
                    }

                    $insert_stmt->close();
                } else {
                    $check_sql = "SELECT * FROM shopcategories WHERE CategoryID = ?";
                    $check_stmt = $conn->prepare($check_sql);
                    $check_stmt->bind_param("i", $categoryID);
                    $check_stmt->execute();
                    $check_result = $check_stmt->get_result();

                    if ($check_result->num_rows > 0) {
                        $update_sql = "UPDATE shopcategories SET Name = ?, Description = ?, Icon = ? WHERE CategoryID = ?";
                        $update_stmt = $conn->prepare($update_sql);
                        $update_stmt->bind_param("sssi", $categoryName, $categoryDescription, $categoryIcon, $categoryID);
                        $update_stmt->execute();

                        if ($update_stmt->affected_rows > 0) {
                            $response['status'] = 'success';
                            $response['message'] = 'Category updated successfully.';
                        } else {
                            $response['message'] = 'Failed to update category.';
                        }

                        $update_stmt->close();
                    } else {
                        $response['message'] = 'Invalid category ID.';
                    }

                    $check_stmt->close();
                }
            } else {
                $response['message'] = 'Category Name and valid Icon are required.';
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