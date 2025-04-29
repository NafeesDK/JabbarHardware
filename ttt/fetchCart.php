<?php
session_start();
require_once 'conndb.php'; // Include your database connection file

$isLoggedIn = isset($_SESSION['user_id']);
$response = array('success' => false, 'data' => []);

if ($isLoggedIn) {
    $user_id = $_SESSION['user_id'];
    
    $cart_sql = "SELECT p.id, p.name, p.description, p.image_url, p.original_price, p.discounted_price, c.quantity 
                  FROM cart c 
                  JOIN products p ON c.product_id = p.id 
                  WHERE c.user_id = ?";
    
    $stmt = $conn->prepare($cart_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch all cart items
    while ($row = $result->fetch_assoc()) {
        $response['data'][] = $row; 
    }
    $response['success'] = true; 
}
header('Content-Type: application/json');
echo json_encode($response);
?>