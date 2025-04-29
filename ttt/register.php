<?php
require_once 'conndb.php';

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Handle Registration
if (isset($_POST['register'])) {
    $fullname = sanitize_input($_POST['fullname']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $response = array('success' => true, 'message' => 'Registration successful! Please login.');

    // Validate inputs
    if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        $response['message'] = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $response['message'] = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $response['message'] = "Password must be at least 6 characters long";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $response['message'] = "Email already registered";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $fullname, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Registration successful! Please login.";
            } else {
                $response['message'] = "Registration failed. Please try again.";
            }
        }
        $stmt->close();
    }
    
    header('Content-Type: application/json'); 
    echo json_encode($response);
    exit(); 
}
?>