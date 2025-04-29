<?php
session_start();
require_once 'conndb.php'; // Include your database connection file

$response = array('success' => false, 'deliveryDetails' => null);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];

    $stmt = $conn->prepare("SELECT full_name, address, city, phone_no FROM `c.l.details` WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response['success'] = true;
        $response['deliveryDetails'] = $row;
    } else {
        $response['message'] = 'No delivery details found.';
    }

    $stmt->close();
    $conn->close();
}

header('Content-Type: application/json');
echo json_encode($response);
?>