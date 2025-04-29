<?php
session_start();
require_once 'conndb.php'; // Include your database connection file

$response = array('success' => false, 'count' => 0);

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Query to count items in cart for this user
    $count_sql = "SELECT SUM(quantity) as total_items FROM cart WHERE user_id = ?";
    
    $stmt = $conn->prepare($count_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $response['count'] = (int)$row['total_items'] ?: 0; // Convert to integer, default to 0 if null
        $response['success'] = true;
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
