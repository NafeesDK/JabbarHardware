<?php
session_start();
require_once 'conndb.php'; // Include your database connection file

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the data from the request
    $user_id = $_SESSION['user_id']; // Assuming the user ID is stored in the session
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $phone_no = $_POST['phone_no'];
    $created_at = date('Y-m-d H:i:s'); // Current timestamp

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO `c.l.details` (user_id, full_name, address, city, phone_no, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $full_name, $address, $city, $phone_no, $created_at);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Order details submitted successfully.';
    } else {
        $response['message'] = 'Failed to submit order details: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

// Always return a JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>