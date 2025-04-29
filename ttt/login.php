<?php
session_start();
require_once 'conndb.php'; // Ensure you have your database connection

if (isset($_POST['login'])) {
    // Get the email and password from the POST request
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute the SQL statement to find the user
    $stmt = $conn->prepare("SELECT id, fullname, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id']; // Set user ID
            $_SESSION['fullname'] = $user['fullname']; // Set full name
            echo json_encode(['success' => true, 'message' => 'Login successful!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid password.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User  not found.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Login failed.']);
}
?>