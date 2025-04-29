<?php
session_start(); // Start the session to access $_SESSION variables
header('Content-Type: application/json'); // Ensure the response is JSON

include 'conndb.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $_DELETE); // Parse the input

    // Check if the parameters are set and user is logged in
    if (isset($_DELETE['id']) && isset($_DELETE['quantity']) && isset($_SESSION['user_id'])) {
        $productId = $_DELETE['id'];
        $quantity = $_DELETE['quantity'];
        $userId = $_SESSION['user_id'];

        error_log("Received Product ID: $productId, Quantity to Delete: $quantity, User ID: $userId");

        // Prepare and execute the update statement
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity - ? WHERE product_id = ? AND user_id = ? AND quantity >= ?");
        $stmt->bind_param("iiid", $quantity, $productId, $userId, $quantity);
        
        if ($stmt->execute()) {
            // Check the new quantity
            $stmt = $conn->prepare("SELECT quantity FROM cart WHERE product_id = ? AND user_id = ?");
            $stmt->bind_param("ii", $productId, $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row && $row['quantity'] <= 0) {
                // If quantity is 0 or less, delete the product from the cart
                $stmt = $conn->prepare("DELETE FROM cart WHERE product_id = ? AND user_id = ?");
                $stmt->bind_param("ii", $productId, $userId);
                
                if ($stmt->execute()) {
                    error_log("Deleted item with zero quantity: Product ID: $productId, User ID: $userId");
                } else {
                    error_log("Failed to delete item with zero quantity: " . $stmt->error);
                }
            }

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating quantity: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'User not logged in']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
        }
    }

    $conn->close();
}
?>