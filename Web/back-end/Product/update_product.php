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
            $itemID = $_POST['ItemID'] ?? null;
            $categoryID = $_POST['CategoryID'] ?? null;
            $name = $_POST['Name'] ?? null;
            $description = $_POST['Description'] ?? null;
            $price = $_POST['Price'] ?? null;
            $images = $_FILES['images'] ?? null;

            if ($categoryID && $name && $description && $price) {
                if ($images && count($images['name']) > 0 && count($images['name']) <= 3) {
                    $allowedTypes = ['image/jpeg', 'image/png'];
                    $totalSize = 0;
                    $imagePaths = [];

                    for ($i = 0; $i < count($images['name']); $i++) {
                        $fileType = $images['type'][$i];
                        $fileSize = $images['size'][$i];
                        $fileTmpName = $images['tmp_name'][$i];
                        $fileExtension = pathinfo($images['name'][$i], PATHINFO_EXTENSION);
                        $fileName = uniqid() . '.' . $fileExtension;

                        if (!in_array($fileType, $allowedTypes)) {
                            $response['message'] = 'Only JPG and PNG images are allowed.';
                            echo json_encode($response);
                            exit;
                        }

                        $totalSize += $fileSize;
                        if ($totalSize > 5 * 1024 * 1024) {
                            $response['message'] = 'Total image size must not exceed 5MB.';
                            echo json_encode($response);
                            exit;
                        }

                        $targetDir = "../../images/products/";
                        $targetFilePath = $targetDir . $fileName;

                        if (move_uploaded_file($fileTmpName, $targetFilePath)) {
                            $imagePaths[] = $targetFilePath;
                        } else {
                            $response['message'] = 'Failed to upload image.';
                            echo json_encode($response);
                            exit;
                        }
                    }

                    $imagePathsString = implode(',', $imagePaths);

                    if ($itemID == 0) {
                        $insert_sql = "INSERT INTO ShopItems (CategoryID, Name, Description, Price, Image) VALUES (?, ?, ?, ?, ?)";
                        $insert_stmt = $conn->prepare($insert_sql);
                        $insert_stmt->bind_param("issss", $categoryID, $name, $description, $price, $imagePathsString);
                        $insert_stmt->execute();

                        if ($insert_stmt->affected_rows > 0) {
                            $response['status'] = 'success';
                            $response['message'] = 'Product created successfully.';
                        } else {
                            $response['message'] = 'Failed to create product.';
                        }

                        $insert_stmt->close();
                    } else {
                        $check_sql = "SELECT * FROM ShopItems WHERE ItemID = ?";
                        $check_stmt = $conn->prepare($check_sql);
                        $check_stmt->bind_param("i", $itemID);
                        $check_stmt->execute();
                        $check_result = $check_stmt->get_result();

                        if ($check_result->num_rows > 0) {
                            $oldImages = $check_result->fetch_assoc()['Image'];
                            $oldImagePaths = explode(',', $oldImages);
                            foreach ($oldImagePaths as $oldImagePath) {
                                if (file_exists($oldImagePath)) {
                                    unlink($oldImagePath);
                                }
                            }

                            $update_sql = "UPDATE ShopItems SET CategoryID = ?, Name = ?, Description = ?, Price = ?, Image = ? WHERE ItemID = ?";
                            $update_stmt = $conn->prepare($update_sql);
                            $update_stmt->bind_param("issssi", $categoryID, $name, $description, $price, $imagePathsString, $itemID);
                            $update_stmt->execute();

                            if ($update_stmt->affected_rows > 0) {
                                $response['status'] = 'success';
                                $response['message'] = 'Product updated successfully.';
                            } else {
                                $response['message'] = 'Failed to update product.';
                            }

                            $update_stmt->close();
                        } else {
                            $response['message'] = 'Invalid product ID.';
                        }

                        $check_stmt->close();
                    }
                } else {
                    $response['message'] = 'Please upload between 1 and 3 images.';
                }
            } else {
                $response['message'] = 'Category ID, Name, Description, and Price are required.';
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